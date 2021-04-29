<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;

/**
 * Test harness for the class CensusColumnReligion
 */
class CensusColumnReligionTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnReligion
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     * @return void
     */
    public function testNoReligion(): void
    {
        $individual = self::createMock(Individual::class);
        $individual
            ->expects(self::exactly(2))
            ->method('facts')
            ->withConsecutive([['RELI']], [])
            ->willReturnOnConsecutiveCalls(new Collection(), new Collection());

        $census = self::createMock(CensusInterface::class);

        $column = new CensusColumnReligion($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnReligion
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     * @return void
     */
    public function testRecordReligion(): void
    {
        $individual = self::createMock(Individual::class);
        $fact       = self::createMock(Fact::class);
        $fact->method('value')->willReturn('Jedi');
        $individual->method('facts')->with(['RELI'])->willReturn(new Collection([$fact]));

        $census = self::createMock(CensusInterface::class);

        $column = new CensusColumnReligion($census, '', '');

        self::assertSame('Jedi', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnReligion
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     * @return void
     */
    public function testEventReligion(): void
    {
        $individual = self::createMock(Individual::class);
        $fact       = self::createMock(Fact::class);
        $fact->method('attribute')->with('RELI')->willReturn('Jedi');
        $individual
            ->expects(self::exactly(2))
            ->method('facts')
            ->withConsecutive(
                [['RELI']],
                []
            )
            ->willReturnOnConsecutiveCalls(
                new Collection(),
                new Collection([$fact])
            );

        $census = self::createMock(CensusInterface::class);

        $column = new CensusColumnReligion($census, '', '');

        self::assertSame('Jedi', $column->generate($individual, $individual));
    }
}
