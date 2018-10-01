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
declare(strict_types=1);


namespace Fisharebest\Webtrees\Census;

/**
 * Definitions for a census
 */
class CensusOfUnitedStates1850 extends CensusOfUnitedStates implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '01 JUN 1850';
    }

    /**
     * The columns of the census.
     *
     * @return CensusColumnInterface[]
     */
    public function columns(): array
    {
        return [
            new CensusColumnFullName($this, 'Name', 'Name'),
            new CensusColumnAge($this, 'Age', 'Age'),
            new CensusColumnSexMF($this, 'Sex', 'Sex'),
            new CensusColumnNull($this, 'Color', 'White, black, or mulatto'),
            new CensusColumnOccupation($this, 'Occupation', 'Profession, occupation, or trade'),
            new CensusColumnNull($this, 'RE', 'Value of real estate owned'),
            new CensusColumnBirthPlaceSimple($this, 'Birthplace', 'Place of birth, naming the state, territory, or country'),
            new CensusColumnMarriedWithinYear($this, 'Mar', 'Married within the year'),
            new CensusColumnNull($this, 'School', 'Attended school within the year'),
            new CensusColumnNull($this, 'R+W', 'Persons over 20 years of age who cannot read and write'),
            new CensusColumnNull($this, 'Infirm', 'Whether deaf and dumb, blind, insane, idiotic, pauper or convict'),
        ];
    }
}
