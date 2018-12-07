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
namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Individual;

/**
 * The individual's full name.
 */
class CensusColumnFullName extends AbstractCensusColumn implements CensusColumnInterface
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
        $name = $this->nameAtCensusDate($individual, $this->date());

        return strip_tags($name['full']);
    }

    /**
     * What was an individual's likely name on a given date, allowing
     * for marriages and married names.
     *
     * @param Individual $individual
     * @param Date       $census_date
     *
     * @return string[]
     */
    protected function nameAtCensusDate(Individual $individual, Date $census_date)
    {
        $names = $individual->getAllNames();
        $name  = $names[0];

        foreach ($individual->getSpouseFamilies() as $family) {
            foreach ($family->getFacts('MARR') as $marriage) {
                if ($marriage->getDate()->isOK() && Date::compare($marriage->getDate(), $census_date) < 0) {
                    $spouse = $family->getSpouse($individual);
                    foreach ($names as $individual_name) {
                        foreach ($spouse->getAllNames() as $spouse_name) {
                            if ($individual_name['type'] === '_MARNM' && $individual_name['surn'] === $spouse_name['surn']) {
                                return $individual_name;
                            }
                        }
                    }
                }
            }
        }

        return $name;
    }
}
