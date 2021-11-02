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

/**
 * Definitions for a census
 */
class CensusOfUnitedStates extends Census implements CensusPlaceInterface
{
    /**
     * All available censuses for this census place.
     *
     * @return array<CensusInterface>
     */
    public function allCensusDates(): array
    {
        return [
            new CensusOfUnitedStates1790(),
            new CensusOfUnitedStates1800(),
            new CensusOfUnitedStates1810(),
            new CensusOfUnitedStates1820(),
            new CensusOfUnitedStates1830(),
            new CensusOfUnitedStates1840(),
            new CensusOfUnitedStates1850(),
            new CensusOfUnitedStates1860(),
            new CensusOfUnitedStates1870(),
            new CensusOfUnitedStates1880(),
            new CensusOfUnitedStates1890(),
            new CensusOfUnitedStates1900(),
            new CensusOfUnitedStates1910(),
            new CensusOfUnitedStates1920(),
            new CensusOfUnitedStates1930(),
            new CensusOfUnitedStates1940(),
        ];
    }

    /**
     * Where did this census occur, in GEDCOM format.
     *
     * @return string
     */
    public function censusPlace(): string
    {
        return 'United States';
    }

    /**
     * In which language was this census written.
     *
     * @return string
     */
    public function censusLanguage(): string
    {
        return 'en-US';
    }
}
