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
 * Test harness for the class CensusOfFrance
 */
class CensusOfFranceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfFrance
     */
    public function testPlace()
    {
        $census = new CensusOfFrance();

        $this->assertSame('France', $census->censusPlace());
    }

    /**
     * Test the census dates
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfFrance
     */
    public function testAllDates()
    {
        $census  = new CensusOfFrance();

        $census_dates = $census->allCensusDates();

        $this->assertCount(22, $census_dates);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1831', $census_dates[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1836', $census_dates[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1841', $census_dates[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1846', $census_dates[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1851', $census_dates[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1856', $census_dates[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1861', $census_dates[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1866', $census_dates[7]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1872', $census_dates[8]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1876', $census_dates[9]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1881', $census_dates[10]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1886', $census_dates[11]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1891', $census_dates[12]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1896', $census_dates[13]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1901', $census_dates[14]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1906', $census_dates[15]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1911', $census_dates[16]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1921', $census_dates[17]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1926', $census_dates[18]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1931', $census_dates[19]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1936', $census_dates[20]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance1946', $census_dates[21]);
    }
}
