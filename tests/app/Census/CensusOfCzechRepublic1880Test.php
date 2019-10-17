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

use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class CensusOfCzechRepublic1880
 */
class CensusOfCzechRepublic1880Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCzechRepublic1880
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfCzechRepublic1880();

        $this->assertSame('Česko', $census->censusPlace());
        $this->assertSame('31 DEC 1880', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCzechRepublic1880
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfCzechRepublic1880();
        $columns = $census->columns();

        $this->assertCount(13, $columns);
        $this->assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        $this->assertInstanceOf(CensusColumnRelationToHead::class, $columns[1]);
        $this->assertInstanceOf(CensusColumnSexMZ::class, $columns[2]);
        $this->assertInstanceOf(CensusColumnBirthDaySlashMonthYear::class, $columns[3]);
        $this->assertInstanceOf(CensusColumnBirthPlace::class, $columns[4]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[5]);
        $this->assertInstanceOf(CensusColumnReligion::class, $columns[6]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[7]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[8]);
        $this->assertInstanceOf(CensusColumnOccupation::class, $columns[9]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[10]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[11]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[12]);

        $this->assertSame('Jméno', $columns[0]->abbreviation());
        $this->assertSame('Vztah', $columns[1]->abbreviation());
        $this->assertSame('Pohlaví', $columns[2]->abbreviation());
        $this->assertSame('Narození', $columns[3]->abbreviation());
        $this->assertSame('Rodiště', $columns[4]->abbreviation());
        $this->assertSame('Přísluší', $columns[5]->abbreviation());
        $this->assertSame('Vyznání', $columns[6]->abbreviation());
        $this->assertSame('Stav', $columns[7]->abbreviation());
        $this->assertSame('Jazyk', $columns[8]->abbreviation());
        $this->assertSame('Povolání', $columns[9]->abbreviation());
        $this->assertSame('Postavení', $columns[10]->abbreviation());
        $this->assertSame('Gramotnost', $columns[11]->abbreviation());
        $this->assertSame('Druh pobytu', $columns[12]->abbreviation());

        $this->assertSame('', $columns[0]->title());
        $this->assertSame('', $columns[1]->title());
        $this->assertSame('', $columns[2]->title());
        $this->assertSame('Datum narození', $columns[3]->title());
        $this->assertSame('Místo narození', $columns[4]->title());
        $this->assertSame('Domovské právo', $columns[5]->title());
        $this->assertSame('', $columns[6]->title());
        $this->assertSame('Rodinný stav', $columns[7]->title());
        $this->assertSame('Jazyk v obcování', $columns[8]->title());
        $this->assertSame('', $columns[9]->title());
        $this->assertSame('Postavení v zaměstnání', $columns[10]->title());
        $this->assertSame('Znalost čtení a psaní', $columns[11]->title());
        $this->assertSame('Pobyt dočasný nebo trvalý', $columns[12]->title());
    }
}
