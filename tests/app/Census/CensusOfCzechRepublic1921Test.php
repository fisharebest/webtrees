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
 * Test harness for the class CensusOfCzechRepublic1921
 */
class CensusOfCzechRepublic1921Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCzechRepublic1921
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfCzechRepublic1921();

        self::assertSame('Česko', $census->censusPlace());
        self::assertSame('15 FEB 1921', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfCzechRepublic1921
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns(): void
    {
        $census  = new CensusOfCzechRepublic1921();
        $columns = $census->columns();

        self::assertCount(20, $columns);
        self::assertInstanceOf(CensusColumnNull::class, $columns[0]);
        self::assertInstanceOf(CensusColumnSurname::class, $columns[1]);
        self::assertInstanceOf(CensusColumnGivenNames::class, $columns[2]);
        self::assertInstanceOf(CensusColumnRelationToHead::class, $columns[3]);
        self::assertInstanceOf(CensusColumnSexMZ::class, $columns[4]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[5]);
        self::assertInstanceOf(CensusColumnBirthDayDotMonthYear::class, $columns[6]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[7]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[10]);
        self::assertInstanceOf(CensusColumnReligion::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[18]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[19]);

        self::assertSame('Ř.č.', $columns[0]->abbreviation());
        self::assertSame('Příjmení', $columns[1]->abbreviation());
        self::assertSame('Jméno', $columns[2]->abbreviation());
        self::assertSame('Vztah', $columns[3]->abbreviation());
        self::assertSame('Pohlaví', $columns[4]->abbreviation());
        self::assertSame('Stav', $columns[5]->abbreviation());
        self::assertSame('Narození', $columns[6]->abbreviation());
        self::assertSame('Rodiště', $columns[7]->abbreviation());
        self::assertSame('Bydlí od', $columns[8]->abbreviation());
        self::assertSame('Přísluší', $columns[9]->abbreviation());
        self::assertSame('Národnost', $columns[10]->abbreviation());
        self::assertSame('Vyznání', $columns[11]->abbreviation());
        self::assertSame('Gramotnost', $columns[12]->abbreviation());
        self::assertSame('Povolání', $columns[13]->abbreviation());
        self::assertSame('Postavení', $columns[14]->abbreviation());
        self::assertSame('Podnik', $columns[15]->abbreviation());
        self::assertSame('Měl povolání 1914', $columns[16]->abbreviation());
        self::assertSame('Povolání 1914', $columns[17]->abbreviation());
        self::assertSame('Postavení 1914', $columns[18]->abbreviation());
        self::assertSame('Poznámka', $columns[19]->abbreviation());

        self::assertSame('Řadové číslo', $columns[0]->title());
        self::assertSame('jméno rodinné', $columns[1]->title());
        self::assertSame('Jméno (křestni)', $columns[2]->title());
        self::assertSame('', $columns[3]->title());
        self::assertSame('', $columns[4]->title());
        self::assertSame('Rodinný stav', $columns[5]->title());
        self::assertSame('Datum narození', $columns[6]->title());
        self::assertSame('Rodná obec, Soudní okres, Země', $columns[7]->title());
        self::assertSame('Od kdy bydlí zapsána osoba v obci?', $columns[8]->title());
        self::assertSame('Domovské právo', $columns[9]->title());
        self::assertSame('Mateřský jazyk', $columns[10]->title());
        self::assertSame('', $columns[11]->title());
        self::assertSame('Znalost čtení a psaní', $columns[12]->title());
        self::assertSame('Druh povolání', $columns[13]->title());
        self::assertSame('Postavení v zaměstnání', $columns[14]->title());
        self::assertSame('', $columns[15]->title());
        self::assertSame('', $columns[16]->title());
        self::assertSame('Druh povolání dne 16. července 1914', $columns[17]->title());
        self::assertSame('Postavení v zaměstnání dne 16. července 1914', $columns[18]->title());
        self::assertSame('', $columns[19]->title());
    }
}
