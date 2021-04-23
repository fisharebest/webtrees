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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;

/**
 * Test harness for the class CensusColumnFullName
 */
class CensusColumnFullNameTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnFullName
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function xxxtestFullName(): void
    {
        $individual = self::createStub(Individual::class);
        $individual->method('getAllNames')->willReturn([['full' => 'Joe Bloggs']]);
        $individual->method('spouseFamilies')->willReturn(new Collection());

        $census = self::createStub(CensusInterface::class);
        $census->method('censusDate')->willReturn('');

        $column = new CensusColumnFullName($census, '', '');

        self::assertSame('Joe Bloggs', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnFullName
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testMarriedName(): void
    {
        $wife_names = [
            ['type' => 'NAME', 'full' => 'Jane Bloggs'],
            ['type' => '_MARNM', 'full' => 'Jane Smith', 'surn' => 'SMITH'],
        ];

        $husband_names = [
            ['type' => 'NAME', 'full' => 'Joe Smith', 'surn' => 'SMITH'],
        ];

        $marriage_date = new Date('02 DATE 2019');

        $marriage = self::createStub(Fact::class);
        $marriage->method('date')->willReturn($marriage_date);

        $spouse = self::createStub(Individual::class);
        $spouse->method('getAllNames')->willReturn($husband_names);

        $family = self::createStub(Family::class);
        $family->method('facts')->willReturn(new Collection([$marriage]));
        $family->method('getMarriageDate')->willReturn($marriage_date);
        $family->method('spouse')->willReturn($spouse);

        $individual = self::createStub(Individual::class);
        $individual->method('getAllNames')->willReturn($wife_names);
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = self::createStub(CensusInterface::class);
        $census->method('censusDate')->willReturn('01 JAN 2020');

        $column = new CensusColumnFullName($census, '', '');

        self::assertSame('Jane Smith', $column->generate($individual, $individual));
    }
}
