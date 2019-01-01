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
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Mockery;

/**
 * Test harness for the class CensusColumnChildrenBornAlive
 */
class CensusColumnChildrenBornAliveTest extends \Fisharebest\Webtrees\TestCase
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
     * @covers \Fisharebest\Webtrees\Census\CensusColumnChildrenBornAlive
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testMale()
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('M');

        $census = Mockery::mock(CensusInterface::class);

        $column = new CensusColumnChildrenBornAlive($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnChildrenBornAlive
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testCountChildren()
    {
        // Stillborn
        $child1 = Mockery::mock(Individual::class);
        $child1->shouldReceive('getBirthDate')->andReturn(new Date('01 FEB 1904'));
        $child1->shouldReceive('getDeathDate')->andReturn(new Date('01 FEB 1904'));

        // Died after census
        $child2 = Mockery::mock(Individual::class);
        $child2->shouldReceive('getBirthDate')->andReturn(new Date('02 FEB 1904'));
        $child2->shouldReceive('getDeathDate')->andReturn(new Date('20 DEC 1912'));

        // Died before census
        $child3 = Mockery::mock(Individual::class);
        $child3->shouldReceive('getBirthDate')->andReturn(new Date('02 FEB 1904'));
        $child3->shouldReceive('getDeathDate')->andReturn(new Date('20 DEC 1910'));

        // Still living
        $child4 = Mockery::mock(Individual::class);
        $child4->shouldReceive('getBirthDate')->andReturn(new Date('01 FEB 1904'));
        $child4->shouldReceive('getDeathDate')->andReturn(new Date(''));

        $family = Mockery::mock(Family::class);
        $family->shouldReceive('getChildren')->andReturn([
            $child1,
            $child2,
            $child3,
            $child4,
        ]);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('F');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('30 MAR 1911');

        $column = new CensusColumnChildrenBornAlive($census, '', '');

        $this->assertSame('3', $column->generate($individual, $individual));
    }
}
