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
 * Test harness for the class CensusOfCanada1891
 */
class CensusOfCanada1891Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCanada1891
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfCanada1891();

        self::assertSame('Canada', $census->censusPlace());
        self::assertSame('06 APR 1891', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCanada1891
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns(): void
    {
        $census  = new CensusOfCanada1891();
        $columns = $census->columns();

        self::assertCount(20, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[1]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[2]);
        self::assertInstanceOf(CensusColumnConditionCanadaMarriedWidowed::class, $columns[3]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[4]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[5]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[6]);
        self::assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[7]);
        self::assertInstanceOf(CensusColumnMotherBirthPlaceSimple::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[18]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[19]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Sex', $columns[1]->abbreviation());
        self::assertSame('Age', $columns[2]->abbreviation());
        self::assertSame('M/W', $columns[3]->abbreviation());
        self::assertSame('Relation', $columns[4]->abbreviation());
        self::assertSame('Birth Loc', $columns[5]->abbreviation());
        self::assertSame('French', $columns[6]->abbreviation());
        self::assertSame('FBP', $columns[7]->abbreviation());
        self::assertSame('MBP', $columns[8]->abbreviation());
        self::assertSame('Religion', $columns[9]->abbreviation());
        self::assertSame('Occupation', $columns[10]->abbreviation());
        self::assertSame('Employers', $columns[11]->abbreviation());
        self::assertSame('Earner', $columns[12]->abbreviation());
        self::assertSame('UnEmp', $columns[13]->abbreviation());
        self::assertSame('AvgEmp', $columns[14]->abbreviation());
        self::assertSame('Read', $columns[15]->abbreviation());
        self::assertSame('Write', $columns[16]->abbreviation());
        self::assertSame('Deaf', $columns[17]->abbreviation());
        self::assertSame('Blind', $columns[18]->abbreviation());
        self::assertSame('Unsound', $columns[19]->abbreviation());

        self::assertSame('Name', $columns[0]->title());
        self::assertSame('Sex', $columns[1]->title());
        self::assertSame('Age at last birthday', $columns[2]->title());
        self::assertSame('Married or Widowed', $columns[3]->title());
        self::assertSame('Relationship to Head of Family', $columns[4]->title());
        self::assertSame('Country or Province of Birth', $columns[5]->title());
        self::assertSame('French Canadians', $columns[6]->title());
        self::assertSame('Place of birth of father', $columns[7]->title());
        self::assertSame('Place of birth of mother', $columns[8]->title());
        self::assertSame('Religion', $columns[9]->title());
        self::assertSame('Profession, Occupation, or Trade', $columns[10]->title());
        self::assertSame('Employers', $columns[11]->title());
        self::assertSame('Wage Earner', $columns[12]->title());
        self::assertSame('Unemployed during week preceeding Census', $columns[13]->title());
        self::assertSame('Employer to state average number of hands employed during year', $columns[14]->title());
        self::assertSame('Instruction - Read', $columns[15]->title());
        self::assertSame('Instruction - Write', $columns[16]->title());
        self::assertSame('Infirmities - Deaf and Dumb', $columns[17]->title());
        self::assertSame('Infirmities - Blind', $columns[18]->title());
        self::assertSame('Infirmities - Unsound Mind', $columns[19]->title());
    }
}
