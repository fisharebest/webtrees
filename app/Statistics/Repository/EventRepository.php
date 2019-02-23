<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\EventRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

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
    private const EVENT_ADOPTION = 'ADOP';
    private const EVENT_BURIAL   = 'BURI';
    private const EVENT_CENSUS   = 'CENS';

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
    private function getEventCount(array $events): int
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
     * @inheritDoc
     */
    public function totalEvents(array $events = []): string
    {
        return I18N::number(
            $this->getEventCount($events)
        );
    }

    /**
     * @inheritDoc
     */
    public function totalEventsBirth(): string
    {
        return $this->totalEvents(Gedcom::BIRTH_EVENTS);
    }

    /**
     * @inheritDoc
     */
    public function totalBirths(): string
    {
        return $this->totalEvents([self::EVENT_BIRTH]);
    }

    /**
     * @inheritDoc
     */
    public function totalEventsDeath(): string
    {
        return $this->totalEvents(Gedcom::DEATH_EVENTS);
    }

    /**
     * @inheritDoc
     */
    public function totalDeaths(): string
    {
        return $this->totalEvents([self::EVENT_DEATH]);
    }

    /**
     * @inheritDoc
     */
    public function totalEventsMarriage(): string
    {
        return $this->totalEvents(Gedcom::MARRIAGE_EVENTS);
    }

    /**
     * @inheritDoc
     */
    public function totalMarriages(): string
    {
        return $this->totalEvents([self::EVENT_MARRIAGE]);
    }

    /**
     * @inheritDoc
     */
    public function totalEventsDivorce(): string
    {
        return $this->totalEvents(Gedcom::DIVORCE_EVENTS);
    }

    /**
     * @inheritDoc
     */
    public function totalDivorces(): string
    {
        return $this->totalEvents([self::EVENT_DIVORCE]);
    }

    /**
     * Retursn the list of common facts used query the data.
     *
     * @return array
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
     * @inheritDoc
     */
    public function totalEventsOther(): string
    {
        $no_facts = array_map(
            function (string $fact) {
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
     * @return Model|Builder|object|null
     */
    private function eventQuery(string $direction)
    {
        return DB::table('dates')
            ->select(['d_gid as id', 'd_year as year', 'd_fact AS fact', 'd_type AS type'])
            ->where('d_file', '=', $this->tree->id())
            ->where('d_gid', '<>', 'HEAD')
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
            $record = GedcomRecord::getInstance($row->id, $this->tree);

            if ($record && $record->canShow()) {
                $result = $record->formatList();
            } else {
                $result = I18N::translate('This information is private and cannot be shown.');
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function firstEvent(): string
    {
        return $this->getFirstLastEvent(self::SORT_ASC);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function firstEventYear(): string
    {
        return $this->getFirstLastEventYear(self::SORT_ASC);
    }

    /**
     * @inheritDoc
     */
    public function lastEventYear(): string
    {
        return $this->getFirstLastEventYear(self::SORT_DESC);
    }

    /**
     * Returns the formatted type of the first/last occuring event.
     *
     * @param string $direction The sorting direction
     *
     * @return string
     */
    private function getFirstLastEventType(string $direction): string
    {
        $row = $this->eventQuery($direction);

        if ($row) {
            $event_types = [
                self::EVENT_BIRTH    => I18N::translate('birth'),
                self::EVENT_DEATH    => I18N::translate('death'),
                self::EVENT_MARRIAGE => I18N::translate('marriage'),
                self::EVENT_ADOPTION => I18N::translate('adoption'),
                self::EVENT_BURIAL   => I18N::translate('burial'),
                self::EVENT_CENSUS   => I18N::translate('census added'),
            ];

            return $event_types[$row->fact] ?? GedcomTag::getLabel($row->fact);
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function firstEventType(): string
    {
        return $this->getFirstLastEventType(self::SORT_ASC);
    }

    /**
     * @inheritDoc
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
            $record = GedcomRecord::getInstance($row->id, $this->tree);

            if ($record) {
                return '<a href="' . e($record->url()) . '">' . $record->fullName() . '</a>';
            }
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function firstEventName(): string
    {
        return $this->getFirstLastEventName(self::SORT_ASC);
    }

    /**
     * @inheritDoc
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
            $record = GedcomRecord::getInstance($row->id, $this->tree);
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
     * @inheritDoc
     */
    public function firstEventPlace(): string
    {
        return $this->getFirstLastEventPlace(self::SORT_ASC);
    }

    /**
     * @inheritDoc
     */
    public function lastEventPlace(): string
    {
        return $this->getFirstLastEventPlace(self::SORT_DESC);
    }
}
