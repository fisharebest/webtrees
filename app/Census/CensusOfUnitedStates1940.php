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
class CensusOfUnitedStates1940 extends CensusOfUnitedStates implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return 'APR 1940';
    }

    /**
     * The columns of the census.
     *
     * @return array<CensusColumnInterface>
     */
    public function columns(): array
    {
        return [
            new CensusColumnNull($this, 'Street', 'Street,avenue,road,etc'),
            new CensusColumnNull($this, 'Number', 'House number (in cities and towns)'),
            new CensusColumnNull($this, 'Home', 'Home owned (O) or rented (R)'),
            new CensusColumnNull($this, 'Value', 'Value of home, if owned, or monthly rental if rented'),
            new CensusColumnNull($this, 'Farm', 'Does this household live on a farm?'),
            new CensusColumnFullName($this, 'Name', 'Name of each person whose usual place of residence on April 1, 1940, was in this household'),
            new CensusColumnRelationToHeadEnglish($this, 'Relation', 'Relationship of this person to the head of the household'),
            new CensusColumnSexMF($this, 'Sex', 'Sex-Male (M),Female (F)'),
            new CensusColumnNull($this, 'Race', 'Color or race'),
            new CensusColumnAge($this, 'Age', 'Age at last birthday'),
            new CensusColumnConditionUs($this, 'Cond', 'Marital Status-Single (S), Married (M), Widowed (W), Divorced (D)'),
            new CensusColumnNull($this, 'School', 'Attended school or college any time since March 1, 1940?'),
            new CensusColumnNull($this, 'Grade', 'Highest grade of school completed'),
            new CensusColumnBirthPlaceSimple($this, 'BP', 'Place of birth'),
            new CensusColumnNull($this, 'Citizen', 'Citizenship of the foreign born'),
            new CensusColumnNull($this, 'City', 'City, town, or village having 2,500 or more inhabitants. Enter "R" for all other places.'),
            new CensusColumnNull($this, 'County', 'County'),
            new CensusColumnNull($this, 'State', 'State (or Territory or foreign country)'),
            new CensusColumnNull($this, 'OnFarm', 'On a farm?'),
            new CensusColumnNull($this, 'Work', 'Was this person AT WORK for pay or profit in private or nonemergency Govt. work during week of March 24-30?'),
            new CensusColumnNull($this, 'Emerg', 'If not, was he at work on, or assigned to, public EMERGENCY WORK (WPA,NYA,CCC,etc.) during week of March 24-30?'),
            new CensusColumnNull($this, 'Seeking', 'Was this person SEEKING WORK?'),
            new CensusColumnNull($this, 'Job', 'If not seeking work, did he HAVE A JOB, business, etc.?'),
            new CensusColumnNull($this, 'Type', 'Indicate whether engaged in home housework (H), in school (S), unable to work (U), or other (Ot)'),
            new CensusColumnNull($this, 'Hours', 'Numbers of hours worked during week of March 24-30, 1940'),
            new CensusColumnNull($this, 'Unemp', 'Duration of unemployment up to March 30, 1940-in weeks'),
            new CensusColumnOccupation($this, 'Occupation', 'Trade, profession, or particular kind of work'),
            new CensusColumnNull($this, 'Industry', 'Industry or business'),
            new CensusColumnNull($this, 'Weeks', 'Number of weeks worked in 1939 (Equivalent full-time weeks)'),
            new CensusColumnNull($this, 'Salary', 'Amount of money wages or salary received (including commissions)'),
            new CensusColumnNull($this, 'Extra', 'Did this person receive income of $50 or more from sources other than money wages or salary?'),
        ];
    }
}
