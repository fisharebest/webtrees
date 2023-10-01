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
 * Test harness for the class CensusOfSlovakia1940
 */
class CensusOfSlovakia1940Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfSlovakia1940
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfSlovakia1940();

        self::assertSame('Slovensko', $census->censusPlace());
        self::assertSame('14 DEC 1940', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfSlovakia1940
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns(): void
    {
        $census  = new CensusOfSlovakia1940();
        $columns = $census->columns();

        self::assertCount(22, $columns);
        self::assertInstanceOf(CensusColumnNull::class, $columns[0]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[1]);
        self::assertInstanceOf(CensusColumnSurname::class, $columns[2]);
        self::assertInstanceOf(CensusColumnGivenNames::class, $columns[3]);
        self::assertInstanceOf(CensusColumnRelationToHead::class, $columns[4]);
        self::assertInstanceOf(CensusColumnSexMZ::class, $columns[5]);
        self::assertInstanceOf(CensusColumnBirthDayDotMonthYear::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnReligion::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[17]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[18]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[19]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[20]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[21]);

        self::assertSame('Č. b.', $columns[0]->abbreviation());
        self::assertSame('Č. os.', $columns[1]->abbreviation());
        self::assertSame('Priezvisko', $columns[2]->abbreviation());
        self::assertSame('Meno', $columns[3]->abbreviation());
        self::assertSame('Pomer', $columns[4]->abbreviation());
        self::assertSame('Poh.', $columns[5]->abbreviation());
        self::assertSame('Nar.', $columns[6]->abbreviation());
        self::assertSame('Stav', $columns[7]->abbreviation());
        self::assertSame('Rodisko', $columns[8]->abbreviation());
        self::assertSame('P. trv.', $columns[9]->abbreviation());
        self::assertSame('Byd. doč.', $columns[10]->abbreviation());
        self::assertSame('Dát. prisťahovania', $columns[11]->abbreviation());
        self::assertSame('Odkiaľ', $columns[12]->abbreviation());
        self::assertSame('Príslušnosť', $columns[13]->abbreviation());
        self::assertSame('Národnosť', $columns[14]->abbreviation());
        self::assertSame('Náb.', $columns[15]->abbreviation());
        self::assertSame('Čít./Pís.', $columns[16]->abbreviation());
        self::assertSame('Povolanie', $columns[17]->abbreviation());
        self::assertSame('Postavenie', $columns[18]->abbreviation());
        self::assertSame('Závod', $columns[19]->abbreviation());
        self::assertSame('Odvetvie', $columns[20]->abbreviation());
        self::assertSame('Poz.', $columns[21]->abbreviation());

        self::assertSame('Radové číslo bytu', $columns[0]->title());
        self::assertSame('Radové číslo osôb v byte', $columns[1]->title());
        self::assertSame('Priezvisko (meno rodinné)', $columns[2]->title());
        self::assertSame('Meno (krstné alebo rodné)', $columns[3]->title());
        self::assertSame('Príbuzenský alebo iný pomer k hlave domácnosti', $columns[4]->title());
        self::assertSame('Pohlavie mužské alebo ženské', $columns[5]->title());
        self::assertSame('Deň, mesiac a rok narodenia', $columns[6]->title());
        self::assertSame('Rodinný stav', $columns[7]->title());
        self::assertSame('a) rodná obec, b) okres', $columns[8]->title());
        self::assertSame('Je sčítaný v obci prítomný trvale?', $columns[9]->title());
        self::assertSame('Ak dočasne, nech uvedie svoje riadne bydlisko', $columns[10]->title());
        self::assertSame('Ak sčítaný nebýva v obci pobytu od narodenia, kedy sa prisťahoval', $columns[11]->title());
        self::assertSame('Ak sčítaný nebýva v obci pobytu od narodenia, odkiaľ sa prisťahoval', $columns[12]->title());
        self::assertSame('Státna príslušnosť', $columns[13]->title());
        self::assertSame('Národnosť', $columns[14]->title());
        self::assertSame('Náboženstvo (cirkevná príslušnosť alebo bez vyznania', $columns[15]->title());
        self::assertSame('Znalosť čítania a písania len u osôb starších 6tich rokov', $columns[16]->title());
        self::assertSame('Druh povolania', $columns[17]->title());
        self::assertSame('Postavenie v povolaní', $columns[18]->title());
        self::assertSame('Názov a sídlo závodu (firmy, úradu)', $columns[19]->title());
        self::assertSame('K akému odvetviu patrí závod (firma, úrad)', $columns[20]->title());
        self::assertSame('Poznámka', $columns[21]->title());
    }
}
