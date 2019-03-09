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
namespace Fisharebest\Webtrees\Census;

/**
 * Test harness for the class CensusOfDenmark
 */
class CensusOfDenmarkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDenmark
     */
    public function testPlace()
    {
        $census = new CensusOfDenmark();

        $this->assertSame('Danmark', $census->censusPlace());
    }

    /**
     * Test the census dates
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDenmark
     */
    public function testAllDates()
    {
        $census  = new CensusOfDenmark();

        $census_dates = $census->allCensusDates();

        $this->assertCount(22, $census_dates);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1787', $census_dates[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1801', $census_dates[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1803', $census_dates[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1834', $census_dates[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1835', $census_dates[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1840', $census_dates[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1845', $census_dates[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1850', $census_dates[7]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1855', $census_dates[8]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1860', $census_dates[9]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1870', $census_dates[10]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1880', $census_dates[11]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1885', $census_dates[12]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1890', $census_dates[13]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1901', $census_dates[14]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1906', $census_dates[15]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1911', $census_dates[16]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1916', $census_dates[17]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1921', $census_dates[18]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1925', $census_dates[19]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1930', $census_dates[20]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark1940', $census_dates[21]);
    }
}
