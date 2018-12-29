<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Statistics\Repository;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Helper\Sql;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\EventRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Statistics submodule providing all EVENT related methods.
 */
class EventRepository implements EventRepositoryInterface
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * Returns the total number of a given list of events (with dates).
     *
     * @param array $events The list of events to count (e.g. BIRT, DEAT, ...)
     *
     * @return int
     */
    private function getEventCount(array $events = []): int
    {
        $query = DB::table('dates')
            ->where('d_file', '=', $this->tree->id());

        $no_types = [
            'HEAD',
            'CHAN',
        ];

        if ($events) {
            $types = [];

            foreach ($events as $type) {
                if (strncmp($type, '!', 1) === 0) {
                    $no_types[] = substr($type, 1);
                } else {
                    $types[] = $type;
                }
            }

            if ($types) {
                $query->whereIn('d_fact', $types);
            }
        }

        return $query->whereNotIn('d_fact', $no_types)
            ->count();
    }

    /**
     * Count the number of events (with dates).
     *
     * @param string[] $events
     *
     * @return string
     */
    public function totalEvents(array $events = []): string
    {
        return I18N::number(
            $this->getEventCount($events)
        );
    }

    /**
     * Count the number of births events (BIRT, CHR, BAPM, ADOP).
     *
     * @return string
     */
    public function totalEventsBirth(): string
    {
        return $this->totalEvents(Gedcom::BIRTH_EVENTS);
    }

    /**
     * Count the number of births (BIRT).
     *
     * @return string
     */
    public function totalBirths(): string
    {
        return $this->totalEvents(['BIRT']);
    }

    /**
     * Count the number of death events (DEAT, BURI, CREM).
     *
     * @return string
     */
    public function totalEventsDeath(): string
    {
        return $this->totalEvents(Gedcom::DEATH_EVENTS);
    }

    /**
     * Count the number of deaths (DEAT).
     *
     * @return string
     */
    public function totalDeaths(): string
    {
        return $this->totalEvents(['DEAT']);
    }

    /**
     * Count the number of marriage events (MARR, _NMR).
     *
     * @return string
     */
    public function totalEventsMarriage(): string
    {
        return $this->totalEvents(Gedcom::MARRIAGE_EVENTS);
    }

    /**
     * Count the number of marriages (MARR).
     *
     * @return string
     */
    public function totalMarriages(): string
    {
        return $this->totalEvents(['MARR']);
    }

    /**
     * Count the number of divorce events (DIV, ANUL, _SEPR).
     *
     * @return string
     */
    public function totalEventsDivorce(): string
    {
        return $this->totalEvents(Gedcom::DIVORCE_EVENTS);
    }

    /**
     * Count the number of divorces (DIV).
     *
     * @return string
     */
    public function totalDivorces(): string
    {
        return $this->totalEvents(['DIV']);
    }

    /**
     * Count the number of other events.
     *
     * @return string
     */
    public function totalEventsOther(): string
    {
        $facts = array_merge(
            Gedcom::BIRTH_EVENTS,
            Gedcom::MARRIAGE_EVENTS,
            Gedcom::DIVORCE_EVENTS,
            Gedcom::DEATH_EVENTS
        );

        $no_facts = array_map(
            function (string $fact) {
                return '!' . $fact;
            },
            $facts
        );

        return $this->totalEvents($no_facts);
    }

    /**
     * Run an SQL query and cache the result.
     *
     * @param string $sql
     *
     * @return \stdClass[]
     */
    private function runSql($sql): array
    {
        return Sql::runSql($sql);
    }

    /**
     * Events
     *
     * @param string   $type
     * @param string   $direction
     * @param string[] $facts
     *
     * @return string
     */
    private function eventQuery(string $type, string $direction, array $facts): string
    {
        $eventTypes = [
            'BIRT' => I18N::translate('birth'),
            'DEAT' => I18N::translate('death'),
            'MARR' => I18N::translate('marriage'),
            'ADOP' => I18N::translate('adoption'),
            'BURI' => I18N::translate('burial'),
            'CENS' => I18N::translate('census added'),
        ];

        $fact_query = "IN ('" . implode("','", $facts) . "')";

        if ($direction !== 'ASC') {
            $direction = 'DESC';
        }

        $rows = $this->runSql(
            ' SELECT' .
            ' d_gid AS id,' .
            ' d_year AS year,' .
            ' d_fact AS fact,' .
            ' d_type AS type' .
            ' FROM' .
            " `##dates`" .
            ' WHERE' .
            " d_file={$this->tree->id()} AND" .
            " d_gid<>'HEAD' AND" .
            " d_fact {$fact_query} AND" .
            ' d_julianday1<>0' .
            ' ORDER BY' .
            " d_julianday1 {$direction}, d_type LIMIT 1"
        );

        if (!isset($rows[0])) {
            return '';
        }
        $row    = $rows[0];
        $record = GedcomRecord::getInstance($row->id, $this->tree);
        switch ($type) {
            default:
            case 'full':
                if ($record && $record->canShow()) {
                    $result = $record->formatList();
                } else {
                    $result = I18N::translate('This information is private and cannot be shown.');
                }
                break;

            case 'year':
                $date   = new Date($row->type . ' ' . $row->year);
                $result = $date->display();
                break;

            case 'type':
                if (isset($eventTypes[$row->fact])) {
                    $result = $eventTypes[$row->fact];
                } else {
                    $result = GedcomTag::getLabel($row->fact);
                }
                break;

            case 'name':
                $result = '<a href="' . e($record->url()) . '">' . $record->getFullName() . '</a>';
                break;

            case 'place':
                $fact = $record->getFirstFact($row->fact);
                if ($fact) {
                    $result = FunctionsPrint::formatFactPlace($fact, true, true, true);
                } else {
                    $result = I18N::translate('Private');
                }
                break;
        }

        return $result;
    }

    /**
     * Find the earliest event.
     *
     * @return string
     */
    public function firstEvent(): string
    {
        return $this->eventQuery(
            'full',
            'ASC',
            array_merge(
                Gedcom::BIRTH_EVENTS,
                Gedcom::MARRIAGE_EVENTS,
                Gedcom::DIVORCE_EVENTS,
                Gedcom::DEATH_EVENTS
            )
        );
    }

    /**
     * Find the year of the earliest event.
     *
     * @return string
     */
    public function firstEventYear(): string
    {
        return $this->eventQuery(
            'year',
            'ASC',
            array_merge(
                Gedcom::BIRTH_EVENTS,
                Gedcom::MARRIAGE_EVENTS,
                Gedcom::DIVORCE_EVENTS,
                Gedcom::DEATH_EVENTS
            )
        );
    }

    /**
     * Find the type of the earliest event.
     *
     * @return string
     */
    public function firstEventType(): string
    {
        return $this->eventQuery(
            'type',
            'ASC',
            array_merge(
                Gedcom::BIRTH_EVENTS,
                Gedcom::MARRIAGE_EVENTS,
                Gedcom::DIVORCE_EVENTS,
                Gedcom::DEATH_EVENTS
            )
        );
    }

    /**
     * Find the name of the individual with the earliest event.
     *
     * @return string
     */
    public function firstEventName(): string
    {
        return $this->eventQuery(
            'name',
            'ASC',
            array_merge(
                Gedcom::BIRTH_EVENTS,
                Gedcom::MARRIAGE_EVENTS,
                Gedcom::DIVORCE_EVENTS,
                Gedcom::DEATH_EVENTS
            )
        );
    }

    /**
     * Find the location of the earliest event.
     *
     * @return string
     */
    public function firstEventPlace(): string
    {
        return $this->eventQuery(
            'place',
            'ASC',
            array_merge(
                Gedcom::BIRTH_EVENTS,
                Gedcom::MARRIAGE_EVENTS,
                Gedcom::DIVORCE_EVENTS,
                Gedcom::DEATH_EVENTS
            )
        );
    }

    /**
     * Find the latest event.
     *
     * @return string
     */
    public function lastEvent(): string
    {
        return $this->eventQuery(
            'full',
            'DESC',
            array_merge(
                Gedcom::BIRTH_EVENTS,
                Gedcom::MARRIAGE_EVENTS,
                Gedcom::DIVORCE_EVENTS,
                Gedcom::DEATH_EVENTS
            )
        );
    }

    /**
     * Find the year of the latest event.
     *
     * @return string
     */
    public function lastEventYear(): string
    {
        return $this->eventQuery(
            'year',
            'DESC',
            array_merge(
                Gedcom::BIRTH_EVENTS,
                Gedcom::MARRIAGE_EVENTS,
                Gedcom::DIVORCE_EVENTS,
                Gedcom::DEATH_EVENTS
            )
        );
    }

    /**
     * Find the type of the latest event.
     *
     * @return string
     */
    public function lastEventType(): string
    {
        return $this->eventQuery(
            'type',
            'DESC',
            array_merge(
                Gedcom::BIRTH_EVENTS,
                Gedcom::MARRIAGE_EVENTS,
                Gedcom::DIVORCE_EVENTS,
                Gedcom::DEATH_EVENTS
            )
        );
    }

    /**
     * Find the name of the individual with the latest event.
     *
     * @return string
     */
    public function lastEventName(): string
    {
        return $this->eventQuery(
            'name',
            'DESC',
            array_merge(
                Gedcom::BIRTH_EVENTS,
                Gedcom::MARRIAGE_EVENTS,
                Gedcom::DIVORCE_EVENTS,
                Gedcom::DEATH_EVENTS
            )
        );
    }

    /**
     * FInd the location of the latest event.
     *
     * @return string
     */
    public function lastEventPlace(): string
    {
        return $this->eventQuery(
            'place',
            'DESC',
            array_merge(
                Gedcom::BIRTH_EVENTS,
                Gedcom::MARRIAGE_EVENTS,
                Gedcom::DIVORCE_EVENTS,
                Gedcom::DEATH_EVENTS
            )
        );
    }
}
