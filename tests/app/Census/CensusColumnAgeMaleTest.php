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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Individual;
use Mockery;

/**
 * Test harness for the class CensusColumnAgeMale
 */
class CensusColumnAgeMaleTest extends \Fisharebest\Webtrees\TestCase
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
     * @covers \Fisharebest\Webtrees\Census\CensusColumnAgeMale
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testMale()
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('01 JAN 1800'));

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1832');

        $column = new CensusColumnAgeMale($census, '', '');

        $this->assertSame('32', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnAgeMale
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testFemale()
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('F');

        $census = Mockery::mock(CensusInterface::class);

        $column = new CensusColumnAgeMale($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnAgeMale
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testUnknownSex()
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('U');
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('01 JAN 1800'));

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1832');

        $column = new CensusColumnAgeMale($census, '', '');

        $this->assertSame('32', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnAgeMale
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testLessThanOneYear()
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('01 JAN 1800'));

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1800');

        $column = new CensusColumnAgeMale($census, '', '');

        $this->assertSame('0', $column->generate($individual, $individual));
    }
}
