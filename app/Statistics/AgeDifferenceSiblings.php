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

namespace Fisharebest\Webtrees\Statistics;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;

/**
 * Find the ages between siblings.
 */
class AgeDifferenceSiblings
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
     * Find the ages between siblings.
     *
     * @param string $type
     * @param int    $total
     * @param bool   $one   Include each family only once if true
     *
     * @return array
     */
    public function query(string $type, int $total, bool $one): array
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

        $top10 = [];
        $dist  = [];
        foreach ($rows as $fam) {
            $family = Family::getInstance($fam->family, $this->tree);
            $child1 = Individual::getInstance($fam->ch1, $this->tree);
            $child2 = Individual::getInstance($fam->ch2, $this->tree);

//            if ($type === 'name') {
//                if ($child1->canShow() && $child2->canShow()) {
//                    $return = '<a href="' . e($child2->url()) . '">' . $child2->getFullName() . '</a> ';
//                    $return .= I18N::translate('and') . ' ';
//                    $return .= '<a href="' . e($child1->url()) . '">' . $child1->getFullName() . '</a>';
//                    $return .= ' <a href="' . e($family->url()) . '">[' . I18N::translate('View this family') . ']</a>';
//                } else {
//                    $return = I18N::translate('This information is private and cannot be shown.');
//                }
//
//                return $return;
//            }

            $age = $fam->age;
            if ((int) ($age / 365.25) > 0) {
                $age = (int) ($age / 365.25) . 'y';
            } elseif ((int) ($age / 30.4375) > 0) {
                $age = (int) ($age / 30.4375) . 'm';
            } else {
                $age .= 'd';
            }

            $age = FunctionsDate::getAgeAtEvent($age);
//            if ($type === 'age') {
//                return $age;
//            }

            if ($type === 'list') {
                if ($one && !in_array($fam->family, $dist)) {
                    if ($child1->canShow() && $child2->canShow()) {
                        $top10[] = [
                            'child1' => $child1,
                            'child2' => $child2,
                            'family' => $family,
                            'age'    => $age,
                        ];

                        $dist[]  = $fam->family;
                    }
                } elseif (!$one && $child1->canShow() && $child2->canShow()) {
                    $top10[] = [
                        'child1' => $child1,
                        'child2' => $child2,
                        'family' => $family,
                        'age'    => $age,
                    ];
                }
            } else {
                if ($child1->canShow() && $child2->canShow()) {
                    // ! Single array (no list)
                    return [
                        'child1' => $child1,
                        'child2' => $child2,
                        'family' => $family,
                        'age'    => $age,
                    ];

                    $return = $child2->formatList();
                    $return .= '<br>' . I18N::translate('and') . '<br>';
                    $return .= $child1->formatList();
                    $return .= '<br><a href="' . e($family->url()) . '">[' . I18N::translate('View this family') . ']</a>';

                    return $return;
                }

//                return I18N::translate('This information is private and cannot be shown.');
            }
        }

//        if ($type === 'list') {
//            $top10 = implode('', $top10);
//        }
//
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
//
//        if ($type === 'list') {
//            return '<ul>' . $top10 . '</ul>';
//        }

        return $top10;
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
        static $cache = [];

        $id = md5($sql);

        if (isset($cache[$id])) {
            return $cache[$id];
        }

        $rows       = Database::prepare($sql)->fetchAll();
        $cache[$id] = $rows;

        return $rows;
    }
}
