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
namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Individual;

/**
 * Did the individual marry within the last year.
 */
class CensusColumnMarriedWithinYear extends AbstractCensusColumn implements CensusColumnInterface
{
    /**
     * Generate the likely value of this census column, based on available information.
     *
     * @param Individual      $individual
     * @param Individual|null $head
     *
     * @return string
     */
    public function generate(Individual $individual, Individual $head = null)
    {
        foreach ($individual->getSpouseFamilies() as $family) {
            foreach ($family->getFacts('MARR') as $fact) {
                $marriage_jd = $fact->getDate()->julianDay();
                $census_jd   = $this->date()->julianDay();
                if ($marriage_jd <= $census_jd && $marriage_jd >= $census_jd - 365) {
                    return 'Y';
                }
            }
        }

        return '';
    }
}
