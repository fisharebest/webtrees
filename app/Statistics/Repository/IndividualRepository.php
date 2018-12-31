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

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Google\ChartAge;
use Fisharebest\Webtrees\Statistics\Google\ChartBirth;
use Fisharebest\Webtrees\Statistics\Google\ChartCommonGiven;
use Fisharebest\Webtrees\Statistics\Google\ChartCommonSurname;
use Fisharebest\Webtrees\Statistics\Google\ChartDeath;
use Fisharebest\Webtrees\Statistics\Google\ChartIndividual;
use Fisharebest\Webtrees\Statistics\Google\ChartMortality;
use Fisharebest\Webtrees\Statistics\Helper\Percentage;
use Fisharebest\Webtrees\Statistics\Helper\Sql;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;

/**
 *
 */
class IndividualRepository
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
     * How many individuals exist in the tree.
     *
     * @return int
     */
    public function totalIndividualsQuery(): int
    {
        return DB::table('individuals')
            ->where('i_file', '=', $this->tree->id())
            ->count();
    }

    /**
     * How many individuals exist in the tree.
     *
     * @return string
     */
    public function totalIndividuals(): string
    {
        return I18N::number($this->totalIndividualsQuery());
    }

    /**
     * Show the total individuals as a percentage.
     *
     * @return string
     */
    public function totalIndividualsPercentage(): string
    {
        $percentageHelper = new Percentage($this->tree);
        return $percentageHelper->getPercentage($this->totalIndividualsQuery(), 'all');
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
     * @return string|int[]
     */
    private function commonGivenQuery(string $sex, string $type, bool $show_tot, int $threshold, int $maxtoshow)
    {
        switch ($sex) {
            case 'M':
                $sex_sql = "i_sex='M'";
                break;
            case 'F':
                $sex_sql = "i_sex='F'";
                break;
            case 'U':
                $sex_sql = "i_sex='U'";
                break;
            case 'B':
            default:
                $sex_sql = "i_sex<>'U'";
                break;
        }

        $ged_id = $this->tree->id();

        $rows     = Database::prepare("SELECT n_givn, COUNT(*) AS num FROM `##name` JOIN `##individuals` ON (n_id=i_id AND n_file=i_file) WHERE n_file={$ged_id} AND n_type<>'_MARNM' AND n_givn NOT IN ('@P.N.', '') AND LENGTH(n_givn)>1 AND {$sex_sql} GROUP BY n_id, n_givn")
            ->fetchAll();

        $nameList = [];
        foreach ($rows as $row) {
            $row->num = (int) $row->num;

            // Split “John Thomas” into “John” and “Thomas” and count against both totals
            foreach (explode(' ', $row->n_givn) as $given) {
                // Exclude initials and particles.
                if (!preg_match('/^([A-Z]|[a-z]{1,3})$/', $given)) {
                    if (array_key_exists($given, $nameList)) {
                        $nameList[$given] += (int) $row->num;
                    } else {
                        $nameList[$given] = (int) $row->num;
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
                ]);

            case 'list':
                return view('lists/given-names-list', [
                    'given_names' => $nameList,
                    'show_totals' => $show_tot,
                ]);

            case 'nolist':
            default:
                array_walk($nameList, function (int &$value, string $key) use ($show_tot): void {
                    if ($show_tot) {
                        $value = '<span dir="auto">' . e($key);
                    } else {
                        $value = '<span dir="auto">' . e($key) . ' (' . I18N::number($value) . ')';
                    }
                });

                return implode(I18N::$list_separator, $nameList);
        }
    }

    /**
     * Find common give names.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGiven(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('B', 'nolist', false, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Create a chart of common given names.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     * @param string      $maxtoshow
     *
     * @return string
     */
    public function chartCommonGiven(
        string $size = null,
        string $color_from = null,
        string $color_to = null,
        string $maxtoshow = '7'
    ): string {
        $tot_indi = $this->totalIndividualsQuery();
        $given    = $this->commonGivenQuery('B', 'chart', false, 1, (int) $maxtoshow);

        return (new ChartCommonGiven())
            ->chartCommonGiven($tot_indi, $given, $size, $color_from, $color_to);
    }

    /**
     * Find common give names.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('B', 'nolist', true, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('B', 'list', false, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('B', 'list', true, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('B', 'table', false, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names of females.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemale(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('F', 'nolist', false, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names of females.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('F', 'nolist', true, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names of females.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('F', 'list', false, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names of females.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('F', 'list', true, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names of females.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('F', 'table', false, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names of males.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMale(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('M', 'nolist', false, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names of males.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('M', 'nolist', true, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names of males.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('M', 'list', false, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names of males.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('M', 'list', true, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names of males.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('M', 'table', false, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names of unknown sexes.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknown(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('U', 'nolist', false, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names of unknown sexes.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('U', 'nolist', true, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names of unknown sexes.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('U', 'list', false, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names of unknown sexes.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('U', 'list', true, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Find common give names of unknown sexes.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->commonGivenQuery('U', 'table', false, (int) $threshold, (int) $maxtoshow);
    }

    /**
     * Count the number of distinct given names, or count the number of
     * occurrences of a specific name or names.
     *
     * @param string ...$params
     *
     * @return string
     */
    public function totalGivennames(...$params): string
    {
        if ($params) {
            $qs       = implode(',', array_fill(0, count($params), '?'));
            $params[] = $this->tree->id();
            $total    = (int) Database::prepare(
                "SELECT COUNT( n_givn) FROM `##name` WHERE n_givn IN ({$qs}) AND n_file=?"
            )->execute(
                $params
            )->fetchOne();
        } else {
            $total = (int) Database::prepare(
                "SELECT COUNT(DISTINCT n_givn) FROM `##name` WHERE n_givn IS NOT NULL AND n_file=?"
            )->execute([
                $this->tree->id(),
            ])->fetchOne();
        }

        return I18N::number($total);
    }

    /**
     * Count the surnames.
     *
     * @param string ...$params
     *
     * @return string
     */
    public function totalSurnames(...$params): string
    {
        if ($params) {
            $opt      = 'IN (' . implode(',', array_fill(0, count($params), '?')) . ')';
            $distinct = '';
        } else {
            $opt      = "IS NOT NULL";
            $distinct = 'DISTINCT';
        }
        $params[] = $this->tree->id();

        $total = (int) Database::prepare(
            "SELECT COUNT({$distinct} n_surn COLLATE '" . I18N::collation() . "')" .
            " FROM `##name`" .
            " WHERE n_surn COLLATE '" . I18N::collation() . "' {$opt} AND n_file=?"
        )->execute(
            $params
        )->fetchOne();

        return I18N::number($total);
    }

    /**
     * @param int $number_of_surnames
     * @param int $threshold
     *
     * @return array
     */
    private function topSurnames(int $number_of_surnames, int $threshold): array
    {
        // Use the count of base surnames.
        $top_surnames = Database::prepare(
            "SELECT n_surn FROM `##name`" .
            " WHERE n_file = :tree_id AND n_type != '_MARNM' AND n_surn NOT IN ('@N.N.', '')" .
            " GROUP BY n_surn" .
            " ORDER BY COUNT(n_surn) DESC" .
            " LIMIT :limit"
        )->execute([
            'tree_id' => $this->tree->id(),
            'limit'   => $number_of_surnames,
        ])->fetchOneColumn();

        $surnames = [];
        foreach ($top_surnames as $top_surname) {
            $variants = Database::prepare(
                "SELECT n_surname COLLATE utf8_bin, COUNT(*) FROM `##name` WHERE n_file = :tree_id AND n_surn COLLATE :collate = :surname GROUP BY 1"
            )->execute([
                'collate' => I18N::collation(),
                'surname' => $top_surname,
                'tree_id' => $this->tree->id(),
            ])->fetchAssoc();

            if (array_sum($variants) > $threshold) {
                $surnames[$top_surname] = $variants;
            }
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
        return implode(', ', array_keys(array_shift($top_surname)) ?? []);
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
        $type,
        $show_tot,
        int $threshold,
        int $number_of_surnames,
        string $sorting
    ): string {
        $surnames = $this->topSurnames($number_of_surnames, $threshold);

        switch ($sorting) {
            default:
            case 'alpha':
                uksort($surnames, [I18N::class, 'strcasecmp']);
                break;
            case 'count':
                break;
            case 'rcount':
                $surnames = array_reverse($surnames, true);
                break;
        }

        return FunctionsPrintLists::surnameList(
            $surnames,
            ($type === 'list' ? 1 : 2),
            $show_tot,
            'individual-list',
            $this->tree
        );
    }

    /**
     * Find common surnames.
     *
     * @param string $threshold
     * @param string $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    public function commonSurnames(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'alpha'
    ): string {
        return $this->commonSurnamesQuery('nolist', false, (int) $threshold, (int) $number_of_surnames, $sorting);
    }

    /**
     * Find common surnames.
     *
     * @param string $threshold
     * @param string $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    public function commonSurnamesTotals(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'rcount'
    ): string {
        return $this->commonSurnamesQuery('nolist', true, (int) $threshold, (int) $number_of_surnames, $sorting);
    }

    /**
     * Find common surnames.
     *
     * @param string $threshold
     * @param string $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    public function commonSurnamesList(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'alpha'
    ): string {
        return $this->commonSurnamesQuery('list', false, (int) $threshold, (int) $number_of_surnames, $sorting);
    }

    /**
     * Find common surnames.
     *
     * @param string $threshold
     * @param string $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    public function commonSurnamesListTotals(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'rcount'
    ): string {
        return $this->commonSurnamesQuery('list', true, (int) $threshold, (int) $number_of_surnames, $sorting);
    }

    /**
     * Create a chart of common surnames.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     * @param string      $number_of_surnames
     *
     * @return string
     */
    public function chartCommonSurnames(
        string $size = null,
        string $color_from = null,
        string $color_to = null,
        string $number_of_surnames = '10'
    ): string {
        $all_surnames = $this->topSurnames((int) $number_of_surnames, 0);

        return (new ChartCommonSurname($this->tree))
            ->chartCommonSurnames($all_surnames, $size, $color_from, $color_to);
    }

    /**
     * How many individuals have one or more sources.
     *
     * @return int
     */
    private function totalIndisWithSourcesQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(DISTINCT i_id)" .
            " FROM `##individuals` JOIN `##link` ON i_id = l_from AND i_file = l_file" .
            " WHERE l_file = :tree_id AND l_type = 'SOUR'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
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
     * Create a chart showing individuals with/without sources.
     *
     * @param string|null $size        // Optional parameter, set from tag
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartIndisWithSources(
        string $size = null,
        string $color_from = null,
        string $color_to = null
    ): string {
        $tot_indi        = $this->totalIndividualsQuery();
        $tot_indi_source = $this->totalIndisWithSourcesQuery();

        return (new ChartIndividual())
            ->chartIndisWithSources($tot_indi, $tot_indi_source, $size, $color_from, $color_to);

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
        return (int) Database::prepare(
            "SELECT COUNT(*) FROM `##individuals` WHERE i_file = :tree_id AND i_gedcom NOT REGEXP '\\n1 ("
            . implode('|', Gedcom::DEATH_EVENTS) . ")'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
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
     * Count the number of living individuals.
     *
     * @return string
     */
    public function totalLivingPercentage(): string
    {
        $percentageHelper = new Percentage($this->tree);
        return $percentageHelper->getPercentage($this->totalLivingQuery(), 'individual');
    }

    /**
     * Count the number of dead individuals.
     *
     * @return int
     */
    private function totalDeceasedQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(*) FROM `##individuals` WHERE i_file = :tree_id AND i_gedcom REGEXP '\\n1 ("
            . implode('|', Gedcom::DEATH_EVENTS) . ")'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
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
     * Count the number of dead individuals.
     *
     * @return string
     */
    public function totalDeceasedPercentage(): string
    {
        $percentageHelper = new Percentage($this->tree);
        return $percentageHelper->getPercentage($this->totalDeceasedQuery(), 'individual');
    }

    /**
     * Create a chart showing mortality.
     *
     * @param string|null $size
     * @param string|null $color_living
     * @param string|null $color_dead
     *
     * @return string
     */
    public function chartMortality(string $size = null, string $color_living = null, string $color_dead = null): string
    {
        $tot_l = $this->totalLivingQuery();
        $tot_d = $this->totalDeceasedQuery();
        $per_l = $this->totalLivingPercentage();
        $per_d = $this->totalDeceasedPercentage();

        return (new ChartMortality())
            ->chartMortality($tot_l, $tot_d, $per_l, $per_d, $size, $color_living, $color_dead);
    }

    /**
     * Create a chart of birth places.
     *
     * @param bool $sex
     * @param int  $year1
     * @param int  $year2
     *
     * @return array
     */
    public function statsBirthQuery($sex = false, $year1 = -1, $year2 = -1): array
    {
        if ($sex) {
            $sql =
                "SELECT d_month, i_sex, COUNT(*) AS total FROM `##dates` " .
                "JOIN `##individuals` ON d_file = i_file AND d_gid = i_id " .
                "WHERE " .
                "d_file={$this->tree->id()} AND " .
                "d_fact='BIRT' AND " .
                "d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
        } else {
            $sql =
                "SELECT d_month, COUNT(*) AS total FROM `##dates` " .
                "WHERE " .
                "d_file={$this->tree->id()} AND " .
                "d_fact='BIRT' AND " .
                "d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
        }

        if ($year1 >= 0 && $year2 >= 0) {
            $sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
        }

        $sql .= " GROUP BY d_month";

        if ($sex) {
            $sql .= ", i_sex";
        }

        return $this->runSql($sql);
    }

    /**
     * General query on births.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsBirth(string $size = null, string $color_from = null, string $color_to = null): string
    {
        return (new ChartBirth($this->tree))
            ->chartBirth($size, $color_from, $color_to);
    }

    /**
     * Create a chart of death places.
     *
     * @param bool $sex
     * @param int  $year1
     * @param int  $year2
     *
     * @return array
     */
    public function statsDeathQuery($sex = false, $year1 = -1, $year2 = -1): array
    {
        if ($sex) {
            $sql =
                "SELECT d_month, i_sex, COUNT(*) AS total FROM `##dates` " .
                "JOIN `##individuals` ON d_file = i_file AND d_gid = i_id " .
                "WHERE " .
                "d_file={$this->tree->id()} AND " .
                "d_fact='DEAT' AND " .
                "d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
        } else {
            $sql =
                "SELECT d_month, COUNT(*) AS total FROM `##dates` " .
                "WHERE " .
                "d_file={$this->tree->id()} AND " .
                "d_fact='DEAT' AND " .
                "d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
        }

        if ($year1 >= 0 && $year2 >= 0) {
            $sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
        }

        $sql .= " GROUP BY d_month";

        if ($sex) {
            $sql .= ", i_sex";
        }

        return $this->runSql($sql);
    }

    /**
     * General query on deaths.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsDeath(string $size = null, string $color_from = null, string $color_to = null): string
    {
        return (new ChartDeath($this->tree))
            ->chartDeath($size, $color_from, $color_to);
    }

    /**
     * General query on ages.
     *
     * @param string $related
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     *
     * @return array|string
     */
    public function statsAgeQuery($related = 'BIRT', $sex = 'BOTH', $year1 = -1, $year2 = -1)
    {
        $sex_search = '';
        $years      = '';

        if ($sex === 'F') {
            $sex_search = " AND i_sex='F'";
        } elseif ($sex === 'M') {
            $sex_search = " AND i_sex='M'";
        }

        if ($year1 >= 0 && $year2 >= 0) {
            if ($related === 'BIRT') {
                $years = " AND birth.d_year BETWEEN '{$year1}' AND '{$year2}'";
            } elseif ($related === 'DEAT') {
                $years = " AND death.d_year BETWEEN '{$year1}' AND '{$year2}'";

            }
        }

        $rows = $this->runSql(
            "SELECT" .
            " death.d_julianday2-birth.d_julianday1 AS age" .
            " FROM" .
            " `##dates` AS death," .
            " `##dates` AS birth," .
            " `##individuals` AS indi" .
            " WHERE" .
            " indi.i_id=birth.d_gid AND" .
            " birth.d_gid=death.d_gid AND" .
            " death.d_file={$this->tree->id()} AND" .
            " birth.d_file=death.d_file AND" .
            " birth.d_file=indi.i_file AND" .
            " birth.d_fact='BIRT' AND" .
            " death.d_fact='DEAT' AND" .
            " birth.d_julianday1 <> 0 AND" .
            " birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND" .
            " death.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND" .
            " death.d_julianday1>birth.d_julianday2" .
            $years .
            $sex_search .
            " ORDER BY age DESC"
        );

        return $rows;
    }

    /**
     * General query on ages.
     *
     * @param string $size
     *
     * @return string
     */
    public function statsAge(string $size = '230x250'): string
    {
        return (new ChartAge($this->tree))->chartAge($size);
    }
}
