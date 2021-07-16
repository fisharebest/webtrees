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
class CensusOfCanada1861 extends CensusOfCanada implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '14 JAN 1861';
    }

    /**
     * The columns of the census.
     *
     * @return CensusColumnInterface[]
     */
    public function columns(): array
    {
        return [
            new CensusColumnFullName($this, 'Name', 'Name of inmates'),
            new CensusColumnOccupation($this, 'Occupation', 'Profession, trade or occupation'),
            new CensusColumnBirthPlaceSimple($this, 'Birth Loc', 'Place of birth. F indicates that the person was born of Canadian parents.'),
            new CensusColumnNull($this, 'Recent Married', 'Married within the last twelve months'),
            new CensusColumnNull($this, 'Religion', 'Religion'),
            new CensusColumnAgeNextBirthDay($this, 'Next BirthDay age', 'Age at NEXT birthday'),
            new CensusColumnSexM($this, 'Sex: male', 'Sex: male'),
            new CensusColumnSexF($this, 'Sex: female', 'Sex: female'),
            new CensusColumnConditionCanadaMarriedSingle($this, 'M/S', 'Married or single'),
            new CensusColumnConditionCanadaWidowedMale($this, 'Widowers', 'Widowers'),
            new CensusColumnConditionCanadaWidowedFemale($this, 'Widows', 'Widows'),
        ];
    }
}
