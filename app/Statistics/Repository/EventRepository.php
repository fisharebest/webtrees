<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Statistics\Repository;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Elements\UnknownElement;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\EventRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;

use function array_map;
use function array_merge;
use function e;
use function strncmp;
use function substr;

/**
 * A repository providing methods for event related statistics.
 */
class EventRepository implements EventRepositoryInterface
{
    /**
     * Sorting directions.
     */
    private const SORT_ASC  = 'ASC';
    private const SORT_DESC = 'DESC';

    /**
     * Event facts.
     */
    private const EVENT_BIRTH    = 'BIRT';
    private const EVENT_DEATH    = 'DEAT';
    private const EVENT_MARRIAGE = 'MARR';
    private const EVENT_DIVORCE  = 'DIV';

    private Tree $tree;

    /**
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * Returns the total number of a given list of events (with dates).
     *
     * @param array<string> $events The list of events to count (e.g. BIRT, DEAT, ...)
     *
     * @return int
     */
    private function getEventCount(array $events): int
    {
        $query = DB::table('dates')
            ->where('d_file', '=', $this->tree->id());

        $no_types = [
            'HEAD',
            'CHAN',
        ];

        if ($events !== []) {
            $types = [];

            foreach ($events as $type) {
                if (strncmp($type, '!', 1) === 0) {
                    $no_types[] = substr($type, 1);
                } else {
                    $types[] = $type;
                }
            }

            if ($types !== []) {
                $query->whereIn('d_fact', $types);
            }
        }

        return $query->whereNotIn('d_fact', $no_types)
            ->count();
    }

    /**
     * @param array<string> $events
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
     * @return string
     */
    public function totalEventsBirth(): string
    {
        return $this->totalEvents(Gedcom::BIRTH_EVENTS);
    }

    /**
     * @return string
     */
    public function totalBirths(): string
    {
        return $this->totalEvents([self::EVENT_BIRTH]);
    }

    /**
     * @return string
     */
    public function totalEventsDeath(): string
    {
        return $this->totalEvents(Gedcom::DEATH_EVENTS);
    }

    /**
     * @return string
     */
    public function totalDeaths(): string
    {
        return $this->totalEvents([self::EVENT_DEATH]);
    }

    /**
     * @return string
     */
    public function totalEventsMarriage(): string
    {
        return $this->totalEvents(Gedcom::MARRIAGE_EVENTS);
    }

    /**
     * @return string
     */
    public function totalMarriages(): string
    {
        return $this->totalEvents([self::EVENT_MARRIAGE]);
    }

    /**
     * @return string
     */
    public function totalEventsDivorce(): string
    {
        return $this->totalEvents(Gedcom::DIVORCE_EVENTS);
    }

    /**
     * @return string
     */
    public function totalDivorces(): string
    {
        return $this->totalEvents([self::EVENT_DIVORCE]);
    }

    /**
     * Retursn the list of common facts used query the data.
     *
     * @return array<string>
     */
    private function getCommonFacts(): array
    {
        // The list of facts used to limit the query result
        return array_merge(
            Gedcom::BIRTH_EVENTS,
            Gedcom::MARRIAGE_EVENTS,
            Gedcom::DIVORCE_EVENTS,
            Gedcom::DEATH_EVENTS
        );
    }

    /**
     * @return string
     */
    public function totalEventsOther(): string
    {
        $no_facts = array_map(
            static function (string $fact): string {
                return '!' . $fact;
            },
            $this->getCommonFacts()
        );

        return $this->totalEvents($no_facts);
    }

    /**
     * Returns the first/last event record from the given list of event facts.
     *
     * @param string $direction The sorting direction of the query (To return first or last record)
     *
     * @return object|null
     */
    private function eventQuery(string $direction): ?object
    {
        return DB::table('dates')
            ->select(['d_gid as id', 'd_year as year', 'd_fact AS fact', 'd_type AS type'])
            ->where('d_file', '=', $this->tree->id())
            ->where('d_gid', '<>', Header::RECORD_TYPE)
            ->whereIn('d_fact', $this->getCommonFacts())
            ->where('d_julianday1', '<>', 0)
            ->orderBy('d_julianday1', $direction)
            ->orderBy('d_type')
            ->first();
    }

    /**
     * Returns the formatted first/last occuring event.
     *
     * @param string $direction The sorting direction
     *
     * @return string
     */
    private function getFirstLastEvent(string $direction): string
    {
        $row    = $this->eventQuery($direction);
        $result = I18N::translate('This information is not available.');

        if ($row) {
            $record = Registry::gedcomRecordFactory()->make($row->id, $this->tree);

            if ($record && $record->canShow()) {
                $result = $record->formatList();
            } else {
                $result = I18N::translate('This information is private and cannot be shown.');
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function firstEvent(): string
    {
        return $this->getFirstLastEvent(self::SORT_ASC);
    }

    /**
     * @return string
     */
    public function lastEvent(): string
    {
        return $this->getFirstLastEvent(self::SORT_DESC);
    }

    /**
     * Returns the formatted year of the first/last occuring event.
     *
     * @param string $direction The sorting direction
     *
     * @return string
     */
    private function getFirstLastEventYear(string $direction): string
    {
        $row = $this->eventQuery($direction);

        if (!$row) {
            return '';
        }

        return (new Date($row->type . ' ' . $row->year))
            ->display();
    }

    /**
     * @return string
     */
    public function firstEventYear(): string
    {
        return $this->getFirstLastEventYear(self::SORT_ASC);
    }

    /**
     * @return string
     */
    public function lastEventYear(): string
    {
        return $this->getFirstLastEventYear(self::SORT_DESC);
    }

    /**
     * Returns the formatted type of the first/last occurring event.
     *
     * @param string $direction The sorting direction
     *
     * @return string
     */
    private function getFirstLastEventType(string $direction): string
    {
        $row = $this->eventQuery($direction);

        if ($row === null) {
            return '';
        }

        foreach ([Individual::RECORD_TYPE, Family::RECORD_TYPE] as $record_type) {
            $element = Registry::elementFactory()->make($record_type . ':' . $row->fact);

            if (!$element instanceof UnknownElement) {
                return $element->label();
            }
        }

        return $row->fact;
    }

    /**
     * @return string
     */
    public function firstEventType(): string
    {
        return $this->getFirstLastEventType(self::SORT_ASC);
    }

    /**
     * @return string
     */
    public function lastEventType(): string
    {
        return $this->getFirstLastEventType(self::SORT_DESC);
    }

    /**
     * Returns the formatted name of the first/last occuring event.
     *
     * @param string $direction The sorting direction
     *
     * @return string
     */
    private function getFirstLastEventName(string $direction): string
    {
        $row = $this->eventQuery($direction);

        if ($row) {
            $record = Registry::gedcomRecordFactory()->make($row->id, $this->tree);

            if ($record) {
                return '<a href="' . e($record->url()) . '">' . $record->fullName() . '</a>';
            }
        }

        return '';
    }

    /**
     * @return string
     */
    public function firstEventName(): string
    {
        return $this->getFirstLastEventName(self::SORT_ASC);
    }

    /**
     * @return string
     */
    public function lastEventName(): string
    {
        return $this->getFirstLastEventName(self::SORT_DESC);
    }

    /**
     * Returns the formatted place of the first/last occuring event.
     *
     * @param string $direction The sorting direction
     *
     * @return string
     */
    private function getFirstLastEventPlace(string $direction): string
    {
        $row = $this->eventQuery($direction);

        if ($row) {
            $record = Registry::gedcomRecordFactory()->make($row->id, $this->tree);
            $fact   = null;

            if ($record) {
                $fact = $record->facts([$row->fact])->first();
            }

            if ($fact instanceof Fact) {
                return FunctionsPrint::formatFactPlace($fact, true, true, true);
            }
        }

        return I18N::translate('Private');
    }

    /**
     * @return string
     */
    public function firstEventPlace(): string
    {
        return $this->getFirstLastEventPlace(self::SORT_ASC);
    }

    /**
     * @return string
     */
    public function lastEventPlace(): string
    {
        return $this->getFirstLastEventPlace(self::SORT_DESC);
    }
}
