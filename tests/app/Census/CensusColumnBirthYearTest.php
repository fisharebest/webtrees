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
use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class CensusColumnAge
 */
class CensusColumnBirthYearTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBirthYear
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testGenerateColumn(): void
    {
        $cal_date = $this->createMock(GregorianDate::class);
        $cal_date->method('format')->willReturn('1800');

        $date = $this->createMock(Date::class);
        $date->method('minimumDate')->willReturn($cal_date);

        $individual = $this->createMock(Individual::class);
        $individual->method('getEstimatedBirthDate')->willReturn($date);

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1832');

        $column = new CensusColumnBirthYear($census, '', '');

        $this->assertSame('1800', $column->generate($individual, $individual));
    }
}
