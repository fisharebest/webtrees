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
class CensusOfUnitedStates1950 extends CensusOfUnitedStates implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return 'APR 1950';
    }

    /**
     * The columns of the census.
     *
     * @return array<CensusColumnInterface>
     */
    public function columns(): array
    {
        return [
            new CensusColumnNull($this, 'Street', 'Name of street,avenue,or road'),
            new CensusColumnNull($this, 'Number', 'House (and apartment) number'),
            new CensusColumnNull($this, 'Serial', 'Serial number of dwelling unit'),
            new CensusColumnNull($this, 'Farm', 'Is this house on a farm (or ranch)?'),
            new CensusColumnNull($this, 'Acres', 'If No in item 4-Is this house on a place of three or more acres?'),
            new CensusColumnFullName($this, 'Name', 'What is the name of the had of this household? What are the names of all other personsl who live here?'),
            new CensusColumnRelationToHeadEnglish($this, 'Relation', 'Enter relationship of person to head of the household'),
            new CensusColumnNull($this, 'Race', 'White(W) Negro(Neg) American Indian(Ind) Japanese(Jap) Chinese(Chi) Filipino(Fil) Other race-spell out'),
            new CensusColumnSexMF($this, 'Sex', 'Sex-Male (M),Female (F)'),
            new CensusColumnAge($this, 'Age', 'How old was he on his last birthday?'),
            new CensusColumnConditionUs($this, 'Cond', 'Is he now married,widowed,divorced,separated,or never married?'),
            new CensusColumnBirthPlaceSimple($this, 'BP', 'What State (or foreign country) was he born in?'),
            new CensusColumnNull($this, 'Nat', 'If foreign born-Is he naturalized?'),
            new CensusColumnNull($this, 'Type', 'What was this person doing most of last week-working,keeping house,or something else?'),
            new CensusColumnNull($this, 'AnyWork', 'If H or Ot in item 15-Did this person do any work at all last week,not counting work around the house?'),
            new CensusColumnNull($this, 'Seeking', 'If No in item 16-Was this person looking for work?'),
            new CensusColumnNull($this, 'Employed', 'If No in item 17-Even though he didn’t work last week,does he have a job or business?'),
            new CensusColumnNull($this, 'Hours', 'If Wk in item 15 or Yes in item 16-How many hours did he work last week?'),
            new CensusColumnOccupation($this, 'Occupation', 'What kind of work was he doing?'),
            new CensusColumnNull($this, 'Industry', 'What kind of business or industry was he working in?'),
            new CensusColumnNull($this, 'Class', 'Class of worker'),
        ];
    }
}
