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

use Fisharebest\Webtrees\TestCase;

/**
 * @covers \Fisharebest\Webtrees\Census\CensusOfCanada1911
 */
class CensusOfCanada1911Test extends TestCase
{
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfCanada1911();

        self::assertSame('Canada', $census->censusPlace());
        self::assertSame('01 JUN 1911', $census->censusDate());
    }

    public function testColumns(): void
    {
        $census  = new CensusOfCanada1911();
        $columns = $census->columns();

        self::assertCount(35, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[1]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[2]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[3]);
        self::assertInstanceOf(CensusColumnConditionCanada::class, $columns[4]);
        self::assertInstanceOf(CensusColumnBirthMonth::class, $columns[5]);
        self::assertInstanceOf(CensusColumnBirthYear::class, $columns[6]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[7]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNationality::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[14]);
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
        self::assertInstanceOf(CensusColumnNull::class, $columns[26]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[27]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[28]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[29]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[30]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[31]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[32]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[33]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[34]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Address', $columns[1]->abbreviation());
        self::assertSame('Sex', $columns[2]->abbreviation());
        self::assertSame('Relation', $columns[3]->abbreviation());
        self::assertSame('S/M/W/D/L', $columns[4]->abbreviation());
        self::assertSame('Month', $columns[5]->abbreviation());
        self::assertSame('Year', $columns[6]->abbreviation());
        self::assertSame('Age', $columns[7]->abbreviation());
        self::assertSame('Birth Loc', $columns[8]->abbreviation());
        self::assertSame('Yr. immigrated', $columns[9]->abbreviation());
        self::assertSame('Yr. naturalized', $columns[10]->abbreviation());
        self::assertSame('Origin', $columns[11]->abbreviation());
        self::assertSame('Nationality', $columns[12]->abbreviation());
        self::assertSame('Religion', $columns[13]->abbreviation());
        self::assertSame('Occupation', $columns[14]->abbreviation());
        self::assertSame('Means', $columns[15]->abbreviation());
        self::assertSame('Employer', $columns[16]->abbreviation());
        self::assertSame('Employee', $columns[17]->abbreviation());
        self::assertSame('Work on OwnAcct', $columns[18]->abbreviation());
        self::assertSame('Where employed', $columns[19]->abbreviation());
        self::assertSame('Weeks employed', $columns[20]->abbreviation());
        self::assertSame('Weeks other', $columns[21]->abbreviation());
        self::assertSame('Hrs worked', $columns[22]->abbreviation());
        self::assertSame('Hrs at other', $columns[23]->abbreviation());
        self::assertSame('Earned 1910 $', $columns[24]->abbreviation());
        self::assertSame('Earned at other 1910 $', $columns[25]->abbreviation());
        self::assertSame('Rate hr-cents', $columns[26]->abbreviation());
        self::assertSame('Life Ins $', $columns[27]->abbreviation());
        self::assertSame('Accident/sick Ins $', $columns[28]->abbreviation());
        self::assertSame('Ins Cost $', $columns[29]->abbreviation());
        self::assertSame('Ms school', $columns[30]->abbreviation());
        self::assertSame('Read', $columns[31]->abbreviation());
        self::assertSame('Write', $columns[32]->abbreviation());
        self::assertSame('Language', $columns[33]->abbreviation());
        self::assertSame('Edu cost', $columns[34]->abbreviation());

        self::assertSame('Name of each person in family, household or institution', $columns[0]->title());
        self::assertSame('Place of Habitation', $columns[1]->title());
        self::assertSame('Sex', $columns[2]->title());
        self::assertSame('Relationship to Head of Family or household', $columns[3]->title());
        self::assertSame('Single, Married, Widowed, Divorced or Legally Separated', $columns[4]->title());
        self::assertSame('Month of birth', $columns[5]->title());
        self::assertSame('Year of birth', $columns[6]->title());
        self::assertSame('Age at last birthday - on June 1, 1911', $columns[7]->title());
        self::assertSame('Country or Place of Birth', $columns[8]->title());
        self::assertSame('Year of immigration to Canada, if an immigrant', $columns[9]->title());
        self::assertSame('Year of naturalization, if formerly an alien', $columns[10]->title());
        self::assertSame('Racial or tribal origin', $columns[11]->title());
        self::assertSame('Nationality', $columns[12]->title());
        self::assertSame('Religion', $columns[13]->title());
        self::assertSame('Chief occupation or trade', $columns[14]->title());
        self::assertSame('Living on own means', $columns[15]->title());
        self::assertSame('Employer', $columns[16]->title());
        self::assertSame('Employee', $columns[17]->title());
        self::assertSame('Working on own account', $columns[18]->title());
        self::assertSame('State where person is employed, as "on Farm," "in Woolen Mill," "at Foundry Shop," "in Drug Store," etc.', $columns[19]->title());
        self::assertSame('Weeks employed in 1910 at chief occupation or trade', $columns[20]->title());
        self::assertSame('Weeks employed in 1910 at other than chief occupation or trade, if any', $columns[21]->title());
        self::assertSame('Hours of working time per week at chief occupation', $columns[22]->title());
        self::assertSame('Hours of working time per week at other occupation, if any', $columns[23]->title());
        self::assertSame('Total earnings in 1910 from chief occupation or trade', $columns[24]->title());
        self::assertSame('Total earnings in 1910 from other than chief occupation or trade, if any', $columns[25]->title());
        self::assertSame('Rate of earnings per hour when employed by the hour-cents', $columns[26]->title());
        self::assertSame('Upon life $, as of June 1, 1911', $columns[27]->title());
        self::assertSame('Insurance $ Against accident or sickness, as of June 1, 1911', $columns[28]->title());
        self::assertSame('Cost of insurance in census year $', $columns[29]->title());
        self::assertSame('Months at school in 1910 for individuals aged 5-21 years', $columns[30]->title());
        self::assertSame('Can read', $columns[31]->title());
        self::assertSame('Can write', $columns[32]->title());
        self::assertSame('Language commonly spoken, E and/or F', $columns[33]->title());
        self::assertSame('Cost of education in 1910 for persons over 16 Years of age', $columns[34]->title());
    }
}
