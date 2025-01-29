<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;

use function abs;
use function e;

class EventRepository
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
     * @param array<string> $events
     */
    private function countEvents(array $events): int
    {
        return DB::table('dates')
            ->where('d_file', '=', $this->tree->id())
            ->whereIn('d_fact', $events)
            ->count();
    }

    /**
     * @param array<string> $events
     */
    private function countOtherEvents(array $events): int
    {
        return DB::table('dates')
            ->where('d_file', '=', $this->tree->id())
            ->whereNotIn('d_fact', $events)
            ->count();
    }

    public function totalEvents(): string
    {
        return I18N::number($this->countOtherEvents(['CHAN']));
    }

    /**
     * @return string
     */
    public function totalEventsBirth(): string
    {
        return I18N::number($this->countEvents(Gedcom::BIRTH_EVENTS));
    }

    /**
     * @return string
     */
    public function totalBirths(): string
    {
        return I18N::number($this->countIndividualsWithEvents([self::EVENT_BIRTH]));
    }

    /**
     * @return string
     */
    public function totalEventsDeath(): string
    {
        return I18N::number($this->countEvents(Gedcom::DEATH_EVENTS));
    }

    /**
     * @return string
     */
    public function totalDeaths(): string
    {
        return I18N::number($this->countIndividualsWithEvents([self::EVENT_DEATH]));
    }

    /**
     * @return string
     */
    public function totalEventsMarriage(): string
    {
        return I18N::number($this->countEvents(Gedcom::MARRIAGE_EVENTS));
    }

    /**
     * @return string
     */
    public function totalMarriages(): string
    {
        return I18N::number($this->countFamiliesWithEvents([self::EVENT_MARRIAGE]));
    }

    /**
     * @return string
     */
    public function totalEventsDivorce(): string
    {
        return I18N::number($this->countEvents(Gedcom::DIVORCE_EVENTS));
    }

    /**
     * @return string
     */
    public function totalDivorces(): string
    {
        return I18N::number($this->countFamiliesWithEvents([self::EVENT_DIVORCE]));
    }

    public function totalEventsOther(): string
    {
        $events = array_merge(
            ['CHAN'],
            Gedcom::BIRTH_EVENTS,
            Gedcom::DEATH_EVENTS,
            Gedcom::MARRIAGE_EVENTS,
            Gedcom::DIVORCE_EVENTS
        );

        return I18N::number($this->countOtherEvents($events));
    }

    /**
     * Returns the first/last event record from the given list of event facts.
     *
     * @param string $direction The sorting direction of the query (To return first or last record)
     *
     * @return object{id:string,year:int,fact:string,type:string}|null
     */
    private function eventQuery(string $direction): ?object
    {
        $events = [
            ...Gedcom::BIRTH_EVENTS,
            ...Gedcom::DEATH_EVENTS,
            ...Gedcom::MARRIAGE_EVENTS,
            ...Gedcom::DIVORCE_EVENTS,
        ];


        return DB::table('dates')
            ->select(['d_gid as id', 'd_year as year', 'd_fact AS fact', 'd_type AS type'])
            ->where('d_file', '=', $this->tree->id())
            ->whereIn('d_fact', $events)
            ->where('d_julianday1', '<>', 0)
            ->orderBy('d_julianday1', $direction)
            ->orderBy('d_type')
            ->limit(1)
            ->get()
            ->map(static fn (object $row): object => (object) [
                'id'   => $row->id,
                'year' => (int) $row->year,
                'fact' => $row->fact,
                'type' => $row->type,
            ])
            ->first();
    }

    /**
     * Returns the formatted first/last occurring event.
     *
     * @param string $direction The sorting direction
     *
     * @return string
     */
    private function getFirstLastEvent(string $direction): string
    {
        $row    = $this->eventQuery($direction);
        $result = I18N::translate('This information is not available.');

        if ($row !== null) {
            $record = Registry::gedcomRecordFactory()->make($row->id, $this->tree);

            if ($record instanceof GedcomRecord && $record->canShow()) {
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
     * Returns the formatted year of the first/last occurring event.
     *
     * @param string $direction The sorting direction
     *
     * @return string
     */
    private function getFirstLastEventYear(string $direction): string
    {
        $row = $this->eventQuery($direction);

        if ($row === null) {
            return '';
        }

        if ($row->year < 0) {
            $row->year = abs($row->year) . ' B.C.';
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
     * Returns the formatted name of the first/last occurring event.
     *
     * @param string $direction The sorting direction
     *
     * @return string
     */
    private function getFirstLastEventName(string $direction): string
    {
        $row = $this->eventQuery($direction);

        if ($row !== null) {
            $record = Registry::gedcomRecordFactory()->make($row->id, $this->tree);

            if ($record instanceof GedcomRecord) {
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
     * Returns the formatted place of the first/last occurring event.
     *
     * @param string $direction The sorting direction
     *
     * @return string
     */
    private function getFirstLastEventPlace(string $direction): string
    {
        $row = $this->eventQuery($direction);

        if ($row !== null) {
            $record = Registry::gedcomRecordFactory()->make($row->id, $this->tree);
            $fact   = null;

            if ($record instanceof GedcomRecord) {
                $fact = $record->facts([$row->fact])->first();
            }

            if ($fact instanceof Fact) {
                return $fact->place()->shortName();
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

    /**
     * @param array<string> $events
     */
    private function countFamiliesWithEvents(array $events): int
    {
        return DB::table('dates')
            ->join('families', static function (JoinClause $join): void {
                $join
                    ->on('f_id', '=', 'd_gid')
                    ->on('f_file', '=', 'd_file');
            })
            ->where('d_file', '=', $this->tree->id())
            ->whereIn('d_fact', $events)
            ->count();
    }

    /**
     * @param array<string> $events
     */
    private function countIndividualsWithEvents(array $events): int
    {
        return DB::table('dates')
            ->join('individuals', static function (JoinClause $join): void {
                $join
                    ->on('i_id', '=', 'd_gid')
                    ->on('i_file', '=', 'd_file');
            })
            ->where('d_file', '=', $this->tree->id())
            ->whereIn('d_fact', $events)
            ->count();
    }
}
