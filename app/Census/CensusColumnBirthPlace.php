<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Individual;

use function strlen;

/**
 * The individual's birth place.
 */
class CensusColumnBirthPlace extends AbstractCensusColumn implements CensusColumnInterface
{
    /**
     * Generate the likely value of this census column, based on available information.
     *
     * @param Individual $individual
     * @param Individual $head
     *
     * @return string
     */
    public function generate(Individual $individual, Individual $head): string
    {
        $birth_place  = $individual->getBirthPlace()->gedcomName();
        $census_place = $this->place();

        // Ignore the census country
        if ($birth_place === $census_place) {
            return '';
        }

        if (substr($birth_place, -strlen($census_place) - 2) === ', ' . $census_place) {
            return substr($birth_place, 0, -strlen($census_place) - 2);
        }

        return $birth_place;
    }
}
