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
 * Test harness for the class CensusColumnAgeFemale5Years
 */
class CensusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Fisharebest\Webtrees\Census\Census
     */
    public function testAllCensus()
    {
        $censuses = Census::allCensusPlaces();

        $this->assertCount(8, $censuses);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfCzechRepublic', $censuses[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDenmark', $censuses[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfDeutschland', $censuses[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfEngland', $censuses[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfFrance', $censuses[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfScotland', $censuses[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfUnitedStates', $censuses[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusOfWales', $censuses[7]);
    }
}
