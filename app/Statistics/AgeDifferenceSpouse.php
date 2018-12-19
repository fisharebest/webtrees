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
use Fisharebest\Webtrees\Tree;

/**
 * Find the ages between spouses.
 */
class AgeDifferenceSpouse
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
     * Find the ages between spouses.
     *
     * @param string $type
     * @param string $age_dir
     * @param int    $total
     *
     * @return array
     */
    public function query(string $type, string $age_dir, int $total): array
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
}
