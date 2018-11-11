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

use Fisharebest\Webtrees\Date;
use Mockery;

/**
 * Test harness for the class CensusColumnAgeMarried
 */
class CensusColumnAgeMarriedTest extends \Fisharebest\Webtrees\TestCase
{
    /**
     * Delete mock objects
     *
     * @return void
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnAgeMarried
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testAgeMarried()
    {
        $fact = Mockery::mock('Fisharebest\Webtrees\Fact');
        $fact->shouldReceive('date')->andReturn(new Date('01 DEC 1859'));

        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getFacts')->with('MARR', true)->andReturn([$fact]);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthDate')->andReturn(new Date('15 MAR 1840'));
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

        $column = new CensusColumnAgeMarried($census, '', '');

        $this->assertSame('19', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnAgeMarried
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testNoBirthDate()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthDate')->andReturn(new Date(''));
        $individual->shouldReceive('getSpouseFamilies')->andReturn([]);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

        $column = new CensusColumnAgeMarried($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnAgeMarried
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testNoMarriage()
    {
        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getFacts')->with('MARR')->andReturn([]);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthDate')->andReturn(new Date(''));
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

        $column = new CensusColumnAgeMarried($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnAgeMarried
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testNoSpouseFamily()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthDate')->andReturn(new Date('15 MAR 1840'));
        $individual->shouldReceive('getSpouseFamilies')->andReturn([]);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

        $column = new CensusColumnAgeMarried($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }
}
