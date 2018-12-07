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
namespace Fisharebest\Webtrees\Census;

use Mockery;

/**
 * Test harness for the class CensusColumnAge
 */
class CensusColumnBirthMonthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Delete mock objects
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBirthMonth
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testGenerateColumn()
    {
        $cal_date = Mockery::mock('Fisharebest\Webtrees\Date\CalendarDate');
        $cal_date->shouldReceive('format')->andReturn('Jan');

        $date = Mockery::mock('Fisharebest\Webtrees\Date');
        $date->shouldReceive('minimumDate')->andReturn($cal_date);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn($date);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1832');

        $column = new CensusColumnBirthMonth($census, '', '');

        $this->assertSame('Jan', $column->generate($individual));
    }
}
