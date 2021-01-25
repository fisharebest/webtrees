<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
 * Test harness for the class CensusOfUnitedStates1920
 */
class CensusOfUnitedStates1920Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1920
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1920();

        self::assertSame('United States', $census->censusPlace());
        self::assertSame('JAN 1920', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1920
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1920();
        $columns = $census->columns();

        self::assertCount(24, $columns);
        self::assertInstanceOf(CensusColumnSurnameGivenNameInitial::class, $columns[0]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[1]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[2]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[3]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[4]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[5]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[6]);
        self::assertInstanceOf(CensusColumnConditionUs::class, $columns[7]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);
        self::assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[18]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[19]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[20]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[21]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[22]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[23]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Relation', $columns[1]->abbreviation());
        self::assertSame('Home', $columns[2]->abbreviation());
        self::assertSame('Mort', $columns[3]->abbreviation());
        self::assertSame('Sex', $columns[4]->abbreviation());
        self::assertSame('Race', $columns[5]->abbreviation());
        self::assertSame('Age', $columns[6]->abbreviation());
        self::assertSame('Condition', $columns[7]->abbreviation());
        self::assertSame('Imm', $columns[8]->abbreviation());
        self::assertSame('Nat', $columns[9]->abbreviation());
        self::assertSame('NatY', $columns[10]->abbreviation());
        self::assertSame('School', $columns[11]->abbreviation());
        self::assertSame('R', $columns[12]->abbreviation());
        self::assertSame('W', $columns[13]->abbreviation());
        self::assertSame('BP', $columns[14]->abbreviation());
        self::assertSame('Lang', $columns[15]->abbreviation());
        self::assertSame('FBP', $columns[16]->abbreviation());
        self::assertSame('Father lang', $columns[17]->abbreviation());
        self::assertSame('MBP', $columns[18]->abbreviation());
        self::assertSame('Mother lang', $columns[19]->abbreviation());
        self::assertSame('Eng', $columns[20]->abbreviation());
        self::assertSame('Occupation', $columns[21]->abbreviation());
        self::assertSame('Ind', $columns[22]->abbreviation());
        self::assertSame('Emp', $columns[23]->abbreviation());

        self::assertSame('Name', $columns[0]->title());
        self::assertSame('Relationship of each person to the head of the family', $columns[1]->title());
        self::assertSame('Owned or rented', $columns[2]->title());
        self::assertSame('If owned, free or mortgaged', $columns[3]->title());
        self::assertSame('Sex', $columns[4]->title());
        self::assertSame('Color or race', $columns[5]->title());
        self::assertSame('Age at last birthday', $columns[6]->title());
        self::assertSame('Whether single, married, widowed, or divorced', $columns[7]->title());
        self::assertSame('Year of immigration to the United States', $columns[8]->title());
        self::assertSame('Naturalized or alien', $columns[9]->title());
        self::assertSame('If naturalized, year of naturalization', $columns[10]->title());
        self::assertSame('Attended school since Sept. 1, 1919', $columns[11]->title());
        self::assertSame('Whether able to read', $columns[12]->title());
        self::assertSame('Whether able to write', $columns[13]->title());
        self::assertSame('Place of birth', $columns[14]->title());
        self::assertSame('Mother tongue', $columns[15]->title());
        self::assertSame('Place of birth of father', $columns[16]->title());
        self::assertSame('Mother tongue of father', $columns[17]->title());
        self::assertSame('Place of birth of mother', $columns[18]->title());
        self::assertSame('Mother tongue of mother', $columns[19]->title());
        self::assertSame('Whether able to speak English', $columns[20]->title());
        self::assertSame('Trade, profession, or particular kind of work done', $columns[21]->title());
        self::assertSame('Industry, business of establishment in which at work', $columns[22]->title());
        self::assertSame('Employer, salary or wage worker, or work on own account', $columns[23]->title());
    }
}
