<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class CensusOfUnitedStates1940
 */
class CensusOfUnitedStates1940Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1940
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1940();

        $this->assertSame('United States', $census->censusPlace());
        $this->assertSame('APR 1940', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1940
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1940();
        $columns = $census->columns();

        $this->assertCount(31, $columns);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[0]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[1]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[2]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[3]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[4]);
        $this->assertInstanceOf(CensusColumnFullName::class, $columns[5]);
        $this->assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[6]);
        $this->assertInstanceOf(CensusColumnSexMF::class, $columns[7]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[8]);
        $this->assertInstanceOf(CensusColumnAge::class, $columns[9]);
        $this->assertInstanceOf(CensusColumnConditionUs::class, $columns[10]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[11]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[12]);
        $this->assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[13]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[14]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[15]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[16]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[17]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[18]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[19]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[20]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[21]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[22]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[23]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[24]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[25]);
        $this->assertInstanceOf(CensusColumnOccupation::class, $columns[26]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[27]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[28]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[29]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[30]);

        $this->assertSame('Street', $columns[0]->abbreviation());
        $this->assertSame('Number', $columns[1]->abbreviation());
        $this->assertSame('Home', $columns[2]->abbreviation());
        $this->assertSame('Value', $columns[3]->abbreviation());
        $this->assertSame('Farm', $columns[4]->abbreviation());
        $this->assertSame('Name', $columns[5]->abbreviation());
        $this->assertSame('Relation', $columns[6]->abbreviation());
        $this->assertSame('Sex', $columns[7]->abbreviation());
        $this->assertSame('Race', $columns[8]->abbreviation());
        $this->assertSame('Age', $columns[9]->abbreviation());
        $this->assertSame('Cond', $columns[10]->abbreviation());
        $this->assertSame('School', $columns[11]->abbreviation());
        $this->assertSame('Grade', $columns[12]->abbreviation());
        $this->assertSame('BP', $columns[13]->abbreviation());
        $this->assertSame('Citizen', $columns[14]->abbreviation());
        $this->assertSame('City', $columns[15]->abbreviation());
        $this->assertSame('County', $columns[16]->abbreviation());
        $this->assertSame('State', $columns[17]->abbreviation());
        $this->assertSame('OnFarm', $columns[18]->abbreviation());
        $this->assertSame('Work', $columns[19]->abbreviation());
        $this->assertSame('Emerg', $columns[20]->abbreviation());
        $this->assertSame('Seeking', $columns[21]->abbreviation());
        $this->assertSame('Job', $columns[22]->abbreviation());
        $this->assertSame('Type', $columns[23]->abbreviation());
        $this->assertSame('Hours', $columns[24]->abbreviation());
        $this->assertSame('Unemp', $columns[25]->abbreviation());
        $this->assertSame('Occupation', $columns[26]->abbreviation());
        $this->assertSame('Industry', $columns[27]->abbreviation());
        $this->assertSame('Weeks', $columns[28]->abbreviation());
        $this->assertSame('Salary', $columns[29]->abbreviation());
        $this->assertSame('Extra', $columns[30]->abbreviation());

        $this->assertSame('Street,avenue,road,etc', $columns[0]->title());
        $this->assertSame('House number (in cities and towns)', $columns[1]->title());
        $this->assertSame('Home owned (O) or rented (R)', $columns[2]->title());
        $this->assertSame('Value of home, if owned, or monthly rental if rented', $columns[3]->title());
        $this->assertSame('Does this household live on a farm?', $columns[4]->title());
        $this->assertSame('Name of each person whose usual place of residence on April 1, 1940, was in this household', $columns[5]->title());
        $this->assertSame('Relationship of this person to the head of the household', $columns[6]->title());
        $this->assertSame('Sex-Male (M),Female (F)', $columns[7]->title());
        $this->assertSame('Color or race', $columns[8]->title());
        $this->assertSame('Age at last birthday', $columns[9]->title());
        $this->assertSame('Marital Status-Single (S), Married (M), Widowed (W), Divorced (D)', $columns[10]->title());
        $this->assertSame('Attended school or college any time since March 1, 1940?', $columns[11]->title());
        $this->assertSame('Highest grade of school completed', $columns[12]->title());
        $this->assertSame('Place of birth', $columns[13]->title());
        $this->assertSame('Citizenship of the foreign born', $columns[14]->title());
        $this->assertSame('City, town, or village having 2,500 or more inhabitants. Enter "R" for all other places.', $columns[15]->title());
        $this->assertSame('County', $columns[16]->title());
        $this->assertSame('State (or Territory or foreign country)', $columns[17]->title());
        $this->assertSame('On a farm?', $columns[18]->title());
        $this->assertSame('Was this person AT WORK for pay or profit in private or nonemergency Govt. work during week of March 24-30?', $columns[19]->title());
        $this->assertSame('If not, was he at work on, or assigned to, public EMERGENCY WORK (WPA,NYA,CCC,etc.) during week of March 24-30?', $columns[20]->title());
        $this->assertSame('Was this person SEEKING WORK?', $columns[21]->title());
        $this->assertSame('If not seeking work, did he HAVE A JOB, business, etc.?', $columns[22]->title());
        $this->assertSame('Indicate whether engaged in home housework (H), in school (S), unable to work (U), or other (Ot)', $columns[23]->title());
        $this->assertSame('Numbers of hours worked during week of March 24-30, 1940', $columns[24]->title());
        $this->assertSame('Duration of unemployment up to March 30, 1940-in weeks', $columns[25]->title());
        $this->assertSame('Trade, profession, or particular kind of work', $columns[26]->title());
        $this->assertSame('Industry or business', $columns[27]->title());
        $this->assertSame('Number of weeks worked in 1939 (Equivalent full-time weeks)', $columns[28]->title());
        $this->assertSame('Amount of money wages or salary received (including commissions)', $columns[29]->title());
        $this->assertSame('Did this person receive income of $50 or more from sources other than money wages or salary?', $columns[30]->title());
    }
}
