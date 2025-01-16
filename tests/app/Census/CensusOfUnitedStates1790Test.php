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
 * Test harness for the class CensusOfUnitedStates1790
 */
class CensusOfUnitedStates1790Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1790
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1790();

        self::assertSame('United States', $census->censusPlace());
        self::assertSame('02 AUG 1790', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1790
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1790();
        $columns = $census->columns();

        self::assertCount(8, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[1]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[2]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[4]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[5]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Occupation', $columns[1]->abbreviation());
        self::assertSame('White male 16+', $columns[2]->abbreviation());
        self::assertSame('White male 0-16', $columns[3]->abbreviation());
        self::assertSame('White female', $columns[4]->abbreviation());
        self::assertSame('Free', $columns[5]->abbreviation());
        self::assertSame('Slaves', $columns[6]->abbreviation());
        self::assertSame('Total', $columns[7]->abbreviation());

        self::assertSame('Name of head of family', $columns[0]->title());
        self::assertSame('Professions and occupation', $columns[1]->title());
        self::assertSame('White male of 16 yrs upward', $columns[2]->title());
        self::assertSame('White males of under 16 yrs', $columns[3]->title());
        self::assertSame('All White Females', $columns[4]->title());
        self::assertSame('All other free persons', $columns[5]->title());
        self::assertSame('Number of slaves', $columns[6]->title());
        self::assertSame('Total', $columns[7]->title());
    }
}
