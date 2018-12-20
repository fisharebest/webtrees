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
use Fisharebest\Webtrees\Statistics\Helper\Percentage;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class Individual
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
     * How many individuals exist in the tree.
     *
     * @return int
     */
    public function totalIndividualsQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(*) FROM `##individuals` WHERE i_file = :tree_id"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
    }

    /**
     * Find common given names.
     *
     * @param string $sex
     * @param string $type
     * @param bool   $show_tot
     * @param int    $threshold
     * @param int    $maxtoshow
     *
     * @return string|int[]
     */
    public function commonGivenQuery(string $sex, string $type, bool $show_tot, int $threshold, int $maxtoshow)
    {
        switch ($sex) {
            case 'M':
                $sex_sql = "i_sex='M'";
                break;
            case 'F':
                $sex_sql = "i_sex='F'";
                break;
            case 'U':
                $sex_sql = "i_sex='U'";
                break;
            case 'B':
            default:
                $sex_sql = "i_sex<>'U'";
                break;
        }

        $ged_id = $this->tree->id();

        $rows     = Database::prepare("SELECT n_givn, COUNT(*) AS num FROM `##name` JOIN `##individuals` ON (n_id=i_id AND n_file=i_file) WHERE n_file={$ged_id} AND n_type<>'_MARNM' AND n_givn NOT IN ('@P.N.', '') AND LENGTH(n_givn)>1 AND {$sex_sql} GROUP BY n_id, n_givn")
            ->fetchAll();

        $nameList = [];
        foreach ($rows as $row) {
            $row->num = (int) $row->num;

            // Split “John Thomas” into “John” and “Thomas” and count against both totals
            foreach (explode(' ', $row->n_givn) as $given) {
                // Exclude initials and particles.
                if (!preg_match('/^([A-Z]|[a-z]{1,3})$/', $given)) {
                    if (array_key_exists($given, $nameList)) {
                        $nameList[$given] += (int) $row->num;
                    } else {
                        $nameList[$given] = (int) $row->num;
                    }
                }
            }
        }
        arsort($nameList);
        $nameList = array_slice($nameList, 0, $maxtoshow);

        foreach ($nameList as $given => $total) {
            if ($total < $threshold) {
                unset($nameList[$given]);
            }
        }

        switch ($type) {
            case 'chart':
                return $nameList;

            case 'table':
                return view('lists/given-names-table', [
                    'given_names' => $nameList,
                ]);

            case 'list':
                return view('lists/given-names-list', [
                    'given_names' => $nameList,
                    'show_totals' => $show_tot,
                ]);

            case 'nolist':
            default:
                array_walk($nameList, function (int &$value, string $key) use ($show_tot): void {
                    if ($show_tot) {
                        $value = '<span dir="auto">' . e($key);
                    } else {
                        $value = '<span dir="auto">' . e($key) . ' (' . I18N::number($value) . ')';
                    }
                });

                return implode(I18N::$list_separator, $nameList);
        }
    }
}
