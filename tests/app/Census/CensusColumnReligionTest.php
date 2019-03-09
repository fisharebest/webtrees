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

use Mockery;

/**
 * Test harness for the class CensusColumnReligion
 */
class CensusColumnReligionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Delete mock objects
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnReligion
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testReligion()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

        $column = new CensusColumnReligion($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }
}
