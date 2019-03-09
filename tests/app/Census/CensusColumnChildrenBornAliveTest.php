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

use Fisharebest\Webtrees\Date;
use Mockery;

/**
 * Test harness for the class CensusColumnChildrenBornAlive
 */
class CensusColumnChildrenBornAliveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Delete mock objects
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnChildrenBornAlive
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testMale()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('M');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');

        $column = new CensusColumnChildrenBornAlive($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnChildrenBornAlive
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testCountChildren()
    {
        // Stillborn
        $child1 = Mockery::mock('Fisharebest\Webtrees\Individual');
        $child1->shouldReceive('getBirthDate')->andReturn(new Date('01 FEB 1904'));
        $child1->shouldReceive('getDeathDate')->andReturn(new Date('01 FEB 1904'));

        // Died after census
        $child2 = Mockery::mock('Fisharebest\Webtrees\Individual');
        $child2->shouldReceive('getBirthDate')->andReturn(new Date('02 FEB 1904'));
        $child2->shouldReceive('getDeathDate')->andReturn(new Date('20 DEC 1912'));

        // Died before census
        $child3 = Mockery::mock('Fisharebest\Webtrees\Individual');
        $child3->shouldReceive('getBirthDate')->andReturn(new Date('02 FEB 1904'));
        $child3->shouldReceive('getDeathDate')->andReturn(new Date('20 DEC 1910'));

        // Still living
        $child4 = Mockery::mock('Fisharebest\Webtrees\Individual');
        $child4->shouldReceive('getBirthDate')->andReturn(new Date('01 FEB 1904'));
        $child4->shouldReceive('getDeathDate')->andReturn(new Date(''));

        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getChildren')->andReturn(array($child1, $child2, $child3, $child4));

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('F');
        $individual->shouldReceive('getSpouseFamilies')->andReturn(array($family));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('30 MAR 1911');

        $column = new CensusColumnChildrenBornAlive($census, '', '');

        $this->assertSame('3', $column->generate($individual));
    }
}
