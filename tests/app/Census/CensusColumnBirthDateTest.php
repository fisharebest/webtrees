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
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Individual;
use Mockery;

/**
 * Test harness for the class CensusColumnAge
 */
class CensusColumnBirthDateTest extends \Fisharebest\Webtrees\TestCase
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
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBirthDate
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testGenerateColumn(): void
    {
        $cal_date = Mockery::mock(GregorianDate::class);
        $cal_date->shouldReceive('format')->andReturn('1 1 1800');

        $date = Mockery::mock(Date::class);
        $date->shouldReceive('minimumDate')->andReturn($cal_date);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn($date);

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1832');

        $column = new CensusColumnBirthDate($census, '', '');

        $this->assertSame('1 1 1800', $column->generate($individual, $individual));
    }
}
