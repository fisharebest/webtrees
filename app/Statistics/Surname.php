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
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class Surname
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
     * @param int $number_of_surnames
     * @param int $threshold
     *
     * @return array
     */
    public function topSurnames(int $number_of_surnames, int $threshold): array
    {
        // Use the count of base surnames.
        $top_surnames = Database::prepare(
            "SELECT n_surn FROM `##name`" .
            " WHERE n_file = :tree_id AND n_type != '_MARNM' AND n_surn NOT IN ('@N.N.', '')" .
            " GROUP BY n_surn" .
            " ORDER BY COUNT(n_surn) DESC" .
            " LIMIT :limit"
        )->execute([
            'tree_id' => $this->tree->id(),
            'limit'   => $number_of_surnames,
        ])->fetchOneColumn();

        $surnames = [];
        foreach ($top_surnames as $top_surname) {
            $variants = Database::prepare(
                "SELECT n_surname COLLATE utf8_bin, COUNT(*) FROM `##name` WHERE n_file = :tree_id AND n_surn COLLATE :collate = :surname GROUP BY 1"
            )->execute([
                'collate' => I18N::collation(),
                'surname' => $top_surname,
                'tree_id' => $this->tree->id(),
            ])->fetchAssoc();

            if (array_sum($variants) > $threshold) {
                $surnames[$top_surname] = $variants;
            }
        }

        return $surnames;
    }

    /**
     * Find common surnames.
     *
     * @return string
     */
    public function getCommonSurname(): string
    {
        $top_surname = $this->topSurnames(1, 0);

        return implode(', ', array_keys(array_shift($top_surname)) ?? []);
    }

    /**
     * Find common surnames.
     *
     * @param string $type
     * @param bool   $show_tot
     * @param int    $threshold
     * @param int    $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    public function commonSurnamesQuery(
        $type,
        $show_tot,
        int $threshold,
        int $number_of_surnames,
        string $sorting
    ): string {
        $surnames = $this->topSurnames($number_of_surnames, $threshold);

        switch ($sorting) {
            default:
            case 'alpha':
                uksort($surnames, [I18N::class, 'strcasecmp']);
                break;
            case 'count':
                break;
            case 'rcount':
                $surnames = array_reverse($surnames, true);
                break;
        }

        return FunctionsPrintLists::surnameList(
            $surnames,
            ($type === 'list' ? 1 : 2),
            $show_tot,
            'individual-list',
            $this->tree
        );
    }
}
