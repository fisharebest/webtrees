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
 * Test harness for the class CensusOfRhodeIsland1915
 */
class CensusOfRhodeIsland1925Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfRhodeIsland1925
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfRhodeIsland1925();

        self::assertSame('Rhode Island, United States', $census->censusPlace());
        self::assertSame('APR 1925', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfRhodeIsland1925
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns(): void
    {
        $census  = new CensusOfRhodeIsland1925();
        $columns = $census->columns();
        self::assertCount(7, $columns);
        self::assertInstanceOf(CensusColumnSurnameGivenNameInitial::class, $columns[0]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[1]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[2]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[3]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[4]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[5]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[6]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Relation', $columns[1]->abbreviation());
        self::assertSame('Sex', $columns[2]->abbreviation());
        self::assertSame('Race', $columns[3]->abbreviation());
        self::assertSame('Age', $columns[4]->abbreviation());
        self::assertSame('BP', $columns[5]->abbreviation());
        self::assertSame('Cit', $columns[6]->abbreviation());

        self::assertSame('Name', $columns[0]->title());
        self::assertSame('Relationship of this person to head of the family', $columns[1]->title());
        self::assertSame('Sex', $columns[2]->title());
        self::assertSame('Color or race', $columns[3]->title());
        self::assertSame('Age at last birthday', $columns[4]->title());
        self::assertSame('Place of birth', $columns[5]->title());
        self::assertSame('Citzenship', $columns[6]->title());
    }
}
