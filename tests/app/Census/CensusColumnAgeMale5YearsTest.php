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

use Fisharebest\Webtrees\Date;
use Mockery;

/**
 * Test harness for the class CensusColumnAgeMale5Years5Years
 */
class CensusColumnAgeMale5YearsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Delete mock objects
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnAgeMale5Years
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testMale()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('01 JAN 1800'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $column = new CensusColumnAgeMale5Years($census, '', '');

        $this->assertSame('30', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnAgeMale5Years
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testFemale()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('F');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');

        $column = new CensusColumnAgeMale5Years($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnAgeMale5Years
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testUnknownSex()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('U');
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('01 JAN 1800'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $column = new CensusColumnAgeMale5Years($census, '', '');

        $this->assertSame('30', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnAgeMale5Years
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testLessThanOneYear()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('01 JAN 1800'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1800');

        $column = new CensusColumnAgeMale5Years($census, '', '');

        $this->assertSame('0', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnAgeMale5Years
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testLessThanFifteenYears()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('01 JAN 1800'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1814');

        $column = new CensusColumnAgeMale5Years($census, '', '');

        $this->assertSame('14', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnAgeMale5Years
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testRoundedDownToFiveYears()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('01 JAN 1800'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1844');

        $column = new CensusColumnAgeMale5Years($census, '', '');

        $this->assertSame('40', $column->generate($individual));
    }
}
