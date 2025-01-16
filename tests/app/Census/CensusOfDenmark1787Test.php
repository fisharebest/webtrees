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

#[CoversClass(CensusOfDenmark1787::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfDenmark1787Test extends TestCase
{
    /**
     * Test the census place and date
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfDenmark1787();

        self::assertSame('Danmark', $census->censusPlace());
        self::assertSame('01 JUL 1787', $census->censusDate());
    }

    /**
     * Test the census columns
     */
    public function testColumns(): void
    {
        $census  = new CensusOfDenmark1787();
        $columns = $census->columns();

        self::assertCount(5, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnRelationToHead::class, $columns[1]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[2]);
        self::assertInstanceOf(CensusColumnConditionDanish::class, $columns[3]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[4]);

        self::assertSame('Navn', $columns[0]->abbreviation());
        self::assertSame('Stilling i familien', $columns[1]->abbreviation());
        self::assertSame('Alder', $columns[2]->abbreviation());
        self::assertSame('Civilstand', $columns[3]->abbreviation());
        self::assertSame('Erhverv', $columns[4]->abbreviation());

        self::assertSame('', $columns[0]->title());
        self::assertSame('', $columns[1]->title());
        self::assertSame('', $columns[2]->title());
        self::assertSame('', $columns[3]->title());
        self::assertSame('', $columns[4]->title());
    }
}
