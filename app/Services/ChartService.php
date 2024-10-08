<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
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
     * @return Collection<int,Individual>
     */
    public function sosaStradonitzAncestors(Individual $individual, int $generations): Collection
    {
        $ancestors = [1 => $individual];

        $queue = [1];

        $max = 2 ** ($generations - 1);

        while ($queue !== []) {
            $sosa_stradonitz_number = array_shift($queue);

            if ($sosa_stradonitz_number >= $max) {
                break;
            }

            $family = $ancestors[$sosa_stradonitz_number]->childFamilies()->first();

            if ($family instanceof Family) {
                if ($family->husband() instanceof Individual) {
                    $ancestors[$sosa_stradonitz_number * 2] = $family->husband();
                    $queue[] = $sosa_stradonitz_number * 2;
                }

                if ($family->wife() instanceof Individual) {
                    $ancestors[$sosa_stradonitz_number * 2 + 1] = $family->wife();
                    $queue[] = $sosa_stradonitz_number * 2 + 1;
                }
            }
        }

        return new Collection($ancestors);
    }

    /**
     * Find the descendants of an individual.
     *
     * @param Individual $individual  Start with this individual
     * @param int        $generations Fetch this number of generations
     *
     * @return Collection<int,Individual>
     */
    public function descendants(Individual $individual, int $generations): Collection
    {
        $descendants = new Collection([$individual->xref() => $individual]);

        if ($generations > 0) {
            foreach ($individual->spouseFamilies() as $family) {
                foreach ($family->children() as $child) {
                    if (!$descendants->has($child->xref())) {
                        $descendants = $descendants->merge($this->descendants($child, $generations - 1));
                    }
                }
            }
        }

        return $descendants->values();
    }

    /**
     * Find the descendants of an individual.
     *
     * @param Individual $individual  Start with this individual
     * @param int        $generations Fetch this number of generations
     *
     * @return Collection<int,Family>
     */
    public function descendantFamilies(Individual $individual, int $generations): Collection
    {
        $descendants = $individual->spouseFamilies();

        if ($generations > 0) {
            foreach ($descendants as $family) {
                foreach ($family->children() as $child) {
                    $descendants = $descendants->merge($this->descendantFamilies($child, $generations - 1));
                }
            }
        }

        return $descendants;
    }
}
