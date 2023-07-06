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

use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class CensusOfUnitedStates1890
 */
class CensusOfUnitedStates1890Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1890
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1890();

        self::assertSame('United States', $census->censusPlace());
        self::assertSame('02 JUN 1890', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1890
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1890();
        $columns = $census->columns();

        self::assertCount(24, $columns);
        self::assertInstanceOf(CensusColumnGivenNameInitial::class, $columns[0]);
        self::assertInstanceOf(CensusColumnSurname::class, $columns[1]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[2]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[4]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[5]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[6]);
        self::assertInstanceOf(CensusColumnConditionUs::class, $columns[7]);
        self::assertInstanceOf(CensusColumnMonthIfMarriedWithinYear::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[10]);
        self::assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[11]);
        self::assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[18]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[19]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[20]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[21]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[22]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[23]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Surname', $columns[1]->abbreviation());
        self::assertSame('CW', $columns[2]->abbreviation());
        self::assertSame('Relation', $columns[3]->abbreviation());
        self::assertSame('Race', $columns[4]->abbreviation());
        self::assertSame('Sex', $columns[5]->abbreviation());
        self::assertSame('Age', $columns[6]->abbreviation());
        self::assertSame('Cond', $columns[7]->abbreviation());
        self::assertSame('Mar', $columns[8]->abbreviation());
        self::assertSame('Chil', $columns[9]->abbreviation());
        self::assertSame('BP', $columns[10]->abbreviation());
        self::assertSame('FBP', $columns[11]->abbreviation());
        self::assertSame('MBP', $columns[12]->abbreviation());
        self::assertSame('US', $columns[13]->abbreviation());
        self::assertSame('Nat', $columns[14]->abbreviation());
        self::assertSame('Papers', $columns[15]->abbreviation());
        self::assertSame('Occupation', $columns[16]->abbreviation());
        self::assertSame('Unemp', $columns[17]->abbreviation());
        self::assertSame('Read', $columns[18]->abbreviation());
        self::assertSame('Write', $columns[19]->abbreviation());
        self::assertSame('Eng', $columns[20]->abbreviation());
        self::assertSame('Disease', $columns[21]->abbreviation());
        self::assertSame('Infirm', $columns[22]->abbreviation());
        self::assertSame('Prisoner', $columns[23]->abbreviation());

        self::assertSame('Christian name in full, and initial of middle name', $columns[0]->title());
        self::assertSame('Surname', $columns[1]->title());
        self::assertSame('Whether a soldier, sailor or marine during the civil war (U.S. or Conf.), or widow of such person', $columns[2]->title());
        self::assertSame('Relation to head of family', $columns[3]->title());
        self::assertSame('Whether white, black, mulatto, quadroon, octoroon, Chinese, Japanese, or Indian', $columns[4]->title());
        self::assertSame('Sex', $columns[5]->title());
        self::assertSame('Age at nearest birthday. If under one year, give age in months', $columns[6]->title());
        self::assertSame('Whether single, married, widowed, or divorced', $columns[7]->title());
        self::assertSame('Whether married during the census year (June 1, 1889, to May 31, 1890)', $columns[8]->title());
        self::assertSame('Mother of how many children, and number of these children living', $columns[9]->title());
        self::assertSame('Place of birth', $columns[10]->title());
        self::assertSame('Place of birth of father', $columns[11]->title());
        self::assertSame('Place of birth of mother', $columns[12]->title());
        self::assertSame('Number of years in the United States', $columns[13]->title());
        self::assertSame('Whether naturalized', $columns[14]->title());
        self::assertSame('Whether naturalization papers have been taken out', $columns[15]->title());
        self::assertSame('Profession, trade, occupation', $columns[16]->title());
        self::assertSame('Months unemployed during the census year (June 1, 1889, to May 31, 1890)', $columns[17]->title());
        self::assertSame('Able to read', $columns[18]->title());
        self::assertSame('Able to write', $columns[19]->title());
        self::assertSame('Able to speak English. If not the language or dialect spoken', $columns[20]->title());
        self::assertSame('Whether suffering from acute or chronic disease, with name of disease and length of time afflicted', $columns[21]->title());
        self::assertSame('Whether defective in mind, sight, hearing, or speech, or whether crippled, maimed, or deformed, with name of defect', $columns[22]->title());
        self::assertSame('Whether a prisoner, convict, homeless child, or pauper', $columns[23]->title());
    }
}
