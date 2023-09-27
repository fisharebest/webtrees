<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
 * Test harness for the class CensusOfCzechRepublic1910
 */
class CensusOfCzechRepublic1910Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCzechRepublic1910
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfCzechRepublic1910();

        self::assertSame('Česko', $census->censusPlace());
        self::assertSame('31 DEC 1910', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCzechRepublic1910
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfCzechRepublic1910();
        $columns = $census->columns();

        self::assertCount(14, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnRelationToHead::class, $columns[1]);
        self::assertInstanceOf(CensusColumnSexMZ::class, $columns[2]);
        self::assertInstanceOf(CensusColumnBirthDaySlashMonthYear::class, $columns[3]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[4]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[5]);
        self::assertInstanceOf(CensusColumnReligion::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);

        self::assertSame('Jméno', $columns[0]->abbreviation());
        self::assertSame('Vztah', $columns[1]->abbreviation());
        self::assertSame('Pohlaví', $columns[2]->abbreviation());
        self::assertSame('Narození', $columns[3]->abbreviation());
        self::assertSame('Rodiště', $columns[4]->abbreviation());
        self::assertSame('Přísluší', $columns[5]->abbreviation());
        self::assertSame('Vyznání', $columns[6]->abbreviation());
        self::assertSame('Stav', $columns[7]->abbreviation());
        self::assertSame('Jazyk', $columns[8]->abbreviation());
        self::assertSame('Gramotnost', $columns[9]->abbreviation());
        self::assertSame('Povolání', $columns[10]->abbreviation());
        self::assertSame('Postavení', $columns[11]->abbreviation());
        self::assertSame('Druh pobytu', $columns[12]->abbreviation());
        self::assertSame('Od roku', $columns[13]->abbreviation());

        self::assertSame('', $columns[0]->title());
        self::assertSame('', $columns[1]->title());
        self::assertSame('', $columns[2]->title());
        self::assertSame('Datum narození', $columns[3]->title());
        self::assertSame('Místo narození', $columns[4]->title());
        self::assertSame('Domovské právo', $columns[5]->title());
        self::assertSame('Vyznání náoženské', $columns[6]->title());
        self::assertSame('Rodinný stav', $columns[7]->title());
        self::assertSame('Řeč obcovací', $columns[8]->title());
        self::assertSame('Znalost čtení a psaní', $columns[9]->title());
        self::assertSame('Obor povolání', $columns[10]->title());
        self::assertSame('Postavení v zaměstnání', $columns[11]->title());
        self::assertSame('Pobyt dočasný nebo trvalý', $columns[12]->title());
        self::assertSame('Počátek pobytu', $columns[13]->title());
    }
}
