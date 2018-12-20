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

use Fisharebest\Webtrees\Tree;

/**
 *
 */
class Age
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
     * General query on ages.
     *
     * @param string $related
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     *
     * @return array
     */
    public function query($related = 'BIRT', $sex = 'BOTH', $year1 = -1, $year2 = -1): array
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
