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
class MarriageAge
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
     * General query on ages at marriage.
     *
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     *
     * @return array
     */
    public function query($sex = 'M', $year1 = -1, $year2 = -1): array
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
