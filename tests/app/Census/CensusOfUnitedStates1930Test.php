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
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CensusOfUnitedStates1930::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfUnitedStates1930Test extends TestCase
{
    /**
     * Test the census place and date
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1930();

        self::assertSame('United States', $census->censusPlace());
        self::assertSame('APR 1930', $census->censusDate());
    }

    /**
     * Test the census columns
     */
    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1930();
        $columns = $census->columns();

        self::assertCount(28, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[1]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[2]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[4]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[5]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[8]);
        self::assertInstanceOf(CensusColumnConditionUs::class, $columns[9]);
        self::assertInstanceOf(CensusColumnAgeMarried::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[13]);
        self::assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[14]);
        self::assertInstanceOf(CensusColumnMotherBirthPlaceSimple::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[18]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[19]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[20]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[21]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[22]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[23]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[24]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[25]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[26]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[27]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Relation', $columns[1]->abbreviation());
        self::assertSame('Home', $columns[2]->abbreviation());
        self::assertSame('V/R', $columns[3]->abbreviation());
        self::assertSame('Radio', $columns[4]->abbreviation());
        self::assertSame('Farm', $columns[5]->abbreviation());
        self::assertSame('Sex', $columns[6]->abbreviation());
        self::assertSame('Race', $columns[7]->abbreviation());
        self::assertSame('Age', $columns[8]->abbreviation());
        self::assertSame('Cond', $columns[9]->abbreviation());
        self::assertSame('AM', $columns[10]->abbreviation());
        self::assertSame('School', $columns[11]->abbreviation());
        self::assertSame('R/W', $columns[12]->abbreviation());
        self::assertSame('BP', $columns[13]->abbreviation());
        self::assertSame('FBP', $columns[14]->abbreviation());
        self::assertSame('MBP', $columns[15]->abbreviation());
        self::assertSame('Lang', $columns[16]->abbreviation());
        self::assertSame('Imm', $columns[17]->abbreviation());
        self::assertSame('Nat', $columns[18]->abbreviation());
        self::assertSame('Eng', $columns[19]->abbreviation());
        self::assertSame('Occupation', $columns[20]->abbreviation());
        self::assertSame('Industry', $columns[21]->abbreviation());
        self::assertSame('Code', $columns[22]->abbreviation());
        self::assertSame('Emp', $columns[23]->abbreviation());
        self::assertSame('Work', $columns[24]->abbreviation());
        self::assertSame('Unemp', $columns[25]->abbreviation());
        self::assertSame('Vet', $columns[26]->abbreviation());
        self::assertSame('War', $columns[27]->abbreviation());

        self::assertSame('Name', $columns[0]->title());
        self::assertSame('Relationship of each person to the head of the family', $columns[1]->title());
        self::assertSame('Home owned or rented', $columns[2]->title());
        self::assertSame('Value of house, if owned, or monthly rental if rented', $columns[3]->title());
        self::assertSame('Radio set', $columns[4]->title());
        self::assertSame('Does this family live on a farm', $columns[5]->title());
        self::assertSame('Sex', $columns[6]->title());
        self::assertSame('Color or race', $columns[7]->title());
        self::assertSame('Age at last birthday', $columns[8]->title());
        self::assertSame('Whether single, married, widowed, or divorced', $columns[9]->title());
        self::assertSame('Age at first marriage', $columns[10]->title());
        self::assertSame('Attended school since Sept. 1, 1929', $columns[11]->title());
        self::assertSame('Whether able to read and write', $columns[12]->title());
        self::assertSame('Place of birth', $columns[13]->title());
        self::assertSame('Place of birth of father', $columns[14]->title());
        self::assertSame('Place of birth of mother', $columns[15]->title());
        self::assertSame('Language spoken in home before coming to the United States', $columns[16]->title());
        self::assertSame('Year of immigration to the United States', $columns[17]->title());
        self::assertSame('Naturalization', $columns[18]->title());
        self::assertSame('Whether able to speak English', $columns[19]->title());
        self::assertSame('Trade, profession, or particular kind of work done', $columns[20]->title());
        self::assertSame('Industry, business of establishment in which at work', $columns[21]->title());
        self::assertSame('Industry code', $columns[22]->title());
        self::assertSame('Class of worker', $columns[23]->title());
        self::assertSame('Whether normally at work yesterday or the last regular working day', $columns[24]->title());
        self::assertSame('If not, …', $columns[25]->title());
        self::assertSame('Whether a veteran of U.S. military or …', $columns[26]->title());
        self::assertSame('What war or …', $columns[27]->title());
    }
}
