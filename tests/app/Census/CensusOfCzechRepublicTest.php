<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

/**
 * Test harness for the class CensusOfCzechRepublic
 */
class CensusOfCzechRepublicTest extends \Fisharebest\Webtrees\TestCase
{
    /**
     * Test the census place
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCzechRepublic
     *
     * @return void
     */
    public function testPlace()
    {
        $census = new CensusOfCzechRepublic;

        $this->assertSame('Česko', $census->censusPlace());
    }

    /**
     * Test the census dates
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCzechRepublic
     *
     * @return void
     */
    public function testAllDates()
    {
        $census = new CensusOfCzechRepublic;

        $census_dates = $census->allCensusDates();

        $this->assertCount(5, $census_dates);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfCzechRepublic1880', $census_dates[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfCzechRepublic1890', $census_dates[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfCzechRepublic1900', $census_dates[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfCzechRepublic1910', $census_dates[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfCzechRepublic1921', $census_dates[4]);
    }
}
