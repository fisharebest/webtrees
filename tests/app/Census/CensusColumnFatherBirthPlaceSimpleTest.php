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

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Place;
use Illuminate\Support\Collection;
use Mockery;

/**
 * Test harness for the class CensusColumnFatherBirthPlaceSimple
 */
class CensusColumnFatherBirthPlaceSimpleTest extends \Fisharebest\Webtrees\TestCase
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
     * Get place mock.
     *
     * @param string $place Gedcom Place
     *
     * @return Place
     */
    private function getPlaceMock($place): Place
    {
        $placeParts = explode(', ', $place);

        $placeMock = Mockery::mock(Place::class);
        $placeMock->shouldReceive('gedcomName')->andReturn($place);
        $placeMock->shouldReceive('lastParts')->andReturn(new Collection($placeParts));

        return $placeMock;
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnFatherBirthPlaceSimple
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testKnownStateAndTown(): void
    {
        $father = Mockery::mock(Individual::class);
        $father->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Miami, Florida, United States'));

        $family = Mockery::mock(Family::class);
        $family->shouldReceive('husband')->andReturn($father);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('primaryChildFamily')->andReturn($family);

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusPlace')->andReturn('United States');

        $column = new CensusColumnFatherBirthPlaceSimple($census, '', '');

        $this->assertSame('Florida', $column->generate($individual, $individual));
    }
}
