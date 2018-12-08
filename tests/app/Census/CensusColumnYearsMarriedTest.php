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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Mockery;

/**
 * Test harness for the class CensusColumnYearsMarried
 */
class CensusColumnYearsMarriedTest extends \Fisharebest\Webtrees\TestCase
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
     * @covers \Fisharebest\Webtrees\Census\CensusColumnYearsMarried
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testNoSpouseFamily()
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSpouseFamilies')->andReturn([]);

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

        $column = new CensusColumnYearsMarried($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnYearsMarried
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testNoMarriage()
    {
        $family = Mockery::mock(Family::class);
        $family->shouldReceive('facts')->with(['MARR'], true)->andReturn([]);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

        $column = new CensusColumnYearsMarried($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnYearsMarried
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testUndatedMarriage()
    {
        $fact = Mockery::mock(Fact::class);
        $fact->shouldReceive('date')->andReturn(new Date(''));

        $family = Mockery::mock(Family::class);
        $family->shouldReceive('facts')->with(['MARR'], true)->andReturn([$fact]);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

        $column = new CensusColumnYearsMarried($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnYearsMarried
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testMarriageAfterCensus()
    {
        $fact = Mockery::mock(Fact::class);
        $fact->shouldReceive('date')->andReturn(new Date('1861'));

        $family = Mockery::mock(Family::class);
        $family->shouldReceive('facts')->with(['MARR'], true)->andReturn([$fact]);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

        $column = new CensusColumnYearsMarried($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnYearsMarried
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testMarriageBeforeCensus()
    {
        $fact = Mockery::mock(Fact::class);
        $fact->shouldReceive('date')->andReturn(new Date('OCT 1851'));

        $family = Mockery::mock(Family::class);
        $family->shouldReceive('facts')->with(['MARR'], true)->andReturn([$fact]);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

        $column = new CensusColumnYearsMarried($census, '', '');

        $this->assertSame('8', $column->generate($individual, $individual));
    }
}
