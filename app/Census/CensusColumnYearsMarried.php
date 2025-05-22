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

use Fisharebest\Webtrees\Age;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;

/**
 * For how many years has the individual been married.
 */
class CensusColumnYearsMarried extends AbstractCensusColumn implements CensusColumnInterface
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
        $marriage_date = null;

        foreach ($individual->spouseFamilies() as $family) {
            foreach ($family->facts(['MARR'], true) as $fact) {
                if ($fact->date()->isOK() && Date::compare($fact->date(), $this->date()) <= 0) {
                    $marriage_date = $fact->date();
                }
            }
        }

        if ($marriage_date === null) {
            return '';
        }

        $age = new Age($marriage_date, $this->date());

        return I18N::number($age->ageYears());
    }
}
