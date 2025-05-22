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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Individual;

/**
 * The nationality of the individual.
 */
class CensusColumnNationality extends AbstractCensusColumn implements CensusColumnInterface
{
    // Convert a country name to a nationality
    private const array NATIONALITIES = [
        'England'     => 'British',
        'Scotland'    => 'British',
        'Wales'       => 'British',
        'Deutschland' => 'Deutsch',
        'Canada'      => 'Canadian',
    ];

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
        $place = $individual->getBirthPlace()->gedcomName();

        // No birthplace?  Assume born in the same country.
        if ($place === '') {
            $place = $this->place();
        }

        // Did we emigrate or naturalise?
        foreach ($individual->facts(['IMMI', 'EMIG', 'NATU'], true) as $fact) {
            if (Date::compare($fact->date(), $this->date()) <= 0) {
                $place = $fact->place()->gedcomName();
            }
        }

        $place = $this->lastPartOfPlace($place);

        return self::NATIONALITIES[$place] ?? $place;
    }
}
