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
 * Test harness for the class CensusOfUnitedStates1850
 */
class CensusOfUnitedStates1850Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1850
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1850();

        self::assertSame('United States', $census->censusPlace());
        self::assertSame('01 JUN 1850', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1850
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1850();
        $columns = $census->columns();

        self::assertCount(11, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[1]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[2]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[3]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[4]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[5]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[6]);
        self::assertInstanceOf(CensusColumnMarriedWithinYear::class, $columns[7]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[10]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Age', $columns[1]->abbreviation());
        self::assertSame('Sex', $columns[2]->abbreviation());
        self::assertSame('Color', $columns[3]->abbreviation());
        self::assertSame('Occupation', $columns[4]->abbreviation());
        self::assertSame('RE', $columns[5]->abbreviation());
        self::assertSame('Birthplace', $columns[6]->abbreviation());
        self::assertSame('Mar', $columns[7]->abbreviation());
        self::assertSame('School', $columns[8]->abbreviation());
        self::assertSame('R+W', $columns[9]->abbreviation());
        self::assertSame('Infirm', $columns[10]->abbreviation());

        self::assertSame('Name', $columns[0]->title());
        self::assertSame('Age', $columns[1]->title());
        self::assertSame('Sex', $columns[2]->title());
        self::assertSame('White, black, or mulatto', $columns[3]->title());
        self::assertSame('Profession, occupation, or trade', $columns[4]->title());
        self::assertSame('Value of real estate owned', $columns[5]->title());
        self::assertSame('Place of birth, naming the state, territory, or country', $columns[6]->title());
        self::assertSame('Married within the year', $columns[7]->title());
        self::assertSame('Attended school within the year', $columns[8]->title());
        self::assertSame('Persons over 20 years of age who cannot read and write', $columns[9]->title());
        self::assertSame('Whether deaf and dumb, blind, insane, idiotic, pauper or convict', $columns[10]->title());
    }
}
