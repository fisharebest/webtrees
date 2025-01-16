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

#[CoversClass(CensusOfScotland::class)]
class CensusOfScotlandTest extends TestCase
{
    /**
     * Test the census place
     */
    public function testPlace(): void
    {
        $census = new CensusOfScotland();

        self::assertSame('Scotland', $census->censusPlace());
    }

    /**
     * Test the census language
     */
    public function testLanguage(): void
    {
        $census = new CensusOfScotland();

        self::assertSame('en-GB', $census->censusLanguage());
    }

    /**
     * Test the census dates
     */
    public function testAllDates(): void
    {
        $census = new CensusOfScotland();

        $census_dates = $census->allCensusDates();

        self::assertCount(9, $census_dates);
        self::assertInstanceOf(CensusOfScotland1841::class, $census_dates[0]);
        self::assertInstanceOf(CensusOfScotland1851::class, $census_dates[1]);
        self::assertInstanceOf(CensusOfScotland1861::class, $census_dates[2]);
        self::assertInstanceOf(CensusOfScotland1871::class, $census_dates[3]);
        self::assertInstanceOf(CensusOfScotland1881::class, $census_dates[4]);
        self::assertInstanceOf(CensusOfScotland1891::class, $census_dates[5]);
        self::assertInstanceOf(CensusOfScotland1901::class, $census_dates[6]);
        self::assertInstanceOf(CensusOfScotland1911::class, $census_dates[7]);
        self::assertInstanceOf(RegisterOfScotland1939::class, $census_dates[8]);
    }
}
