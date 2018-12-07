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
 * The nationality of the individual.
 */
class CensusColumnNationality extends AbstractCensusColumn implements CensusColumnInterface
{
    /** @var array Convert a country name to a nationality */
    private $nationalities = array(
        'England'     => 'British',
        'Scotland'    => 'British',
        'Wales'       => 'British',
        'Deutschland' => 'Deutsch',
    );

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
        $place = $individual->getBirthPlace();

        // No birthplace?  Assume born in the same country.
        if ($place === '') {
            $place = $this->place();
        }

        // Did we emigrate or naturalise?
        foreach ($individual->getFacts('IMMI|EMIG|NATU', true) as $fact) {
            if (Date::compare($fact->getDate(), $this->date()) <= 0) {
                $place = $fact->getPlace()->getGedcomName();
            }
        }

        $place = $this->lastPartOfPlace($place);

        if (array_key_exists($place, $this->nationalities)) {
            return $this->nationalities[$place];
        } else {
            return $place;
        }
    }
}
