<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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
 * Test harness for the class CensusOfFrance
 */
class CensusOfFranceTest extends TestCase
{
    /**
     * Test the census place
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfFrance
     *
     * @return void
     */
    public function testPlace(): void
    {
        $census = new CensusOfFrance();

        self::assertSame('France', $census->censusPlace());
    }

    /**
     * Test the census language
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfFrance
     *
     * @return void
     */
    public function testLanguage(): void
    {
        $census = new CensusOfFrance();

        self::assertSame('fr', $census->censusLanguage());
    }

    /**
     * Test the census dates
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfFrance
     *
     * @return void
     */
    public function testAllDates(): void
    {
        $census = new CensusOfFrance();

        $census_dates = $census->allCensusDates();

        self::assertCount(22, $census_dates);
        self::assertInstanceOf(CensusOfFrance1831::class, $census_dates[0]);
        self::assertInstanceOf(CensusOfFrance1836::class, $census_dates[1]);
        self::assertInstanceOf(CensusOfFrance1841::class, $census_dates[2]);
        self::assertInstanceOf(CensusOfFrance1846::class, $census_dates[3]);
        self::assertInstanceOf(CensusOfFrance1851::class, $census_dates[4]);
        self::assertInstanceOf(CensusOfFrance1856::class, $census_dates[5]);
        self::assertInstanceOf(CensusOfFrance1861::class, $census_dates[6]);
        self::assertInstanceOf(CensusOfFrance1866::class, $census_dates[7]);
        self::assertInstanceOf(CensusOfFrance1872::class, $census_dates[8]);
        self::assertInstanceOf(CensusOfFrance1876::class, $census_dates[9]);
        self::assertInstanceOf(CensusOfFrance1881::class, $census_dates[10]);
        self::assertInstanceOf(CensusOfFrance1886::class, $census_dates[11]);
        self::assertInstanceOf(CensusOfFrance1891::class, $census_dates[12]);
        self::assertInstanceOf(CensusOfFrance1896::class, $census_dates[13]);
        self::assertInstanceOf(CensusOfFrance1901::class, $census_dates[14]);
        self::assertInstanceOf(CensusOfFrance1906::class, $census_dates[15]);
        self::assertInstanceOf(CensusOfFrance1911::class, $census_dates[16]);
        self::assertInstanceOf(CensusOfFrance1921::class, $census_dates[17]);
        self::assertInstanceOf(CensusOfFrance1926::class, $census_dates[18]);
        self::assertInstanceOf(CensusOfFrance1931::class, $census_dates[19]);
        self::assertInstanceOf(CensusOfFrance1936::class, $census_dates[20]);
        self::assertInstanceOf(CensusOfFrance1946::class, $census_dates[21]);
    }
}
