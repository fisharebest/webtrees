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

use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class CensusColumnGivenNameInitial
 */
class CensusColumnGivenNameInitialTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnGivenNameInitial
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testOneGivenName(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getAllNames')->willReturn([['givn' => 'Joe']]);

        $census = $this->createMock(CensusInterface::class);

        $column = new CensusColumnGivenNameInitial($census, '', '');

        $this->assertSame('Joe', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnGivenNameInitial
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testMultipleGivenNames(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getAllNames')->willReturn([['givn' => 'Joe Fred']]);

        $census = $this->createMock(CensusInterface::class);

        $column = new CensusColumnGivenNameInitial($census, '', '');

        $this->assertSame('Joe F', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnGivenNameInitial
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testNoName(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getAllNames')->willReturn([]);

        $census = $this->createMock(CensusInterface::class);

        $column = new CensusColumnGivenNameInitial($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }
}
