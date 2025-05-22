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

/**
 * Definitions for a census
 */
class CensusOfWales1911 extends CensusOfWales implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '02 APR 1911';
    }

    /**
     * The columns of the census.
     *
     * @return array<CensusColumnInterface>
     */
    public function columns(): array
    {
        return [
            new CensusColumnFullName($this, 'Name', 'Name and surname'),
            new CensusColumnRelationToHeadEnglish($this, 'Relation', 'Relation to head of household'),
            new CensusColumnAgeMale($this, 'AgeM', 'Age (males)'),
            new CensusColumnAgeFemale($this, 'AgeF', 'Age (females)'),
            new CensusColumnConditionEnglish($this, 'Condition', 'Condition'),
            new CensusColumnYearsMarried($this, 'YrM', 'Years married'),
            new CensusColumnChildrenBornAlive($this, 'ChA', 'Children born alive'),
            new CensusColumnChildrenLiving($this, 'ChL', 'Children who are still alive'),
            new CensusColumnChildrenDied($this, 'ChD', 'Children who have died'),
            new CensusColumnOccupation($this, 'Occupation', 'Rank, profession or occupation'),
            new CensusColumnNull($this, 'Ind', 'Industry'),
            new CensusColumnNull($this, 'Emp', 'Employer, worker or own account'),
            new CensusColumnNull($this, 'Home', 'Working at home'),
            new CensusColumnBirthPlace($this, 'Birthplace', 'Where born'),
            new CensusColumnNationality($this, 'Nat', 'Nationality'),
            new CensusColumnNull($this, 'Infirm', 'Infirmity'),
            new CensusColumnNull($this, 'Lang', 'Language spoken'),
        ];
    }
}
