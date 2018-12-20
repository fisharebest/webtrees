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

use Fisharebest\Webtrees\Statistics\Helper\Sql;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class Marriage
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
     * General query on marriages.
     *
     * @param bool $first
     * @param int  $year1
     * @param int  $year2
     *
     * @return string|array
     */
    public function query($first = false, $year1 = -1, $year2 = -1)
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
}
