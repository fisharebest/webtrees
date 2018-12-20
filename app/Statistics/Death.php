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
class Death
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
     * Create a chart of death places
     *
     * @param bool $sex
     * @param int  $year1
     * @param int  $year2
     *
     * @return array
     */
    public function query($sex = false, $year1 = -1, $year2 = -1): array
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
