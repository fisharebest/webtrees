<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

        $this->assertSame('United States', $census->censusPlace());
        $this->assertSame('JAN 1920', $census->censusDate());
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

        $this->assertCount(24, $columns);
        $this->assertInstanceOf(CensusColumnSurnameGivenNameInitial::class, $columns[0]);
        $this->assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[1]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[2]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[3]);
        $this->assertInstanceOf(CensusColumnSexMF::class, $columns[4]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[5]);
        $this->assertInstanceOf(CensusColumnAge::class, $columns[6]);
        $this->assertInstanceOf(CensusColumnConditionUs::class, $columns[7]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[8]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[9]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[10]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[11]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[12]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[13]);
        $this->assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[14]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[15]);
        $this->assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[16]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[17]);
        $this->assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[18]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[19]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[20]);
        $this->assertInstanceOf(CensusColumnOccupation::class, $columns[21]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[22]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[23]);

        $this->assertSame('Name', $columns[0]->abbreviation());
        $this->assertSame('Relation', $columns[1]->abbreviation());
        $this->assertSame('Home', $columns[2]->abbreviation());
        $this->assertSame('Mort', $columns[3]->abbreviation());
        $this->assertSame('Sex', $columns[4]->abbreviation());
        $this->assertSame('Race', $columns[5]->abbreviation());
        $this->assertSame('Age', $columns[6]->abbreviation());
        $this->assertSame('Condition', $columns[7]->abbreviation());
        $this->assertSame('Imm', $columns[8]->abbreviation());
        $this->assertSame('Nat', $columns[9]->abbreviation());
        $this->assertSame('NatY', $columns[10]->abbreviation());
        $this->assertSame('School', $columns[11]->abbreviation());
        $this->assertSame('R', $columns[12]->abbreviation());
        $this->assertSame('W', $columns[13]->abbreviation());
        $this->assertSame('BP', $columns[14]->abbreviation());
        $this->assertSame('Lang', $columns[15]->abbreviation());
        $this->assertSame('FBP', $columns[16]->abbreviation());
        $this->assertSame('Father lang', $columns[17]->abbreviation());
        $this->assertSame('MBP', $columns[18]->abbreviation());
        $this->assertSame('Mother lang', $columns[19]->abbreviation());
        $this->assertSame('Eng', $columns[20]->abbreviation());
        $this->assertSame('Occupation', $columns[21]->abbreviation());
        $this->assertSame('Ind', $columns[22]->abbreviation());
        $this->assertSame('Emp', $columns[23]->abbreviation());

        $this->assertSame('Name', $columns[0]->title());
        $this->assertSame('Relationship of each person to the head of the family', $columns[1]->title());
        $this->assertSame('Owned or rented', $columns[2]->title());
        $this->assertSame('If owned, free or mortgaged', $columns[3]->title());
        $this->assertSame('Sex', $columns[4]->title());
        $this->assertSame('Color or race', $columns[5]->title());
        $this->assertSame('Age at last birthday', $columns[6]->title());
        $this->assertSame('Whether single, married, widowed, or divorced', $columns[7]->title());
        $this->assertSame('Year of immigration to the United States', $columns[8]->title());
        $this->assertSame('Naturalized or alien', $columns[9]->title());
        $this->assertSame('If naturalized, year of naturalization', $columns[10]->title());
        $this->assertSame('Attended school since Sept. 1, 1919', $columns[11]->title());
        $this->assertSame('Whether able to read', $columns[12]->title());
        $this->assertSame('Whether able to write', $columns[13]->title());
        $this->assertSame('Place of birth', $columns[14]->title());
        $this->assertSame('Mother tongue', $columns[15]->title());
        $this->assertSame('Place of birth of father', $columns[16]->title());
        $this->assertSame('Mother tongue of father', $columns[17]->title());
        $this->assertSame('Place of birth of mother', $columns[18]->title());
        $this->assertSame('Mother tongue of mother', $columns[19]->title());
        $this->assertSame('Whether able to speak English', $columns[20]->title());
        $this->assertSame('Trade, profession, or particular kind of work done', $columns[21]->title());
        $this->assertSame('Industry, business of establishment in which at work', $columns[22]->title());
        $this->assertSame('Employer, salary or wage worker, or work on own account', $columns[23]->title());
    }
}
