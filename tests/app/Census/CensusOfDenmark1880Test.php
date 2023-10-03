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
 * Test harness for the class CensusOfDenmark1880
 */
class CensusOfDenmark1880Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDenmark1880
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfDenmark1880();

        self::assertSame('Danmark', $census->censusPlace());
        self::assertSame('01 FEB 1880', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDenmark1880
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns(): void
    {
        $census  = new CensusOfDenmark1880();
        $columns = $census->columns();

        self::assertCount(13, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnSexMK::class, $columns[1]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[2]);
        self::assertInstanceOf(CensusColumnConditionDanish::class, $columns[3]);
        self::assertInstanceOf(CensusColumnReligion::class, $columns[4]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[5]);
        self::assertInstanceOf(CensusColumnRelationToHead::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);

        self::assertSame('Navn', $columns[0]->abbreviation());
        self::assertSame('Køn', $columns[1]->abbreviation());
        self::assertSame('Alder', $columns[2]->abbreviation());
        self::assertSame('Civilstand', $columns[3]->abbreviation());
        self::assertSame('Religion', $columns[4]->abbreviation());
        self::assertSame('Erhverv', $columns[5]->abbreviation());
        self::assertSame('Stilling i familien', $columns[6]->abbreviation());
        self::assertSame('', $columns[7]->abbreviation());
        self::assertSame('', $columns[8]->abbreviation());
        self::assertSame('', $columns[9]->abbreviation());
        self::assertSame('', $columns[10]->abbreviation());
        self::assertSame('', $columns[11]->abbreviation());
        self::assertSame('', $columns[12]->abbreviation());

        self::assertSame('', $columns[0]->title());
        self::assertSame('', $columns[1]->title());
        self::assertSame('', $columns[2]->title());
        self::assertSame('', $columns[3]->title());
        self::assertSame('', $columns[4]->title());
        self::assertSame('', $columns[5]->title());
        self::assertSame('', $columns[6]->title());
        self::assertSame('', $columns[7]->title());
        self::assertSame('', $columns[8]->title());
        self::assertSame('', $columns[9]->title());
        self::assertSame('', $columns[10]->title());
        self::assertSame('', $columns[11]->title());
        self::assertSame('', $columns[12]->title());
    }
}
