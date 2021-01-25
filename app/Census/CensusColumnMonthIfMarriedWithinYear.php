<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

/**
 * The month of marriage, if within the last year.
 */
class CensusColumnMonthIfMarriedWithinYear extends AbstractCensusColumn implements CensusColumnInterface
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
        foreach ($individual->spouseFamilies() as $family) {
            foreach ($family->facts(['MARR']) as $fact) {
                $marriage_jd = $fact->date()->julianDay();
                $census_jd   = $this->date()->julianDay();
                if ($marriage_jd <= $census_jd && $marriage_jd >= $census_jd - 365) {
                    // Use the GEDCOM month, as we need this in English - for the US census
                    return ucfirst(strtolower($fact->date()->minimumDate()->format('%O')));
                }
            }
        }

        return '';
    }
}
