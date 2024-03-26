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


#[CoversClass(CensusOfDenmark::class)]
class CensusOfDenmarkTest extends TestCase
{
    /**
     * Test the census place
     */
    public function testPlace(): void
    {
        $census = new CensusOfDenmark();

        self::assertSame('Danmark', $census->censusPlace());
    }

    /**
     * Test the census language
     */
    public function testLanguage(): void
    {
        $census = new CensusOfDenmark();

        self::assertSame('da', $census->censusLanguage());
    }

    /**
     * Test the census dates
     */
    public function testAllDates(): void
    {
        $census = new CensusOfDenmark();

        $census_dates = $census->allCensusDates();

        self::assertCount(22, $census_dates);
        self::assertInstanceOf(CensusOfDenmark1787::class, $census_dates[0]);
        self::assertInstanceOf(CensusOfDenmark1801::class, $census_dates[1]);
        self::assertInstanceOf(CensusOfDenmark1803::class, $census_dates[2]);
        self::assertInstanceOf(CensusOfDenmark1834::class, $census_dates[3]);
        self::assertInstanceOf(CensusOfDenmark1835::class, $census_dates[4]);
        self::assertInstanceOf(CensusOfDenmark1840::class, $census_dates[5]);
        self::assertInstanceOf(CensusOfDenmark1845::class, $census_dates[6]);
        self::assertInstanceOf(CensusOfDenmark1850::class, $census_dates[7]);
        self::assertInstanceOf(CensusOfDenmark1855::class, $census_dates[8]);
        self::assertInstanceOf(CensusOfDenmark1860::class, $census_dates[9]);
        self::assertInstanceOf(CensusOfDenmark1870::class, $census_dates[10]);
        self::assertInstanceOf(CensusOfDenmark1880::class, $census_dates[11]);
        self::assertInstanceOf(CensusOfDenmark1885::class, $census_dates[12]);
        self::assertInstanceOf(CensusOfDenmark1890::class, $census_dates[13]);
        self::assertInstanceOf(CensusOfDenmark1901::class, $census_dates[14]);
        self::assertInstanceOf(CensusOfDenmark1906::class, $census_dates[15]);
        self::assertInstanceOf(CensusOfDenmark1911::class, $census_dates[16]);
        self::assertInstanceOf(CensusOfDenmark1916::class, $census_dates[17]);
        self::assertInstanceOf(CensusOfDenmark1921::class, $census_dates[18]);
        self::assertInstanceOf(CensusOfDenmark1925::class, $census_dates[19]);
        self::assertInstanceOf(CensusOfDenmark1930::class, $census_dates[20]);
        self::assertInstanceOf(CensusOfDenmark1940::class, $census_dates[21]);
    }
}
