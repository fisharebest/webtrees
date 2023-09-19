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
 * Test harness for the class CensusOfCanada
 */
class CensusOfCanadaTest extends TestCase
{
    /**
     * Test the census place
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCanada
     *
     * @return void
     */
    public function testPlace(): void
    {
        $census = new CensusOfCanada();

        self::assertSame('Canada', $census->censusPlace());
    }

    /**
     * Test the census language
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCanada
     *
     * @return void
     */
    public function testLanguage(): void
    {
        $census = new CensusOfCanada();

        self::assertSame('en-GB', $census->censusLanguage());
    }

    /**
     * Test the census dates
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCanada
     *
     * @return void
     */
    public function testAllDates(): void
    {
        $census = new CensusOfCanada();

        $census_dates = $census->allCensusDates();

        self::assertCount(10, $census_dates);
        self::assertInstanceOf(CensusOfCanada1851::class, $census_dates[0]);
        self::assertInstanceOf(CensusOfCanada1861::class, $census_dates[1]);
        self::assertInstanceOf(CensusOfCanada1871::class, $census_dates[2]);
        self::assertInstanceOf(CensusOfCanada1881::class, $census_dates[3]);
        self::assertInstanceOf(CensusOfCanada1901::class, $census_dates[4]);
        self::assertInstanceOf(CensusOfCanada1911::class, $census_dates[5]);
        self::assertInstanceOf(CensusOfCanadaPraries1916::class, $census_dates[6]);
        self::assertInstanceOf(CensusOfCanada1921::class, $census_dates[7]);
        self::assertInstanceOf(CensusOfCanadaPraries1926::class, $census_dates[8]);
        self::assertInstanceOf(CensusOfCanada1931::class, $census_dates[9]);
    }
}
