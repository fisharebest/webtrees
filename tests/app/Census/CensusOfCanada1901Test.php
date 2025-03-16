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
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CensusOfCanada1901::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfCanada1901Test extends TestCase
{
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfCanada1901();

        self::assertSame('Canada', $census->censusPlace());
        self::assertSame('31 MAR 1901', $census->censusDate());
    }

    public function testColumns(): void
    {
        $census  = new CensusOfCanada1901();
        $columns = $census->columns();

        self::assertCount(30, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[1]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[2]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[3]);
        self::assertInstanceOf(CensusColumnConditionCanada::class, $columns[4]);
        self::assertInstanceOf(CensusColumnBirthMonthDay::class, $columns[5]);
        self::assertInstanceOf(CensusColumnBirthYear::class, $columns[6]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[7]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnNationality::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
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
        self::assertInstanceOf(CensusColumnNull::class, $columns[26]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[27]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[28]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[29]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Sex', $columns[1]->abbreviation());
        self::assertSame('Color', $columns[2]->abbreviation());
        self::assertSame('Relation', $columns[3]->abbreviation());
        self::assertSame('S/M/W/D', $columns[4]->abbreviation());
        self::assertSame('M/D', $columns[5]->abbreviation());
        self::assertSame('Year', $columns[6]->abbreviation());
        self::assertSame('Age', $columns[7]->abbreviation());
        self::assertSame('Birth Loc', $columns[8]->abbreviation());
        self::assertSame('Origin', $columns[9]->abbreviation());
        self::assertSame('Nationality', $columns[10]->abbreviation());
        self::assertSame('Religion', $columns[11]->abbreviation());
        self::assertSame('Occupation', $columns[12]->abbreviation());
        self::assertSame('Retired', $columns[13]->abbreviation());
        self::assertSame('Employer', $columns[14]->abbreviation());
        self::assertSame('Employee', $columns[15]->abbreviation());
        self::assertSame('Work on own', $columns[16]->abbreviation());
        self::assertSame('Trade', $columns[17]->abbreviation());
        self::assertSame('Ms Fac', $columns[18]->abbreviation());
        self::assertSame('Ms Home', $columns[19]->abbreviation());
        self::assertSame('Ms Other', $columns[20]->abbreviation());
        self::assertSame('Earnings', $columns[21]->abbreviation());
        self::assertSame('Extra Earn', $columns[22]->abbreviation());
        self::assertSame('Edu Month', $columns[23]->abbreviation());
        self::assertSame('Read', $columns[24]->abbreviation());
        self::assertSame('Write', $columns[25]->abbreviation());
        self::assertSame('English', $columns[26]->abbreviation());
        self::assertSame('French', $columns[27]->abbreviation());
        self::assertSame('Mother toungue', $columns[28]->abbreviation());
        self::assertSame('Infirmities', $columns[29]->abbreviation());

        self::assertSame('Name', $columns[0]->title());
        self::assertSame('Sex', $columns[1]->title());
        self::assertSame('Colour', $columns[2]->title());
        self::assertSame('Relationship to Head of Family', $columns[3]->title());
        self::assertSame('Single, married, widowed or divorced', $columns[4]->title());
        self::assertSame('Month and date of birth', $columns[5]->title());
        self::assertSame('Year of birth', $columns[6]->title());
        self::assertSame('Age at last birthday', $columns[7]->title());
        self::assertSame('Country or Place of Birth, plus Rural or Urban', $columns[8]->title());
        self::assertSame('Racial or Tribal origin', $columns[9]->title());
        self::assertSame('Nationality', $columns[10]->title());
        self::assertSame('Religion', $columns[11]->title());
        self::assertSame('Profession, Occupation or Trade', $columns[12]->title());
        self::assertSame('Living on own means', $columns[13]->title());
        self::assertSame('Employer', $columns[14]->title());
        self::assertSame('Employee', $columns[15]->title());
        self::assertSame('Working on own account', $columns[16]->title());
        self::assertSame('Working at trade in factory or home', $columns[17]->title());
        self::assertSame('Months employed at trade in factory', $columns[18]->title());
        self::assertSame('Months employed at trade in home', $columns[19]->title());
        self::assertSame('Months employed in other than trade in factory or home', $columns[20]->title());
        self::assertSame('Earnings from occupation or trade $', $columns[21]->title());
        self::assertSame('Extra earnings (from other than occupation or trade) $', $columns[22]->title());
        self::assertSame('Months at school in year', $columns[23]->title());
        self::assertSame('Can Read', $columns[24]->title());
        self::assertSame('Can Write', $columns[25]->title());
        self::assertSame('Speaks English', $columns[26]->title());
        self::assertSame('Speaks French', $columns[27]->title());
        self::assertSame('Mother toungue', $columns[28]->title());
        self::assertSame('Infirmities - a. Deaf and dumb, b. Blind, c. Unsound mind', $columns[29]->title());
    }
}
