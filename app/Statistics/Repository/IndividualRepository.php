<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\IndividualListModule;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleListInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Statistics\Google\ChartAge;
use Fisharebest\Webtrees\Statistics\Google\ChartBirth;
use Fisharebest\Webtrees\Statistics\Google\ChartCommonGiven;
use Fisharebest\Webtrees\Statistics\Google\ChartCommonSurname;
use Fisharebest\Webtrees\Statistics\Google\ChartDeath;
use Fisharebest\Webtrees\Statistics\Google\ChartFamilyWithSources;
use Fisharebest\Webtrees\Statistics\Google\ChartIndividualWithSources;
use Fisharebest\Webtrees\Statistics\Google\ChartMortality;
use Fisharebest\Webtrees\Statistics\Google\ChartSex;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\IndividualRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Service\CenturyService;
use Fisharebest\Webtrees\Statistics\Service\ColorService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use stdClass;

use function array_key_exists;
use function array_keys;
use function array_reverse;
use function array_shift;
use function array_slice;
use function array_walk;
use function arsort;
use function e;
use function explode;
use function implode;
use function preg_match;
use function uksort;
use function view;

/**
 * A repository providing methods for individual related statistics.
 */
class IndividualRepository implements IndividualRepositoryInterface
{
    private CenturyService $century_service;

    private ColorService $color_service;

    private Tree $tree;

    /**
     * @param CenturyService $century_service
     * @param ColorService $color_service
     * @param Tree         $tree
     */
    public function __construct(CenturyService $century_service, ColorService $color_service, Tree $tree)
    {
        $this->century_service = $century_service;
        $this->color_service   = $color_service;
        $this->tree            = $tree;
    }

    /**
     * Find common given names.
     *
     * @param string $sex
     * @param string $type
     * @param bool   $show_tot
     * @param int    $threshold
     * @param int    $maxtoshow
     *
     * @return string|array<int>
     */
    private function commonGivenQuery(string $sex, string $type, bool $show_tot, int $threshold, int $maxtoshow)
    {
        $query = DB::table('name')
            ->join('individuals', static function (JoinClause $join): void {
                $join
                    ->on('i_file', '=', 'n_file')
                    ->on('i_id', '=', 'n_id');
            })
            ->where('n_file', '=', $this->tree->id())
            ->where('n_type', '<>', '_MARNM')
            ->where('n_givn', '<>', Individual::PRAENOMEN_NESCIO)
            ->where(new Expression('LENGTH(n_givn)'), '>', 1);

        switch ($sex) {
            case 'M':
            case 'F':
            case 'U':
                $query->where('i_sex', '=', $sex);
                break;

            case 'B':
            default:
                $query->where('i_sex', '<>', 'U');
                break;
        }

        $rows = $query
            ->groupBy(['n_givn'])
            ->pluck(new Expression('COUNT(distinct n_id) AS count'), 'n_givn');

        $nameList = [];

        foreach ($rows as $n_givn => $count) {
            // Split “John Thomas” into “John” and “Thomas” and count against both totals
            foreach (explode(' ', (string) $n_givn) as $given) {
                // Exclude initials and particles.
                if (preg_match('/^([A-Z]|[a-z]{1,3})$/', $given) !== 1) {
                    if (array_key_exists($given, $nameList)) {
                        $nameList[$given] += (int) $count;
                    } else {
                        $nameList[$given] = (int) $count;
                    }
                }
            }
        }
        arsort($nameList);
        $nameList = array_slice($nameList, 0, $maxtoshow);

        foreach ($nameList as $given => $total) {
            if ($total < $threshold) {
                unset($nameList[$given]);
            }
        }

        switch ($type) {
            case 'chart':
                return $nameList;

            case 'table':
                return view('lists/given-names-table', [
                    'given_names' => $nameList,
                    'order'       => [[1, 'desc']],
                ]);

            case 'list':
                return view('lists/given-names-list', [
                    'given_names' => $nameList,
                    'show_totals' => $show_tot,
                ]);

            case 'nolist':
            default:
                array_walk($nameList, static function (string &$value, string $key) use ($show_tot): void {
                    if ($show_tot) {
                        $value = '<bdi>' . e($key) . '</bdi> (' . I18N::number((int) $value) . ')';
                    } else {
                        $value = '<bdi>' . e($key) . '</bdi>';
                    }
                });

                return implode(I18N::$list_separator, $nameList);
        }
    }

    /**
     * Find common give names.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGiven(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('B', 'nolist', false, $threshold, $maxtoshow);
    }

    /**
     * Find common give names.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenTotals(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('B', 'nolist', true, $threshold, $maxtoshow);
    }

    /**
     * Find common give names.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenList(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('B', 'list', false, $threshold, $maxtoshow);
    }

    /**
     * Find common give names.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenListTotals(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('B', 'list', true, $threshold, $maxtoshow);
    }

    /**
     * Find common give names.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenTable(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('B', 'table', false, $threshold, $maxtoshow);
    }

    /**
     * Find common give names of females.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemale(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('F', 'nolist', false, $threshold, $maxtoshow);
    }

    /**
     * Find common give names of females.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleTotals(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('F', 'nolist', true, $threshold, $maxtoshow);
    }

    /**
     * Find common give names of females.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleList(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('F', 'list', false, $threshold, $maxtoshow);
    }

    /**
     * Find common give names of females.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleListTotals(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('F', 'list', true, $threshold, $maxtoshow);
    }

    /**
     * Find common give names of females.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleTable(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('F', 'table', false, $threshold, $maxtoshow);
    }

    /**
     * Find common give names of males.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenMale(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('M', 'nolist', false, $threshold, $maxtoshow);
    }

    /**
     * Find common give names of males.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleTotals(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('M', 'nolist', true, $threshold, $maxtoshow);
    }

    /**
     * Find common give names of males.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleList(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('M', 'list', false, $threshold, $maxtoshow);
    }

    /**
     * Find common give names of males.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleListTotals(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('M', 'list', true, $threshold, $maxtoshow);
    }

    /**
     * Find common give names of males.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleTable(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('M', 'table', false, $threshold, $maxtoshow);
    }

    /**
     * Find common give names of unknown sexes.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknown(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('U', 'nolist', false, $threshold, $maxtoshow);
    }

    /**
     * Find common give names of unknown sexes.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownTotals(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('U', 'nolist', true, $threshold, $maxtoshow);
    }

    /**
     * Find common give names of unknown sexes.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownList(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('U', 'list', false, $threshold, $maxtoshow);
    }

    /**
     * Find common give names of unknown sexes.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownListTotals(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('U', 'list', true, $threshold, $maxtoshow);
    }

    /**
     * Find common give names of unknown sexes.
     *
     * @param int $threshold
     * @param int $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownTable(int $threshold = 1, int $maxtoshow = 10): string
    {
        return $this->commonGivenQuery('U', 'table', false, $threshold, $maxtoshow);
    }

    /**
     * Count the number of distinct given names (or the number of occurrences of specific given names).
     *
     * @param array<string> ...$params
     *
     * @return string
     */
    public function totalGivennames(...$params): string
    {
        $query = DB::table('name')
            ->where('n_file', '=', $this->tree->id());

        if ($params === []) {
            // Count number of distinct given names.
            $query
                ->distinct()
                ->where('n_givn', '<>', Individual::PRAENOMEN_NESCIO)
                ->whereNotNull('n_givn');
        } else {
            // Count number of occurrences of specific given names.
            $query->whereIn('n_givn', $params);
        }

        $count = $query->count('n_givn');

        return I18N::number($count);
    }

    /**
     * Count the number of distinct surnames (or the number of occurrences of specific surnames).
     *
     * @param array<string> ...$params
     *
     * @return string
     */
    public function totalSurnames(...$params): string
    {
        $query = DB::table('name')
            ->where('n_file', '=', $this->tree->id());

        if ($params === []) {
            // Count number of distinct surnames
            $query->distinct()
                ->whereNotNull('n_surn');
        } else {
            // Count number of occurrences of specific surnames.
            $query->whereIn('n_surn', $params);
        }

        $count = $query->count('n_surn');

        return I18N::number($count);
    }

    /**
     * @param int $number_of_surnames
     * @param int $threshold
     *
     * @return array<array<int>>
     */
    private function topSurnames(int $number_of_surnames, int $threshold): array
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
            ->take($number_of_surnames)
            ->get()
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
                ->get()
                ->pluck('count', 'n_surn')
                ->map(static fn (string $count): int => (int) $count)
                ->all();
        }

        return $surnames;
    }

    /**
     * Find common surnames.
     *
     * @return string
     */
    public function getCommonSurname(): string
    {
        $top_surname = $this->topSurnames(1, 0);

        return implode(', ', array_keys(array_shift($top_surname) ?? []));
    }

    /**
     * Find common surnames.
     *
     * @param string $type
     * @param bool   $show_tot
     * @param int    $threshold
     * @param int    $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    private function commonSurnamesQuery(
        string $type,
        bool $show_tot,
        int $threshold,
        int $number_of_surnames,
        string $sorting
    ): string {
        $surnames = $this->topSurnames($number_of_surnames, $threshold);

        switch ($sorting) {
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

        // find a module providing individual lists
        $module_service = Registry::container()->get(ModuleService::class);

        $module = $module_service
            ->findByComponent(ModuleListInterface::class, $this->tree, Auth::user())
            ->first(static fn (ModuleInterface $module): bool => $module instanceof IndividualListModule);

        if ($type === 'list') {
            return view('lists/surnames-bullet-list', [
                'surnames' => $surnames,
                'module'   => $module,
                'totals'   => $show_tot,
                'tree'     => $this->tree,
            ]);
        }

        return view('lists/surnames-compact-list', [
            'surnames' => $surnames,
            'module'   => $module,
            'totals'   => $show_tot,
            'tree'     => $this->tree,
        ]);
    }

    /**
     * Find common surnames.
     *
     * @param int    $threshold
     * @param int    $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    public function commonSurnames(
        int $threshold = 1,
        int $number_of_surnames = 10,
        string $sorting = 'alpha'
    ): string {
        return $this->commonSurnamesQuery('nolist', false, $threshold, $number_of_surnames, $sorting);
    }

    /**
     * Find common surnames.
     *
     * @param int    $threshold
     * @param int    $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    public function commonSurnamesTotals(
        int $threshold = 1,
        int $number_of_surnames = 10,
        string $sorting = 'count'
    ): string {
        return $this->commonSurnamesQuery('nolist', true, $threshold, $number_of_surnames, $sorting);
    }

    /**
     * Find common surnames.
     *
     * @param int    $threshold
     * @param int    $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    public function commonSurnamesList(
        int $threshold = 1,
        int $number_of_surnames = 10,
        string $sorting = 'alpha'
    ): string {
        return $this->commonSurnamesQuery('list', false, $threshold, $number_of_surnames, $sorting);
    }

    /**
     * Find common surnames.
     *
     * @param int    $threshold
     * @param int    $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    public function commonSurnamesListTotals(
        int $threshold = 1,
        int $number_of_surnames = 10,
        string $sorting = 'count'
    ): string {
        return $this->commonSurnamesQuery('list', true, $threshold, $number_of_surnames, $sorting);
    }

    /**
     * Get a count of births by month.
     *
     * @param int  $year1
     * @param int  $year2
     *
     * @return Builder
     */
    public function statsBirthQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        $query = DB::table('dates')
            ->select(['d_month', new Expression('COUNT(*) AS total')])
            ->where('d_file', '=', $this->tree->id())
            ->where('d_fact', '=', 'BIRT')
            ->whereIn('d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->groupBy(['d_month']);

        if ($year1 >= 0 && $year2 >= 0) {
            $query->whereBetween('d_year', [$year1, $year2]);
        }

        return $query;
    }

    /**
     * Get a count of births by month.
     *
     * @param int  $year1
     * @param int  $year2
     *
     * @return Builder
     */
    public function statsBirthBySexQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->statsBirthQuery($year1, $year2)
            ->select(['d_month', 'i_sex', new Expression('COUNT(*) AS total')])
            ->join('individuals', static function (JoinClause $join): void {
                $join
                    ->on('i_id', '=', 'd_gid')
                    ->on('i_file', '=', 'd_file');
            })
            ->groupBy(['i_sex']);
    }

    /**
     * General query on births.
     *
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsBirth(string|null $color_from = null, string|null $color_to = null): string
    {
        return (new ChartBirth($this->century_service, $this->color_service, $this->tree))
            ->chartBirth($color_from, $color_to);
    }

    /**
     * Get a list of death dates.
     *
     * @param int  $year1
     * @param int  $year2
     *
     * @return Builder
     */
    public function statsDeathQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        $query = DB::table('dates')
            ->select(['d_month', new Expression('COUNT(*) AS total')])
            ->where('d_file', '=', $this->tree->id())
            ->where('d_fact', '=', 'DEAT')
            ->whereIn('d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->groupBy(['d_month']);

        if ($year1 >= 0 && $year2 >= 0) {
            $query->whereBetween('d_year', [$year1, $year2]);
        }

        return $query;
    }

    /**
     * Get a list of death dates.
     *
     * @param int  $year1
     * @param int  $year2
     *
     * @return Builder
     */
    public function statsDeathBySexQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->statsDeathQuery($year1, $year2)
            ->select(['d_month', 'i_sex', new Expression('COUNT(*) AS total')])
            ->join('individuals', static function (JoinClause $join): void {
                $join
                    ->on('i_id', '=', 'd_gid')
                    ->on('i_file', '=', 'd_file');
            })
            ->groupBy(['i_sex']);
    }

    /**
     * General query on deaths.
     *
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsDeath(string|null $color_from = null, string|null $color_to = null): string
    {
        return (new ChartDeath($this->century_service, $this->color_service, $this->tree))
            ->chartDeath($color_from, $color_to);
    }

    /**
     * General query on ages.
     *
     * @param string $related
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     *
     * @return array<stdClass>
     */
    public function statsAgeQuery(string $related = 'BIRT', string $sex = 'BOTH', int $year1 = -1, int $year2 = -1): array
    {
        $prefix = DB::connection()->getTablePrefix();

        $query = $this->birthAndDeathQuery($sex);

        if ($year1 >= 0 && $year2 >= 0) {
            $query
                ->whereIn('birth.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
                ->whereIn('death.d_type', ['@#DGREGORIAN@', '@#DJULIAN@']);

            if ($related === 'BIRT') {
                $query->whereBetween('birth.d_year', [$year1, $year2]);
            } elseif ($related === 'DEAT') {
                $query->whereBetween('death.d_year', [$year1, $year2]);
            }
        }

        return $query
            ->select([new Expression($prefix . 'death.d_julianday2 - ' . $prefix . 'birth.d_julianday1 AS days')])
            ->orderBy('days', 'desc')
            ->get()
            ->all();
    }

    /**
     * General query on ages.
     *
     * @return string
     */
    public function statsAge(): string
    {
        return (new ChartAge($this->century_service, $this->tree))->chartAge();
    }

    /**
     * Lifespan
     *
     * @param string $type
     * @param string $sex
     *
     * @return string
     */
    private function longlifeQuery(string $type, string $sex): string
    {
        $prefix = DB::connection()->getTablePrefix();

        $row = $this->birthAndDeathQuery($sex)
            ->orderBy('days', 'desc')
            ->select(['individuals.*', new Expression($prefix . 'death.d_julianday2 - ' . $prefix . 'birth.d_julianday1 AS days')])
            ->first();

        if ($row === null) {
            return '';
        }

        $individual = Registry::individualFactory()->mapper($this->tree)($row);

        if ($type !== 'age' && !$individual->canShow()) {
            return I18N::translate('This information is private and cannot be shown.');
        }

        switch ($type) {
            default:
            case 'full':
                return $individual->formatList();

            case 'age':
                return I18N::number((int) ($row->days / 365.25));

            case 'name':
                return '<a href="' . e($individual->url()) . '">' . $individual->fullName() . '</a>';
        }
    }

    /**
     * Find the longest lived individual.
     *
     * @return string
     */
    public function longestLife(): string
    {
        return $this->longlifeQuery('full', 'BOTH');
    }

    /**
     * Find the age of the longest lived individual.
     *
     * @return string
     */
    public function longestLifeAge(): string
    {
        return $this->longlifeQuery('age', 'BOTH');
    }

    /**
     * Find the name of the longest lived individual.
     *
     * @return string
     */
    public function longestLifeName(): string
    {
        return $this->longlifeQuery('name', 'BOTH');
    }

    /**
     * Find the longest lived female.
     *
     * @return string
     */
    public function longestLifeFemale(): string
    {
        return $this->longlifeQuery('full', 'F');
    }

    /**
     * Find the age of the longest lived female.
     *
     * @return string
     */
    public function longestLifeFemaleAge(): string
    {
        return $this->longlifeQuery('age', 'F');
    }

    /**
     * Find the name of the longest lived female.
     *
     * @return string
     */
    public function longestLifeFemaleName(): string
    {
        return $this->longlifeQuery('name', 'F');
    }

    /**
     * Find the longest lived male.
     *
     * @return string
     */
    public function longestLifeMale(): string
    {
        return $this->longlifeQuery('full', 'M');
    }

    /**
     * Find the age of the longest lived male.
     *
     * @return string
     */
    public function longestLifeMaleAge(): string
    {
        return $this->longlifeQuery('age', 'M');
    }

    /**
     * Find the name of the longest lived male.
     *
     * @return string
     */
    public function longestLifeMaleName(): string
    {
        return $this->longlifeQuery('name', 'M');
    }

    /**
     * Returns the calculated age the time of event.
     *
     * @param int $days The age from the database record
     *
     * @return string
     */
    private function calculateAge(int $days): string
    {
        if ($days < 31) {
            return I18N::plural('%s day', '%s days', $days, I18N::number($days));
        }

        if ($days < 365) {
            $months = (int) ($days / 30.5);
            return I18N::plural('%s month', '%s months', $months, I18N::number($months));
        }

        $years = (int) ($days / 365.25);

        return I18N::plural('%s year', '%s years', $years, I18N::number($years));
    }

    /**
     * Find the oldest individuals.
     *
     * @param string $sex
     * @param int    $total
     *
     * @return array<array<string,mixed>>
     */
    private function topTenOldestQuery(string $sex, int $total): array
    {
        $prefix = DB::connection()->getTablePrefix();

        $rows = $this->birthAndDeathQuery($sex)
            ->groupBy(['i_id', 'i_file'])
            ->orderBy('days', 'desc')
            ->select(['individuals.*', new Expression('MAX(' . $prefix . 'death.d_julianday2 - ' . $prefix . 'birth.d_julianday1) AS days')])
            ->take($total)
            ->get();

        $top10 = [];
        foreach ($rows as $row) {
            $individual = Registry::individualFactory()->mapper($this->tree)($row);

            if ($individual->canShow()) {
                $top10[] = [
                    'person' => $individual,
                    'age'    => $this->calculateAge((int) $row->days),
                ];
            }
        }

        return $top10;
    }

    /**
     * Find the oldest individuals.
     *
     * @param int $total
     *
     * @return string
     */
    public function topTenOldest(int $total = 10): string
    {
        $records = $this->topTenOldestQuery('BOTH', $total);

        return view('statistics/individuals/top10-nolist', [
            'records' => $records,
        ]);
    }

    /**
     * Find the oldest living individuals.
     *
     * @param int $total
     *
     * @return string
     */
    public function topTenOldestList(int $total = 10): string
    {
        $records = $this->topTenOldestQuery('BOTH', $total);

        return view('statistics/individuals/top10-list', [
            'records' => $records,
        ]);
    }

    /**
     * Find the oldest females.
     *
     * @param int $total
     *
     * @return string
     */
    public function topTenOldestFemale(int $total = 10): string
    {
        $records = $this->topTenOldestQuery('F', $total);

        return view('statistics/individuals/top10-nolist', [
            'records' => $records,
        ]);
    }

    /**
     * Find the oldest living females.
     *
     * @param int $total
     *
     * @return string
     */
    public function topTenOldestFemaleList(int $total = 10): string
    {
        $records = $this->topTenOldestQuery('F', $total);

        return view('statistics/individuals/top10-list', [
            'records' => $records,
        ]);
    }

    /**
     * Find the longest lived males.
     *
     * @param int $total
     *
     * @return string
     */
    public function topTenOldestMale(int $total = 10): string
    {
        $records = $this->topTenOldestQuery('M', $total);

        return view('statistics/individuals/top10-nolist', [
            'records' => $records,
        ]);
    }

    /**
     * Find the longest lived males.
     *
     * @param int $total
     *
     * @return string
     */
    public function topTenOldestMaleList(int $total = 10): string
    {
        $records = $this->topTenOldestQuery('M', $total);

        return view('statistics/individuals/top10-list', [
            'records' => $records,
        ]);
    }

    /**
     * Find the oldest living individuals.
     *
     * @param string $sex   "M", "F" or "BOTH"
     * @param int    $total
     *
     * @return array<array<string,mixed>>
     */
    private function topTenOldestAliveQuery(string $sex, int $total): array
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

        if ($sex === 'F' || $sex === 'M') {
            $query->where('i_sex', '=', $sex);
        }

        return $query
            ->groupBy(['i_id', 'i_file'])
            ->orderBy(new Expression('MIN(d_julianday1)'))
            ->select(['individuals.*'])
            ->take($total)
            ->get()
            ->map(Registry::individualFactory()->mapper($this->tree))
            ->filter(GedcomRecord::accessFilter())
            ->map(fn(Individual $individual): array => [
                'person' => $individual,
                'age'    => $this->calculateAge(Registry::timestampFactory()->now()->julianDay() - $individual->getBirthDate()->minimumJulianDay()),
            ])
            ->all();
    }

    /**
     * Find the oldest living individuals.
     *
     * @param int $total
     *
     * @return string
     */
    public function topTenOldestAlive(int $total = 10): string
    {
        if (!Auth::isMember($this->tree)) {
            return I18N::translate('This information is private and cannot be shown.');
        }

        $records = $this->topTenOldestAliveQuery('BOTH', $total);

        return view('statistics/individuals/top10-nolist', [
            'records' => $records,
        ]);
    }

    /**
     * Find the oldest living individuals.
     *
     * @param int $total
     *
     * @return string
     */
    public function topTenOldestListAlive(int $total = 10): string
    {
        if (!Auth::isMember($this->tree)) {
            return I18N::translate('This information is private and cannot be shown.');
        }

        $records = $this->topTenOldestAliveQuery('BOTH', $total);

        return view('statistics/individuals/top10-list', [
            'records' => $records,
        ]);
    }

    /**
     * Find the oldest living females.
     *
     * @param int $total
     *
     * @return string
     */
    public function topTenOldestFemaleAlive(int $total = 10): string
    {
        if (!Auth::isMember($this->tree)) {
            return I18N::translate('This information is private and cannot be shown.');
        }

        $records = $this->topTenOldestAliveQuery('F', $total);

        return view('statistics/individuals/top10-nolist', [
            'records' => $records,
        ]);
    }

    /**
     * Find the oldest living females.
     *
     * @param int $total
     *
     * @return string
     */
    public function topTenOldestFemaleListAlive(int $total = 10): string
    {
        if (!Auth::isMember($this->tree)) {
            return I18N::translate('This information is private and cannot be shown.');
        }

        $records = $this->topTenOldestAliveQuery('F', $total);

        return view('statistics/individuals/top10-list', [
            'records' => $records,
        ]);
    }

    /**
     * Find the longest lived living males.
     *
     * @param int $total
     *
     * @return string
     */
    public function topTenOldestMaleAlive(int $total = 10): string
    {
        if (!Auth::isMember($this->tree)) {
            return I18N::translate('This information is private and cannot be shown.');
        }

        $records = $this->topTenOldestAliveQuery('M', $total);

        return view('statistics/individuals/top10-nolist', [
            'records' => $records,
        ]);
    }

    /**
     * Find the longest lived living males.
     *
     * @param int $total
     *
     * @return string
     */
    public function topTenOldestMaleListAlive(int $total = 10): string
    {
        if (!Auth::isMember($this->tree)) {
            return I18N::translate('This information is private and cannot be shown.');
        }

        $records = $this->topTenOldestAliveQuery('M', $total);

        return view('statistics/individuals/top10-list', [
            'records' => $records,
        ]);
    }

    /**
     * Find the average lifespan.
     *
     * @param string $sex        "M", "F" or "BOTH"
     * @param bool   $show_years
     *
     * @return string
     */
    private function averageLifespanQuery(string $sex, bool $show_years): string
    {
        $prefix = DB::connection()->getTablePrefix();

        $days = (int) $this->birthAndDeathQuery($sex)
            ->select([new Expression('AVG(' . $prefix . 'death.d_julianday2 - ' . $prefix . 'birth.d_julianday1) AS days')])
            ->value('days');

        if ($show_years) {
            return $this->calculateAge($days);
        }

        return I18N::number((int) ($days / 365.25));
    }

    /**
     * Find the average lifespan.
     *
     * @param bool $show_years
     *
     * @return string
     */
    public function averageLifespan(bool $show_years): string
    {
        return $this->averageLifespanQuery('BOTH', $show_years);
    }

    /**
     * Find the average lifespan of females.
     *
     * @param bool $show_years
     *
     * @return string
     */
    public function averageLifespanFemale(bool $show_years): string
    {
        return $this->averageLifespanQuery('F', $show_years);
    }

    /**
     * Find the average male lifespan.
     *
     * @param bool $show_years
     *
     * @return string
     */
    public function averageLifespanMale(bool $show_years): string
    {
        return $this->averageLifespanQuery('M', $show_years);
    }

    /**
     * Convert totals into percentages.
     *
     * @param int $count
     * @param int $total
     *
     * @return string
     */
    private function getPercentage(int $count, int $total): string
    {
        return $total !== 0 ? I18N::percentage($count / $total, 1) : '';
    }

    /**
     * Returns how many individuals exist in the tree.
     *
     * @return int
     */
    private function totalIndividualsQuery(): int
    {
        return DB::table('individuals')
            ->where('i_file', '=', $this->tree->id())
            ->count();
    }

    /**
     * Count the number of living individuals.
     *
     * The totalLiving/totalDeceased queries assume that every dead person will
     * have a DEAT record. It will not include individuals who were born more
     * than MAX_ALIVE_AGE years ago, and who have no DEAT record.
     * A good reason to run the “Add missing DEAT records” batch-update!
     *
     * @return int
     */
    private function totalLivingQuery(): int
    {
        $query = DB::table('individuals')
            ->where('i_file', '=', $this->tree->id());

        foreach (Gedcom::DEATH_EVENTS as $death_event) {
            $query->where('i_gedcom', 'NOT LIKE', "%\n1 " . $death_event . '%');
        }

        return $query->count();
    }

    /**
     * Count the number of dead individuals.
     *
     * @return int
     */
    private function totalDeceasedQuery(): int
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

    /**
     * Returns the total count of a specific sex.
     *
     * @param string $sex The sex to query
     *
     * @return int
     */
    private function getTotalSexQuery(string $sex): int
    {
        return DB::table('individuals')
            ->where('i_file', '=', $this->tree->id())
            ->where('i_sex', '=', $sex)
            ->count();
    }

    /**
     * Returns the total number of males.
     *
     * @return int
     */
    private function totalSexMalesQuery(): int
    {
        return $this->getTotalSexQuery('M');
    }

    /**
     * Returns the total number of females.
     *
     * @return int
     */
    private function totalSexFemalesQuery(): int
    {
        return $this->getTotalSexQuery('F');
    }

    /**
     * Returns the total number of individuals with unknown sex.
     *
     * @return int
     */
    private function totalSexUnknownQuery(): int
    {
        return $this->getTotalSexQuery('U');
    }

    /**
     * Count the total families.
     *
     * @return int
     */
    private function totalFamiliesQuery(): int
    {
        return DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->count();
    }

    /**
     * How many individuals have one or more sources.
     *
     * @return int
     */
    private function totalIndisWithSourcesQuery(): int
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

    /**
     * Count the families with source records.
     *
     * @return int
     */
    private function totalFamsWithSourcesQuery(): int
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
     * Count the number of repositories.
     *
     * @return int
     */
    private function totalRepositoriesQuery(): int
    {
        return DB::table('other')
            ->where('o_file', '=', $this->tree->id())
            ->where('o_type', '=', 'REPO')
            ->count();
    }

    /**
     * Count the total number of sources.
     *
     * @return int
     */
    private function totalSourcesQuery(): int
    {
        return DB::table('sources')
            ->where('s_file', '=', $this->tree->id())
            ->count();
    }

    /**
     * Count the number of notes.
     *
     * @return int
     */
    private function totalNotesQuery(): int
    {
        return DB::table('other')
            ->where('o_file', '=', $this->tree->id())
            ->where('o_type', '=', 'NOTE')
            ->count();
    }

    /**
     * Count the total media.
     *
     * @return int
     */
    private function totalMediaQuery(): int
    {
        return DB::table('media')
            ->where('m_file', '=', $this->tree->id())
            ->count();
    }

    /**
     * Returns the total number of records.
     *
     * @return int
     */
    private function totalRecordsQuery(): int
    {
        return $this->totalIndividualsQuery()
            + $this->totalFamiliesQuery()
            + $this->totalMediaQuery()
            + $this->totalNotesQuery()
            + $this->totalRepositoriesQuery()
            + $this->totalSourcesQuery();
    }

    /**
     * @return string
     */
    public function totalRecords(): string
    {
        return I18N::number($this->totalRecordsQuery());
    }

    /**
     * @return string
     */
    public function totalIndividuals(): string
    {
        return I18N::number($this->totalIndividualsQuery());
    }

    /**
     * Count the number of living individuals.
     *
     * @return string
     */
    public function totalLiving(): string
    {
        return I18N::number($this->totalLivingQuery());
    }

    /**
     * Count the number of dead individuals.
     *
     * @return string
     */
    public function totalDeceased(): string
    {
        return I18N::number($this->totalDeceasedQuery());
    }

    /**
     * @return string
     */
    public function totalSexMales(): string
    {
        return I18N::number($this->totalSexMalesQuery());
    }

    /**
     * @return string
     */
    public function totalSexFemales(): string
    {
        return I18N::number($this->totalSexFemalesQuery());
    }

    /**
     * @return string
     */
    public function totalSexUnknown(): string
    {
        return I18N::number($this->totalSexUnknownQuery());
    }

    /**
     * @return string
     */
    public function totalFamilies(): string
    {
        return I18N::number($this->totalFamiliesQuery());
    }

    /**
     * How many individuals have one or more sources.
     *
     * @return string
     */
    public function totalIndisWithSources(): string
    {
        return I18N::number($this->totalIndisWithSourcesQuery());
    }

    /**
     * Count the families with with source records.
     *
     * @return string
     */
    public function totalFamsWithSources(): string
    {
        return I18N::number($this->totalFamsWithSourcesQuery());
    }

    /**
     * @return string
     */
    public function totalRepositories(): string
    {
        return I18N::number($this->totalRepositoriesQuery());
    }

    /**
     * @return string
     */
    public function totalSources(): string
    {
        return I18N::number($this->totalSourcesQuery());
    }

    /**
     * @return string
     */
    public function totalNotes(): string
    {
        return I18N::number($this->totalNotesQuery());
    }

    /**
     * @return string
     */
    public function totalIndividualsPercentage(): string
    {
        return $this->getPercentage(
            $this->totalIndividualsQuery(),
            $this->totalRecordsQuery()
        );
    }

    /**
     * @return string
     */
    public function totalIndisWithSourcesPercentage(): string
    {
        return $this->getPercentage(
            $this->totalIndisWithSourcesQuery(),
            $this->totalIndividualsQuery()
        );
    }

    /**
     * @return string
     */
    public function totalFamiliesPercentage(): string
    {
        return $this->getPercentage(
            $this->totalFamiliesQuery(),
            $this->totalRecordsQuery()
        );
    }

    /**
     * @return string
     */
    public function totalFamsWithSourcesPercentage(): string
    {
        return $this->getPercentage(
            $this->totalFamsWithSourcesQuery(),
            $this->totalFamiliesQuery()
        );
    }

    /**
     * @return string
     */
    public function totalRepositoriesPercentage(): string
    {
        return $this->getPercentage(
            $this->totalRepositoriesQuery(),
            $this->totalRecordsQuery()
        );
    }

    /**
     * @return string
     */
    public function totalSourcesPercentage(): string
    {
        return $this->getPercentage(
            $this->totalSourcesQuery(),
            $this->totalRecordsQuery()
        );
    }

    /**
     * @return string
     */
    public function totalNotesPercentage(): string
    {
        return $this->getPercentage(
            $this->totalNotesQuery(),
            $this->totalRecordsQuery()
        );
    }

    /**
     * @return string
     */
    public function totalLivingPercentage(): string
    {
        return $this->getPercentage(
            $this->totalLivingQuery(),
            $this->totalIndividualsQuery()
        );
    }

    /**
     * @return string
     */
    public function totalDeceasedPercentage(): string
    {
        return $this->getPercentage(
            $this->totalDeceasedQuery(),
            $this->totalIndividualsQuery()
        );
    }

    /**
     * @return string
     */
    public function totalSexMalesPercentage(): string
    {
        return $this->getPercentage(
            $this->totalSexMalesQuery(),
            $this->totalIndividualsQuery()
        );
    }

    /**
     * @return string
     */
    public function totalSexFemalesPercentage(): string
    {
        return $this->getPercentage(
            $this->totalSexFemalesQuery(),
            $this->totalIndividualsQuery()
        );
    }

    /**
     * @return string
     */
    public function totalSexUnknownPercentage(): string
    {
        return $this->getPercentage(
            $this->totalSexUnknownQuery(),
            $this->totalIndividualsQuery()
        );
    }

    /**
     * Create a chart of common given names.
     *
     * @param string|null $color_from
     * @param string|null $color_to
     * @param int         $maxtoshow
     *
     * @return string
     */
    public function chartCommonGiven(
        string|null $color_from = null,
        string|null $color_to = null,
        int $maxtoshow = 7
    ): string {
        $tot_indi = $this->totalIndividualsQuery();
        $given    = $this->commonGivenQuery('B', 'chart', false, 1, $maxtoshow);

        if ($given === []) {
            return I18N::translate('This information is not available.');
        }

        return (new ChartCommonGiven($this->color_service))
            ->chartCommonGiven($tot_indi, $given, $color_from, $color_to);
    }

    /**
     * Create a chart of common surnames.
     *
     * @param string|null $color_from
     * @param string|null $color_to
     * @param int         $number_of_surnames
     *
     * @return string
     */
    public function chartCommonSurnames(
        string|null $color_from = null,
        string|null $color_to = null,
        int $number_of_surnames = 10
    ): string {
        $tot_indi     = $this->totalIndividualsQuery();
        $all_surnames = $this->topSurnames($number_of_surnames, 0);

        if ($all_surnames === []) {
            return I18N::translate('This information is not available.');
        }

        $surname_tradition = Registry::surnameTraditionFactory()
            ->make($this->tree->getPreference('SURNAME_TRADITION'));

        return (new ChartCommonSurname($this->color_service, $surname_tradition))
            ->chartCommonSurnames($tot_indi, $all_surnames, $color_from, $color_to);
    }

    /**
     * Create a chart showing mortality.
     *
     * @param string|null $color_living
     * @param string|null $color_dead
     *
     * @return string
     */
    public function chartMortality(string|null $color_living = null, string|null $color_dead = null): string
    {
        $tot_l = $this->totalLivingQuery();
        $tot_d = $this->totalDeceasedQuery();

        return (new ChartMortality($this->color_service))
            ->chartMortality($tot_l, $tot_d, $color_living, $color_dead);
    }

    /**
     * Create a chart showing individuals with/without sources.
     *
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartIndisWithSources(
        string|null $color_from = null,
        string $color_to = null
    ): string {
        $tot_indi        = $this->totalIndividualsQuery();
        $tot_indi_source = $this->totalIndisWithSourcesQuery();

        return (new ChartIndividualWithSources($this->color_service))
            ->chartIndisWithSources($tot_indi, $tot_indi_source, $color_from, $color_to);
    }

    /**
     * Create a chart of individuals with/without sources.
     *
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartFamsWithSources(
        string|null $color_from = null,
        string $color_to = null
    ): string {
        $tot_fam        = $this->totalFamiliesQuery();
        $tot_fam_source = $this->totalFamsWithSourcesQuery();

        return (new ChartFamilyWithSources($this->color_service))
            ->chartFamsWithSources($tot_fam, $tot_fam_source, $color_from, $color_to);
    }

    /**
     * @param string|null $color_female
     * @param string|null $color_male
     * @param string|null $color_unknown
     *
     * @return string
     */
    public function chartSex(
        string|null $color_female = null,
        string|null $color_male = null,
        string $color_unknown = null
    ): string {
        $tot_m = $this->totalSexMalesQuery();
        $tot_f = $this->totalSexFemalesQuery();
        $tot_u = $this->totalSexUnknownQuery();

        return (new ChartSex())
            ->chartSex($tot_m, $tot_f, $tot_u, $color_female, $color_male, $color_unknown);
    }

    /**
     * Query individuals, with their births and deaths.
     *
     * @param string $sex
     *
     * @return Builder
     */
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

        if ($sex === 'M' || $sex === 'F') {
            $query->where('i_sex', '=', $sex);
        }

        return $query;
    }
}
