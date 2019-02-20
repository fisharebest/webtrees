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

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Statistics\Google\ChartChildren;
use Fisharebest\Webtrees\Statistics\Google\ChartDivorce;
use Fisharebest\Webtrees\Statistics\Google\ChartFamilyLargest;
use Fisharebest\Webtrees\Statistics\Google\ChartMarriage;
use Fisharebest\Webtrees\Statistics\Google\ChartMarriageAge;
use Fisharebest\Webtrees\Statistics\Google\ChartNoChildrenFamilies;
use Fisharebest\Webtrees\Statistics\Helper\Sql;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use stdClass;

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
        $row = DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->orderBy('f_numchil', 'desc')
            ->first();

        if ($row === null) {
            return '';
        }

        /** @var Family $family */
        $family = Family::rowMapper()($row);

        if (!$family->canShow()) {
            return I18N::translate('This information is private and cannot be shown.');
        }

        switch ($type) {
            default:
            case 'full':
                return $family->formatList();

            case 'size':
                return I18N::number((int) $row->f_numchil);

            case 'name':
                return '<a href="' . e($family->url()) . '">' . $family->fullName() . '</a>';
        }
    }

    /**
     * Run an SQL query and cache the result.
     *
     * @param string $sql
     *
     * @return stdClass[]
     */
    private function runSql($sql): array
    {
        return Sql::runSql($sql);
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
        return DB::table('families')
            ->join('link AS children', function (JoinClause $join) {
                $join
                    ->on('children.l_from', '=', 'f_id')
                    ->on('children.l_file', '=', 'f_file')
                    ->where('children.l_type', '=', 'CHIL');
            })->join('link AS mchildren', function (JoinClause $join) {
                $join
                    ->on('mchildren.l_file', '=', 'children.l_file')
                    ->on('mchildren.l_from', '=', 'children.l_to')
                    ->where('mchildren.l_type', '=', 'FAMS');
            })->join('link AS gchildren', function (JoinClause $join) {
                $join
                    ->on('gchildren.l_file', '=', 'mchildren.l_file')
                    ->on('gchildren.l_from', '=', 'mchildren.l_to')
                    ->where('gchildren.l_type', '=', 'CHIL');
            })
            ->where('f_file', '=', $this->tree->id())
            ->groupBy(['f_id', 'f_file'])
            ->orderBy(DB::raw('COUNT(*)'), 'DESC')
            ->select('families.*')
            ->limit($total)
            ->get()
            ->map(Family::rowMapper())
            ->filter(GedcomRecord::accessFilter())
            ->map(function (Family $family): array {
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

    /**
     * Find the couple with the most grandchildren.
     *
     * @param int $total
     *
     * @return string
     */
    public function topTenLargestGrandFamily(int $total = 10): string
    {
        return view('statistics/families/top10-nolist-grand', [
            'records' => $this->topTenGrandFamilyQuery($total),
        ]);
    }

    /**
     * Find the couple with the most grandchildren.
     *
     * @param int $total
     *
     * @return string
     */
    public function topTenLargestGrandFamilyList(int $total = 10): string
    {
        return view('statistics/families/top10-list-grand', [
            'records' => $this->topTenGrandFamilyQuery($total),
        ]);
    }

    /**
     * Find the families with no children.
     *
     * @return int
     */
    private function noChildrenFamiliesQuery(): int
    {
        return DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->where('f_numchil', '=', 0)
            ->count();
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
        $families = DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->where('f_numchil', '=', 0)
            ->get()
            ->map(Family::rowMapper())
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
     * Create a chart of children with no families.
     *
     * @param int $year1
     * @param int $year2
     *
     * @return string
     */
    public function chartNoChildrenFamilies(int $year1 = -1, int $year2 = -1): string
    {
        $no_child_fam = $this->noChildrenFamiliesQuery();

        return (new ChartNoChildrenFamilies($this->tree))
            ->chartNoChildrenFamilies($no_child_fam, $year1, $year2);
    }

    /**
     * Returns the ages between siblings.
     *
     * @param int $total The total number of records to query
     *
     * @return array
     */
    private function ageBetweenSiblingsQuery(int $total): array
    {
        $rows = $this->runSql(
            " SELECT DISTINCT" .
            " link1.l_from AS family," .
            " link1.l_to AS ch1," .
            " link2.l_to AS ch2," .
            " child1.d_julianday2-child2.d_julianday2 AS age" .
            " FROM `##link` AS link1" .
            " LEFT JOIN `##dates` AS child1 ON child1.d_file = {$this->tree->id()}" .
            " LEFT JOIN `##dates` AS child2 ON child2.d_file = {$this->tree->id()}" .
            " LEFT JOIN `##link` AS link2 ON link2.l_file = {$this->tree->id()}" .
            " WHERE" .
            " link1.l_file = {$this->tree->id()} AND" .
            " link1.l_from = link2.l_from AND" .
            " link1.l_type = 'CHIL' AND" .
            " child1.d_gid = link1.l_to AND" .
            " child1.d_fact = 'BIRT' AND" .
            " link2.l_type = 'CHIL' AND" .
            " child2.d_gid = link2.l_to AND" .
            " child2.d_fact = 'BIRT' AND" .
            " child1.d_julianday2 > child2.d_julianday2 AND" .
            " child2.d_julianday2 <> 0 AND" .
            " child1.d_gid <> child2.d_gid" .
            " ORDER BY age DESC" .
            " LIMIT " . $total
        );

        if (!isset($rows[0])) {
            return [];
        }

        return $rows;
    }

    /**
     * Returns the calculated age the time of event.
     *
     * @param int $age The age from the database record
     *
     * @return string
     */
    private function calculateAge(int $age): string
    {
        if ((int) ($age / 365.25) > 0) {
            $result = (int) ($age / 365.25) . 'y';
        } elseif ((int) ($age / 30.4375) > 0) {
            $result = (int) ($age / 30.4375) . 'm';
        } else {
            $result = $age . 'd';
        }

        return FunctionsDate::getAgeAtEvent($result);
    }

    /**
     * Find the ages between siblings.
     *
     * @param int $total The total number of records to query
     *
     * @return array
     * @throws \Exception
     */
    private function ageBetweenSiblingsNoList(int $total): array
    {
        $rows = $this->ageBetweenSiblingsQuery($total);

        foreach ($rows as $fam) {
            $family = Family::getInstance($fam->family, $this->tree);
            $child1 = Individual::getInstance($fam->ch1, $this->tree);
            $child2 = Individual::getInstance($fam->ch2, $this->tree);

            if ($child1->canShow() && $child2->canShow()) {
                // ! Single array (no list)
                return [
                    'child1' => $child1,
                    'child2' => $child2,
                    'family' => $family,
                    'age'    => $this->calculateAge((int) $fam->age),
                ];
            }
        }

        return [];
    }

    /**
     * Find the ages between siblings.
     *
     * @param int  $total The total number of records to query
     * @param bool $one   Include each family only once if true
     *
     * @return array
     * @throws \Exception
     */
    private function ageBetweenSiblingsList(int $total, bool $one): array
    {
        $rows  = $this->ageBetweenSiblingsQuery($total);
        $top10 = [];
        $dist  = [];

        foreach ($rows as $fam) {
            $family = Family::getInstance($fam->family, $this->tree);
            $child1 = Individual::getInstance($fam->ch1, $this->tree);
            $child2 = Individual::getInstance($fam->ch2, $this->tree);

            $age = $this->calculateAge((int) $fam->age);

            if ($one && !\in_array($fam->family, $dist, true)) {
                if ($child1->canShow() && $child2->canShow()) {
                    $top10[] = [
                        'child1' => $child1,
                        'child2' => $child2,
                        'family' => $family,
                        'age'    => $age,
                    ];

                    $dist[] = $fam->family;
                }
            } elseif (!$one && $child1->canShow() && $child2->canShow()) {
                $top10[] = [
                    'child1' => $child1,
                    'child2' => $child2,
                    'family' => $family,
                    'age'    => $age,
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
     * Find the ages between siblings.
     *
     * @param int $total The total number of records to query
     *
     * @return string
     */
    private function ageBetweenSiblingsAge(int $total): string
    {
        $rows = $this->ageBetweenSiblingsQuery($total);

        foreach ($rows as $fam) {
            return $this->calculateAge((int) $fam->age);
        }

        return '';
    }

    /**
     * Find the ages between siblings.
     *
     * @param int $total The total number of records to query
     *
     * @return string
     * @throws \Exception
     */
    private function ageBetweenSiblingsName(int $total): string
    {
        $rows = $this->ageBetweenSiblingsQuery($total);

        foreach ($rows as $fam) {
            $family = Family::getInstance($fam->family, $this->tree);
            $child1 = Individual::getInstance($fam->ch1, $this->tree);
            $child2 = Individual::getInstance($fam->ch2, $this->tree);

            if ($child1->canShow() && $child2->canShow()) {
                $return = '<a href="' . e($child2->url()) . '">' . $child2->fullName() . '</a> ';
                $return .= I18N::translate('and') . ' ';
                $return .= '<a href="' . e($child1->url()) . '">' . $child1->fullName() . '</a>';
                $return .= ' <a href="' . e($family->url()) . '">[' . I18N::translate('View this family') . ']</a>';
            } else {
                $return = I18N::translate('This information is private and cannot be shown.');
            }

            return $return;
        }

        return '';
    }

    /**
     * Find the names of siblings with the widest age gap.
     *
     * @param int $total
     *
     * @return string
     */
    public function topAgeBetweenSiblingsName(int $total = 10): string
    {
        return $this->ageBetweenSiblingsName($total);
    }

    /**
     * Find the widest age gap between siblings.
     *
     * @param int $total
     *
     * @return string
     */
    public function topAgeBetweenSiblings(int $total = 10): string
    {
        return $this->ageBetweenSiblingsAge($total);
    }

    /**
     * Find the name of siblings with the widest age gap.
     *
     * @param int $total
     *
     * @return string
     */
    public function topAgeBetweenSiblingsFullName(int $total = 10): string
    {
        $record = $this->ageBetweenSiblingsNoList($total);

        if (empty($record)) {
            return I18N::translate('This information is not available.');
        }

        return view('statistics/families/top10-nolist-age', [
            'record' => $record,
        ]);
    }

    /**
     * Find the siblings with the widest age gaps.
     *
     * @param int    $total
     * @param string $one
     *
     * @return string
     */
    public function topAgeBetweenSiblingsList(int $total = 10, string $one = ''): string
    {
        $records = $this->ageBetweenSiblingsList($total, (bool) $one);

        return view('statistics/families/top10-list-age', [
            'records' => $records,
        ]);
    }

    /**
     * General query on familes/children.
     *
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     *
     * @return stdClass[]
     */
    public function statsChildrenQuery(string $sex = 'BOTH', int $year1 = -1, int $year2 = -1): array
    {
        if ($sex === 'M') {
            $sql =
                "SELECT num, COUNT(*) AS total FROM " .
                "(SELECT count(i_sex) AS num FROM `##link` " .
                "LEFT OUTER JOIN `##individuals` " .
                "ON l_from=i_id AND l_file=i_file AND i_sex='M' AND l_type='FAMC' " .
                "JOIN `##families` ON f_file=l_file AND f_id=l_to WHERE f_file={$this->tree->id()} GROUP BY l_to" .
                ") boys" .
                " GROUP BY num" .
                " ORDER BY num";
        } elseif ($sex === 'F') {
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
     * @return string
     */
    public function statsChildren(): string
    {
        return (new ChartChildren($this->tree))
            ->chartChildren();
    }

    /**
     * Count the total children.
     *
     * @return string
     */
    public function totalChildren(): string
    {
        $total = (int) DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->sum('f_numchil');

        return I18N::number($total);
    }

    /**
     * Find the average number of children in families.
     *
     * @return string
     */
    public function averageChildren(): string
    {
        $average = (float) DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->avg('f_numchil');

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
        return DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->orderBy('f_numchil', 'DESC')
            ->limit($total)
            ->get()
            ->map(Family::rowMapper())
            ->filter(GedcomRecord::accessFilter())
            ->map(function (Family $family): array {
                return [
                    'family' => $family,
                    'count'  => $family->numberOfChildren(),
                ];
            })
            ->all();
    }

    /**
     * The the families with the most children.
     *
     * @param int $total
     *
     * @return string
     */
    public function topTenLargestFamily(int $total = 10): string
    {
        $records = $this->topTenFamilyQuery($total);

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
     * @param int $total
     *
     * @return string
     */
    public function topTenLargestFamilyList(int $total = 10): string
    {
        $records = $this->topTenFamilyQuery($total);

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
     * @param string|null $color_from
     * @param string|null $color_to
     * @param int         $total
     *
     * @return string
     */
    public function chartLargestFamilies(
        string $color_from = null,
        string $color_to = null,
        int $total = 10
    ): string {
        return (new ChartFamilyLargest($this->tree))
            ->chartLargestFamilies($color_from, $color_to, $total);
    }

    /**
     * Find the month in the year of the birth of the first child.
     *
     * @param bool $sex
     *
     * @return stdClass[]
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
        $n = (int) DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->where('f_gedcom', 'LIKE', "%\n1 MARR%")
            ->distinct()
            ->count('f_husb');

        return I18N::number($n);
    }

    /**
     * Number of wives.
     *
     * @return string
     */
    public function totalMarriedFemales(): string
    {
        $n = (int) DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->where('f_gedcom', 'LIKE', "%\n1 MARR%")
            ->distinct()
            ->count('f_wife');

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

        $prefix = DB::connection()->getTablePrefix();

        $row = DB::table('link AS parentfamily')
            ->join('link AS childfamily', function (JoinClause $join): void {
                $join
                    ->on('childfamily.l_file', '=', 'parentfamily.l_file')
                    ->on('childfamily.l_from', '=', 'parentfamily.l_from')
                    ->where('childfamily.l_type', '=', 'CHIL');
            })
            ->join('dates AS birth', function (JoinClause $join): void {
                $join
                    ->on('birth.d_file', '=', 'parentfamily.l_file')
                    ->on('birth.d_gid', '=', 'parentfamily.l_to')
                    ->where('birth.d_fact', '=', 'BIRT')
                    ->where('birth.d_julianday1', '<>', 0);
            })
            ->join('dates AS childbirth', function (JoinClause $join): void {
                $join
                    ->on('childbirth.d_file', '=', 'parentfamily.l_file')
                    ->on('childbirth.d_gid', '=', 'childfamily.l_to');
            })
            ->where('childfamily.l_file', '=', $this->tree->id())
            ->where('parentfamily.l_type', '=', $sex_field)
            ->where('childbirth.d_julianday2', '>', 'birth.d_julianday1')
            ->select(['parentfamily.l_to AS id', DB::raw($prefix . 'childbirth.d_julianday2 - ' . $prefix . 'birth.d_julianday1 AS age')])
            ->take(1)
            ->orderBy('age', $age_dir)
            ->get()
            ->first();

        if ($row === null) {
            return '';
        }

        $person = Individual::getInstance($row->id, $this->tree);

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
     * @param string $age_dir "ASC" or "DESC"
     * @param int    $total
     *
     * @return string
     */
    private function ageOfMarriageQuery(string $type, string $age_dir, int $total): string
    {
        $prefix = DB::connection()->getTablePrefix();

        $hrows = DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->join('dates AS married', function (JoinClause $join): void {
                $join
                    ->on('married.d_file', '=', 'f_file')
                    ->on('married.d_gid', '=', 'f_id')
                    ->where('married.d_fact', '=', 'MARR')
                    ->where('married.d_julianday1', '<>', 0);
            })
            ->join('dates AS husbdeath', function (JoinClause $join): void {
                $join
                    ->on('husbdeath.d_gid', '=', 'f_husb')
                    ->on('husbdeath.d_file', '=', 'f_file')
                    ->where('husbdeath.d_fact', '=', 'DEAT');
            })
            ->whereColumn('married.d_julianday1', '<', 'husbdeath.d_julianday2')
            ->groupBy('f_id')
            ->select(['f_id AS family', DB::raw('MIN(' . $prefix . 'husbdeath.d_julianday2 - ' . $prefix . 'married.d_julianday1) AS age')])
            ->get()
            ->all();

        $wrows = DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->join('dates AS married', function (JoinClause $join): void {
                $join
                    ->on('married.d_file', '=', 'f_file')
                    ->on('married.d_gid', '=', 'f_id')
                    ->where('married.d_fact', '=', 'MARR')
                    ->where('married.d_julianday1', '<>', 0);
            })
            ->join('dates AS wifedeath', function (JoinClause $join): void {
                $join
                    ->on('wifedeath.d_gid', '=', 'f_wife')
                    ->on('wifedeath.d_file', '=', 'f_file')
                    ->where('wifedeath.d_fact', '=', 'DEAT');
            })
            ->whereColumn('married.d_julianday1', '<', 'wifedeath.d_julianday2')
            ->groupBy('f_id')
            ->select(['f_id AS family', DB::raw('MIN(' . $prefix . 'wifedeath.d_julianday2 - ' . $prefix . 'married.d_julianday1) AS age')])
            ->get()
            ->all();

        $drows = DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->join('dates AS married', function (JoinClause $join): void {
                $join
                    ->on('married.d_file', '=', 'f_file')
                    ->on('married.d_gid', '=', 'f_id')
                    ->where('married.d_fact', '=', 'MARR')
                    ->where('married.d_julianday1', '<>', 0);
            })
            ->join('dates AS divorced', function (JoinClause $join): void {
                $join
                    ->on('divorced.d_gid', '=', 'f_id')
                    ->on('divorced.d_file', '=', 'f_file')
                    ->whereIn('divorced.d_fact', ['DIV', 'ANUL', '_SEPR']);
            })
            ->whereColumn('married.d_julianday1', '<', 'divorced.d_julianday2')
            ->groupBy('f_id')
            ->select(['f_id AS family', DB::raw('MIN(' . $prefix . 'divorced.d_julianday2 - ' . $prefix . 'married.d_julianday1) AS age')])
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
        foreach ($rows as $fam => $age) {
            $family = Family::getInstance($fam, $this->tree);
            if ($type === 'name') {
                return $family->formatList();
            }

            $age = $this->calculateAge((int) $age);

            if ($type === 'age') {
                return $age;
            }

            $husb = $family->husband();
            $wife = $family->wife();

            if (($husb && ($husb->getAllDeathDates() || !$husb->isDead()))
                && ($wife && ($wife->getAllDeathDates() || !$wife->isDead()))
            ) {
                if ($family && $family->canShow()) {
                    if ($type === 'list') {
                        $top10[] = '<li><a href="' . e($family->url()) . '">' . $family->fullName() . '</a> (' . $age . ')' . '</li>';
                    } else {
                        $top10[] = '<a href="' . e($family->url()) . '">' . $family->fullName() . '</a> (' . $age . ')';
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
     * @param int $total
     *
     * @return string
     */
    public function topAgeOfMarriageFamilies(int $total = 10): string
    {
        return $this->ageOfMarriageQuery('nolist', 'DESC', $total);
    }

    /**
     * General query on marriage ages.
     *
     * @param int $total
     *
     * @return string
     */
    public function topAgeOfMarriageFamiliesList(int $total = 10): string
    {
        return $this->ageOfMarriageQuery('list', 'DESC', $total);
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
     * @param int $total
     *
     * @return string
     */
    public function minAgeOfMarriageFamilies(int $total = 10): string
    {
        return $this->ageOfMarriageQuery('nolist', 'ASC', $total);
    }

    /**
     * General query on marriage ages.
     *
     * @param int $total
     *
     * @return string
     */
    public function minAgeOfMarriageFamiliesList(int $total = 10): string
    {
        return $this->ageOfMarriageQuery('list', 'ASC', $total);
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
        $prefix = DB::connection()->getTablePrefix();

        $query = DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->join('dates AS wife', function (JoinClause $join): void {
                $join
                    ->on('wife.d_gid', '=', 'f_wife')
                    ->on('wife.d_file', '=', 'f_file')
                    ->where('wife.d_fact', '=', 'BIRT')
                    ->where('wife.d_julianday1', '<>', 0);
            })
            ->join('dates AS husb', function (JoinClause $join): void {
                $join
                    ->on('husb.d_gid', '=', 'f_husb')
                    ->on('husb.d_file', '=', 'f_file')
                    ->where('husb.d_fact', '=', 'BIRT')
                    ->where('husb.d_julianday1', '<>', 0);
            });

        if ($age_dir === 'DESC') {
            $query
                ->whereColumn('wife.d_julianday1', '>=', 'husb.d_julianday1')
                ->orderBy(DB::raw('MIN(' . $prefix . 'wife.d_julianday1) - MIN(' . $prefix . 'husb.d_julianday1)'), 'DESC');
        } else {
            $query
                ->whereColumn('husb.d_julianday1', '>=', 'wife.d_julianday1')
                ->orderBy(DB::raw('MIN(' . $prefix . 'husb.d_julianday1) - MIN(' . $prefix . 'wife.d_julianday1)'), 'DESC');
        }

        $families = $query
            ->groupBy(['f_id', 'f_file'])
            ->select('families.*')
            ->take($total)
            ->get()
            ->map(Family::rowMapper())
            ->filter(GedcomRecord::accessFilter());

        $top10 = [];

        /** @var Family $family */
        foreach ($families as $family) {
            $husb_birt_jd = $family->husband()->getBirthDate()->minimumJulianDay();
            $wife_birt_jd = $family->wife()->getBirthDate()->minimumJulianDay();

            if ($age_dir === 'DESC') {
                $diff = $wife_birt_jd - $husb_birt_jd;
            } else {
                $diff = $husb_birt_jd - $wife_birt_jd;
            }

            $top10[] = [
                'family' => $family,
                'age'    => $this->calculateAge((int) $diff),
            ];
        }

        return $top10;
    }

    /**
     * Find the age between husband and wife.
     *
     * @param int $total
     *
     * @return string
     */
    public function ageBetweenSpousesMF(int $total = 10): string
    {
        $records = $this->ageBetweenSpousesQuery('DESC', $total);

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
     * @param int $total
     *
     * @return string
     */
    public function ageBetweenSpousesMFList(int $total = 10): string
    {
        $records = $this->ageBetweenSpousesQuery('DESC', $total);

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
     * @param int $total
     *
     * @return string
     */
    public function ageBetweenSpousesFM(int $total = 10): string
    {
        $records = $this->ageBetweenSpousesQuery('ASC', $total);

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
     * @param int $total
     *
     * @return string
     */
    public function ageBetweenSpousesFMList(int $total = 10): string
    {
        $records = $this->ageBetweenSpousesQuery('ASC', $total);

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
     * @return string
     */
    public function statsMarrAge(): string
    {
        return (new ChartMarriageAge($this->tree))
            ->chartMarriageAge();
    }

    /**
     * Query the database for marriage tags.
     *
     * @param string $type       "full", "name" or "age"
     * @param string $age_dir    "ASC" or "DESC"
     * @param string $sex        "F" or "M"
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

        $prefix = DB::connection()->getTablePrefix();

        $row = DB::table('families')
            ->join('dates AS married', function (JoinClause $join): void {
                $join
                    ->on('married.d_file', '=', 'f_file')
                    ->on('married.d_gid', '=', 'f_id')
                    ->where('married.d_fact', '=', 'MARR');
            })
            ->join('individuals', function (JoinClause $join) use ($sex, $sex_field): void {
                $join
                    ->on('i_file', '=', 'f_file')
                    ->on('i_id', '=', $sex_field)
                    ->where('i_sex', '=', $sex);
            })
            ->join('dates AS birth', function (JoinClause $join): void {
                $join
                    ->on('birth.d_file', '=', 'i_file')
                    ->on('birth.d_gid', '=', 'i_id')
                    ->where('birth.d_fact', '=', 'BIRT')
                    ->where('birth.d_julianday1', '<>', 0);
            })
            ->where('f_file', '=', $this->tree->id())
            ->where('married.d_julianday2', '>', 'birth.d_julianday1')
            ->orderBy(DB::raw($prefix . 'married.d_julianday2 - ' . $prefix . 'birth.d_julianday1'), $age_dir)
            ->select(['f_id AS famid', $sex_field, DB::raw($prefix . 'married.d_julianday2 - ' . $prefix . 'birth.d_julianday1 AS age'), 'i_id'])
            ->take(1)
            ->get()
            ->first();

        if ($row === null) {
            return '';
        }

        $family = Family::getInstance($row->famid, $this->tree);
        $person = Individual::getInstance($row->i_id, $this->tree);

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
     * @param bool $first_marriage
     * @param int  $year1
     * @param int  $year2
     *
     * @return array
     */
    public function statsMarrQuery(bool $first_marriage = false, int $year1 = -1, int $year2 = -1): array
    {
        if ($first_marriage) {
            $query = DB::table('families')
                ->join('dates', function (JoinClause $join): void {
                    $join
                        ->on('d_gid', '=', 'f_id')
                        ->on('d_file', '=', 'f_file')
                        ->where('d_fact', '=', 'MARR')
                        ->where('d_julianday2', '<>', 0);
                })->join('individuals', function (JoinClause $join): void {
                    $join
                        ->on('i_file', '=', 'f_file');
                })
                ->where('f_file', '=', $this->tree->id())
                ->where(function (Builder $query): void {
                    $query
                        ->whereColumn('i_id', '=', 'f_husb')
                        ->orWhereColumn('i_id', '=', 'f_wife');
                });

            if ($year1 >= 0 && $year2 >= 0) {
                $query->whereBetween('d_year', [$year1, $year2]);
            }

            return $query
                ->select(['f_id AS fams', 'f_husb', 'f_wife', 'd_julianday2 AS age', 'd_month AS month', 'i_id AS indi'])
                ->orderBy('f_id')
                ->orderBy('i_id')
                ->orderBy('d_julianday2')
                ->get()
                ->all();
        } else {
            $query = DB::table('dates')
                ->where('d_file', '=', $this->tree->id())
                ->where('d_fact', '=', 'MARR')
                ->select(['d_month', DB::raw('COUNT(*) AS total')])
                ->groupBy('d_month');

            if ($year1 >= 0 && $year2 >= 0) {
                $query->whereBetween('d_year', [$year1, $year2]);
            }

            return $query
                ->get()
                ->all();
        }
    }

    /**
     * General query on marriages.
     *
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsMarr(string $color_from = null, string $color_to = null): string
    {
        return (new ChartMarriage($this->tree))
            ->chartMarriage($color_from, $color_to);
    }

    /**
     * General divorce query.
     *
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsDiv(string $color_from = null, string $color_to = null): string
    {
        return (new ChartDivorce($this->tree))
            ->chartDivorce($color_from, $color_to);
    }
}
