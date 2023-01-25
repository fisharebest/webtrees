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

        self::assertSame('United States', $census->censusPlace());
        self::assertSame('APR 1940', $census->censusDate());
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

        self::assertCount(31, $columns);
        self::assertInstanceOf(CensusColumnNull::class, $columns[0]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[1]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[2]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[4]);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[5]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[6]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[7]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[8]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[9]);
        self::assertInstanceOf(CensusColumnConditionUs::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[18]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[19]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[20]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[21]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[22]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[23]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[24]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[25]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[26]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[27]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[28]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[29]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[30]);

        self::assertSame('Street', $columns[0]->abbreviation());
        self::assertSame('Number', $columns[1]->abbreviation());
        self::assertSame('Home', $columns[2]->abbreviation());
        self::assertSame('Value', $columns[3]->abbreviation());
        self::assertSame('Farm', $columns[4]->abbreviation());
        self::assertSame('Name', $columns[5]->abbreviation());
        self::assertSame('Relation', $columns[6]->abbreviation());
        self::assertSame('Sex', $columns[7]->abbreviation());
        self::assertSame('Race', $columns[8]->abbreviation());
        self::assertSame('Age', $columns[9]->abbreviation());
        self::assertSame('Cond', $columns[10]->abbreviation());
        self::assertSame('School', $columns[11]->abbreviation());
        self::assertSame('Grade', $columns[12]->abbreviation());
        self::assertSame('BP', $columns[13]->abbreviation());
        self::assertSame('Citizen', $columns[14]->abbreviation());
        self::assertSame('City', $columns[15]->abbreviation());
        self::assertSame('County', $columns[16]->abbreviation());
        self::assertSame('State', $columns[17]->abbreviation());
        self::assertSame('OnFarm', $columns[18]->abbreviation());
        self::assertSame('Work', $columns[19]->abbreviation());
        self::assertSame('Emerg', $columns[20]->abbreviation());
        self::assertSame('Seeking', $columns[21]->abbreviation());
        self::assertSame('Job', $columns[22]->abbreviation());
        self::assertSame('Type', $columns[23]->abbreviation());
        self::assertSame('Hours', $columns[24]->abbreviation());
        self::assertSame('Unemp', $columns[25]->abbreviation());
        self::assertSame('Occupation', $columns[26]->abbreviation());
        self::assertSame('Industry', $columns[27]->abbreviation());
        self::assertSame('Weeks', $columns[28]->abbreviation());
        self::assertSame('Salary', $columns[29]->abbreviation());
        self::assertSame('Extra', $columns[30]->abbreviation());

        self::assertSame('Street,avenue,road,etc', $columns[0]->title());
        self::assertSame('House number (in cities and towns)', $columns[1]->title());
        self::assertSame('Home owned (O) or rented (R)', $columns[2]->title());
        self::assertSame('Value of home, if owned, or monthly rental if rented', $columns[3]->title());
        self::assertSame('Does this household live on a farm?', $columns[4]->title());
        self::assertSame('Name of each person whose usual place of residence on April 1, 1940, was in this household', $columns[5]->title());
        self::assertSame('Relationship of this person to the head of the household', $columns[6]->title());
        self::assertSame('Sex-Male (M),Female (F)', $columns[7]->title());
        self::assertSame('Color or race', $columns[8]->title());
        self::assertSame('Age at last birthday', $columns[9]->title());
        self::assertSame('Marital Status-Single (S), Married (M), Widowed (W), Divorced (D)', $columns[10]->title());
        self::assertSame('Attended school or college any time since March 1, 1940?', $columns[11]->title());
        self::assertSame('Highest grade of school completed', $columns[12]->title());
        self::assertSame('Place of birth', $columns[13]->title());
        self::assertSame('Citizenship of the foreign born', $columns[14]->title());
        self::assertSame('City, town, or village having 2,500 or more inhabitants. Enter "R" for all other places.', $columns[15]->title());
        self::assertSame('County', $columns[16]->title());
        self::assertSame('State (or Territory or foreign country)', $columns[17]->title());
        self::assertSame('On a farm?', $columns[18]->title());
        self::assertSame('Was this person AT WORK for pay or profit in private or nonemergency Govt. work during week of March 24-30?', $columns[19]->title());
        self::assertSame('If not, was he at work on, or assigned to, public EMERGENCY WORK (WPA,NYA,CCC,etc.) during week of March 24-30?', $columns[20]->title());
        self::assertSame('Was this person SEEKING WORK?', $columns[21]->title());
        self::assertSame('If not seeking work, did he HAVE A JOB, business, etc.?', $columns[22]->title());
        self::assertSame('Indicate whether engaged in home housework (H), in school (S), unable to work (U), or other (Ot)', $columns[23]->title());
        self::assertSame('Numbers of hours worked during week of March 24-30, 1940', $columns[24]->title());
        self::assertSame('Duration of unemployment up to March 30, 1940-in weeks', $columns[25]->title());
        self::assertSame('Trade, profession, or particular kind of work', $columns[26]->title());
        self::assertSame('Industry or business', $columns[27]->title());
        self::assertSame('Number of weeks worked in 1939 (Equivalent full-time weeks)', $columns[28]->title());
        self::assertSame('Amount of money wages or salary received (including commissions)', $columns[29]->title());
        self::assertSame('Did this person receive income of $50 or more from sources other than money wages or salary?', $columns[30]->title());
    }
}
