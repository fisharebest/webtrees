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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Elements\UnknownElement;
use Fisharebest\Webtrees\Http\RequestHandlers\MessagePage;
use Fisharebest\Webtrees\Module\IndividualListModule;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleListInterface;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

use function abs;
use function app;
use function array_keys;
use function array_reverse;
use function array_search;
use function array_shift;
use function array_slice;
use function arsort;
use function asort;
use function count;
use function e;
use function explode;
use function floor;
use function implode;
use function in_array;
use function preg_match;
use function preg_quote;
use function route;
use function str_replace;
use function strip_tags;
use function uksort;
use function view;

class StatisticsData
{
    private Tree $tree;
    private UserService $user_service;

    public function __construct(Tree $tree, UserService $user_service)
    {
        $this->tree         = $tree;
        $this->user_service = $user_service;
    }

    public function averageChildrenPerFamily(): float
    {
        return (float) DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->avg('f_numchil');
    }

    public function averageLifespanDays(string $sex): int
    {
        $prefix = DB::connection()->getTablePrefix();

        return (int) $this->birthAndDeathQuery($sex)
            ->select([new Expression('AVG(' . $prefix . 'death.d_julianday2 - ' . $prefix . 'birth.d_julianday1) AS days')])
            ->value('days');
    }

    /**
     * @return Collection<string,int>
     */
    public function commonGivenNames(string $sex, int $threshold, int $limit): Collection
    {
        $query = DB::table('name')
            ->where('n_file', '=', $this->tree->id())
            ->where('n_type', '<>', '_MARNM')
            ->where('n_givn', '<>', Individual::PRAENOMEN_NESCIO)
            ->where(new Expression('LENGTH(n_givn)'), '>', 1);

        if ($sex !== 'ALL') {
            $query
                ->join('individuals', static function (JoinClause $join): void {
                    $join
                        ->on('i_file', '=', 'n_file')
                        ->on('i_id', '=', 'n_id');
                })
                ->where('i_sex', '=', $sex);
        }

        $rows = $query
            ->groupBy(['n_givn'])
            ->pluck(new Expression('COUNT(DISTINCT n_id)'), 'n_givn')
            ->map(static fn ($count): int => (int) $count);


        $given_names = [];

        foreach ($rows as $n_givn => $count) {
            // Split “John Thomas” into “John” and “Thomas” and count against both totals
            foreach (explode(' ', (string) $n_givn) as $given) {
                // Exclude initials and particles.
                if (preg_match('/^([A-Z]|[a-z]{1,3})$/', $given) !== 1) {
                    $given_names[$given] ??= 0;
                    $given_names[$given] += (int) $count;
                }
            }
        }

        return (new Collection($given_names))
            ->sortDesc()
            ->slice(0, $limit)
            ->filter(static fn (int $count): bool => $count >= $threshold);
    }

    /**
     * @return array<array<int>>
     */
    public function commonSurnames(int $limit, int $threshold, string $sort): array
    {
        // Use the count of base surnames.
        $top_surnames = DB::table('name')
            ->where('n_file', '=', $this->tree->id())
            ->where('n_type', '<>', '_MARNM')
            ->whereNotIn('n_surn', ['', Individual::NOMEN_NESCIO])
            ->select(['n_surn'])
            ->groupBy(['n_surn'])
            ->orderByRaw('COUNT(n_surn) DESC')
            ->orderBy(new Expression('COUNT(n_surn)'), 'DESC')
            ->having(new Expression('COUNT(n_surn)'), '>=', $threshold)
            ->take($limit)
            ->pluck('n_surn')
            ->all();

        $surnames = [];

        foreach ($top_surnames as $top_surname) {
            $surnames[$top_surname] = DB::table('name')
                ->where('n_file', '=', $this->tree->id())
                ->where('n_type', '<>', '_MARNM')
                ->where('n_surn', '=', $top_surname)
                ->select(['n_surn', new Expression('COUNT(n_surn) AS count')])
                ->groupBy(['n_surn'])
                ->orderBy('n_surn')
                ->pluck('count', 'n_surn')
                ->map(static fn (string $count): int => (int) $count)
                ->all();
        }

        switch ($sort) {
            default:
            case 'alpha':
                uksort($surnames, I18N::comparator());
                break;
            case 'count':
                break;
            case 'rcount':
                $surnames = array_reverse($surnames, true);
                break;
        }

        return $surnames;
    }

    /**
     * @param array<string> $events
     */
    public function countAllEvents(array $events): int
    {
        return DB::table('dates')
            ->where('d_file', '=', $this->tree->id())
            ->whereIn('d_fact', $events)
            ->count();
    }

    public function countAllPlaces(): int
    {
        return DB::table('places')
            ->where('p_file', '=', $this->tree->id())
            ->count();
    }

    public function countAllRecords(): int
    {
        return
            $this->countIndividuals() +
            $this->countFamilies() +
            $this->countMedia() +
            $this->countNotes() +
            $this->countRepositories() +
            $this->countSources();
    }

    public function countChildren(): int
    {
        return (int) DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->sum('f_numchil');
    }

    /**
     * @return array<array{place:Place,count:int}>
     */
    public function countCountries(int $limit): array
    {
        return DB::table('places')
            ->join('placelinks', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'p_file')
                    ->on('pl_p_id', '=', 'p_id');
            })
            ->where('p_file', '=', $this->tree->id())
            ->where('p_parent_id', '=', 0)
            ->groupBy(['p_place'])
            ->orderByDesc(new Expression('COUNT(*)'))
            ->orderBy('p_place')
            ->take($limit)
            ->select([new Expression('COUNT(*) AS total'), 'p_place AS place'])
            ->get()
            ->map(fn (object $row): array => [
                'place' => new Place($row->place, $this->tree),
                'count' => (int) $row->total,
            ])
            ->all();
    }

    private function countEventQuery(string $event, int $year1 = 0, int $year2 = 0): Builder
    {
        $query = DB::table('dates')
            ->where('d_file', '=', $this->tree->id())
            ->where('d_fact', '=', $event)
            ->whereIn('d_type', ['@#DGREGORIAN@', '@#DJULIAN@']);

        if ($year1 !== 0 && $year2 !== 0) {
            $query->whereBetween('d_year', [$year1, $year2]);
        } else {
            $query->where('d_year', '<>', 0);
        }

        return $query;
    }

    /**
     * @return array<string,int>
     */
    public function countEventsByMonth(string $event, int $year1, int $year2): array
    {
        return $this->countEventQuery($event, $year1, $year2)
            ->groupBy(['d_month'])
            ->pluck(new Expression('COUNT(*)'), 'd_month')
            ->map(static fn (string $total): int => (int) $total)
            ->all();
    }

    /**
     * @return array<object{month:string,sex:string,total:int}>
     */
    public function countEventsByMonthAndSex(string $event, int $year1, int $year2): array
    {
        return $this->countEventQuery($event, $year1, $year2)
            ->join('individuals', static function (JoinClause $join): void {
                $join
                    ->on('i_id', '=', 'd_gid')
                    ->on('i_file', '=', 'd_file');
            })
            ->groupBy(['i_sex', 'd_month'])
            ->select(['d_month', 'i_sex', new Expression('COUNT(*) AS total')])
            ->get()
            ->map(static fn (object $row): object => (object) [
                'month' => $row->d_month,
                'sex'   => $row->i_sex,
                'total' => (int) $row->total,
            ])
            ->all();
    }

    /**
     * @return array<int,array{0:string,1:int}>
     */
    public function countEventsByCentury(string $event): array
    {
        return $this->countEventQuery($event, 0, 0)
            ->select([new Expression('ROUND((d_year + 49) / 100, 0) AS century'), new Expression('COUNT(*) AS total')])
            ->groupBy(['century'])
            ->orderBy('century')
            ->get()
            ->map(fn (object $row): array => [$this->centuryName((int) $row->century), (int) $row->total])
            ->all();
    }

    public function countFamilies(): int
    {
        return DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->count();
    }

    /**
     * @param array<string> $events
     */
    public function countFamiliesWithEvents(array $events): int
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

    public function countFamiliesWithNoChildren(): int
    {
        return DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->where('f_numchil', '=', 0)
            ->count();
    }

    public function countFamiliesWithSources(): int
    {
        return DB::table('families')
            ->select(['f_id'])
            ->distinct()
            ->join('link', static function (JoinClause $join): void {
                $join->on('f_id', '=', 'l_from')
                    ->on('f_file', '=', 'l_file');
            })
            ->where('l_file', '=', $this->tree->id())
            ->where('l_type', '=', 'SOUR')
            ->count('f_id');
    }

    /**
     * @return array<string,int>
     */
    public function countFirstChildrenByMonth(int $year1, int $year2): array
    {
        return $this->countFirstChildrenQuery($year1, $year2)
            ->groupBy(['d_month'])
            ->pluck(new Expression('COUNT(*)'), 'd_month')
            ->map(static fn (string $total): int => (int) $total)
            ->all();
    }

    /**
     * @return array<object{month:string,sex:string,total:int}>
     */
    public function countFirstChildrenByMonthAndSex(int $year1, int $year2): array
    {
        return $this->countFirstChildrenQuery($year1, $year2)
            ->join('individuals', static function (JoinClause $join): void {
                $join
                    ->on('i_file', '=', 'l_file')
                    ->on('i_id', '=', 'l_to');
            })
            ->groupBy(['d_month', 'i_sex'])
            ->select(['d_month', 'i_sex', new Expression('COUNT(*) AS total')])
            ->get()
            ->map(static fn (object $row): object => (object) [
                'month' => $row->d_month,
                'sex'   => $row->i_sex,
                'total' => (int) $row->total,
            ])
            ->all();
    }

    private function countFirstChildrenQuery(int $year1, int $year2): Builder
    {
        $first_child_subquery = DB::table('link')
            ->join('dates', static function (JoinClause $join): void {
                $join
                    ->on('d_gid', '=', 'l_to')
                    ->on('d_file', '=', 'l_file')
                    ->where('d_julianday1', '<>', 0)
                    ->whereIn('d_month', ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC']);
            })
            ->where('l_file', '=', $this->tree->id())
            ->where('l_type', '=', 'CHIL')
            ->select(['l_from AS family_id', new Expression('MIN(d_julianday1) AS min_birth_jd')])
            ->groupBy(['family_id']);

        $query = DB::table('link')
            ->join('dates', static function (JoinClause $join): void {
                $join
                    ->on('d_gid', '=', 'l_to')
                    ->on('d_file', '=', 'l_file');
            })
            ->joinSub($first_child_subquery, 'subquery', static function (JoinClause $join): void {
                $join
                    ->on('family_id', '=', 'l_from')
                    ->on('min_birth_jd', '=', 'd_julianday1');
            })
            ->where('link.l_file', '=', $this->tree->id())
            ->where('link.l_type', '=', 'CHIL');

        if ($year1 !== 0 && $year2 !== 0) {
            $query->whereBetween('d_year', [$year1, $year2]);
        }

        return $query;
    }

    /**
     * @return array<string,int>
     */
    public function countFirstMarriagesByMonth(Tree $tree, int $year1, int $year2): array
    {
        $query = DB::table('families')
            ->join('dates', static function (JoinClause $join): void {
                $join
                    ->on('d_gid', '=', 'f_id')
                    ->on('d_file', '=', 'f_file')
                    ->where('d_fact', '=', 'MARR')
                    ->whereIn('d_month', ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'])
                    ->where('d_julianday2', '<>', 0);
            })
            ->where('f_file', '=', $tree->id());

        if ($year1 !== 0 && $year2 !== 0) {
            $query->whereBetween('d_year', [$year1, $year2]);
        }

        $rows = $query
            ->orderBy('d_julianday2')
            ->select(['f_husb AS husb', 'f_wife AS wife', 'd_month AS month'])
            ->get();

        $months = [
            'JAN' => 0,
            'FEB' => 0,
            'MAR' => 0,
            'APR' => 0,
            'MAY' => 0,
            'JUN' => 0,
            'JUL' => 0,
            'AUG' => 0,
            'SEP' => 0,
            'OCT' => 0,
            'NOV' => 0,
            'DEC' => 0,
        ];
        $seen = [];

        foreach ($rows as $row) {
            if (!in_array($row->husb, $seen, true) && !in_array($row->wife, $seen, true)) {
                $months[$row->month]++;
                $seen[] = $row->husb;
                $seen[] = $row->wife;
            }
        }

        return $months;
    }

    /**
     * @param array<string> $names
     */
    public function countGivenNames(array $names): int
    {
        if ($names === []) {
            // Count number of distinct given names.
            return DB::table('name')
                ->where('n_file', '=', $this->tree->id())
                ->distinct()
                ->where('n_givn', '<>', Individual::PRAENOMEN_NESCIO)
                ->whereNotNull('n_givn')
                ->count('n_givn');
        }

        // Count number of occurrences of specific given names.
        return DB::table('name')
            ->where('n_file', '=', $this->tree->id())
            ->whereIn('n_givn', $names)
            ->count('n_givn');
    }

    public function countHits(string $page_name, string $page_parameter): int
    {
        return (int) DB::table('hit_counter')
            ->where('gedcom_id', '=', $this->tree->id())
            ->where('page_name', '=', $page_name)
            ->where('page_parameter', '=', $page_parameter)
            ->sum('page_count');
    }

    public function countIndividuals(): int
    {
        return DB::table('individuals')
            ->where('i_file', '=', $this->tree->id())
            ->count();
    }

    public function countIndividualsBySex(string $sex): int
    {
        return DB::table('individuals')
            ->where('i_file', '=', $this->tree->id())
            ->where('i_sex', '=', $sex)
            ->count();
    }

    public function countIndividualsDeceased(): int
    {
        return DB::table('individuals')
            ->where('i_file', '=', $this->tree->id())
            ->where(static function (Builder $query): void {
                foreach (Gedcom::DEATH_EVENTS as $death_event) {
                    $query->orWhere('i_gedcom', 'LIKE', "%\n1 " . $death_event . '%');
                }
            })
            ->count();
    }

    public function countIndividualsLiving(): int
    {
        $query = DB::table('individuals')
            ->where('i_file', '=', $this->tree->id());

        foreach (Gedcom::DEATH_EVENTS as $death_event) {
            $query->where('i_gedcom', 'NOT LIKE', "%\n1 " . $death_event . '%');
        }

        return $query->count();
    }

    /**
     * @param array<string> $events
     */
    public function countIndividualsWithEvents(array $events): int
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

    public function countIndividualsWithSources(): int
    {
        return DB::table('individuals')
            ->select(['i_id'])
            ->distinct()
            ->join('link', static function (JoinClause $join): void {
                $join->on('i_id', '=', 'l_from')
                    ->on('i_file', '=', 'l_file');
            })
            ->where('l_file', '=', $this->tree->id())
            ->where('l_type', '=', 'SOUR')
            ->count('i_id');
    }

    public function countMarriedFemales(): int
    {
        return DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->where('f_gedcom', 'LIKE', "%\n1 MARR%")
            ->distinct()
            ->count('f_wife');
    }

    public function countMarriedMales(): int
    {
        return DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->where('f_gedcom', 'LIKE', "%\n1 MARR%")
            ->distinct()
            ->count('f_husb');
    }

    public function countMedia(string $type = 'all'): int
    {
        $query = DB::table('media_file')
            ->where('m_file', '=', $this->tree->id());

        if ($type !== 'all') {
            $query->where('source_media_type', '=', $type);
        }

        return $query->count();
    }

    /**
     * @return array<array{0:string,1:int}>
     */
    public function countMediaByType(): array
    {
        $element = Registry::elementFactory()->make('OBJE:FILE:FORM:TYPE');
        $values  = $element->values();

        return DB::table('media_file')
            ->where('m_file', '=', $this->tree->id())
            ->groupBy('source_media_type')
            ->select([new Expression('COUNT(*) AS total'), 'source_media_type'])
            ->get()
            ->map(static fn (object $row): array => [
                $values[$element->canonical($row->source_media_type)] ?? I18N::translate('Other'),
                (int) $row->total,
            ])
            ->all();
    }

    public function countNotes(): int
    {
        return DB::table('other')
            ->where('o_file', '=', $this->tree->id())
            ->where('o_type', '=', 'NOTE')
            ->count();
    }

    /**
     * @param array<string> $events
     */
    public function countOtherEvents(array $events): int
    {
        return DB::table('dates')
            ->where('d_file', '=', $this->tree->id())
            ->whereNotIn('d_fact', $events)
            ->count();
    }

    /**
     * @param array<string> $rows
     *
     * @return array<array{place:Place,count:int}>
     */
    private function countPlaces(array $rows, string $event, int $limit): array
    {
        $places = [];

        foreach ($rows as $gedcom) {
            if (preg_match('/\n1 ' . $event . '(?:\n[2-9].*)*\n2 PLAC (.+)/', $gedcom, $match) === 1) {
                $places[$match[1]] ??= 0;
                $places[$match[1]]++;
            }
        }

        arsort($places);

        $records = [];

        foreach (array_slice($places, 0, $limit) as $place => $count) {
            $records[] = [
                'place' => new Place((string) $place, $this->tree),
                'count' => $count,
            ];
        }

        return $records;
    }

    /**
     * @return array<array{place:Place,count:int}>
     */
    public function countPlacesForFamilies(string $event, int $limit): array
    {
        $rows = DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->where('f_gedcom', 'LIKE', "%\n2 PLAC %")
            ->pluck('f_gedcom')
            ->all();

        return $this->countPlaces($rows, $event, $limit);
    }

    /**
     * @return array<array{place:Place,count:int}>
     */
    public function countPlacesForIndividuals(string $event, int $limit): array
    {
        $rows = DB::table('individuals')
            ->where('i_file', '=', $this->tree->id())
            ->where('i_gedcom', 'LIKE', "%\n2 PLAC %")
            ->pluck('i_gedcom')
            ->all();

        return $this->countPlaces($rows, $event, $limit);
    }

    public function countRepositories(): int
    {
        return DB::table('other')
            ->where('o_file', '=', $this->tree->id())
            ->where('o_type', '=', 'REPO')
            ->count();
    }

    public function countSources(): int
    {
        return DB::table('sources')
            ->where('s_file', '=', $this->tree->id())
            ->count();
    }

    /**
     * @param array<string> $names
     */
    public function countSurnames(array $names): int
    {
        if ($names === []) {
            // Count number of distinct surnames
            return DB::table('name')
                ->where('n_file', '=', $this->tree->id())->distinct()
                ->whereNotNull('n_surn')
                ->count('n_surn');
        }

        // Count number of occurrences of specific surnames.
        return DB::table('name')
            ->where('n_file', '=', $this->tree->id())
            ->whereIn('n_surn', $names)
            ->count('n_surn');
    }

    public function countTreeFavorites(): int
    {
        return DB::table('favorite')
            ->where('gedcom_id', '=', $this->tree->id())
            ->count();
    }

    public function countTreeNews(): int
    {
        return DB::table('news')
            ->where('gedcom_id', '=', $this->tree->id())
            ->count();
    }

    public function countUserfavorites(): int
    {
        return DB::table('favorite')
            ->where('user_id', '=', Auth::id())
            ->count();
    }

    public function countUserJournal(): int
    {
        return DB::table('news')
            ->where('user_id', '=', Auth::id())
            ->count();
    }

    public function countUserMessages(): int
    {
        return DB::table('message')
            ->where('user_id', '=', Auth::id())
            ->count();
    }

    /**
     * @return array<int,object{family:Family,children:int}>
     */
    public function familiesWithTheMostChildren(int $limit): array
    {
        return DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->orderByDesc('f_numchil')
            ->limit($limit)
            ->get()
            ->map(fn (object $row): object => (object) [
                'family'   => Registry::familyFactory()->make($row->f_id, $this->tree, $row->f_gedcom),
                'children' => (int) $row->f_numchil,
            ])
            ->all();
    }

    /**
     * @param array<string> $events
     *
     * @return object{id:string,year:int,fact:string,type:string}|null
     */
    private function firstEvent(array $events, bool $ascending): ?object
    {
        if ($events === []) {
            $events = [
                ...Gedcom::BIRTH_EVENTS,
                ...Gedcom::DEATH_EVENTS,
                ...Gedcom::MARRIAGE_EVENTS,
                ...Gedcom::DIVORCE_EVENTS,
            ];
        }

        return DB::table('dates')
            ->select(['d_gid as id', 'd_year as year', 'd_fact AS fact', 'd_type AS type'])
            ->where('d_file', '=', $this->tree->id())
            ->whereIn('d_fact', $events)
            ->where('d_julianday1', '<>', 0)
            ->orderBy('d_julianday1', $ascending ? 'ASC' : 'DESC')
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
     * @param array<string> $events
     */
    public function firstEventName(array $events, bool $ascending): string
    {
        $row = $this->firstEvent($events, $ascending);

        if ($row !== null) {
            $record = Registry::gedcomRecordFactory()->make($row->id, $this->tree);

            if ($record instanceof GedcomRecord) {
                return '<a href="' . e($record->url()) . '">' . $record->fullName() . '</a>';
            }
        }

        return '';
    }

    /**
     * @param array<string> $events
     */
    public function firstEventPlace(array $events, bool $ascending): string
    {
        $row = $this->firstEvent($events, $ascending);

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
     * @param array<string> $events
     */
    public function firstEventRecord(array $events, bool $ascending): string
    {
        $row = $this->firstEvent($events, $ascending);
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
     * @param array<string> $events
     */
    public function firstEventType(array $events, bool $ascending): string
    {
        $row = $this->firstEvent($events, $ascending);

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
     * @param array<string> $events
     */
    public function firstEventYear(array $events, bool $ascending): string
    {
        $row = $this->firstEvent($events, $ascending);

        if ($row === null) {
            return '-';
        }

        if ($row->year < 0) {
            $date = new Date($row->type . ' ' . abs($row->year) . ' B.C.');
        } else {
            $date = new Date($row->type . ' ' . $row->year);
        }

        return $date->display();
    }

    public function isUserLoggedIn(?int $user_id): bool
    {
        return $user_id !== null && DB::table('session')
            ->where('user_id', '=', $user_id)
            ->exists();
    }

    public function latestUserId(): ?int
    {
        $user_id = DB::table('user')
            ->select(['user.user_id'])
            ->leftJoin('user_setting', 'user.user_id', '=', 'user_setting.user_id')
            ->where('setting_name', '=', UserInterface::PREF_TIMESTAMP_REGISTERED)
            ->orderByDesc('setting_value')
            ->value('user_id');

        if ($user_id === null) {
            return null;
        }

        return (int) $user_id;
    }

    /**
     * @return array<object{family:Family,child1:Individual,child2:Individual,age:string}>
     */
    public function maximumAgeBetweenSiblings(int $limit): array
    {
        $prefix = DB::connection()->getTablePrefix();

        return DB::table('link AS link1')
            ->join('link AS link2', static function (JoinClause $join): void {
                $join
                    ->on('link2.l_from', '=', 'link1.l_from')
                    ->on('link2.l_type', '=', 'link1.l_type')
                    ->on('link2.l_file', '=', 'link1.l_file');
            })
            ->join('dates AS child1', static function (JoinClause $join): void {
                $join
                    ->on('child1.d_gid', '=', 'link1.l_to')
                    ->on('child1.d_file', '=', 'link1.l_file')
                    ->where('child1.d_fact', '=', 'BIRT')
                    ->where('child1.d_julianday1', '<>', 0);
            })
            ->join('dates AS child2', static function (JoinClause $join): void {
                $join
                    ->on('child2.d_gid', '=', 'link2.l_to')
                    ->on('child2.d_file', '=', 'link2.l_file')
                    ->where('child2.d_fact', '=', 'BIRT')
                    ->whereColumn('child2.d_julianday2', '>', 'child1.d_julianday1');
            })
            ->where('link1.l_type', '=', 'CHIL')
            ->where('link1.l_file', '=', $this->tree->id())
            ->distinct()
            ->select(['link1.l_from AS family', 'link1.l_to AS child1', 'link2.l_to AS child2', new Expression($prefix . 'child2.d_julianday2 - ' . $prefix . 'child1.d_julianday1 AS age')])
            ->orderBy('age', 'DESC')
            ->take($limit)
            ->get()
            ->map(fn (object $row): object => (object) [
                'family' => Registry::familyFactory()->make($row->family, $this->tree),
                'child1' => Registry::individualFactory()->make($row->child1, $this->tree),
                'child2' => Registry::individualFactory()->make($row->child2, $this->tree),
                'age'    => $this->calculateAge((int) $row->age),
            ])
            ->filter(static fn (object $row): bool => $row->family !== null)
            ->filter(static fn (object $row): bool => $row->child1 !== null)
            ->filter(static fn (object $row): bool => $row->child2 !== null)
            ->all();
    }

    /**
     * @return Collection<int,Individual>
     */
    public function topTenOldestAliveQuery(string $sex, int $limit): Collection
    {
        $query = DB::table('dates')
            ->join('individuals', static function (JoinClause $join): void {
                $join
                    ->on('i_id', '=', 'd_gid')
                    ->on('i_file', '=', 'd_file');
            })
            ->where('d_file', '=', $this->tree->id())
            ->where('d_julianday1', '<>', 0)
            ->where('d_fact', '=', 'BIRT')
            ->where('i_gedcom', 'NOT LIKE', "%\n1 DEAT%")
            ->where('i_gedcom', 'NOT LIKE', "%\n1 BURI%")
            ->where('i_gedcom', 'NOT LIKE', "%\n1 CREM%");

        if ($sex === 'F' || $sex === 'M' || $sex === 'U' || $sex === 'X') {
            $query->where('i_sex', '=', $sex);
        }

        return $query
            ->groupBy(['i_id', 'i_file'])
            ->orderBy(new Expression('MIN(d_julianday1)'))
            ->select(['individuals.*'])
            ->take($limit)
            ->get()
            ->map(Registry::individualFactory()->mapper($this->tree))
            ->filter(GedcomRecord::accessFilter());
    }

    public function commonSurnamesQuery(string $type, bool $totals, int $threshold, int $limit, string $sort): string
    {
        $surnames = $this->commonSurnames($limit, $threshold, $sort);

        // find a module providing individual lists
        $module = app(ModuleService::class)
            ->findByComponent(ModuleListInterface::class, $this->tree, Auth::user())
            ->first(static fn (ModuleInterface $module): bool => $module instanceof IndividualListModule);

        if ($type === 'list') {
            return view('lists/surnames-bullet-list', [
                'surnames' => $surnames,
                'module'   => $module,
                'totals'   => $totals,
                'tree'     => $this->tree,
            ]);
        }

        return view('lists/surnames-compact-list', [
            'surnames' => $surnames,
            'module'   => $module,
            'totals'   => $totals,
            'tree'     => $this->tree,
        ]);
    }

    /**
     * @return  array<object{age:float,century:int,sex:string}>
     */
    public function statsAge(): array
    {
        $prefix = DB::connection()->getTablePrefix();

        return DB::table('individuals')
            ->select([
                new Expression('AVG(' . $prefix . 'death.d_julianday2 - ' . $prefix . 'birth.d_julianday1) / 365.25 AS age'),
                new Expression('ROUND((' . $prefix . 'death.d_year + 49) / 100, 0) AS century'),
                'i_sex AS sex'
            ])
            ->join('dates AS birth', static function (JoinClause $join): void {
                $join
                    ->on('birth.d_file', '=', 'i_file')
                    ->on('birth.d_gid', '=', 'i_id');
            })
            ->join('dates AS death', static function (JoinClause $join): void {
                $join
                    ->on('death.d_file', '=', 'i_file')
                    ->on('death.d_gid', '=', 'i_id');
            })
            ->where('i_file', '=', $this->tree->id())
            ->where('birth.d_fact', '=', 'BIRT')
            ->where('death.d_fact', '=', 'DEAT')
            ->whereIn('birth.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->whereIn('death.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->whereColumn('death.d_julianday1', '>=', 'birth.d_julianday2')
            ->where('birth.d_julianday2', '<>', 0)
            ->groupBy(['century', 'sex'])
            ->orderBy('century')
            ->orderBy('sex')
            ->get()
            ->map(static fn (object $row): object => (object) [
                'age'     => (float) $row->age,
                'century' => (int) $row->century,
                'sex'     => $row->sex,
            ])
            ->all();
    }

    /**
     * General query on ages.
     *
     * @return array<object{days:int}>
     */
    public function statsAgeQuery(string $sex, int $year1, int $year2): array
    {
        $prefix = DB::connection()->getTablePrefix();

        $query = $this->birthAndDeathQuery($sex);

        if ($year1 !== 0 && $year2 !== 0) {
            $query
                ->whereIn('birth.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
                ->whereIn('death.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
                ->whereBetween('death.d_year', [$year1, $year2]);
        }

        return $query
            ->select([new Expression($prefix . 'death.d_julianday2 - ' . $prefix . 'birth.d_julianday1 AS days')])
            ->orderBy('days', 'desc')
            ->get()
            ->map(static fn (object $row): object => (object) ['days' => (int) $row->days])
            ->all();
    }

    private function birthAndDeathQuery(string $sex): Builder
    {
        $query = DB::table('individuals')
            ->where('i_file', '=', $this->tree->id())
            ->join('dates AS birth', static function (JoinClause $join): void {
                $join
                    ->on('birth.d_file', '=', 'i_file')
                    ->on('birth.d_gid', '=', 'i_id');
            })
            ->join('dates AS death', static function (JoinClause $join): void {
                $join
                    ->on('death.d_file', '=', 'i_file')
                    ->on('death.d_gid', '=', 'i_id');
            })
            ->where('birth.d_fact', '=', 'BIRT')
            ->where('death.d_fact', '=', 'DEAT')
            ->whereColumn('death.d_julianday1', '>=', 'birth.d_julianday2')
            ->where('birth.d_julianday2', '<>', 0);

        if ($sex !== 'ALL') {
            $query->where('i_sex', '=', $sex);
        }

        return $query;
    }

    /**
     * @return object{individual:Individual,days:int}|null
     */
    public function longlifeQuery(string $sex): ?object
    {
        $prefix = DB::connection()->getTablePrefix();

        return $this->birthAndDeathQuery($sex)
            ->orderBy('days', 'desc')
            ->select(['individuals.*', new Expression($prefix . 'death.d_julianday2 - ' . $prefix . 'birth.d_julianday1 AS days')])
            ->take(1)
            ->get()
            ->map(fn (object $row): object => (object) [
                'individual' => Registry::individualFactory()->mapper($this->tree)($row),
                'days'       => (int) $row->days
            ])
            ->first();
    }

    /**
     * @return Collection<int,object{individual:Individual,days:int}>
     */
    public function topTenOldestQuery(string $sex, int $limit): Collection
    {
        $prefix = DB::connection()->getTablePrefix();

        return $this->birthAndDeathQuery($sex)
            ->groupBy(['i_id', 'i_file'])
            ->orderBy('days', 'desc')
            ->select(['individuals.*', new Expression('MAX(' . $prefix . 'death.d_julianday2 - ' . $prefix . 'birth.d_julianday1) AS days')])
            ->take($limit)
            ->get()
            ->map(fn (object $row): object => (object) [
                'individual' => Registry::individualFactory()->mapper($this->tree)($row),
                'days'       => (int) $row->days
            ]);
    }

    /**
     * @return array<string>
     */
    private function getIso3166Countries(): array
    {
        // Get the country names for each language
        $country_to_iso3166 = [];

        $current_language = I18N::languageTag();

        foreach (I18N::activeLocales() as $locale) {
            I18N::init($locale->languageTag());

            $countries = $this->getAllCountries();

            foreach ($this->iso3166() as $three => $two) {
                $country_to_iso3166[$three]             = $two;
                $country_to_iso3166[$countries[$three]] = $two;
            }
        }

        I18N::init($current_language);

        return $country_to_iso3166;
    }

    /**
     * Returns the data structure required by google geochart.
     *
     * @param array<int> $places
     *
     * @return array<int,array<int|string|array<string,string>>>
     */
    private function createChartData(array $places): array
    {
        $data = [
            [
                I18N::translate('Country'),
                I18N::translate('Total'),
            ],
        ];

        // webtrees uses 3-letter country codes and localised country names, but google uses 2 letter codes.
        foreach ($places as $country => $count) {
            $data[] = [
                [
                    'v' => $country,
                    'f' => $this->mapTwoLetterToName($country),
                ],
                $count
            ];
        }

        return $data;
    }

    /**
     * @return array<string,int>
     */
    private function countIndividualsByCountry(Tree $tree): array
    {
        $rows = DB::table('places')
            ->where('p_file', '=', $tree->id())
            ->where('p_parent_id', '=', 0)
            ->join('placelinks', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'p_file')
                    ->on('pl_p_id', '=', 'p_id');
            })
            ->join('individuals', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'i_file')
                    ->on('pl_gid', '=', 'i_id');
            })
            ->groupBy('p_place')
            ->pluck(new Expression('COUNT(*)'), 'p_place')
            ->all();

        $totals = [];

        $country_to_iso3166 = $this->getIso3166Countries();

        foreach ($rows as $country => $count) {
            $country_code = $country_to_iso3166[$country] ?? null;

            if ($country_code !== null) {
                $totals[$country_code] ??= 0;
                $totals[$country_code] += $count;
            }
        }

        return $totals;
    }

    /**
     * @return array<string,int>
     */
    private function countSurnamesByCountry(Tree $tree, string $surname): array
    {
        $rows =
            DB::table('places')
                ->where('p_file', '=', $tree->id())
                ->where('p_parent_id', '=', 0)
                ->join('placelinks', static function (JoinClause $join): void {
                    $join
                        ->on('pl_file', '=', 'p_file')
                        ->on('pl_p_id', '=', 'p_id');
                })
                ->join('name', static function (JoinClause $join): void {
                    $join
                        ->on('n_file', '=', 'pl_file')
                        ->on('n_id', '=', 'pl_gid');
                })
                ->where('n_surn', '=', $surname)
                ->groupBy('p_place')
                ->pluck(new Expression('COUNT(*)'), 'p_place');

        $totals = [];

        $country_to_iso3166 = $this->getIso3166Countries();

        foreach ($rows as $country => $count) {
            $country_code = $country_to_iso3166[$country] ?? null;

            if ($country_code !== null) {
                $totals[$country_code] ??= 0;
                $totals[$country_code] += $count;
            }
        }

        return $totals;
    }

    /**
     * @return array<string,int>
     */
    private function countFamilyEventsByCountry(Tree $tree, string $fact): array
    {
        $query = DB::table('places')
            ->where('p_file', '=', $tree->id())
            ->where('p_parent_id', '=', 0)
            ->join('placelinks', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'p_file')
                    ->on('pl_p_id', '=', 'p_id');
            })
            ->join('families', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'f_file')
                    ->on('pl_gid', '=', 'f_id');
            })
            ->select(['p_place AS place', 'f_gedcom AS gedcom']);

        return $this->filterEventPlaces($query, $fact);
    }

    /**
     * @return array<string,int>
     */
    private function countIndividualEventsByCountry(Tree $tree, string $fact): array
    {
        $query = DB::table('places')
            ->where('p_file', '=', $tree->id())
            ->where('p_parent_id', '=', 0)
            ->join('placelinks', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'p_file')
                    ->on('pl_p_id', '=', 'p_id');
            })
            ->join('individuals', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'i_file')
                    ->on('pl_gid', '=', 'i_id');
            })
            ->select(['p_place AS place', 'i_gedcom AS gedcom']);

        return $this->filterEventPlaces($query, $fact);
    }

    /**
     * @return array<string,int>
     */
    private function filterEventPlaces(Builder $query, string $fact): array
    {
        $totals = [];

        $country_to_iso3166 = $this->getIso3166Countries();

        foreach ($query->cursor() as $row) {
            $country_code = $country_to_iso3166[$row->place] ?? null;

            if ($country_code !== null) {
                $place_regex = '/\n1 ' . $fact . '(?:\n[2-9].*)*\n2 PLAC.*[, ]' . preg_quote($row->place, '(?:\n|$)/i') . '\n/';

                if (preg_match($place_regex, $row->gedcom) === 1) {
                    $totals[$country_code] = 1 + ($totals[$country_code] ?? 0);
                }
            }
        }

        return $totals;
    }

    /**
     * Create a chart showing where events occurred.
     *
     * @param string $chart_shows The type of chart map to show
     * @param string $chart_type  The type of chart to show
     * @param string $surname     The surname for surname based distribution chart
     */
    public function chartDistribution(
        string $chart_shows = 'world',
        string $chart_type = '',
        string $surname = ''
    ): string {
        switch ($chart_type) {
            case 'surname_distribution_chart':
                $chart_title = I18N::translate('Surname distribution chart') . ': ' . $surname;
                $surnames    = $this->commonSurnames(1, 0, 'count');
                $surname     = implode(I18N::$list_separator, array_keys(array_shift($surnames) ?? []));
                $data        = $this->createChartData($this->countSurnamesByCountry($this->tree, $surname));
                break;

            case 'birth_distribution_chart':
                $chart_title = I18N::translate('Birth by country');
                $data        = $this->createChartData($this->countIndividualEventsByCountry($this->tree, 'BIRT'));
                break;

            case 'death_distribution_chart':
                $chart_title = I18N::translate('Death by country');
                $data        = $this->createChartData($this->countIndividualEventsByCountry($this->tree, 'DEAT'));
                break;

            case 'marriage_distribution_chart':
                $chart_title = I18N::translate('Marriage by country');
                $data        = $this->createChartData($this->countFamilyEventsByCountry($this->tree, 'MARR'));
                break;

            case 'indi_distribution_chart':
            default:
                $chart_title = I18N::translate('Individual distribution chart');
                $data        = $this->createChartData($this->countIndividualsByCountry($this->tree));
                break;
        }

        return view('statistics/other/charts/geo', [
            'chart_title'  => $chart_title,
            'chart_color2' => '84beff',
            'chart_color3' => 'c3dfff',
            'region'       => $chart_shows,
            'data'         => $data,
            'language'     => I18N::languageTag(),
        ]);
    }

    /**
     * @return array<array{family:Family,count:int}>
     */
    private function topTenGrandFamilyQuery(int $limit): array
    {
        return DB::table('families')
            ->join('link AS children', static function (JoinClause $join): void {
                $join
                    ->on('children.l_from', '=', 'f_id')
                    ->on('children.l_file', '=', 'f_file')
                    ->where('children.l_type', '=', 'CHIL');
            })->join('link AS mchildren', static function (JoinClause $join): void {
                $join
                    ->on('mchildren.l_file', '=', 'children.l_file')
                    ->on('mchildren.l_from', '=', 'children.l_to')
                    ->where('mchildren.l_type', '=', 'FAMS');
            })->join('link AS gchildren', static function (JoinClause $join): void {
                $join
                    ->on('gchildren.l_file', '=', 'mchildren.l_file')
                    ->on('gchildren.l_from', '=', 'mchildren.l_to')
                    ->where('gchildren.l_type', '=', 'CHIL');
            })
            ->where('f_file', '=', $this->tree->id())
            ->groupBy(['f_id', 'f_file'])
            ->orderBy(new Expression('COUNT(*)'), 'DESC')
            ->select(['families.*'])
            ->limit($limit)
            ->get()
            ->map(Registry::familyFactory()->mapper($this->tree))
            ->filter(GedcomRecord::accessFilter())
            ->map(static function (Family $family): array {
                $count = 0;
                foreach ($family->children() as $child) {
                    foreach ($child->spouseFamilies() as $spouse_family) {
                        $count += $spouse_family->children()->count();
                    }
                }

                return [
                    'family' => $family,
                    'count'  => $count,
                ];
            })
            ->all();
    }

    public function topTenLargestGrandFamily(int $limit = 10): string
    {
        return view('statistics/families/top10-nolist-grand', [
            'records' => $this->topTenGrandFamilyQuery($limit),
        ]);
    }

    public function topTenLargestGrandFamilyList(int $limit = 10): string
    {
        return view('statistics/families/top10-list-grand', [
            'records' => $this->topTenGrandFamilyQuery($limit),
        ]);
    }

    public function noChildrenFamiliesList(string $type = 'list'): string
    {
        $families = DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->where('f_numchil', '=', 0)
            ->get()
            ->map(Registry::familyFactory()->mapper($this->tree))
            ->filter(GedcomRecord::accessFilter());

        $top10 = [];

        foreach ($families as $family) {
            if ($type === 'list') {
                $top10[] = '<li><a href="' . e($family->url()) . '">' . $family->fullName() . '</a></li>';
            } else {
                $top10[] = '<a href="' . e($family->url()) . '">' . $family->fullName() . '</a>';
            }
        }

        if ($type === 'list') {
            $top10 = implode('', $top10);
        } else {
            $top10 = implode('; ', $top10);
        }

        if ($type === 'list') {
            return '<ul>' . $top10 . '</ul>';
        }

        return $top10;
    }

    /**
     * Returns the calculated age the time of event.
     *
     * @param int $age The age from the database record
     */
    private function calculateAge(int $age): string
    {
        if ($age < 31) {
            return I18N::plural('%s day', '%s days', $age, I18N::number($age));
        }

        if ($age < 365) {
            $months = (int) ($age / 30.5);

            return I18N::plural('%s month', '%s months', $months, I18N::number($months));
        }

        $years = (int) ($age / 365.25);

        return I18N::plural('%s year', '%s years', $years, I18N::number($years));
    }

    public function topAgeBetweenSiblings(): string
    {
        $rows = $this->maximumAgeBetweenSiblings(1);

        if ($rows === []) {
            return I18N::translate('This information is not available.');
        }

        return $rows[0]->age;
    }

    public function topAgeBetweenSiblingsFullName(): string
    {
        $rows = $this->maximumAgeBetweenSiblings(1);

        if ($rows === []) {
            return I18N::translate('This information is not available.');
        }

        return view('statistics/families/top10-nolist-age', ['record' => (array) $rows[0]]);
    }

    public function topAgeBetweenSiblingsList(int $limit, bool $unique_families): string
    {
        $rows    = $this->maximumAgeBetweenSiblings($limit);
        $records = [];
        $dist    = [];

        foreach ($rows as $row) {
            if (!$unique_families || !in_array($row->family, $dist, true)) {
                $records[] = [
                    'child1' => $row->child1,
                    'child2' => $row->child2,
                    'family' => $row->family,
                    'age'    => $row->age,
                ];

                $dist[] = $row->family;
            }
        }

        return view('statistics/families/top10-list-age', [
            'records' => $records,
        ]);
    }

    /**
     * @return array<object{f_numchil:int,total:int}>
     */
    public function statsChildrenQuery(int $year1, int $year2): array
    {
        $query = DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->groupBy(['f_numchil'])
            ->select(['f_numchil', new Expression('COUNT(*) AS total')]);

        if ($year1 !== 0 && $year2 !== 0) {
            $query
                ->join('dates', static function (JoinClause $join): void {
                    $join
                        ->on('d_file', '=', 'f_file')
                        ->on('d_gid', '=', 'f_id');
                })
                ->where('d_fact', '=', 'MARR')
                ->whereIn('d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
                ->whereBetween('d_year', [$year1, $year2]);
        }

        return $query->get()
            ->map(static fn (object $row): object => (object) [
                'f_numchil' => (int) $row->f_numchil,
                'total'     => (int) $row->total,
            ])
            ->all();
    }

    /**
     * @return array<array{family:Family,count:int}>
     */
    private function topTenFamilyQuery(int $limit): array
    {
        return DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->orderBy('f_numchil', 'DESC')
            ->limit($limit)
            ->get()
            ->map(Registry::familyFactory()->mapper($this->tree))
            ->filter(GedcomRecord::accessFilter())
            ->map(static fn (Family $family): array => [
                'family' => $family,
                'count'  => $family->numberOfChildren(),
            ])
            ->all();
    }

    public function topTenLargestFamily(int $limit = 10): string
    {
        $records = $this->topTenFamilyQuery($limit);

        return view('statistics/families/top10-nolist', [
            'records' => $records,
        ]);
    }

    public function topTenLargestFamilyList(int $limit = 10): string
    {
        $records = $this->topTenFamilyQuery($limit);

        return view('statistics/families/top10-list', [
            'records' => $records,
        ]);
    }

    public function parentsQuery(string $type, string $age_dir, string $sex, bool $show_years): string
    {
        $prefix = DB::connection()->getTablePrefix();

        if ($sex === 'F') {
            $sex_field = 'WIFE';
        } else {
            $sex_field = 'HUSB';
        }

        if ($age_dir !== 'ASC') {
            $age_dir = 'DESC';
        }

        $row = DB::table('link AS parentfamily')
            ->join('link AS childfamily', static function (JoinClause $join): void {
                $join
                    ->on('childfamily.l_file', '=', 'parentfamily.l_file')
                    ->on('childfamily.l_from', '=', 'parentfamily.l_from')
                    ->where('childfamily.l_type', '=', 'CHIL');
            })
            ->join('dates AS birth', static function (JoinClause $join): void {
                $join
                    ->on('birth.d_file', '=', 'parentfamily.l_file')
                    ->on('birth.d_gid', '=', 'parentfamily.l_to')
                    ->where('birth.d_fact', '=', 'BIRT')
                    ->where('birth.d_julianday1', '<>', 0);
            })
            ->join('dates AS childbirth', static function (JoinClause $join): void {
                $join
                    ->on('childbirth.d_file', '=', 'parentfamily.l_file')
                    ->on('childbirth.d_gid', '=', 'childfamily.l_to')
                    ->where('childbirth.d_fact', '=', 'BIRT');
            })
            ->where('childfamily.l_file', '=', $this->tree->id())
            ->where('parentfamily.l_type', '=', $sex_field)
            ->where('childbirth.d_julianday2', '>', new Expression($prefix . 'birth.d_julianday1'))
            ->select(['parentfamily.l_to AS id', new Expression($prefix . 'childbirth.d_julianday2 - ' . $prefix . 'birth.d_julianday1 AS age')])
            ->take(1)
            ->orderBy('age', $age_dir)
            ->get()
            ->first();

        if ($row === null) {
            return I18N::translate('This information is not available.');
        }

        $person = Registry::individualFactory()->make($row->id, $this->tree);

        switch ($type) {
            default:
            case 'full':
                if ($person !== null && $person->canShow()) {
                    $result = $person->formatList();
                } else {
                    $result = I18N::translate('This information is private and cannot be shown.');
                }
                break;

            case 'name':
                $result = '<a href="' . e($person->url()) . '">' . $person->fullName() . '</a>';
                break;

            case 'age':
                $age = $row->age;

                if ($show_years) {
                    $result = $this->calculateAge((int) $row->age);
                } else {
                    $result = (string) floor($age / 365.25);
                }

                break;
        }

        return $result;
    }

    /**
     * General query on age at marriage.
     *
     * @param string $type
     * @param string $age_dir "ASC" or "DESC"
     * @param int    $limit
     */
    public function ageOfMarriageQuery(string $type, string $age_dir, int $limit): string
    {
        $prefix = DB::connection()->getTablePrefix();

        $hrows = DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->join('dates AS married', static function (JoinClause $join): void {
                $join
                    ->on('married.d_file', '=', 'f_file')
                    ->on('married.d_gid', '=', 'f_id')
                    ->where('married.d_fact', '=', 'MARR')
                    ->where('married.d_julianday1', '<>', 0);
            })
            ->join('dates AS husbdeath', static function (JoinClause $join): void {
                $join
                    ->on('husbdeath.d_gid', '=', 'f_husb')
                    ->on('husbdeath.d_file', '=', 'f_file')
                    ->where('husbdeath.d_fact', '=', 'DEAT');
            })
            ->whereColumn('married.d_julianday1', '<', 'husbdeath.d_julianday2')
            ->groupBy(['f_id'])
            ->select(['f_id AS family', new Expression('MIN(' . $prefix . 'husbdeath.d_julianday2 - ' . $prefix . 'married.d_julianday1) AS age')])
            ->get()
            ->all();

        $wrows = DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->join('dates AS married', static function (JoinClause $join): void {
                $join
                    ->on('married.d_file', '=', 'f_file')
                    ->on('married.d_gid', '=', 'f_id')
                    ->where('married.d_fact', '=', 'MARR')
                    ->where('married.d_julianday1', '<>', 0);
            })
            ->join('dates AS wifedeath', static function (JoinClause $join): void {
                $join
                    ->on('wifedeath.d_gid', '=', 'f_wife')
                    ->on('wifedeath.d_file', '=', 'f_file')
                    ->where('wifedeath.d_fact', '=', 'DEAT');
            })
            ->whereColumn('married.d_julianday1', '<', 'wifedeath.d_julianday2')
            ->groupBy(['f_id'])
            ->select(['f_id AS family', new Expression('MIN(' . $prefix . 'wifedeath.d_julianday2 - ' . $prefix . 'married.d_julianday1) AS age')])
            ->get()
            ->all();

        $drows = DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->join('dates AS married', static function (JoinClause $join): void {
                $join
                    ->on('married.d_file', '=', 'f_file')
                    ->on('married.d_gid', '=', 'f_id')
                    ->where('married.d_fact', '=', 'MARR')
                    ->where('married.d_julianday1', '<>', 0);
            })
            ->join('dates AS divorced', static function (JoinClause $join): void {
                $join
                    ->on('divorced.d_gid', '=', 'f_id')
                    ->on('divorced.d_file', '=', 'f_file')
                    ->whereIn('divorced.d_fact', ['DIV', 'ANUL', '_SEPR']);
            })
            ->whereColumn('married.d_julianday1', '<', 'divorced.d_julianday2')
            ->groupBy(['f_id'])
            ->select(['f_id AS family', new Expression('MIN(' . $prefix . 'divorced.d_julianday2 - ' . $prefix . 'married.d_julianday1) AS age')])
            ->get()
            ->all();

        $rows = [];
        foreach ($drows as $family) {
            $rows[$family->family] = $family->age;
        }

        foreach ($hrows as $family) {
            if (!isset($rows[$family->family])) {
                $rows[$family->family] = $family->age;
            }
        }

        foreach ($wrows as $family) {
            if (!isset($rows[$family->family])) {
                $rows[$family->family] = $family->age;
            } elseif ($rows[$family->family] > $family->age) {
                $rows[$family->family] = $family->age;
            }
        }

        if ($age_dir === 'DESC') {
            arsort($rows);
        } else {
            asort($rows);
        }

        $top10 = [];
        $i     = 0;
        foreach ($rows as $xref => $age) {
            $family = Registry::familyFactory()->make((string) $xref, $this->tree);
            if ($type === 'name') {
                return $family->formatList();
            }

            $age = $this->calculateAge((int) $age);

            if ($type === 'age') {
                return $age;
            }

            $husb = $family->husband();
            $wife = $family->wife();

            if (
                $husb instanceof Individual &&
                $wife instanceof Individual &&
                ($husb->getAllDeathDates() || !$husb->isDead()) &&
                ($wife->getAllDeathDates() || !$wife->isDead())
            ) {
                if ($family->canShow()) {
                    if ($type === 'list') {
                        $top10[] = '<li><a href="' . e($family->url()) . '">' . $family->fullName() . '</a> (' . $age . ')' . '</li>';
                    } else {
                        $top10[] = '<a href="' . e($family->url()) . '">' . $family->fullName() . '</a> (' . $age . ')';
                    }
                }
                if (++$i === $limit) {
                    break;
                }
            }
        }

        if ($type === 'list') {
            $top10 = implode('', $top10);
        } else {
            $top10 = implode('; ', $top10);
        }

        if (I18N::direction() === 'rtl') {
            $top10 = str_replace([
                '[',
                ']',
                '(',
                ')',
                '+',
            ], [
                '&rlm;[',
                '&rlm;]',
                '&rlm;(',
                '&rlm;)',
                '&rlm;+',
            ], $top10);
        }

        if ($type === 'list') {
            return '<ul>' . $top10 . '</ul>';
        }

        return $top10;
    }

    /**
     * @return array<array{family:Family,age:string}>
     */
    private function ageBetweenSpousesQuery(string $age_dir, int $limit): array
    {
        $prefix = DB::connection()->getTablePrefix();

        $query = DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->join('dates AS wife', static function (JoinClause $join): void {
                $join
                    ->on('wife.d_gid', '=', 'f_wife')
                    ->on('wife.d_file', '=', 'f_file')
                    ->where('wife.d_fact', '=', 'BIRT')
                    ->where('wife.d_julianday1', '<>', 0);
            })
            ->join('dates AS husb', static function (JoinClause $join): void {
                $join
                    ->on('husb.d_gid', '=', 'f_husb')
                    ->on('husb.d_file', '=', 'f_file')
                    ->where('husb.d_fact', '=', 'BIRT')
                    ->where('husb.d_julianday1', '<>', 0);
            });

        if ($age_dir === 'DESC') {
            $query
                ->whereColumn('wife.d_julianday1', '>=', 'husb.d_julianday1')
                ->orderBy(new Expression('MIN(' . $prefix . 'wife.d_julianday1) - MIN(' . $prefix . 'husb.d_julianday1)'), 'DESC');
        } else {
            $query
                ->whereColumn('husb.d_julianday1', '>=', 'wife.d_julianday1')
                ->orderBy(new Expression('MIN(' . $prefix . 'husb.d_julianday1) - MIN(' . $prefix . 'wife.d_julianday1)'), 'DESC');
        }

        return $query
            ->groupBy(['f_id', 'f_file'])
            ->select(['families.*'])
            ->take($limit)
            ->get()
            ->map(Registry::familyFactory()->mapper($this->tree))
            ->filter(GedcomRecord::accessFilter())
            ->map(function (Family $family) use ($age_dir): array {
                $husb_birt_jd = $family->husband()->getBirthDate()->minimumJulianDay();
                $wife_birt_jd = $family->wife()->getBirthDate()->minimumJulianDay();

                if ($age_dir === 'DESC') {
                    $diff = $wife_birt_jd - $husb_birt_jd;
                } else {
                    $diff = $husb_birt_jd - $wife_birt_jd;
                }

                return [
                    'family' => $family,
                    'age'    => $this->calculateAge($diff),
                ];
            })
            ->all();
    }

    public function ageBetweenSpousesMF(int $limit = 10): string
    {
        $records = $this->ageBetweenSpousesQuery('DESC', $limit);

        return view('statistics/families/top10-nolist-spouses', [
            'records' => $records,
        ]);
    }

    public function ageBetweenSpousesMFList(int $limit = 10): string
    {
        $records = $this->ageBetweenSpousesQuery('DESC', $limit);

        return view('statistics/families/top10-list-spouses', [
            'records' => $records,
        ]);
    }

    public function ageBetweenSpousesFM(int $limit = 10): string
    {
        return view('statistics/families/top10-nolist-spouses', [
            'records' => $this->ageBetweenSpousesQuery('ASC', $limit),
        ]);
    }

    public function ageBetweenSpousesFMList(int $limit = 10): string
    {
        return view('statistics/families/top10-list-spouses', [
            'records' => $this->ageBetweenSpousesQuery('ASC', $limit),
        ]);
    }

    /**
     * @return array<object{f_id:string,d_gid:string,age:int}>
     */
    public function statsMarrAgeQuery(string $sex, int $year1, int $year2): array
    {
        $prefix = DB::connection()->getTablePrefix();

        $query = DB::table('dates AS married')
            ->join('families', static function (JoinClause $join): void {
                $join
                    ->on('f_file', '=', 'married.d_file')
                    ->on('f_id', '=', 'married.d_gid');
            })
            ->join('dates AS birth', static function (JoinClause $join) use ($sex): void {
                $join
                    ->on('birth.d_file', '=', 'married.d_file')
                    ->on('birth.d_gid', '=', $sex === 'M' ? 'f_husb' : 'f_wife')
                    ->where('birth.d_julianday1', '<>', 0)
                    ->where('birth.d_fact', '=', 'BIRT')
                    ->whereIn('birth.d_type', ['@#DGREGORIAN@', '@#DJULIAN@']);
            })
            ->where('married.d_file', '=', $this->tree->id())
            ->where('married.d_fact', '=', 'MARR')
            ->whereIn('married.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->whereColumn('married.d_julianday1', '>', 'birth.d_julianday1')
            ->select(['f_id', 'birth.d_gid', new Expression($prefix . 'married.d_julianday2 - ' . $prefix . 'birth.d_julianday1 AS age')]);

        if ($year1 !== 0 && $year2 !== 0) {
            $query->whereBetween('married.d_year', [$year1, $year2]);
        }

        return $query
            ->get()
            ->map(static fn (object $row): object => (object) [
                'f_id'  => $row->f_id,
                'd_gid' => $row->d_gid,
                'age'   => (int) $row->age,
            ])
            ->all();
    }

    /**
     * Query the database for marriage tags.
     *
     * @param string $show    "full", "name" or "age"
     * @param string $age_dir "ASC" or "DESC"
     * @param string $sex     "F" or "M"
     * @param bool   $show_years
     */
    public function marriageQuery(string $show, string $age_dir, string $sex, bool $show_years): string
    {
        $prefix = DB::connection()->getTablePrefix();

        if ($sex === 'F') {
            $sex_field = 'f_wife';
        } else {
            $sex_field = 'f_husb';
        }

        if ($age_dir !== 'ASC') {
            $age_dir = 'DESC';
        }

        $row = DB::table('families')
            ->join('dates AS married', static function (JoinClause $join): void {
                $join
                    ->on('married.d_file', '=', 'f_file')
                    ->on('married.d_gid', '=', 'f_id')
                    ->where('married.d_fact', '=', 'MARR');
            })
            ->join('individuals', static function (JoinClause $join) use ($sex, $sex_field): void {
                $join
                    ->on('i_file', '=', 'f_file')
                    ->on('i_id', '=', $sex_field)
                    ->where('i_sex', '=', $sex);
            })
            ->join('dates AS birth', static function (JoinClause $join): void {
                $join
                    ->on('birth.d_file', '=', 'i_file')
                    ->on('birth.d_gid', '=', 'i_id')
                    ->where('birth.d_fact', '=', 'BIRT')
                    ->where('birth.d_julianday1', '<>', 0);
            })
            ->where('f_file', '=', $this->tree->id())
            ->where('married.d_julianday2', '>', new Expression($prefix . 'birth.d_julianday1'))
            ->orderBy(new Expression($prefix . 'married.d_julianday2 - ' . $prefix . 'birth.d_julianday1'), $age_dir)
            ->select(['f_id AS famid', $sex_field, new Expression($prefix . 'married.d_julianday2 - ' . $prefix . 'birth.d_julianday1 AS age'), 'i_id'])
            ->take(1)
            ->get()
            ->first();

        if ($row === null) {
            return I18N::translate('This information is not available.');
        }

        $family = Registry::familyFactory()->make($row->famid, $this->tree);
        $person = Registry::individualFactory()->make($row->i_id, $this->tree);

        switch ($show) {
            default:
            case 'full':
                if ($family !== null && $family->canShow()) {
                    $result = $family->formatList();
                } else {
                    $result = I18N::translate('This information is private and cannot be shown.');
                }
                break;

            case 'name':
                $result = '<a href="' . e($family->url()) . '">' . $person->fullName() . '</a>';
                break;

            case 'age':
                $age = $row->age;

                if ($show_years) {
                    $result = $this->calculateAge((int) $row->age);
                } else {
                    $result = I18N::number((int) ($age / 365.25));
                }

                break;
        }

        return $result;
    }

    /**
     * Who is currently logged in?
     *
     * @param string $type "list" or "nolist"
     */
    private function usersLoggedInQuery(string $type): string
    {
        $content   = '';
        $anonymous = 0;
        $logged_in = [];

        foreach ($this->user_service->allLoggedIn() as $user) {
            if (Auth::isAdmin() || $user->getPreference(UserInterface::PREF_IS_VISIBLE_ONLINE) === '1') {
                $logged_in[] = $user;
            } else {
                $anonymous++;
            }
        }

        $count_logged_in = count($logged_in);

        if ($count_logged_in === 0 && $anonymous === 0) {
            $content .= I18N::translate('No signed-in and no anonymous users');
        }

        if ($anonymous > 0) {
            $content .= '<b>' . I18N::plural('%s anonymous signed-in user', '%s anonymous signed-in users', $anonymous, I18N::number($anonymous)) . '</b>';
        }

        if ($count_logged_in > 0) {
            if ($anonymous !== 0) {
                if ($type === 'list') {
                    $content .= '<br><br>';
                } else {
                    $content .= ' ' . I18N::translate('and') . ' ';
                }
            }
            $content .= '<b>' . I18N::plural('%s signed-in user', '%s signed-in users', $count_logged_in, I18N::number($count_logged_in)) . '</b>';
            if ($type === 'list') {
                $content .= '<ul>';
            } else {
                $content .= ': ';
            }
        }

        if (Auth::check()) {
            foreach ($logged_in as $user) {
                if ($type === 'list') {
                    $content .= '<li>';
                }

                $individual = Registry::individualFactory()->make($this->tree->getUserPreference($user, UserInterface::PREF_TREE_ACCOUNT_XREF), $this->tree);

                if ($individual instanceof Individual && $individual->canShow()) {
                    $content .= '<a href="' . e($individual->url()) . '">' . e($user->realName()) . '</a>';
                } else {
                    $content .= e($user->realName());
                }

                $content .= ' - ' . e($user->userName());

                if ($user->getPreference(UserInterface::PREF_CONTACT_METHOD) !== MessageService::CONTACT_METHOD_NONE && Auth::id() !== $user->id()) {
                    $content .= '<a href="' . e(route(MessagePage::class, ['to' => $user->userName(), 'tree' => $this->tree->name()])) . '" class="btn btn-link" title="' . I18N::translate('Send a message') . '">' . view('icons/email') . '</a>';
                }

                if ($type === 'list') {
                    $content .= '</li>';
                }
            }
        }

        if ($type === 'list') {
            $content .= '</ul>';
        }

        return $content;
    }

    public function usersLoggedIn(): string
    {
        return $this->usersLoggedInQuery('nolist');
    }

    public function usersLoggedInList(): string
    {
        return $this->usersLoggedInQuery('list');
    }

    /**
     * Century name, English => 21st, Polish => XXI, etc.
     */
    private function centuryName(int $century): string
    {
        if ($century < 0) {
            return I18N::translate('%s BCE', $this->centuryName(-$century));
        }

        // The current chart engine (Google charts) can't handle <sup></sup> markup
        switch ($century) {
            case 21:
                return strip_tags(I18N::translateContext('CENTURY', '21st'));
            case 20:
                return strip_tags(I18N::translateContext('CENTURY', '20th'));
            case 19:
                return strip_tags(I18N::translateContext('CENTURY', '19th'));
            case 18:
                return strip_tags(I18N::translateContext('CENTURY', '18th'));
            case 17:
                return strip_tags(I18N::translateContext('CENTURY', '17th'));
            case 16:
                return strip_tags(I18N::translateContext('CENTURY', '16th'));
            case 15:
                return strip_tags(I18N::translateContext('CENTURY', '15th'));
            case 14:
                return strip_tags(I18N::translateContext('CENTURY', '14th'));
            case 13:
                return strip_tags(I18N::translateContext('CENTURY', '13th'));
            case 12:
                return strip_tags(I18N::translateContext('CENTURY', '12th'));
            case 11:
                return strip_tags(I18N::translateContext('CENTURY', '11th'));
            case 10:
                return strip_tags(I18N::translateContext('CENTURY', '10th'));
            case 9:
                return strip_tags(I18N::translateContext('CENTURY', '9th'));
            case 8:
                return strip_tags(I18N::translateContext('CENTURY', '8th'));
            case 7:
                return strip_tags(I18N::translateContext('CENTURY', '7th'));
            case 6:
                return strip_tags(I18N::translateContext('CENTURY', '6th'));
            case 5:
                return strip_tags(I18N::translateContext('CENTURY', '5th'));
            case 4:
                return strip_tags(I18N::translateContext('CENTURY', '4th'));
            case 3:
                return strip_tags(I18N::translateContext('CENTURY', '3rd'));
            case 2:
                return strip_tags(I18N::translateContext('CENTURY', '2nd'));
            case 1:
                return strip_tags(I18N::translateContext('CENTURY', '1st'));
            default:
                return ($century - 1) . '01-' . $century . '00';
        }
    }

    /**
     * @return array<string>
     */
    private function getAllCountries(): array
    {
        return [
            /* I18N: Name of a country or state */
            '???' => I18N::translate('Unknown'),
            /* I18N: Name of a country or state */
            'ABW' => I18N::translate('Aruba'),
            /* I18N: Name of a country or state */
            'AFG' => I18N::translate('Afghanistan'),
            /* I18N: Name of a country or state */
            'AGO' => I18N::translate('Angola'),
            /* I18N: Name of a country or state */
            'AIA' => I18N::translate('Anguilla'),
            /* I18N: Name of a country or state */
            'ALA' => I18N::translate('Åland Islands'),
            /* I18N: Name of a country or state */
            'ALB' => I18N::translate('Albania'),
            /* I18N: Name of a country or state */
            'AND' => I18N::translate('Andorra'),
            /* I18N: Name of a country or state */
            'ARE' => I18N::translate('United Arab Emirates'),
            /* I18N: Name of a country or state */
            'ARG' => I18N::translate('Argentina'),
            /* I18N: Name of a country or state */
            'ARM' => I18N::translate('Armenia'),
            /* I18N: Name of a country or state */
            'ASM' => I18N::translate('American Samoa'),
            /* I18N: Name of a country or state */
            'ATA' => I18N::translate('Antarctica'),
            /* I18N: Name of a country or state */
            'ATF' => I18N::translate('French Southern Territories'),
            /* I18N: Name of a country or state */
            'ATG' => I18N::translate('Antigua and Barbuda'),
            /* I18N: Name of a country or state */
            'AUS' => I18N::translate('Australia'),
            /* I18N: Name of a country or state */
            'AUT' => I18N::translate('Austria'),
            /* I18N: Name of a country or state */
            'AZE' => I18N::translate('Azerbaijan'),
            /* I18N: Name of a country or state */
            'AZR' => I18N::translate('Azores'),
            /* I18N: Name of a country or state */
            'BDI' => I18N::translate('Burundi'),
            /* I18N: Name of a country or state */
            'BEL' => I18N::translate('Belgium'),
            /* I18N: Name of a country or state */
            'BEN' => I18N::translate('Benin'),
            // BES => Bonaire, Sint Eustatius and Saba
            /* I18N: Name of a country or state */
            'BFA' => I18N::translate('Burkina Faso'),
            /* I18N: Name of a country or state */
            'BGD' => I18N::translate('Bangladesh'),
            /* I18N: Name of a country or state */
            'BGR' => I18N::translate('Bulgaria'),
            /* I18N: Name of a country or state */
            'BHR' => I18N::translate('Bahrain'),
            /* I18N: Name of a country or state */
            'BHS' => I18N::translate('Bahamas'),
            /* I18N: Name of a country or state */
            'BIH' => I18N::translate('Bosnia and Herzegovina'),
            // BLM => Saint Barthélemy
            'BLM' => I18N::translate('Saint Barthélemy'),
            /* I18N: Name of a country or state */
            'BLR' => I18N::translate('Belarus'),
            /* I18N: Name of a country or state */
            'BLZ' => I18N::translate('Belize'),
            /* I18N: Name of a country or state */
            'BMU' => I18N::translate('Bermuda'),
            /* I18N: Name of a country or state */
            'BOL' => I18N::translate('Bolivia'),
            /* I18N: Name of a country or state */
            'BRA' => I18N::translate('Brazil'),
            /* I18N: Name of a country or state */
            'BRB' => I18N::translate('Barbados'),
            /* I18N: Name of a country or state */
            'BRN' => I18N::translate('Brunei Darussalam'),
            /* I18N: Name of a country or state */
            'BTN' => I18N::translate('Bhutan'),
            /* I18N: Name of a country or state */
            'BVT' => I18N::translate('Bouvet Island'),
            /* I18N: Name of a country or state */
            'BWA' => I18N::translate('Botswana'),
            /* I18N: Name of a country or state */
            'CAF' => I18N::translate('Central African Republic'),
            /* I18N: Name of a country or state */
            'CAN' => I18N::translate('Canada'),
            /* I18N: Name of a country or state */
            'CCK' => I18N::translate('Cocos (Keeling) Islands'),
            /* I18N: Name of a country or state */
            'CHE' => I18N::translate('Switzerland'),
            /* I18N: Name of a country or state */
            'CHL' => I18N::translate('Chile'),
            /* I18N: Name of a country or state */
            'CHN' => I18N::translate('China'),
            /* I18N: Name of a country or state */
            'CIV' => I18N::translate('Côte d’Ivoire'),
            /* I18N: Name of a country or state */
            'CMR' => I18N::translate('Cameroon'),
            /* I18N: Name of a country or state */
            'COD' => I18N::translate('Democratic Republic of the Congo'),
            /* I18N: Name of a country or state */
            'COG' => I18N::translate('Republic of the Congo'),
            /* I18N: Name of a country or state */
            'COK' => I18N::translate('Cook Islands'),
            /* I18N: Name of a country or state */
            'COL' => I18N::translate('Colombia'),
            /* I18N: Name of a country or state */
            'COM' => I18N::translate('Comoros'),
            /* I18N: Name of a country or state */
            'CPV' => I18N::translate('Cape Verde'),
            /* I18N: Name of a country or state */
            'CRI' => I18N::translate('Costa Rica'),
            /* I18N: Name of a country or state */
            'CUB' => I18N::translate('Cuba'),
            /* I18N: Name of a country or state */
            'CUW' => I18N::translate('Curaçao'),
            /* I18N: Name of a country or state */
            'CXR' => I18N::translate('Christmas Island'),
            /* I18N: Name of a country or state */
            'CYM' => I18N::translate('Cayman Islands'),
            /* I18N: Name of a country or state */
            'CYP' => I18N::translate('Cyprus'),
            /* I18N: Name of a country or state */
            'CZE' => I18N::translate('Czech Republic'),
            /* I18N: Name of a country or state */
            'DEU' => I18N::translate('Germany'),
            /* I18N: Name of a country or state */
            'DJI' => I18N::translate('Djibouti'),
            /* I18N: Name of a country or state */
            'DMA' => I18N::translate('Dominica'),
            /* I18N: Name of a country or state */
            'DNK' => I18N::translate('Denmark'),
            /* I18N: Name of a country or state */
            'DOM' => I18N::translate('Dominican Republic'),
            /* I18N: Name of a country or state */
            'DZA' => I18N::translate('Algeria'),
            /* I18N: Name of a country or state */
            'ECU' => I18N::translate('Ecuador'),
            /* I18N: Name of a country or state */
            'EGY' => I18N::translate('Egypt'),
            /* I18N: Name of a country or state */
            'ENG' => I18N::translate('England'),
            /* I18N: Name of a country or state */
            'ERI' => I18N::translate('Eritrea'),
            /* I18N: Name of a country or state */
            'ESH' => I18N::translate('Western Sahara'),
            /* I18N: Name of a country or state */
            'ESP' => I18N::translate('Spain'),
            /* I18N: Name of a country or state */
            'EST' => I18N::translate('Estonia'),
            /* I18N: Name of a country or state */
            'ETH' => I18N::translate('Ethiopia'),
            /* I18N: Name of a country or state */
            'FIN' => I18N::translate('Finland'),
            /* I18N: Name of a country or state */
            'FJI' => I18N::translate('Fiji'),
            /* I18N: Name of a country or state */
            'FLD' => I18N::translate('Flanders'),
            /* I18N: Name of a country or state */
            'FLK' => I18N::translate('Falkland Islands'),
            /* I18N: Name of a country or state */
            'FRA' => I18N::translate('France'),
            /* I18N: Name of a country or state */
            'FRO' => I18N::translate('Faroe Islands'),
            /* I18N: Name of a country or state */
            'FSM' => I18N::translate('Micronesia'),
            /* I18N: Name of a country or state */
            'GAB' => I18N::translate('Gabon'),
            /* I18N: Name of a country or state */
            'GBR' => I18N::translate('United Kingdom'),
            /* I18N: Name of a country or state */
            'GEO' => I18N::translate('Georgia'),
            /* I18N: Name of a country or state */
            'GGY' => I18N::translate('Guernsey'),
            /* I18N: Name of a country or state */
            'GHA' => I18N::translate('Ghana'),
            /* I18N: Name of a country or state */
            'GIB' => I18N::translate('Gibraltar'),
            /* I18N: Name of a country or state */
            'GIN' => I18N::translate('Guinea'),
            /* I18N: Name of a country or state */
            'GLP' => I18N::translate('Guadeloupe'),
            /* I18N: Name of a country or state */
            'GMB' => I18N::translate('Gambia'),
            /* I18N: Name of a country or state */
            'GNB' => I18N::translate('Guinea-Bissau'),
            /* I18N: Name of a country or state */
            'GNQ' => I18N::translate('Equatorial Guinea'),
            /* I18N: Name of a country or state */
            'GRC' => I18N::translate('Greece'),
            /* I18N: Name of a country or state */
            'GRD' => I18N::translate('Grenada'),
            /* I18N: Name of a country or state */
            'GRL' => I18N::translate('Greenland'),
            /* I18N: Name of a country or state */
            'GTM' => I18N::translate('Guatemala'),
            /* I18N: Name of a country or state */
            'GUF' => I18N::translate('French Guiana'),
            /* I18N: Name of a country or state */
            'GUM' => I18N::translate('Guam'),
            /* I18N: Name of a country or state */
            'GUY' => I18N::translate('Guyana'),
            /* I18N: Name of a country or state */
            'HKG' => I18N::translate('Hong Kong'),
            /* I18N: Name of a country or state */
            'HMD' => I18N::translate('Heard Island and McDonald Islands'),
            /* I18N: Name of a country or state */
            'HND' => I18N::translate('Honduras'),
            /* I18N: Name of a country or state */
            'HRV' => I18N::translate('Croatia'),
            /* I18N: Name of a country or state */
            'HTI' => I18N::translate('Haiti'),
            /* I18N: Name of a country or state */
            'HUN' => I18N::translate('Hungary'),
            /* I18N: Name of a country or state */
            'IDN' => I18N::translate('Indonesia'),
            /* I18N: Name of a country or state */
            'IND' => I18N::translate('India'),
            /* I18N: Name of a country or state */
            'IOM' => I18N::translate('Isle of Man'),
            /* I18N: Name of a country or state */
            'IOT' => I18N::translate('British Indian Ocean Territory'),
            /* I18N: Name of a country or state */
            'IRL' => I18N::translate('Ireland'),
            /* I18N: Name of a country or state */
            'IRN' => I18N::translate('Iran'),
            /* I18N: Name of a country or state */
            'IRQ' => I18N::translate('Iraq'),
            /* I18N: Name of a country or state */
            'ISL' => I18N::translate('Iceland'),
            /* I18N: Name of a country or state */
            'ISR' => I18N::translate('Israel'),
            /* I18N: Name of a country or state */
            'ITA' => I18N::translate('Italy'),
            /* I18N: Name of a country or state */
            'JAM' => I18N::translate('Jamaica'),
            //'JEY' => Jersey
            /* I18N: Name of a country or state */
            'JOR' => I18N::translate('Jordan'),
            /* I18N: Name of a country or state */
            'JPN' => I18N::translate('Japan'),
            /* I18N: Name of a country or state */
            'KAZ' => I18N::translate('Kazakhstan'),
            /* I18N: Name of a country or state */
            'KEN' => I18N::translate('Kenya'),
            /* I18N: Name of a country or state */
            'KGZ' => I18N::translate('Kyrgyzstan'),
            /* I18N: Name of a country or state */
            'KHM' => I18N::translate('Cambodia'),
            /* I18N: Name of a country or state */
            'KIR' => I18N::translate('Kiribati'),
            /* I18N: Name of a country or state */
            'KNA' => I18N::translate('Saint Kitts and Nevis'),
            /* I18N: Name of a country or state */
            'KOR' => I18N::translate('Korea'),
            /* I18N: Name of a country or state */
            'KWT' => I18N::translate('Kuwait'),
            /* I18N: Name of a country or state */
            'LAO' => I18N::translate('Laos'),
            /* I18N: Name of a country or state */
            'LBN' => I18N::translate('Lebanon'),
            /* I18N: Name of a country or state */
            'LBR' => I18N::translate('Liberia'),
            /* I18N: Name of a country or state */
            'LBY' => I18N::translate('Libya'),
            /* I18N: Name of a country or state */
            'LCA' => I18N::translate('Saint Lucia'),
            /* I18N: Name of a country or state */
            'LIE' => I18N::translate('Liechtenstein'),
            /* I18N: Name of a country or state */
            'LKA' => I18N::translate('Sri Lanka'),
            /* I18N: Name of a country or state */
            'LSO' => I18N::translate('Lesotho'),
            /* I18N: Name of a country or state */
            'LTU' => I18N::translate('Lithuania'),
            /* I18N: Name of a country or state */
            'LUX' => I18N::translate('Luxembourg'),
            /* I18N: Name of a country or state */
            'LVA' => I18N::translate('Latvia'),
            /* I18N: Name of a country or state */
            'MAC' => I18N::translate('Macau'),
            // MAF => Saint Martin
            /* I18N: Name of a country or state */
            'MAR' => I18N::translate('Morocco'),
            /* I18N: Name of a country or state */
            'MCO' => I18N::translate('Monaco'),
            /* I18N: Name of a country or state */
            'MDA' => I18N::translate('Moldova'),
            /* I18N: Name of a country or state */
            'MDG' => I18N::translate('Madagascar'),
            /* I18N: Name of a country or state */
            'MDV' => I18N::translate('Maldives'),
            /* I18N: Name of a country or state */
            'MEX' => I18N::translate('Mexico'),
            /* I18N: Name of a country or state */
            'MHL' => I18N::translate('Marshall Islands'),
            /* I18N: Name of a country or state */
            'MKD' => I18N::translate('Macedonia'),
            /* I18N: Name of a country or state */
            'MLI' => I18N::translate('Mali'),
            /* I18N: Name of a country or state */
            'MLT' => I18N::translate('Malta'),
            /* I18N: Name of a country or state */
            'MMR' => I18N::translate('Myanmar'),
            /* I18N: Name of a country or state */
            'MNG' => I18N::translate('Mongolia'),
            /* I18N: Name of a country or state */
            'MNP' => I18N::translate('Northern Mariana Islands'),
            /* I18N: Name of a country or state */
            'MNT' => I18N::translate('Montenegro'),
            /* I18N: Name of a country or state */
            'MOZ' => I18N::translate('Mozambique'),
            /* I18N: Name of a country or state */
            'MRT' => I18N::translate('Mauritania'),
            /* I18N: Name of a country or state */
            'MSR' => I18N::translate('Montserrat'),
            /* I18N: Name of a country or state */
            'MTQ' => I18N::translate('Martinique'),
            /* I18N: Name of a country or state */
            'MUS' => I18N::translate('Mauritius'),
            /* I18N: Name of a country or state */
            'MWI' => I18N::translate('Malawi'),
            /* I18N: Name of a country or state */
            'MYS' => I18N::translate('Malaysia'),
            /* I18N: Name of a country or state */
            'MYT' => I18N::translate('Mayotte'),
            /* I18N: Name of a country or state */
            'NAM' => I18N::translate('Namibia'),
            /* I18N: Name of a country or state */
            'NCL' => I18N::translate('New Caledonia'),
            /* I18N: Name of a country or state */
            'NER' => I18N::translate('Niger'),
            /* I18N: Name of a country or state */
            'NFK' => I18N::translate('Norfolk Island'),
            /* I18N: Name of a country or state */
            'NGA' => I18N::translate('Nigeria'),
            /* I18N: Name of a country or state */
            'NIC' => I18N::translate('Nicaragua'),
            /* I18N: Name of a country or state */
            'NIR' => I18N::translate('Northern Ireland'),
            /* I18N: Name of a country or state */
            'NIU' => I18N::translate('Niue'),
            /* I18N: Name of a country or state */
            'NLD' => I18N::translate('Netherlands'),
            /* I18N: Name of a country or state */
            'NOR' => I18N::translate('Norway'),
            /* I18N: Name of a country or state */
            'NPL' => I18N::translate('Nepal'),
            /* I18N: Name of a country or state */
            'NRU' => I18N::translate('Nauru'),
            /* I18N: Name of a country or state */
            'NZL' => I18N::translate('New Zealand'),
            /* I18N: Name of a country or state */
            'OMN' => I18N::translate('Oman'),
            /* I18N: Name of a country or state */
            'PAK' => I18N::translate('Pakistan'),
            /* I18N: Name of a country or state */
            'PAN' => I18N::translate('Panama'),
            /* I18N: Name of a country or state */
            'PCN' => I18N::translate('Pitcairn'),
            /* I18N: Name of a country or state */
            'PER' => I18N::translate('Peru'),
            /* I18N: Name of a country or state */
            'PHL' => I18N::translate('Philippines'),
            /* I18N: Name of a country or state */
            'PLW' => I18N::translate('Palau'),
            /* I18N: Name of a country or state */
            'PNG' => I18N::translate('Papua New Guinea'),
            /* I18N: Name of a country or state */
            'POL' => I18N::translate('Poland'),
            /* I18N: Name of a country or state */
            'PRI' => I18N::translate('Puerto Rico'),
            /* I18N: Name of a country or state */
            'PRK' => I18N::translate('North Korea'),
            /* I18N: Name of a country or state */
            'PRT' => I18N::translate('Portugal'),
            /* I18N: Name of a country or state */
            'PRY' => I18N::translate('Paraguay'),
            /* I18N: Name of a country or state */
            'PSE' => I18N::translate('Occupied Palestinian Territory'),
            /* I18N: Name of a country or state */
            'PYF' => I18N::translate('French Polynesia'),
            /* I18N: Name of a country or state */
            'QAT' => I18N::translate('Qatar'),
            /* I18N: Name of a country or state */
            'REU' => I18N::translate('Réunion'),
            /* I18N: Name of a country or state */
            'ROM' => I18N::translate('Romania'),
            /* I18N: Name of a country or state */
            'RUS' => I18N::translate('Russia'),
            /* I18N: Name of a country or state */
            'RWA' => I18N::translate('Rwanda'),
            /* I18N: Name of a country or state */
            'SAU' => I18N::translate('Saudi Arabia'),
            /* I18N: Name of a country or state */
            'SCT' => I18N::translate('Scotland'),
            /* I18N: Name of a country or state */
            'SDN' => I18N::translate('Sudan'),
            /* I18N: Name of a country or state */
            'SEA' => I18N::translate('At sea'),
            /* I18N: Name of a country or state */
            'SEN' => I18N::translate('Senegal'),
            /* I18N: Name of a country or state */
            'SER' => I18N::translate('Serbia'),
            /* I18N: Name of a country or state */
            'SGP' => I18N::translate('Singapore'),
            /* I18N: Name of a country or state */
            'SGS' => I18N::translate('South Georgia and the South Sandwich Islands'),
            /* I18N: Name of a country or state */
            'SHN' => I18N::translate('Saint Helena'),
            /* I18N: Name of a country or state */
            'SJM' => I18N::translate('Svalbard and Jan Mayen'),
            /* I18N: Name of a country or state */
            'SLB' => I18N::translate('Solomon Islands'),
            /* I18N: Name of a country or state */
            'SLE' => I18N::translate('Sierra Leone'),
            /* I18N: Name of a country or state */
            'SLV' => I18N::translate('El Salvador'),
            /* I18N: Name of a country or state */
            'SMR' => I18N::translate('San Marino'),
            /* I18N: Name of a country or state */
            'SOM' => I18N::translate('Somalia'),
            /* I18N: Name of a country or state */
            'SPM' => I18N::translate('Saint Pierre and Miquelon'),
            /* I18N: Name of a country or state */
            'SSD' => I18N::translate('South Sudan'),
            /* I18N: Name of a country or state */
            'STP' => I18N::translate('Sao Tome and Principe'),
            /* I18N: Name of a country or state */
            'SUR' => I18N::translate('Suriname'),
            /* I18N: Name of a country or state */
            'SVK' => I18N::translate('Slovakia'),
            /* I18N: Name of a country or state */
            'SVN' => I18N::translate('Slovenia'),
            /* I18N: Name of a country or state */
            'SWE' => I18N::translate('Sweden'),
            /* I18N: Name of a country or state */
            'SWZ' => I18N::translate('Swaziland'),
            // SXM => Sint Maarten
            /* I18N: Name of a country or state */
            'SYC' => I18N::translate('Seychelles'),
            /* I18N: Name of a country or state */
            'SYR' => I18N::translate('Syria'),
            /* I18N: Name of a country or state */
            'TCA' => I18N::translate('Turks and Caicos Islands'),
            /* I18N: Name of a country or state */
            'TCD' => I18N::translate('Chad'),
            /* I18N: Name of a country or state */
            'TGO' => I18N::translate('Togo'),
            /* I18N: Name of a country or state */
            'THA' => I18N::translate('Thailand'),
            /* I18N: Name of a country or state */
            'TJK' => I18N::translate('Tajikistan'),
            /* I18N: Name of a country or state */
            'TKL' => I18N::translate('Tokelau'),
            /* I18N: Name of a country or state */
            'TKM' => I18N::translate('Turkmenistan'),
            /* I18N: Name of a country or state */
            'TLS' => I18N::translate('Timor-Leste'),
            /* I18N: Name of a country or state */
            'TON' => I18N::translate('Tonga'),
            /* I18N: Name of a country or state */
            'TTO' => I18N::translate('Trinidad and Tobago'),
            /* I18N: Name of a country or state */
            'TUN' => I18N::translate('Tunisia'),
            /* I18N: Name of a country or state */
            'TUR' => I18N::translate('Turkey'),
            /* I18N: Name of a country or state */
            'TUV' => I18N::translate('Tuvalu'),
            /* I18N: Name of a country or state */
            'TWN' => I18N::translate('Taiwan'),
            /* I18N: Name of a country or state */
            'TZA' => I18N::translate('Tanzania'),
            /* I18N: Name of a country or state */
            'UGA' => I18N::translate('Uganda'),
            /* I18N: Name of a country or state */
            'UKR' => I18N::translate('Ukraine'),
            /* I18N: Name of a country or state */
            'UMI' => I18N::translate('US Minor Outlying Islands'),
            /* I18N: Name of a country or state */
            'URY' => I18N::translate('Uruguay'),
            /* I18N: Name of a country or state */
            'USA' => I18N::translate('United States'),
            /* I18N: Name of a country or state */
            'UZB' => I18N::translate('Uzbekistan'),
            /* I18N: Name of a country or state */
            'VAT' => I18N::translate('Vatican City'),
            /* I18N: Name of a country or state */
            'VCT' => I18N::translate('Saint Vincent and the Grenadines'),
            /* I18N: Name of a country or state */
            'VEN' => I18N::translate('Venezuela'),
            /* I18N: Name of a country or state */
            'VGB' => I18N::translate('British Virgin Islands'),
            /* I18N: Name of a country or state */
            'VIR' => I18N::translate('US Virgin Islands'),
            /* I18N: Name of a country or state */
            'VNM' => I18N::translate('Vietnam'),
            /* I18N: Name of a country or state */
            'VUT' => I18N::translate('Vanuatu'),
            /* I18N: Name of a country or state */
            'WLF' => I18N::translate('Wallis and Futuna'),
            /* I18N: Name of a country or state */
            'WLS' => I18N::translate('Wales'),
            /* I18N: Name of a country or state */
            'WSM' => I18N::translate('Samoa'),
            /* I18N: Name of a country or state */
            'YEM' => I18N::translate('Yemen'),
            /* I18N: Name of a country or state */
            'ZAF' => I18N::translate('South Africa'),
            /* I18N: Name of a country or state */
            'ZMB' => I18N::translate('Zambia'),
            /* I18N: Name of a country or state */
            'ZWE' => I18N::translate('Zimbabwe'),
        ];
    }

    /**
     * ISO3166 3 letter codes, with their 2 letter equivalent.
     * NOTE: this is not 1:1. ENG/SCO/WAL/NIR => GB
     * NOTE: this also includes chapman codes and others. Should it?
     *
     * @return array<string>
     */
    private function iso3166(): array
    {
        return [
            'GBR' => 'GB', // Must come before ENG, NIR, SCT and WLS
            'ABW' => 'AW',
            'AFG' => 'AF',
            'AGO' => 'AO',
            'AIA' => 'AI',
            'ALA' => 'AX',
            'ALB' => 'AL',
            'AND' => 'AD',
            'ARE' => 'AE',
            'ARG' => 'AR',
            'ARM' => 'AM',
            'ASM' => 'AS',
            'ATA' => 'AQ',
            'ATF' => 'TF',
            'ATG' => 'AG',
            'AUS' => 'AU',
            'AUT' => 'AT',
            'AZE' => 'AZ',
            'BDI' => 'BI',
            'BEL' => 'BE',
            'BEN' => 'BJ',
            'BFA' => 'BF',
            'BGD' => 'BD',
            'BGR' => 'BG',
            'BHR' => 'BH',
            'BHS' => 'BS',
            'BIH' => 'BA',
            'BLR' => 'BY',
            'BLZ' => 'BZ',
            'BMU' => 'BM',
            'BOL' => 'BO',
            'BRA' => 'BR',
            'BRB' => 'BB',
            'BRN' => 'BN',
            'BTN' => 'BT',
            'BVT' => 'BV',
            'BWA' => 'BW',
            'CAF' => 'CF',
            'CAN' => 'CA',
            'CCK' => 'CC',
            'CHE' => 'CH',
            'CHL' => 'CL',
            'CHN' => 'CN',
            'CIV' => 'CI',
            'CMR' => 'CM',
            'COD' => 'CD',
            'COG' => 'CG',
            'COK' => 'CK',
            'COL' => 'CO',
            'COM' => 'KM',
            'CPV' => 'CV',
            'CRI' => 'CR',
            'CUB' => 'CU',
            'CXR' => 'CX',
            'CYM' => 'KY',
            'CYP' => 'CY',
            'CZE' => 'CZ',
            'DEU' => 'DE',
            'DJI' => 'DJ',
            'DMA' => 'DM',
            'DNK' => 'DK',
            'DOM' => 'DO',
            'DZA' => 'DZ',
            'ECU' => 'EC',
            'EGY' => 'EG',
            'ENG' => 'GB',
            'ERI' => 'ER',
            'ESH' => 'EH',
            'ESP' => 'ES',
            'EST' => 'EE',
            'ETH' => 'ET',
            'FIN' => 'FI',
            'FJI' => 'FJ',
            'FLK' => 'FK',
            'FRA' => 'FR',
            'FRO' => 'FO',
            'FSM' => 'FM',
            'GAB' => 'GA',
            'GEO' => 'GE',
            'GHA' => 'GH',
            'GIB' => 'GI',
            'GIN' => 'GN',
            'GLP' => 'GP',
            'GMB' => 'GM',
            'GNB' => 'GW',
            'GNQ' => 'GQ',
            'GRC' => 'GR',
            'GRD' => 'GD',
            'GRL' => 'GL',
            'GTM' => 'GT',
            'GUF' => 'GF',
            'GUM' => 'GU',
            'GUY' => 'GY',
            'HKG' => 'HK',
            'HMD' => 'HM',
            'HND' => 'HN',
            'HRV' => 'HR',
            'HTI' => 'HT',
            'HUN' => 'HU',
            'IDN' => 'ID',
            'IND' => 'IN',
            'IOT' => 'IO',
            'IRL' => 'IE',
            'IRN' => 'IR',
            'IRQ' => 'IQ',
            'ISL' => 'IS',
            'ISR' => 'IL',
            'ITA' => 'IT',
            'JAM' => 'JM',
            'JOR' => 'JO',
            'JPN' => 'JP',
            'KAZ' => 'KZ',
            'KEN' => 'KE',
            'KGZ' => 'KG',
            'KHM' => 'KH',
            'KIR' => 'KI',
            'KNA' => 'KN',
            'KOR' => 'KO',
            'KWT' => 'KW',
            'LAO' => 'LA',
            'LBN' => 'LB',
            'LBR' => 'LR',
            'LBY' => 'LY',
            'LCA' => 'LC',
            'LIE' => 'LI',
            'LKA' => 'LK',
            'LSO' => 'LS',
            'LTU' => 'LT',
            'LUX' => 'LU',
            'LVA' => 'LV',
            'MAC' => 'MO',
            'MAR' => 'MA',
            'MCO' => 'MC',
            'MDA' => 'MD',
            'MDG' => 'MG',
            'MDV' => 'MV',
            'MEX' => 'MX',
            'MHL' => 'MH',
            'MKD' => 'MK',
            'MLI' => 'ML',
            'MLT' => 'MT',
            'MMR' => 'MM',
            'MNG' => 'MN',
            'MNP' => 'MP',
            'MNT' => 'ME',
            'MOZ' => 'MZ',
            'MRT' => 'MR',
            'MSR' => 'MS',
            'MTQ' => 'MQ',
            'MUS' => 'MU',
            'MWI' => 'MW',
            'MYS' => 'MY',
            'MYT' => 'YT',
            'NAM' => 'NA',
            'NCL' => 'NC',
            'NER' => 'NE',
            'NFK' => 'NF',
            'NGA' => 'NG',
            'NIC' => 'NI',
            'NIR' => 'GB',
            'NIU' => 'NU',
            'NLD' => 'NL',
            'NOR' => 'NO',
            'NPL' => 'NP',
            'NRU' => 'NR',
            'NZL' => 'NZ',
            'OMN' => 'OM',
            'PAK' => 'PK',
            'PAN' => 'PA',
            'PCN' => 'PN',
            'PER' => 'PE',
            'PHL' => 'PH',
            'PLW' => 'PW',
            'PNG' => 'PG',
            'POL' => 'PL',
            'PRI' => 'PR',
            'PRK' => 'KP',
            'PRT' => 'PT',
            'PRY' => 'PY',
            'PSE' => 'PS',
            'PYF' => 'PF',
            'QAT' => 'QA',
            'REU' => 'RE',
            'ROM' => 'RO',
            'RUS' => 'RU',
            'RWA' => 'RW',
            'SAU' => 'SA',
            'SCT' => 'GB',
            'SDN' => 'SD',
            'SEN' => 'SN',
            'SER' => 'RS',
            'SGP' => 'SG',
            'SGS' => 'GS',
            'SHN' => 'SH',
            'SJM' => 'SJ',
            'SLB' => 'SB',
            'SLE' => 'SL',
            'SLV' => 'SV',
            'SMR' => 'SM',
            'SOM' => 'SO',
            'SPM' => 'PM',
            'STP' => 'ST',
            'SUR' => 'SR',
            'SVK' => 'SK',
            'SVN' => 'SI',
            'SWE' => 'SE',
            'SWZ' => 'SZ',
            'SYC' => 'SC',
            'SYR' => 'SY',
            'TCA' => 'TC',
            'TCD' => 'TD',
            'TGO' => 'TG',
            'THA' => 'TH',
            'TJK' => 'TJ',
            'TKL' => 'TK',
            'TKM' => 'TM',
            'TLS' => 'TL',
            'TON' => 'TO',
            'TTO' => 'TT',
            'TUN' => 'TN',
            'TUR' => 'TR',
            'TUV' => 'TV',
            'TWN' => 'TW',
            'TZA' => 'TZ',
            'UGA' => 'UG',
            'UKR' => 'UA',
            'UMI' => 'UM',
            'URY' => 'UY',
            'USA' => 'US',
            'UZB' => 'UZ',
            'VAT' => 'VA',
            'VCT' => 'VC',
            'VEN' => 'VE',
            'VGB' => 'VG',
            'VIR' => 'VI',
            'VNM' => 'VN',
            'VUT' => 'VU',
            'WLF' => 'WF',
            'WLS' => 'GB',
            'WSM' => 'WS',
            'YEM' => 'YE',
            'ZAF' => 'ZA',
            'ZMB' => 'ZM',
            'ZWE' => 'ZW',
        ];
    }

    /**
     * Returns the translated country name based on the given two letter country code.
     */
    private function mapTwoLetterToName(string $twoLetterCode): string
    {
        $threeLetterCode = array_search($twoLetterCode, $this->iso3166(), true);
        $threeLetterCode = $threeLetterCode ?: '???';

        return $this->getAllCountries()[$threeLetterCode];
    }
}
