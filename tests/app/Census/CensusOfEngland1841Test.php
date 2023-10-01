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
 * Test harness for the class CensusOfEngland1841
 */
class CensusOfEngland1841Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfEngland1841
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfEngland1841();

        self::assertSame('England', $census->censusPlace());
        self::assertSame('06 JUN 1841', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfEngland1841
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns(): void
    {
        $census  = new CensusOfEngland1841();
        $columns = $census->columns();

        self::assertCount(6, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnAgeMale5Years::class, $columns[1]);
        self::assertInstanceOf(CensusColumnAgeFemale5Years::class, $columns[2]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[4]);
        self::assertInstanceOf(CensusColumnBornForeignParts::class, $columns[5]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('AgeM', $columns[1]->abbreviation());
        self::assertSame('AgeF', $columns[2]->abbreviation());
        self::assertSame('Occupation', $columns[3]->abbreviation());
        self::assertSame('BiC', $columns[4]->abbreviation());
        self::assertSame('SIF', $columns[5]->abbreviation());

        self::assertSame('Name', $columns[0]->title());
        self::assertSame('Age (males)', $columns[1]->title());
        self::assertSame('Age (females)', $columns[2]->title());
        self::assertSame('Profession, trade, employment or of independent means', $columns[3]->title());
        self::assertSame('Born in same county', $columns[4]->title());
        self::assertSame('Born in Scotland, Ireland or foreign parts', $columns[5]->title());
    }
}
