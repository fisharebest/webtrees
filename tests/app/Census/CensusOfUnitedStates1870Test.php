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
 * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1870
 * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
 */
class CensusOfUnitedStates1870Test extends TestCase
{
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1870();

        self::assertSame('United States', $census->censusPlace());
        self::assertSame('JUN 1870', $census->censusDate());
    }

    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1870();
        $columns = $census->columns();

        self::assertCount(18, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[1]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[2]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[3]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[4]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[5]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[6]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[7]);
        self::assertInstanceOf(CensusColumnFatherForeign::class, $columns[8]);
        self::assertInstanceOf(CensusColumnMotherForeign::class, $columns[9]);
        self::assertInstanceOf(CensusColumnMonthIfBornWithinYear::class, $columns[10]);
        self::assertInstanceOf(CensusColumnMonthIfMarriedWithinYear::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Age', $columns[1]->abbreviation());
        self::assertSame('Sex', $columns[2]->abbreviation());
        self::assertSame('Color', $columns[3]->abbreviation());
        self::assertSame('Occupation', $columns[4]->abbreviation());
        self::assertSame('RE', $columns[5]->abbreviation());
        self::assertSame('PE', $columns[6]->abbreviation());
        self::assertSame('Birthplace', $columns[7]->abbreviation());
        self::assertSame('FFB', $columns[8]->abbreviation());
        self::assertSame('MFB', $columns[9]->abbreviation());
        self::assertSame('Born', $columns[10]->abbreviation());
        self::assertSame('Mar', $columns[11]->abbreviation());
        self::assertSame('School', $columns[12]->abbreviation());
        self::assertSame('Read', $columns[13]->abbreviation());
        self::assertSame('Write', $columns[14]->abbreviation());
        self::assertSame('Infirm', $columns[15]->abbreviation());
        self::assertSame('Cit', $columns[16]->abbreviation());
        self::assertSame('Dis', $columns[17]->abbreviation());

        self::assertSame('Name', $columns[0]->title());
        self::assertSame('Age', $columns[1]->title());
        self::assertSame('Sex', $columns[2]->title());
        self::assertSame('White, Black, Mulatto, Chinese, Indian', $columns[3]->title());
        self::assertSame('Profession, occupation, or trade', $columns[4]->title());
        self::assertSame('Value of real estate owned', $columns[5]->title());
        self::assertSame('Value of personal estate owned', $columns[6]->title());
        self::assertSame('Place of birth, naming the state, territory, or country', $columns[7]->title());
        self::assertSame('Father of foreign birth', $columns[8]->title());
        self::assertSame('Mother of foreign birth', $columns[9]->title());
        self::assertSame('If born within the year, state month', $columns[10]->title());
        self::assertSame('If married within the year, state month', $columns[11]->title());
        self::assertSame('Attended school within the year', $columns[12]->title());
        self::assertSame('Cannot read', $columns[13]->title());
        self::assertSame('Cannot write', $columns[14]->title());
        self::assertSame('Whether deaf and dumb, blind, insane, or idiotic', $columns[15]->title());
        self::assertSame('Male citizen of US', $columns[16]->title());
        self::assertSame('Male citizen of US, where right to vote is denied or abridged', $columns[17]->title());
    }
}
