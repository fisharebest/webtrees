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
 * Test harness for the class CensusOfCanada1851
 */
class CensusOfCanada1851Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCanada1851
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfCanada1851();

        self::assertSame('Canada', $census->censusPlace());
        self::assertSame('31 DEC 1851', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCanada1851
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfCanada1851();
        $columns = $census->columns();

        self::assertCount(11, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[1]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[2]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[3]);
        self::assertInstanceOf(CensusColumnAgeNextBirthDay::class, $columns[4]);
        self::assertInstanceOf(CensusColumnSexM::class, $columns[5]);
        self::assertInstanceOf(CensusColumnSexF::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);
        self::assertInstanceOf(CensusColumnNationality::class, $columns[8]);
        self::assertInstanceOf(CensusColumnConditionCanadaMarriedSingle::class, $columns[9]);
        self::assertInstanceOf(CensusColumnConditionCanadaWidowed::class, $columns[10]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Occupation', $columns[1]->abbreviation());
        self::assertSame('Birth Loc', $columns[2]->abbreviation());
        self::assertSame('Religion', $columns[3]->abbreviation());
        self::assertSame('Next BDay age', $columns[4]->abbreviation());
        self::assertSame('Sex: male', $columns[5]->abbreviation());
        self::assertSame('Sex: female', $columns[6]->abbreviation());
        self::assertSame('Born', $columns[7]->abbreviation());
        self::assertSame('Origin', $columns[8]->abbreviation());
        self::assertSame('M/S', $columns[9]->abbreviation());
        self::assertSame('Widowed', $columns[10]->abbreviation());

        self::assertSame('Name of inmates', $columns[0]->title());
        self::assertSame('Profession, trade or occupation', $columns[1]->title());
        self::assertSame('Place of birth. F indicates that the person was born of Canadian parents.', $columns[2]->title());
        self::assertSame('Religion', $columns[3]->title());
        self::assertSame('Age at NEXT birthday', $columns[4]->title());
        self::assertSame('Sex: male', $columns[5]->title());
        self::assertSame('Sex: female', $columns[6]->title());
        self::assertSame('Born within last twelve months', $columns[7]->title());
        self::assertSame('Origin', $columns[8]->title());
        self::assertSame('Married or single', $columns[9]->title());
        self::assertSame('Widowers or Widows', $columns[10]->title());
    }
}
