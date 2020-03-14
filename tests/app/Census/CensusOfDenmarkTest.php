<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class CensusOfDenmark
 */
class CensusOfDenmarkTest extends TestCase
{
    /**
     * Test the census place
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDenmark
     *
     * @return void
     */
    public function testPlace(): void
    {
        $census = new CensusOfDenmark();

        $this->assertSame('Danmark', $census->censusPlace());
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
        $census = new CensusOfDenmark();

        $this->assertSame('da', $census->censusLanguage());
    }

    /**
     * Test the census dates
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDenmark
     *
     * @return void
     */
    public function testAllDates(): void
    {
        $census = new CensusOfDenmark();

        $census_dates = $census->allCensusDates();

        $this->assertCount(22, $census_dates);
        $this->assertInstanceOf(CensusOfDenmark1787::class, $census_dates[0]);
        $this->assertInstanceOf(CensusOfDenmark1801::class, $census_dates[1]);
        $this->assertInstanceOf(CensusOfDenmark1803::class, $census_dates[2]);
        $this->assertInstanceOf(CensusOfDenmark1834::class, $census_dates[3]);
        $this->assertInstanceOf(CensusOfDenmark1835::class, $census_dates[4]);
        $this->assertInstanceOf(CensusOfDenmark1840::class, $census_dates[5]);
        $this->assertInstanceOf(CensusOfDenmark1845::class, $census_dates[6]);
        $this->assertInstanceOf(CensusOfDenmark1850::class, $census_dates[7]);
        $this->assertInstanceOf(CensusOfDenmark1855::class, $census_dates[8]);
        $this->assertInstanceOf(CensusOfDenmark1860::class, $census_dates[9]);
        $this->assertInstanceOf(CensusOfDenmark1870::class, $census_dates[10]);
        $this->assertInstanceOf(CensusOfDenmark1880::class, $census_dates[11]);
        $this->assertInstanceOf(CensusOfDenmark1885::class, $census_dates[12]);
        $this->assertInstanceOf(CensusOfDenmark1890::class, $census_dates[13]);
        $this->assertInstanceOf(CensusOfDenmark1901::class, $census_dates[14]);
        $this->assertInstanceOf(CensusOfDenmark1906::class, $census_dates[15]);
        $this->assertInstanceOf(CensusOfDenmark1911::class, $census_dates[16]);
        $this->assertInstanceOf(CensusOfDenmark1916::class, $census_dates[17]);
        $this->assertInstanceOf(CensusOfDenmark1921::class, $census_dates[18]);
        $this->assertInstanceOf(CensusOfDenmark1925::class, $census_dates[19]);
        $this->assertInstanceOf(CensusOfDenmark1930::class, $census_dates[20]);
        $this->assertInstanceOf(CensusOfDenmark1940::class, $census_dates[21]);
    }
}
