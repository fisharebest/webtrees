<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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
class CensusOfRhodeIsland1905 extends CensusOfRhodeIsland implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return 'JUN 1905';
    }

    /**
     * The columns of the census.
     *
     * @return array<CensusColumnInterface>
     */
    public function columns(): array
    {
        return [
            new CensusColumnSexMF($this, 'Sex', 'Sex'),
            new CensusColumnNull($this, 'Num', 'Number of people in the family'),
            new CensusColumnSurnameGivenNameInitial($this, 'Name', 'Name'),
            new CensusColumnRelationToHeadEnglish($this, 'Relation', 'Relationship to head of household'),
            new CensusColumnNull($this, 'Race', 'Color or race'),
            new CensusColumnAge($this, 'Age', 'Age at last birthday'),
            new CensusColumnConditionUs($this, 'Cond', 'Congugal Condition'),
            new CensusColumnBirthYear($this, 'Year', 'Year of Birth'),
            new CensusColumnBirthMonthDay($this, 'Month Day', 'Month Day'),
            new CensusColumnBirthPlaceSimple($this, 'BP', 'Place of birth'),
            new CensusColumnNull($this, 'N/F', 'Native or Foreign Born'),
            new CensusColumnNull($this, 'R', 'Read'),
            new CensusColumnNull($this, 'W', 'Write'),
            new CensusColumnNull($this, 'Imm', 'Year of immigration to the United States'),
            new CensusColumnNull($this, 'US yrs', 'Years in US'),
            new CensusColumnNull($this, 'RI yrs', 'Years resident of Rhode Island'),
            new CensusColumnNull($this, 'Town mnths', 'Months in current year as a Rhode Island resident'),
            new CensusColumnFatherBirthPlaceSimple($this, 'FBP', 'Place of birth of father of this person'),
            new CensusColumnMotherBirthPlaceSimple($this, 'MBP', 'Place of birth of mother of this person'),
            new CensusColumnOccupation($this, 'Occupation', 'Occupation'),
            new CensusColumnNull($this, 'Unemp', 'Months unemployed during Census Year'),
            new CensusColumnNull($this, 'Pen', 'Did you receive a pension'),
            new CensusColumnReligion($this, 'Rel', 'Religious preference'),
            new CensusColumnNull($this, 'Mil', 'Military or widow of military'),
            new CensusColumnNull($this, 'Nat', 'Naturalization information'),
            new CensusColumnNull($this, 'Vtr', 'Voter information'),
            new CensusColumnChildrenBornAlive($this, 'Chil born', 'Mother of how many children'),
            new CensusColumnChildrenLiving($this, 'Chil liv', 'Number of these children living on June 1 1905'),
        ];
    }
}
