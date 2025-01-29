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
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Elements\UnknownElement;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Query\JoinClause;

use function abs;
use function e;

class EventRepository
{
    /**
     * Sorting directions.
     */
    private const string SORT_ASC  = 'ASC';
    private const string SORT_DESC = 'DESC';

    /**
     * Event facts.
     */
    private const string EVENT_BIRTH    = 'BIRT';
    private const string EVENT_DEATH    = 'DEAT';
    private const string EVENT_MARRIAGE = 'MARR';
    private const string EVENT_DIVORCE  = 'DIV';

    private Tree $tree;

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

    public function totalEventsBirth(): string
    {
        return I18N::number($this->countEvents(Gedcom::BIRTH_EVENTS));
    }

    public function totalBirths(): string
    {
        return I18N::number($this->countIndividualsWithEvents([self::EVENT_BIRTH]));
    }

    public function totalEventsDeath(): string
    {
        return I18N::number($this->countEvents(Gedcom::DEATH_EVENTS));
    }

    public function totalDeaths(): string
    {
        return I18N::number($this->countIndividualsWithEvents([self::EVENT_DEATH]));
    }

    public function totalEventsMarriage(): string
    {
        return I18N::number($this->countEvents(Gedcom::MARRIAGE_EVENTS));
    }

    public function totalMarriages(): string
    {
        return I18N::number($this->countFamiliesWithEvents([self::EVENT_MARRIAGE]));
    }

    public function totalEventsDivorce(): string
    {
        return I18N::number($this->countEvents(Gedcom::DIVORCE_EVENTS));
    }

    public function totalDivorces(): string
    {
        return I18N::number($this->countFamiliesWithEvents([self::EVENT_DIVORCE]));
    }

    public function totalEventsOther(): string
    {
        $events = [
            'CHAN',
            ...Gedcom::BIRTH_EVENTS,
            ...Gedcom::DEATH_EVENTS,
            ...Gedcom::MARRIAGE_EVENTS,
            ...Gedcom::DIVORCE_EVENTS,
        ];

        return I18N::number($this->countOtherEvents($events));
    }

    /**
     * Returns the first/last event record from the given list of event facts.
     *
     * @param string $direction The sorting direction of the query (To return first or last record)
     *
     * @return object{id:string,year:int,fact:string,type:string}|null
     */
    private function eventQuery(string $direction): object|null
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

    public function firstEvent(): string
    {
        return $this->getFirstLastEvent(self::SORT_ASC);
    }

    public function lastEvent(): string
    {
        return $this->getFirstLastEvent(self::SORT_DESC);
    }

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

    public function firstEventYear(): string
    {
        return $this->getFirstLastEventYear(self::SORT_ASC);
    }

    public function lastEventYear(): string
    {
        return $this->getFirstLastEventYear(self::SORT_DESC);
    }

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

    public function firstEventType(): string
    {
        return $this->getFirstLastEventType(self::SORT_ASC);
    }

    public function lastEventType(): string
    {
        return $this->getFirstLastEventType(self::SORT_DESC);
    }

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

    public function firstEventName(): string
    {
        return $this->getFirstLastEventName(self::SORT_ASC);
    }

    public function lastEventName(): string
    {
        return $this->getFirstLastEventName(self::SORT_DESC);
    }

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

    public function firstEventPlace(): string
    {
        return $this->getFirstLastEventPlace(self::SORT_ASC);
    }

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
