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
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Tree;

/**
 * A selection of pre-formatted statistical queries.
 *
 * These are primarily used for embedded keywords on HTML blocks, but
 * are also used elsewhere in the code.
 */
class Places
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * BirthPlaces constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * Places
     *
     * @param string $what
     * @param string $fact
     * @param int    $parent
     * @param bool   $country
     *
     * @return int[]|\stdClass[]
     */
    public function statsPlaces($what = 'ALL', $fact = '', $parent = 0, $country = false): array
    {
        if ($fact) {
            if ($what === 'INDI') {
                $rows = Database::prepare(
                    "SELECT i_gedcom AS ged FROM `##individuals` WHERE i_file = :tree_id AND i_gedcom LIKE '%\n2 PLAC %'"
                )->execute([
                    'tree_id' => $this->tree->id(),
                ])->fetchAll();
            } elseif ($what === 'FAM') {
                $rows = Database::prepare(
                    "SELECT f_gedcom AS ged FROM `##families` WHERE f_file = :tree_id AND f_gedcom LIKE '%\n2 PLAC %'"
                )->execute([
                    'tree_id' => $this->tree->id(),
                ])->fetchAll();
            }

            $placelist = [];

            foreach ($rows as $row) {
                if (preg_match('/\n1 ' . $fact . '(?:\n[2-9].*)*\n2 PLAC (.+)/', $row->ged, $match)) {
                    if ($country) {
                        $tmp   = explode(Place::GEDCOM_SEPARATOR, $match[1]);
                        $place = end($tmp);
                    } else {
                        $place = $match[1];
                    }
                    if (!isset($placelist[$place])) {
                        $placelist[$place] = 1;
                    } else {
                        $placelist[$place]++;
                    }
                }
            }

            return $placelist;
        }

        if ($parent > 0) {
            // used by placehierarchy googlemap module
            if ($what === 'INDI') {
                $join = " JOIN `##individuals` ON pl_file = i_file AND pl_gid = i_id";
            } elseif ($what === 'FAM') {
                $join = " JOIN `##families` ON pl_file = f_file AND pl_gid = f_id";
            } else {
                $join = "";
            }
            $rows = $this->runSql(
                " SELECT" .
                " p_place AS place," .
                " COUNT(*) AS tot" .
                " FROM" .
                " `##places`" .
                " JOIN `##placelinks` ON pl_file=p_file AND p_id=pl_p_id" .
                $join .
                " WHERE" .
                " p_id={$parent} AND" .
                " p_file={$this->tree->id()}" .
                " GROUP BY place"
            );

            return $rows;
        }

        if ($what === 'INDI') {
            $join = " JOIN `##individuals` ON pl_file = i_file AND pl_gid = i_id";
        } elseif ($what === 'FAM') {
            $join = " JOIN `##families` ON pl_file = f_file AND pl_gid = f_id";
        } else {
            $join = "";
        }

        $rows = $this->runSql(
            " SELECT" .
            " p_place AS country," .
            " COUNT(*) AS tot" .
            " FROM" .
            " `##places`" .
            " JOIN `##placelinks` ON pl_file=p_file AND p_id=pl_p_id" .
            $join .
            " WHERE" .
            " p_file={$this->tree->id()}" .
            " AND p_parent_id='0'" .
            " GROUP BY country ORDER BY tot DESC, country ASC"
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
