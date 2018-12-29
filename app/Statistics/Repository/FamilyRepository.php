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
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Statistics\AgeDifferenceSiblings;
use Fisharebest\Webtrees\Statistics\Google\ChartChildren;
use Fisharebest\Webtrees\Statistics\Google\ChartFamily;
use Fisharebest\Webtrees\Statistics\Google\ChartMarriageAge;
use Fisharebest\Webtrees\Statistics\Helper\Percentage;
use Fisharebest\Webtrees\Statistics\Helper\Sql;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;

/**
 *
 */
class FamilyRepository
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
     * General query on family.
     *
     * @param string $type
     *
     * @return string
     */
    private function familyQuery(string $type): string
    {
        $rows = $this->runSql(
            " SELECT f_numchil AS tot, f_id AS id" .
            " FROM `##families`" .
            " WHERE" .
            " f_file={$this->tree->id()}" .
            " AND f_numchil = (" .
            "  SELECT max( f_numchil )" .
            "  FROM `##families`" .
            "  WHERE f_file ={$this->tree->id()}" .
            " )" .
            " LIMIT 1"
        );

        if (!isset($rows[0])) {
            return '';
        }

        $row    = $rows[0];
        $family = Family::getInstance($row->id, $this->tree);

        if (!$family) {
            return '';
        }

        switch ($type) {
            default:
            case 'full':
                if ($family->canShow()) {
                    $result = $family->formatList();
                } else {
                    $result = I18N::translate('This information is private and cannot be shown.');
                }
                break;
            case 'size':
                $result = I18N::number((int) $row->tot);
                break;
            case 'name':
                $result = '<a href="' . e($family->url()) . '">' . $family->getFullName() . '</a>';
                break;
        }

        return $result;
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
     * Count the total families.
     *
     * @return int
     */
    public function totalFamiliesQuery(): int
    {
        return DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->count();
    }

    /**
     * Count the total families.
     *
     * @return string
     */
    public function totalFamilies(): string
    {
        return I18N::number($this->totalFamiliesQuery());
    }

    /**
     * Show the total families as a percentage.
     *
     * @return string
     */
    public function totalFamiliesPercentage(): string
    {
        $percentageHelper = new Percentage($this->tree);
        return $percentageHelper->getPercentage($this->totalFamiliesQuery(), 'all');
    }

    /**
     * Count the families with source records.
     *
     * @return int
     */
    private function totalFamsWithSourcesQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(DISTINCT f_id)" .
            " FROM `##families` JOIN `##link` ON f_id = l_from AND f_file = l_file" .
            " WHERE l_file = :tree_id AND l_type = 'SOUR'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
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
     * Create a chart of individuals with/without sources.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartFamsWithSources(string $size = null, string $color_from = null, string $color_to = null): string
    {
        $tot_fam        = $this->totalFamiliesQuery();
        $tot_fam_source = $this->totalFamsWithSourcesQuery();

        return (new ChartFamily($this->tree))
            ->chartFamsWithSources($tot_fam, $tot_fam_source, $size, $color_from, $color_to);
    }

    /**
     * Find the family with the most children.
     *
     * @return string
     */
    public function largestFamily(): string
    {
        return $this->familyQuery('full');
    }

    /**
     * Find the number of children in the largest family.
     *
     * @return string
     */
    public function largestFamilySize(): string
    {
        return $this->familyQuery('size');
    }

    /**
     * Find the family with the most children.
     *
     * @return string
     */
    public function largestFamilyName(): string
    {
        return $this->familyQuery('name');
    }

    /**
     * Find the couple with the most grandchildren.
     *
     * @param int $total
     *
     * @return array
     */
    private function topTenGrandFamilyQuery(int $total): array
    {
        $rows = $this->runSql(
            "SELECT COUNT(*) AS tot, f_id AS id" .
            " FROM `##families`" .
            " JOIN `##link` AS children ON children.l_file = {$this->tree->id()}" .
            " JOIN `##link` AS mchildren ON mchildren.l_file = {$this->tree->id()}" .
            " JOIN `##link` AS gchildren ON gchildren.l_file = {$this->tree->id()}" .
            " WHERE" .
            " f_file={$this->tree->id()} AND" .
            " children.l_from=f_id AND" .
            " children.l_type='CHIL' AND" .
            " children.l_to=mchildren.l_from AND" .
            " mchildren.l_type='FAMS' AND" .
            " mchildren.l_to=gchildren.l_from AND" .
            " gchildren.l_type='CHIL'" .
            " GROUP BY id" .
            " ORDER BY tot DESC" .
            " LIMIT " . $total
        );

        if (!isset($rows[0])) {
            return [];
        }

        $top10 = [];

        foreach ($rows as $row) {
            $family = Family::getInstance($row->id, $this->tree);

            if ($family && $family->canShow()) {
                $total = (int) $row->tot;

                $top10[] = [
                    'family' => $family,
                    'count'  => $total,
                ];
            }
        }

        // TODO
//        if (I18N::direction() === 'rtl') {
//            $top10 = str_replace([
//                '[',
//                ']',
//                '(',
//                ')',
//                '+',
//            ], [
//                '&rlm;[',
//                '&rlm;]',
//                '&rlm;(',
//                '&rlm;)',
//                '&rlm;+',
//            ], $top10);
//        }

        return $top10;
    }

    /**
     * Find the couple with the most grandchildren.
     *
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestGrandFamily(string $total = '10'): string
    {
        $records = $this->topTenGrandFamilyQuery((int) $total);

        return view(
            'statistics/families/top10-nolist-grand',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the couple with the most grandchildren.
     *
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestGrandFamilyList(string $total = '10'): string
    {
        $records = $this->topTenGrandFamilyQuery((int) $total);

        return view(
            'statistics/families/top10-list-grand',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the families with no children.
     *
     * @return int
     */
    private function noChildrenFamiliesQuery(): int
    {
        $rows = $this->runSql(
            " SELECT COUNT(*) AS tot" .
            " FROM  `##families`" .
            " WHERE f_numchil = 0 AND f_file = {$this->tree->id()}"
        );

        return (int) $rows[0]->tot;
    }

    /**
     * Find the families with no children.
     *
     * @return string
     */
    public function noChildrenFamilies(): string
    {
        return I18N::number($this->noChildrenFamiliesQuery());
    }

    /**
     * Find the families with no children.
     *
     * @param string $type
     *
     * @return string
     */
    public function noChildrenFamiliesList($type = 'list'): string
    {
        $rows = $this->runSql(
            " SELECT f_id AS family" .
            " FROM `##families` AS fam" .
            " WHERE f_numchil = 0 AND fam.f_file = {$this->tree->id()}"
        );

        if (!isset($rows[0])) {
            return '';
        }

        $top10 = [];
        foreach ($rows as $row) {
            $family = Family::getInstance($row->family, $this->tree);
            if ($family->canShow()) {
                if ($type === 'list') {
                    $top10[] = '<li><a href="' . e($family->url()) . '">' . $family->getFullName() . '</a></li>';
                } else {
                    $top10[] = '<a href="' . e($family->url()) . '">' . $family->getFullName() . '</a>';
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
     * Create a chart of children with no families.
     *
     * @param string $size
     * @param string $year1
     * @param string $year2
     *
     * @return string
     */
    public function chartNoChildrenFamilies(string $size = '220x200', string $year1 = '-1', string $year2 = '-1'): string
    {
        $no_child_fam = $this->noChildrenFamiliesQuery();

        return (new ChartFamily($this->tree))
            ->chartNoChildrenFamilies($no_child_fam, $size, $year1, $year2);
    }

    /**
     * Find the ages between siblings.
     *
     * @param string $type
     * @param int    $total
     * @param bool   $one   Include each family only once if true
     *
     * @return array
     */
    private function ageBetweenSiblingsQuery(string $type, int $total, bool $one): array
    {
        $ageDiff = new AgeDifferenceSiblings($this->tree);
        return $ageDiff->query($type, $total, $one);
    }

    /**
     * Find the names of siblings with the widest age gap.
     *
     * @param string $total
     * @param string $one
     *
     * @return string
     */
    public function topAgeBetweenSiblingsName(string $total = '10', string $one = ''): string
    {
        // TODO
//        return $this->ageBetweenSiblingsQuery('name', (int) $total, (bool) $one);
        return 'topAgeBetweenSiblingsName';
    }

    /**
     * Find the widest age gap between siblings.
     *
     * @param string $total
     * @param string $one
     *
     * @return string
     */
    public function topAgeBetweenSiblings(string $total = '10', string $one = ''): string
    {
        // TODO
//        return $this->ageBetweenSiblingsQuery('age', (int) $total, (bool) $one);
        return 'topAgeBetweenSiblings';
    }

    /**
     * Find the name of siblings with the widest age gap.
     *
     * @param string $total
     * @param string $one
     *
     * @return string
     */
    public function topAgeBetweenSiblingsFullName(string $total = '10', string $one = ''): string
    {
        $record = $this->ageBetweenSiblingsQuery('nolist', (int) $total, (bool) $one);

        return view(
            'statistics/families/top10-nolist-age',
            [
                'record' => $record,
            ]
        );
    }

    /**
     * Find the siblings with the widest age gaps.
     *
     * @param string $total
     * @param string $one
     *
     * @return string
     */
    public function topAgeBetweenSiblingsList(string $total = '10', string $one = ''): string
    {
        $records = $this->ageBetweenSiblingsQuery('list', (int) $total, (bool) $one);

        return view(
            'statistics/families/top10-list-age',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * General query on familes/children.
     *
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     *
     * @return \stdClass[]
     */
    public function statsChildrenQuery($sex = 'BOTH', $year1 = -1, $year2 = -1): array
    {
        if ($sex == 'M') {
            $sql =
                "SELECT num, COUNT(*) AS total FROM " .
                "(SELECT count(i_sex) AS num FROM `##link` " .
                "LEFT OUTER JOIN `##individuals` " .
                "ON l_from=i_id AND l_file=i_file AND i_sex='M' AND l_type='FAMC' " .
                "JOIN `##families` ON f_file=l_file AND f_id=l_to WHERE f_file={$this->tree->id()} GROUP BY l_to" .
                ") boys" .
                " GROUP BY num" .
                " ORDER BY num";
        } elseif ($sex == 'F') {
            $sql =
                "SELECT num, COUNT(*) AS total FROM " .
                "(SELECT count(i_sex) AS num FROM `##link` " .
                "LEFT OUTER JOIN `##individuals` " .
                "ON l_from=i_id AND l_file=i_file AND i_sex='F' AND l_type='FAMC' " .
                "JOIN `##families` ON f_file=l_file AND f_id=l_to WHERE f_file={$this->tree->id()} GROUP BY l_to" .
                ") girls" .
                " GROUP BY num" .
                " ORDER BY num";
        } else {
            $sql = "SELECT f_numchil, COUNT(*) AS total FROM `##families` ";

            if ($year1 >= 0 && $year2 >= 0) {
                $sql .=
                    "AS fam LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->id()}"
                    . " WHERE"
                    . " married.d_gid = fam.f_id AND"
                    . " fam.f_file = {$this->tree->id()} AND"
                    . " married.d_fact = 'MARR' AND"
                    . " married.d_year BETWEEN '{$year1}' AND '{$year2}'";
            } else {
                $sql .= "WHERE f_file={$this->tree->id()}";
            }

            $sql .= ' GROUP BY f_numchil';
        }

        return $this->runSql($sql);
    }

    /**
     * Genearl query on families/children.
     *
     * @param string $size
     *
     * @return string
     */
    public function statsChildren(string $size = '220x200'): string
    {
        return (new ChartChildren($this->tree))
            ->chartChildren($size);
    }

    /**
     * Count the total children.
     *
     * @return string
     */
    public function totalChildren(): string
    {
        $total = (int) Database::prepare(
            "SELECT SUM(f_numchil) FROM `##families` WHERE f_file = :tree_id"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();

        return I18N::number($total);
    }

    /**
     * Find the average number of children in families.
     *
     * @return string
     */
    public function averageChildren(): string
    {
        $average = (float) Database::prepare(
            "SELECT AVG(f_numchil) AS tot FROM `##families` WHERE f_file = :tree_id"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();

        return I18N::number($average, 2);
    }

    /**
     * General query on families.
     *
     * @param int $total
     *
     * @return array
     */
    private function topTenFamilyQuery(int $total): array
    {
        $rows = $this->runSql(
            "SELECT f_numchil AS tot, f_id AS id" .
            " FROM `##families`" .
            " WHERE" .
            " f_file={$this->tree->id()}" .
            " ORDER BY tot DESC" .
            " LIMIT " . $total
        );

        if (empty($rows)) {
            return [];
        }

        $top10 = [];
        foreach ($rows as $row) {
            $family = Family::getInstance($row->id, $this->tree);

            if ($family && $family->canShow()) {
                $top10[] = [
                    'family' => $family,
                    'count'  => (int) $row->tot,
                ];
            }
        }

        // TODO
//        if (I18N::direction() === 'rtl') {
//            $top10 = str_replace([
//                '[',
//                ']',
//                '(',
//                ')',
//                '+',
//            ], [
//                '&rlm;[',
//                '&rlm;]',
//                '&rlm;(',
//                '&rlm;)',
//                '&rlm;+',
//            ], $top10);
//        }

        return $top10;
    }

    /**
     * The the families with the most children.
     *
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestFamily(string $total = '10'): string
    {
        $records = $this->topTenFamilyQuery((int) $total);

        return view(
            'statistics/families/top10-nolist',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the families with the most children.
     *
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestFamilyList(string $total = '10'): string
    {
        $records = $this->topTenFamilyQuery((int) $total);

        return view(
            'statistics/families/top10-list',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Create a chart of the largest families.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     * @param string      $total
     *
     * @return string
     */
    public function chartLargestFamilies(
        string $size       = null,
        string $color_from = null,
        string $color_to   = null,
        string $total      = '10'
    ): string {
        return (new ChartFamily($this->tree))
            ->chartLargestFamilies($size, $color_from, $color_to, $total);
    }

    /**
     * Find the month in the year of the birth of the first child.
     *
     * @param bool $sex
     *
     * @return \stdClass[]
     */
    public function monthFirstChildQuery(bool $sex = false): array
    {
        if ($sex) {
            $sql_sex1 = ', i_sex';
            $sql_sex2 = " JOIN `##individuals` AS child ON child1.d_file = i_file AND child1.d_gid = child.i_id ";
        } else {
            $sql_sex1 = '';
            $sql_sex2 = '';
        }

        $sql =
            "SELECT d_month{$sql_sex1}, COUNT(*) AS total " .
            "FROM (" .
            " SELECT family{$sql_sex1}, MIN(date) AS d_date, d_month" .
            " FROM (" .
            "  SELECT" .
            "  link1.l_from AS family," .
            "  link1.l_to AS child," .
            "  child1.d_julianday2 AS date," .
            "  child1.d_month as d_month" .
            $sql_sex1 .
            "  FROM `##link` AS link1" .
            "  LEFT JOIN `##dates` AS child1 ON child1.d_file = {$this->tree->id()}" .
            $sql_sex2 .
            "  WHERE" .
            "  link1.l_file = {$this->tree->id()} AND" .
            "  link1.l_type = 'CHIL' AND" .
            "  child1.d_gid = link1.l_to AND" .
            "  child1.d_fact = 'BIRT' AND" .
            "  child1.d_month IN ('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC')" .
            "  ORDER BY date" .
            " ) AS children" .
            " GROUP BY family, d_month{$sql_sex1}" .
            ") AS first_child " .
            "GROUP BY d_month";

        if ($sex) {
            $sql .= ', i_sex';
        }

        return $this->runSql($sql);
    }

    /**
     * Number of husbands.
     *
     * @return string
     */
    public function totalMarriedMales(): string
    {
        $n = (int) Database::prepare(
            "SELECT COUNT(DISTINCT f_husb) FROM `##families` WHERE f_file = :tree_id AND f_gedcom LIKE '%\\n1 MARR%'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();

        return I18N::number($n);
    }

    /**
     * Number of wives.
     *
     * @return string
     */
    public function totalMarriedFemales(): string
    {
        $n = (int) Database::prepare(
            "SELECT COUNT(DISTINCT f_wife) FROM `##families` WHERE f_file = :tree_id AND f_gedcom LIKE '%\\n1 MARR%'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();

        return I18N::number($n);
    }

    /**
     * General query on parents.
     *
     * @param string $type
     * @param string $age_dir
     * @param string $sex
     * @param bool   $show_years
     *
     * @return string
     */
    private function parentsQuery(string $type, string $age_dir, string $sex, bool $show_years): string
    {
        if ($sex === 'F') {
            $sex_field = 'WIFE';
        } else {
            $sex_field = 'HUSB';
        }

        if ($age_dir !== 'ASC') {
            $age_dir = 'DESC';
        }

        $rows = $this->runSql(
            " SELECT" .
            " parentfamily.l_to AS id," .
            " childbirth.d_julianday2-birth.d_julianday1 AS age" .
            " FROM `##link` AS parentfamily" .
            " JOIN `##link` AS childfamily ON childfamily.l_file = {$this->tree->id()}" .
            " JOIN `##dates` AS birth ON birth.d_file = {$this->tree->id()}" .
            " JOIN `##dates` AS childbirth ON childbirth.d_file = {$this->tree->id()}" .
            " WHERE" .
            " birth.d_gid = parentfamily.l_to AND" .
            " childfamily.l_to = childbirth.d_gid AND" .
            " childfamily.l_type = 'CHIL' AND" .
            " parentfamily.l_type = '{$sex_field}' AND" .
            " childfamily.l_from = parentfamily.l_from AND" .
            " parentfamily.l_file = {$this->tree->id()} AND" .
            " birth.d_fact = 'BIRT' AND" .
            " childbirth.d_fact = 'BIRT' AND" .
            " birth.d_julianday1 <> 0 AND" .
            " childbirth.d_julianday2 > birth.d_julianday1" .
            " ORDER BY age {$age_dir} LIMIT 1"
        );

        if (!isset($rows[0])) {
            return '';
        }

        $row = $rows[0];
        if (isset($row->id)) {
            $person = Individual::getInstance($row->id, $this->tree);
        }

        switch ($type) {
            default:
            case 'full':
                if ($person && $person->canShow()) {
                    $result = $person->formatList();
                } else {
                    $result = I18N::translate('This information is private and cannot be shown.');
                }
                break;
            case 'name':
                $result = '<a href="' . e($person->url()) . '">' . $person->getFullName() . '</a>';
                break;
            case 'age':
                $age = $row->age;
                if ($show_years) {
                    if ((int) ($age / 365.25) > 0) {
                        $age = (int) ($age / 365.25) . 'y';
                    } elseif ((int) ($age / 30.4375) > 0) {
                        $age = (int) ($age / 30.4375) . 'm';
                    } else {
                        $age .= 'd';
                    }
                    $result = FunctionsDate::getAgeAtEvent($age);
                } else {
                    $result = (string) floor($age / 365.25);
                }
                break;
        }

        return $result;
    }

    /**
     * Find the youngest mother
     *
     * @return string
     */
    public function youngestMother(): string
    {
        return $this->parentsQuery('full', 'ASC', 'F', false);
    }

    /**
     * Find the name of the youngest mother.
     *
     * @return string
     */
    public function youngestMotherName(): string
    {
        return $this->parentsQuery('name', 'ASC', 'F', false);
    }

    /**
     * Find the age of the youngest mother.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function youngestMotherAge(string $show_years = ''): string
    {
        return $this->parentsQuery('age', 'ASC', 'F', (bool) $show_years);
    }

    /**
     * Find the oldest mother.
     *
     * @return string
     */
    public function oldestMother(): string
    {
        return $this->parentsQuery('full', 'DESC', 'F', false);
    }

    /**
     * Find the name of the oldest mother.
     *
     * @return string
     */
    public function oldestMotherName(): string
    {
        return $this->parentsQuery('name', 'DESC', 'F', false);
    }

    /**
     * Find the age of the oldest mother.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function oldestMotherAge(string $show_years = ''): string
    {
        return $this->parentsQuery('age', 'DESC', 'F', (bool) $show_years);
    }

    /**
     * Find the youngest father.
     *
     * @return string
     */
    public function youngestFather(): string
    {
        return $this->parentsQuery('full', 'ASC', 'M', false);
    }

    /**
     * Find the name of the youngest father.
     *
     * @return string
     */
    public function youngestFatherName(): string
    {
        return $this->parentsQuery('name', 'ASC', 'M', false);
    }

    /**
     * Find the age of the youngest father.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function youngestFatherAge(string $show_years = ''): string
    {
        return $this->parentsQuery('age', 'ASC', 'M', (bool) $show_years);
    }

    /**
     * Find the oldest father.
     *
     * @return string
     */
    public function oldestFather(): string
    {
        return $this->parentsQuery('full', 'DESC', 'M', false);
    }

    /**
     * Find the name of the oldest father.
     *
     * @return string
     */
    public function oldestFatherName(): string
    {
        return $this->parentsQuery('name', 'DESC', 'M', false);
    }

    /**
     * Find the age of the oldest father.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function oldestFatherAge(string $show_years = ''): string
    {
        return $this->parentsQuery('age', 'DESC', 'M', (bool) $show_years);
    }

    /**
     * General query on age at marriage.
     *
     * @param string $type
     * @param string $age_dir
     * @param int    $total
     *
     * @return string
     */
    private function ageOfMarriageQuery(string $type, string $age_dir, int $total): string
    {
        if ($age_dir !== 'ASC') {
            $age_dir = 'DESC';
        }

        $hrows = $this->runSql(
            " SELECT DISTINCT fam.f_id AS family, MIN(husbdeath.d_julianday2-married.d_julianday1) AS age" .
            " FROM `##families` AS fam" .
            " LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->id()}" .
            " LEFT JOIN `##dates` AS husbdeath ON husbdeath.d_file = {$this->tree->id()}" .
            " WHERE" .
            " fam.f_file = {$this->tree->id()} AND" .
            " husbdeath.d_gid = fam.f_husb AND" .
            " husbdeath.d_fact = 'DEAT' AND" .
            " married.d_gid = fam.f_id AND" .
            " married.d_fact = 'MARR' AND" .
            " married.d_julianday1 < husbdeath.d_julianday2 AND" .
            " married.d_julianday1 <> 0" .
            " GROUP BY family" .
            " ORDER BY age {$age_dir}"
        );

        $wrows = $this->runSql(
            " SELECT DISTINCT fam.f_id AS family, MIN(wifedeath.d_julianday2-married.d_julianday1) AS age" .
            " FROM `##families` AS fam" .
            " LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->id()}" .
            " LEFT JOIN `##dates` AS wifedeath ON wifedeath.d_file = {$this->tree->id()}" .
            " WHERE" .
            " fam.f_file = {$this->tree->id()} AND" .
            " wifedeath.d_gid = fam.f_wife AND" .
            " wifedeath.d_fact = 'DEAT' AND" .
            " married.d_gid = fam.f_id AND" .
            " married.d_fact = 'MARR' AND" .
            " married.d_julianday1 < wifedeath.d_julianday2 AND" .
            " married.d_julianday1 <> 0" .
            " GROUP BY family" .
            " ORDER BY age {$age_dir}"
        );

        $drows = $this->runSql(
            " SELECT DISTINCT fam.f_id AS family, MIN(divorced.d_julianday2-married.d_julianday1) AS age" .
            " FROM `##families` AS fam" .
            " LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->id()}" .
            " LEFT JOIN `##dates` AS divorced ON divorced.d_file = {$this->tree->id()}" .
            " WHERE" .
            " fam.f_file = {$this->tree->id()} AND" .
            " married.d_gid = fam.f_id AND" .
            " married.d_fact = 'MARR' AND" .
            " divorced.d_gid = fam.f_id AND" .
            " divorced.d_fact IN ('DIV', 'ANUL', '_SEPR', '_DETS') AND" .
            " married.d_julianday1 < divorced.d_julianday2 AND" .
            " married.d_julianday1 <> 0" .
            " GROUP BY family" .
            " ORDER BY age {$age_dir}"
        );

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
        foreach ($rows as $fam => $age) {
            $family = Family::getInstance($fam, $this->tree);
            if ($type === 'name') {
                return $family->formatList();
            }
            if ((int) ($age / 365.25) > 0) {
                $age = (int) ($age / 365.25) . 'y';
            } elseif ((int) ($age / 30.4375) > 0) {
                $age = (int) ($age / 30.4375) . 'm';
            } else {
                $age .= 'd';
            }
            $age = FunctionsDate::getAgeAtEvent($age);
            if ($type === 'age') {
                return $age;
            }
            $husb = $family->getHusband();
            $wife = $family->getWife();
            if ($husb && $wife && ($husb->getAllDeathDates() && $wife->getAllDeathDates() || !$husb->isDead() || !$wife->isDead())) {
                if ($family && $family->canShow()) {
                    if ($type === 'list') {
                        $top10[] = '<li><a href="' . e($family->url()) . '">' . $family->getFullName() . '</a> (' . $age . ')' . '</li>';
                    } else {
                        $top10[] = '<a href="' . e($family->url()) . '">' . $family->getFullName() . '</a> (' . $age . ')';
                    }
                }
                if (++$i === $total) {
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
     * General query on marriage ages.
     *
     * @return string
     */
    public function topAgeOfMarriageFamily(): string
    {
        return $this->ageOfMarriageQuery('name', 'DESC', 1);
    }

    /**
     * General query on marriage ages.
     *
     * @return string
     */
    public function topAgeOfMarriage(): string
    {
        return $this->ageOfMarriageQuery('age', 'DESC', 1);
    }

    /**
     * General query on marriage ages.
     *
     * @param string $total
     *
     * @return string
     */
    public function topAgeOfMarriageFamilies(string $total = '10'): string
    {
        return $this->ageOfMarriageQuery('nolist', 'DESC', (int) $total);
    }

    /**
     * General query on marriage ages.
     *
     * @param string $total
     *
     * @return string
     */
    public function topAgeOfMarriageFamiliesList(string $total = '10'): string
    {
        return $this->ageOfMarriageQuery('list', 'DESC', (int) $total);
    }

    /**
     * General query on marriage ages.
     *
     * @return string
     */
    public function minAgeOfMarriageFamily(): string
    {
        return $this->ageOfMarriageQuery('name', 'ASC', 1);
    }

    /**
     * General query on marriage ages.
     *
     * @return string
     */
    public function minAgeOfMarriage(): string
    {
        return $this->ageOfMarriageQuery('age', 'ASC', 1);
    }

    /**
     * General query on marriage ages.
     *
     * @param string $total
     *
     * @return string
     */
    public function minAgeOfMarriageFamilies(string $total = '10'): string
    {
        return $this->ageOfMarriageQuery('nolist', 'ASC', (int) $total);
    }

    /**
     * General query on marriage ages.
     *
     * @param string $total
     *
     * @return string
     */
    public function minAgeOfMarriageFamiliesList(string $total = '10'): string
    {
        return $this->ageOfMarriageQuery('list', 'ASC', (int) $total);
    }

    /**
     * Find the ages between spouses.
     *
     * @param string $age_dir
     * @param int    $total
     *
     * @return array
     */
    private function ageBetweenSpousesQuery(string $age_dir, int $total): array
    {
        if ($age_dir === 'DESC') {
            $sql =
                "SELECT f_id AS xref, MIN(wife.d_julianday2-husb.d_julianday1) AS age" .
                " FROM `##families`" .
                " JOIN `##dates` AS wife ON wife.d_gid = f_wife AND wife.d_file = f_file" .
                " JOIN `##dates` AS husb ON husb.d_gid = f_husb AND husb.d_file = f_file" .
                " WHERE f_file = :tree_id" .
                " AND husb.d_fact = 'BIRT'" .
                " AND wife.d_fact = 'BIRT'" .
                " AND wife.d_julianday2 >= husb.d_julianday1 AND husb.d_julianday1 <> 0" .
                " GROUP BY xref" .
                " ORDER BY age DESC" .
                " LIMIT :limit";
        } else {
            $sql =
                "SELECT f_id AS xref, MIN(husb.d_julianday2-wife.d_julianday1) AS age" .
                " FROM `##families`" .
                " JOIN `##dates` AS wife ON wife.d_gid = f_wife AND wife.d_file = f_file" .
                " JOIN `##dates` AS husb ON husb.d_gid = f_husb AND husb.d_file = f_file" .
                " WHERE f_file = :tree_id" .
                " AND husb.d_fact = 'BIRT'" .
                " AND wife.d_fact = 'BIRT'" .
                " AND husb.d_julianday2 >= wife.d_julianday1 AND wife.d_julianday1 <> 0" .
                " GROUP BY xref" .
                " ORDER BY age DESC" .
                " LIMIT :limit";
        }

        $rows = Database::prepare(
            $sql
        )->execute([
            'tree_id' => $this->tree->id(),
            'limit'   => $total,
        ])->fetchAll();

        $top10 = [];

        foreach ($rows as $fam) {
            $family = Family::getInstance($fam->xref, $this->tree);

            if ($fam->age < 0) {
                break;
            }

            $age = $fam->age;

            if ((int) ($age / 365.25) > 0) {
                $age = (int) ($age / 365.25) . 'y';
            } elseif ((int) ($age / 30.4375) > 0) {
                $age = (int) ($age / 30.4375) . 'm';
            } else {
                $age .= 'd';
            }

            $age = FunctionsDate::getAgeAtEvent($age);

            if ($family->canShow()) {
                $top10[] = [
                    'family' => $family,
                    'age'    => $age,
                ];
            }
        }

        return $top10;
    }

    /**
     * Find the age between husband and wife.
     *
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesMF(string $total = '10'): string
    {
        $records = $this->ageBetweenSpousesQuery('DESC', (int) $total);

        return view(
            'statistics/families/top10-nolist-spouses',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the age between husband and wife.
     *
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesMFList(string $total = '10'): string
    {
        $records = $this->ageBetweenSpousesQuery('DESC', (int) $total);

        return view(
            'statistics/families/top10-list-spouses',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the age between wife and husband..
     *
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesFM(string $total = '10'): string
    {
        $records = $this->ageBetweenSpousesQuery('ASC', (int) $total);

        return view(
            'statistics/families/top10-nolist-spouses',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the age between wife and husband..
     *
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesFMList(string $total = '10'): string
    {
        $records = $this->ageBetweenSpousesQuery('ASC', (int) $total);

        return view(
            'statistics/families/top10-list-spouses',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * General query on ages at marriage.
     *
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     *
     * @return array
     */
    public function statsMarrAgeQuery($sex = 'M', $year1 = -1, $year2 = -1): array
    {
        if ($year1 >= 0 && $year2 >= 0) {
            $years = " married.d_year BETWEEN {$year1} AND {$year2} AND ";
        } else {
            $years = '';
        }

        $rows = $this->runSql(
            "SELECT " .
            " fam.f_id, " .
            " birth.d_gid, " .
            " married.d_julianday2-birth.d_julianday1 AS age " .
            "FROM `##dates` AS married " .
            "JOIN `##families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) " .
            "JOIN `##dates` AS birth ON (birth.d_gid=fam.f_husb AND birth.d_file=fam.f_file) " .
            "WHERE " .
            " '{$sex}' IN ('M', 'BOTH') AND {$years} " .
            " married.d_file={$this->tree->id()} AND married.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND married.d_fact='MARR' AND " .
            " birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND birth.d_fact='BIRT' AND " .
            " married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 " .
            "UNION ALL " .
            "SELECT " .
            " fam.f_id, " .
            " birth.d_gid, " .
            " married.d_julianday2-birth.d_julianday1 AS age " .
            "FROM `##dates` AS married " .
            "JOIN `##families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) " .
            "JOIN `##dates` AS birth ON (birth.d_gid=fam.f_wife AND birth.d_file=fam.f_file) " .
            "WHERE " .
            " '{$sex}' IN ('F', 'BOTH') AND {$years} " .
            " married.d_file={$this->tree->id()} AND married.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND married.d_fact='MARR' AND " .
            " birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND birth.d_fact='BIRT' AND " .
            " married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 "
        );

        foreach ($rows as $row) {
            $row->age = (int) $row->age;
        }

        return $rows;
    }

    /**
     * General query on marriage ages.
     *
     * @param string $size
     *
     * @return string
     */
    public function statsMarrAge(string $size = '200x250'): string
    {
        return (new ChartMarriageAge($this->tree))
            ->chartMarriageAge($size);
    }

    /**
     * Query the database for marriage tags.
     *
     * @param string $type
     * @param string $age_dir
     * @param string $sex
     * @param bool   $show_years
     *
     * @return string
     */
    private function marriageQuery(string $type, string $age_dir, string $sex, bool $show_years): string
    {
        if ($sex === 'F') {
            $sex_field = 'f_wife';
        } else {
            $sex_field = 'f_husb';
        }

        if ($age_dir !== 'ASC') {
            $age_dir = 'DESC';
        }

        $rows = $this->runSql(
            " SELECT fam.f_id AS famid, fam.{$sex_field}, married.d_julianday2-birth.d_julianday1 AS age, indi.i_id AS i_id" .
            " FROM `##families` AS fam" .
            " LEFT JOIN `##dates` AS birth ON birth.d_file = {$this->tree->id()}" .
            " LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->id()}" .
            " LEFT JOIN `##individuals` AS indi ON indi.i_file = {$this->tree->id()}" .
            " WHERE" .
            " birth.d_gid = indi.i_id AND" .
            " married.d_gid = fam.f_id AND" .
            " indi.i_id = fam.{$sex_field} AND" .
            " fam.f_file = {$this->tree->id()} AND" .
            " birth.d_fact = 'BIRT' AND" .
            " married.d_fact = 'MARR' AND" .
            " birth.d_julianday1 <> 0 AND" .
            " married.d_julianday2 > birth.d_julianday1 AND" .
            " i_sex='{$sex}'" .
            " ORDER BY" .
            " married.d_julianday2-birth.d_julianday1 {$age_dir} LIMIT 1"
        );

        if (!isset($rows[0])) {
            return '';
        }

        $row = $rows[0];
        if (isset($row->famid)) {
            $family = Family::getInstance($row->famid, $this->tree);
        }

        if (isset($row->i_id)) {
            $person = Individual::getInstance($row->i_id, $this->tree);
        }

        switch ($type) {
            default:
            case 'full':
                if ($family && $family->canShow()) {
                    $result = $family->formatList();
                } else {
                    $result = I18N::translate('This information is private and cannot be shown.');
                }
                break;

            case 'name':
                $result = '<a href="' . e($family->url()) . '">' . $person->getFullName() . '</a>';
                break;

            case 'age':
                $age = $row->age;
                if ($show_years) {
                    if ((int) ($age / 365.25) > 0) {
                        $age = (int) ($age / 365.25) . 'y';
                    } elseif ((int) ($age / 30.4375) > 0) {
                        $age = (int) ($age / 30.4375) . 'm';
                    } else {
                        $age .= 'd';
                    }
                    $result = FunctionsDate::getAgeAtEvent($age);
                } else {
                    $result = I18N::number((int) ($age / 365.25));
                }
                break;
        }

        return $result;
    }

    /**
     * Find the youngest wife.
     *
     * @return string
     */
    public function youngestMarriageFemale(): string
    {
        return $this->marriageQuery('full', 'ASC', 'F', false);
    }

    /**
     * Find the name of the youngest wife.
     *
     * @return string
     */
    public function youngestMarriageFemaleName(): string
    {
        return $this->marriageQuery('name', 'ASC', 'F', false);
    }

    /**
     * Find the age of the youngest wife.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function youngestMarriageFemaleAge(string $show_years = ''): string
    {
        return $this->marriageQuery('age', 'ASC', 'F', (bool) $show_years);
    }

    /**
     * Find the oldest wife.
     *
     * @return string
     */
    public function oldestMarriageFemale(): string
    {
        return $this->marriageQuery('full', 'DESC', 'F', false);
    }

    /**
     * Find the name of the oldest wife.
     *
     * @return string
     */
    public function oldestMarriageFemaleName(): string
    {
        return $this->marriageQuery('name', 'DESC', 'F', false);
    }

    /**
     * Find the age of the oldest wife.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function oldestMarriageFemaleAge(string $show_years = ''): string
    {
        return $this->marriageQuery('age', 'DESC', 'F', (bool) $show_years);
    }

    /**
     * Find the youngest husband.
     *
     * @return string
     */
    public function youngestMarriageMale(): string
    {
        return $this->marriageQuery('full', 'ASC', 'M', false);
    }

    /**
     * Find the name of the youngest husband.
     *
     * @return string
     */
    public function youngestMarriageMaleName(): string
    {
        return $this->marriageQuery('name', 'ASC', 'M', false);
    }

    /**
     * Find the age of the youngest husband.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function youngestMarriageMaleAge(string $show_years = ''): string
    {
        return $this->marriageQuery('age', 'ASC', 'M', (bool) $show_years);
    }

    /**
     * Find the oldest husband.
     *
     * @return string
     */
    public function oldestMarriageMale(): string
    {
        return $this->marriageQuery('full', 'DESC', 'M', false);
    }

    /**
     * Find the name of the oldest husband.
     *
     * @return string
     */
    public function oldestMarriageMaleName(): string
    {
        return $this->marriageQuery('name', 'DESC', 'M', false);
    }

    /**
     * Find the age of the oldest husband.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function oldestMarriageMaleAge(string $show_years = ''): string
    {
        return $this->marriageQuery('age', 'DESC', 'M', (bool) $show_years);
    }

    /**
     * General query on marriages.
     *
     * @param bool $first
     * @param int  $year1
     * @param int  $year2
     *
     * @return array
     */
    public function statsMarrQuery(bool $first = false, int $year1 = -1, int $year2 = -1): array
    {
        if ($first) {
            $years = '';

            if ($year1 >= 0 && $year2 >= 0) {
                $years = " married.d_year BETWEEN '{$year1}' AND '{$year2}' AND";
            }

            $sql =
                " SELECT fam.f_id AS fams, fam.f_husb, fam.f_wife, married.d_julianday2 AS age, married.d_month AS month, indi.i_id AS indi" .
                " FROM `##families` AS fam" .
                " LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->id()}" .
                " LEFT JOIN `##individuals` AS indi ON indi.i_file = {$this->tree->id()}" .
                " WHERE" .
                " married.d_gid = fam.f_id AND" .
                " fam.f_file = {$this->tree->id()} AND" .
                " married.d_fact = 'MARR' AND" .
                " married.d_julianday2 <> 0 AND" .
                $years .
                " (indi.i_id = fam.f_husb OR indi.i_id = fam.f_wife)" .
                " ORDER BY fams, indi, age ASC";
        } else {
            $sql =
                "SELECT d_month, COUNT(*) AS total" .
                " FROM `##dates`" .
                " WHERE d_file={$this->tree->id()} AND d_fact='MARR'";

            if ($year1 >= 0 && $year2 >= 0) {
                $sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
            }

            $sql .= " GROUP BY d_month";
        }

        return $this->runSql($sql);
    }
}
