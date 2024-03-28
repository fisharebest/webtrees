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
class CensusOfEngland1921 extends CensusOfEngland implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '19 JUN 1921';
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
			new CensusColumnRelationToHead($this, 'Relation', 'Relationship to head of household'),
			new CensusColumnAge($this, 'Age', 'Age (Y/M)'),
			new CensusColumnSexMF($this, 'Sex', 'Male or Female'),
			new CensusColumnConditionEnglish($this, 'Condition', 'Marriage or Orphanhood'),
			new CensusColumnBirthPlace($this, 'Birthplace', 'Where born in UK', 'width: 100px;'),
			new CensusColumnNationality($this, 'Nat', 'Where born if not in UK'),
			new CensusColumnOccupation($this, 'Occupation', 'Personal Occupation or Schooling', 'width: 100px;'),
			new CensusColumnNull($this, 'Emp', 'Employment'),
			new CensusColumnNull($this, 'Work place', 'Place of Work'),
			new CensusColumnNull($this, 'ChL', 'No. of living children, total and by age'),
        ];
    }
}
