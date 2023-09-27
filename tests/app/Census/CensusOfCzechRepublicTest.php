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
 * Test harness for the class CensusOfCzechRepublic
 */
class CensusOfCzechRepublicTest extends TestCase
{
    /**
     * Test the census place
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCzechRepublic
     *
     * @return void
     */
    public function testPlace(): void
    {
        $census = new CensusOfCzechRepublic();

        self::assertSame('Česko', $census->censusPlace());
    }

    /**
     * Test the census language
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCzechRepublic
     *
     * @return void
     */
    public function testLanguage(): void
    {
        $census = new CensusOfCzechRepublic();

        self::assertSame('cs', $census->censusLanguage());
    }

    /**
     * Test the census dates
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCzechRepublic
     *
     * @return void
     */
    public function testAllDates(): void
    {
        $census = new CensusOfCzechRepublic();

        $census_dates = $census->allCensusDates();

        self::assertCount(5, $census_dates);
        self::assertInstanceOf(CensusOfCzechRepublic1880::class, $census_dates[0]);
        self::assertInstanceOf(CensusOfCzechRepublic1890::class, $census_dates[1]);
        self::assertInstanceOf(CensusOfCzechRepublic1900::class, $census_dates[2]);
        self::assertInstanceOf(CensusOfCzechRepublic1910::class, $census_dates[3]);
        self::assertInstanceOf(CensusOfCzechRepublic1921::class, $census_dates[4]);
    }
}
