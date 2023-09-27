<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
class CensusOfCanadaPraries1916 extends CensusOfCanada implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '01 JUN 1916';
    }

    /**
     * The columns of the census.
     *
     * @return array<CensusColumnInterface>
     */
    public function columns(): array
    {
        return [
            new CensusColumnFullName($this, 'Name', 'Name of each person in family, household or institution'),
            new CensusColumnNull($this, 'Mil', 'Military Service'),
            new CensusColumnRelationToHeadEnglish($this, 'Relation', 'Relationship to Head of Family or household'),
            new CensusColumnSexMF($this, 'Sex', 'Sex'),
            new CensusColumnConditionCanada($this, 'S/M/W/D/L', 'Single, Married, Widowed, Divorced or Legally Separated'),
            new CensusColumnAge($this, 'Age', 'Age at last birthday - on June 1, 1916'),
            new CensusColumnBirthPlaceSimple($this, 'Birth Loc', 'Country or place of birth'),
            new CensusColumnNull($this, 'Religion', 'Religion'),
            new CensusColumnNull($this, 'Yr. immigrated', 'Year of immigration to Canada'),
            new CensusColumnNull($this, 'Nationality', 'Nationality'),
            new CensusColumnNull($this, 'Origin', 'Racial or tribal origin'),
            new CensusColumnNull($this, 'English', 'Can speak English'),
            new CensusColumnNull($this, 'French', 'Can speak French'),
            new CensusColumnNull($this, 'Language', 'Mother tongue'),
            new CensusColumnNull($this, 'Read', 'Can read'),
            new CensusColumnNull($this, 'Write', 'Can write'),
            new CensusColumnOccupation($this, 'Occupation', 'Chief occupation or trade'),
            new CensusColumnNull($this, 'E/W/OA', 'Employer "e", Employee or worker "w", Working on own account "o.a."'),
            new CensusColumnNull($this, 'Where employed', 'State where person was employed as "on farm", "in cotton mill", "in foundry", "in dry goods store", "in saw-mill", etc.'),
        ];
    }
}
