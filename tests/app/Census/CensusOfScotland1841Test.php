<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use Fisharebest\Webtrees\TestCase;

/**
 * @covers \Fisharebest\Webtrees\Census\CensusOfScotland1841
 */
class CensusOfScotland1841Test extends TestCase
{
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfScotland1841();

        self::assertSame('Scotland', $census->censusPlace());
        self::assertSame('06 JUN 1841', $census->censusDate());
    }

    public function testColumns(): void
    {
        $census  = new CensusOfScotland1841();
        $columns = $census->columns();

        self::assertCount(6, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnAgeMale5Years::class, $columns[1]);
        self::assertInstanceOf(CensusColumnAgeFemale5Years::class, $columns[2]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[4]);
        self::assertInstanceOf(CensusColumnBornForeignParts::class, $columns[5]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('AgeM', $columns[1]->abbreviation());
        self::assertSame('AgeF', $columns[2]->abbreviation());
        self::assertSame('Occupation', $columns[3]->abbreviation());
        self::assertSame('BiC', $columns[4]->abbreviation());
        self::assertSame('EIF', $columns[5]->abbreviation());

        self::assertSame('Name', $columns[0]->title());
        self::assertSame('Age (males)', $columns[1]->title());
        self::assertSame('Age (females)', $columns[2]->title());
        self::assertSame('Profession, trade, employment or of independent means', $columns[3]->title());
        self::assertSame('Born in same county', $columns[4]->title());
        self::assertSame('Born in England, Ireland or foreign parts', $columns[5]->title());
    }
}
