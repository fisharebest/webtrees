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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Helper\Sql;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class Children
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
     * General query on familes/children.
     *
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     *
     * @return \stdClass[]
     */
    public function query($sex = 'BOTH', $year1 = -1, $year2 = -1): array
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
}
