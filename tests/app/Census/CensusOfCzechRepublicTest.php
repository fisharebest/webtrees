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
 * Test harness for the class CensusOfCzechRepublic
 */
class CensusOfCzechRepublicTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfCzechRepublic
     */
    public function testPlace()
    {
        $census = new CensusOfCzechRepublic();

        $this->assertSame('Česko', $census->censusPlace());
    }

    /**
     * Test the census dates
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfCzechRepublic
     */
    public function testAllDates()
    {
        $census  = new CensusOfCzechRepublic();

        $census_dates = $census->allCensusDates();

        $this->assertCount(2, $census_dates);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfCzechRepublic1880', $census_dates[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfCzechRepublic1921', $census_dates[1]);
    }
}
