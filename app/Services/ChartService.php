<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Illuminate\Support\Collection;

/**
 * Find ancestors, descendants, cousins, etc for drawing charts.
 */
class ChartService
{
    /**
     * Find the ancestors of an individual, indexed by their Sosa-Stradonitz number.
     *
     * @param Individual $individual  Start with this individual
     * @param int        $generations Fetch this number of generations
     *
     * @return Collection|Individual[]
     */
    public function sosaStradonitzAncestors(Individual $individual, int $generations): Collection
    {
        $ancestors = [1 => $individual];

        $queue = [1];

        $max = 2 ** ($generations - 1);

        while (!empty($queue)) {
            $sosa_stradonitz_number = array_shift($queue);

            if ($sosa_stradonitz_number >= $max) {
                break;
            }

            $family = $ancestors[$sosa_stradonitz_number]->getPrimaryChildFamily();

            if ($family instanceof Family) {
                if ($family->getHusband() instanceof Individual) {
                    $ancestors[$sosa_stradonitz_number * 2] = $family->getHusband();
                    $queue[] = $sosa_stradonitz_number * 2;
                }

                if ($family->getWife() instanceof Individual) {
                    $ancestors[$sosa_stradonitz_number * 2 + 1] = $family->getWife();
                    $queue[] = $sosa_stradonitz_number * 2 + 1;
                }
            }
        }

        return new Collection($ancestors);
    }
}
