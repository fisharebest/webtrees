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

use Exception;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Statistics\Google\ChartChildren;
use Fisharebest\Webtrees\Statistics\Google\ChartDivorce;
use Fisharebest\Webtrees\Statistics\Google\ChartFamilyLargest;
use Fisharebest\Webtrees\Statistics\Google\ChartMarriage;
use Fisharebest\Webtrees\Statistics\Google\ChartMarriageAge;
use Fisharebest\Webtrees\Statistics\Google\ChartNoChildrenFamilies;
use Fisharebest\Webtrees\Statistics\Service\CenturyService;
use Fisharebest\Webtrees\Statistics\Service\ColorService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;

use function arsort;
use function asort;
use function e;
use function floor;
use function implode;
use function in_array;
use function str_replace;
use function view;

/**
 * A repository providing methods for family related statistics.
 */
class FamilyRepository
{
    private CenturyService $century_service;

    private ColorService $color_service;

    private Tree $tree;

    /**
     * @param CenturyService $century_service
     * @param ColorService   $color_service
     * @param Tree           $tree
     */
    public function __construct(CenturyService $century_service, ColorService $color_service, Tree $tree)
    {
        $this->century_service = $century_service;
        $this->color_service   = $color_service;
        $this->tree            = $tree;
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

        $family = Registry::familyFactory()->mapper($this->tree)($row);

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
     * @return array<array<string,int|Family>>
     */
    private function topTenGrandFamilyQuery(int $total): array
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
            ->limit($total)
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

        return (new ChartNoChildrenFamilies($this->century_service, $this->tree))
            ->chartNoChildrenFamilies($no_child_fam, $year1, $year2);
    }

    /**
     * Returns the ages between siblings.
     *
     * @param int $total The total number of records to query
     *
     * @return array<object>
     */
    private function ageBetweenSiblingsQuery(int $total): array
    {
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
            ->select(['link1.l_from AS family', 'link1.l_to AS ch1', 'link2.l_to AS ch2', new Expression(DB::prefix('child2.d_julianday2') . ' - ' . DB::prefix('child1.d_julianday1') . ' AS age')])
            ->orderBy('age', 'DESC')
            ->take($total)
            ->get()
            ->all();
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

    /**
     * Find the ages between siblings.
     *
     * @param int $total The total number of records to query
     *
     * @return array<string,Individual|Family|string>
     * @throws Exception
     */
    private function ageBetweenSiblingsNoList(int $total): array
    {
        $rows = $this->ageBetweenSiblingsQuery($total);

        foreach ($rows as $fam) {
            $family = Registry::familyFactory()->make($fam->family, $this->tree);
            $child1 = Registry::individualFactory()->make($fam->ch1, $this->tree);
            $child2 = Registry::individualFactory()->make($fam->ch2, $this->tree);

            if ($family !== null && $child1 !== null && $child2 !== null && $child1->canShow() && $child2->canShow()) {
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
     * @return array<int,array<string,Individual|Family|string>>
     * @throws Exception
     */
    private function ageBetweenSiblingsList(int $total, bool $one): array
    {
        $rows  = $this->ageBetweenSiblingsQuery($total);
        $top10 = [];
        $dist  = [];

        foreach ($rows as $fam) {
            $family = Registry::familyFactory()->make($fam->family, $this->tree);
            $child1 = Registry::individualFactory()->make($fam->ch1, $this->tree);
            $child2 = Registry::individualFactory()->make($fam->ch2, $this->tree);

            $age = $this->calculateAge((int) $fam->age);

            if ($one && !in_array($fam->family, $dist, true)) {
                if ($family !== null && $child1 !== null && $child2 !== null && $child1->canShow() && $child2->canShow()) {
                    $top10[] = [
                        'child1' => $child1,
                        'child2' => $child2,
                        'family' => $family,
                        'age'    => $age,
                    ];

                    $dist[] = $fam->family;
                }
            } elseif (!$one && $family !== null && $child1 !== null && $child2 !== null && $child1->canShow() && $child2->canShow()) {
                $top10[] = [
                    'child1' => $child1,
                    'child2' => $child2,
                    'family' => $family,
                    'age'    => $age,
                ];
            }
        }

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
     * @throws Exception
     */
    private function ageBetweenSiblingsName(int $total): string
    {
        $rows = $this->ageBetweenSiblingsQuery($total);

        foreach ($rows as $fam) {
            $family = Registry::familyFactory()->make($fam->family, $this->tree);
            $child1 = Registry::individualFactory()->make($fam->ch1, $this->tree);
            $child2 = Registry::individualFactory()->make($fam->ch2, $this->tree);

            if ($family !== null && $child1 !== null && $child2 !== null && $child1->canShow() && $child2->canShow()) {
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

        if ($record === []) {
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
     * General query on families/children.
     *
     * @param int    $year1
     * @param int    $year2
     *
     * @return array<object>
     */
    public function statsChildrenQuery(int $year1 = -1, int $year2 = -1): array
    {
        $query = DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->groupBy(['f_numchil'])
            ->select(['f_numchil', new Expression('COUNT(*) AS total')]);

        if ($year1 >= 0 && $year2 >= 0) {
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

        return $query->get()->all();
    }

    /**
     * Genearl query on families/children.
     *
     * @return string
     */
    public function statsChildren(): string
    {
        return (new ChartChildren($this->century_service, $this->tree))
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
     * @return array<array<string,mixed>>
     */
    private function topTenFamilyQuery(int $total): array
    {
        return DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->orderBy('f_numchil', 'DESC')
            ->limit($total)
            ->get()
            ->map(Registry::familyFactory()->mapper($this->tree))
            ->filter(GedcomRecord::accessFilter())
            ->map(static fn (Family $family): array => [
                'family' => $family,
                'count'  => $family->numberOfChildren(),
            ])
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

        return view('statistics/families/top10-nolist', [
            'records' => $records,
        ]);
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

        return view('statistics/families/top10-list', [
            'records' => $records,
        ]);
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
        string|null $color_from = null,
        string|null $color_to = null,
        int $total = 10
    ): string {
        return (new ChartFamilyLargest($this->color_service, $this->tree))
            ->chartLargestFamilies($color_from, $color_to, $total);
    }

    /**
     * Find the month in the year of the birth of the first child.
     *
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function monthFirstChildQuery(int $year1 = -1, int $year2 = -1): Builder
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
            ->where('link.l_type', '=', 'CHIL')
            ->select(['d_month', new Expression('COUNT(*) AS total')])
            ->groupBy(['d_month']);

        if ($year1 >= 0 && $year2 >= 0) {
            $query->whereBetween('d_year', [$year1, $year2]);
        }

        return $query;
    }

    /**
     * Find the month in the year of the birth of the first child.
     *
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function monthFirstChildBySexQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->monthFirstChildQuery($year1, $year2)
            ->join('individuals', static function (JoinClause $join): void {
                $join
                    ->on('i_file', '=', 'l_file')
                    ->on('i_id', '=', 'l_to');
            })
            ->select(['d_month', 'i_sex', new Expression('COUNT(*) AS total')])
            ->groupBy(['d_month', 'i_sex']);
    }

    /**
     * Number of husbands.
     *
     * @return string
     */
    public function totalMarriedMales(): string
    {
        $n = DB::table('families')
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
        $n = DB::table('families')
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
            ->where('childbirth.d_julianday2', '>', new Expression(DB::prefix('birth.d_julianday1')))
            ->select(['parentfamily.l_to AS id', new Expression(DB::prefix('childbirth.d_julianday2') . ' - ' . DB::prefix('birth.d_julianday1') . ' AS age')])
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
            ->select(['f_id AS family', new Expression('MIN(' . DB::prefix('husbdeath.d_julianday2') . ' - ' . DB::prefix('married.d_julianday1') . ') AS age')])
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
            ->select(['f_id AS family', new Expression('MIN(' . DB::prefix('wifedeath.d_julianday2') . ' - ' . DB::prefix('married.d_julianday1') . ') AS age')])
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
            ->select(['f_id AS family', new Expression('MIN(' . DB::prefix('divorced.d_julianday2') . ' - ' . DB::prefix('married.d_julianday1') . ') AS age')])
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
     * @return array<array<string,mixed>>
     */
    private function ageBetweenSpousesQuery(string $age_dir, int $total): array
    {
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
                ->orderBy(new Expression('MIN(' . DB::prefix('wife.d_julianday1') . ') - MIN(' . DB::prefix('husb.d_julianday1') . ')'), 'DESC');
        } else {
            $query
                ->whereColumn('husb.d_julianday1', '>=', 'wife.d_julianday1')
                ->orderBy(new Expression('MIN(' . DB::prefix('husb.d_julianday1') . ') - MIN(' . DB::prefix('wife.d_julianday1') . ')'), 'DESC');
        }

        return $query
            ->groupBy(['f_id', 'f_file'])
            ->select(['families.*'])
            ->take($total)
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

        return view('statistics/families/top10-nolist-spouses', [
            'records' => $records,
        ]);
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

        return view('statistics/families/top10-list-spouses', [
            'records' => $records,
        ]);
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

        return view('statistics/families/top10-nolist-spouses', [
            'records' => $records,
        ]);
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

        return view('statistics/families/top10-list-spouses', [
            'records' => $records,
        ]);
    }

    /**
     * General query on ages at marriage.
     *
     * @param string $sex "M" or "F"
     * @param int    $year1
     * @param int    $year2
     *
     * @return array<object>
     */
    public function statsMarrAgeQuery(string $sex, int $year1 = -1, int $year2 = -1): array
    {
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
            ->select(['f_id', 'birth.d_gid', new Expression(DB::prefix('married.d_julianday2') . ' - ' . DB::prefix('birth.d_julianday1') . ' AS age')]);

        if ($year1 >= 0 && $year2 >= 0) {
            $query->whereBetween('married.d_year', [$year1, $year2]);
        }

        return $query
            ->get()
            ->map(static function (object $row): object {
                $row->age = (int) $row->age;

                return $row;
            })
            ->all();
    }

    /**
     * General query on marriage ages.
     *
     * @return string
     */
    public function statsMarrAge(): string
    {
        return (new ChartMarriageAge($this->century_service, $this->tree))
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
            ->where('married.d_julianday2', '>', new Expression(DB::prefix('birth.d_julianday1')))
            ->orderBy(new Expression(DB::prefix('married.d_julianday2') . ' - ' . DB::prefix('birth.d_julianday1')), $age_dir)
            ->select(['f_id AS famid', $sex_field, new Expression(DB::prefix('married.d_julianday2') . ' - ' . DB::prefix('birth.d_julianday1') . ' AS age'), 'i_id'])
            ->take(1)
            ->get()
            ->first();

        if ($row === null) {
            return I18N::translate('This information is not available.');
        }

        $family = Registry::familyFactory()->make($row->famid, $this->tree);
        $person = Registry::individualFactory()->make($row->i_id, $this->tree);

        switch ($type) {
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
     * @param int  $year1
     * @param int  $year2
     *
     * @return Builder
     */
    public function statsMarriageQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        $query = DB::table('dates')
            ->where('d_file', '=', $this->tree->id())
            ->where('d_fact', '=', 'MARR')
            ->select(['d_month', new Expression('COUNT(*) AS total')])
            ->groupBy(['d_month']);

        if ($year1 >= 0 && $year2 >= 0) {
            $query->whereBetween('d_year', [$year1, $year2]);
        }

        return $query;
    }

    /**
     * General query on marriages.
     *
     * @param int  $year1
     * @param int  $year2
     *
     * @return Builder
     */
    public function statsFirstMarriageQuery(int $year1 = -1, int $year2 = -1): Builder
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
            ->where('f_file', '=', $this->tree->id());

        if ($year1 >= 0 && $year2 >= 0) {
            $query->whereBetween('d_year', [$year1, $year2]);
        }

        return $query
            ->select(['f_husb', 'f_wife', 'd_month AS month'])
            ->orderBy('d_julianday2');
    }

    /**
     * General query on marriages.
     *
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsMarr(string|null $color_from = null, string|null $color_to = null): string
    {
        return (new ChartMarriage($this->century_service, $this->color_service, $this->tree))
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
    public function statsDiv(string|null $color_from = null, string|null $color_to = null): string
    {
        return (new ChartDivorce($this->century_service, $this->color_service, $this->tree))
            ->chartDivorce($color_from, $color_to);
    }
}
