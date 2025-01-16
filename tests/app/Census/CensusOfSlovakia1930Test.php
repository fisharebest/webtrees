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
 * Test harness for the class CensusOfSlovakia1930
 */
class CensusOfSlovakia1930Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfSlovakia1930
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfSlovakia1930();

        self::assertSame('Slovensko', $census->censusPlace());
        self::assertSame('01 DEC 1930', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfSlovakia1930
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns(): void
    {
        $census  = new CensusOfSlovakia1930();
        $columns = $census->columns();

        self::assertCount(27, $columns);
        self::assertInstanceOf(CensusColumnNull::class, $columns[0]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[1]);
        self::assertInstanceOf(CensusColumnSurname::class, $columns[2]);
        self::assertInstanceOf(CensusColumnGivenNames::class, $columns[3]);
        self::assertInstanceOf(CensusColumnRelationToHead::class, $columns[4]);
        self::assertInstanceOf(CensusColumnSexMZ::class, $columns[5]);
        self::assertInstanceOf(CensusColumnBirthDayDotMonthYear::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnReligion::class, $columns[17]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[18]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[19]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[20]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[21]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[22]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[23]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[24]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[25]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[26]);

        self::assertSame('Č. b.', $columns[0]->abbreviation());
        self::assertSame('Č. os.', $columns[1]->abbreviation());
        self::assertSame('Priezvisko', $columns[2]->abbreviation());
        self::assertSame('Meno', $columns[3]->abbreviation());
        self::assertSame('Pomer', $columns[4]->abbreviation());
        self::assertSame('Poh.', $columns[5]->abbreviation());
        self::assertSame('Nar.', $columns[6]->abbreviation());
        self::assertSame('Stav', $columns[7]->abbreviation());
        self::assertSame('Dát. sňatku', $columns[8]->abbreviation());
        self::assertSame('Dát. ovodv.', $columns[9]->abbreviation());
        self::assertSame('Poč. detí', $columns[10]->abbreviation());
        self::assertSame('Zomrelo', $columns[11]->abbreviation());
        self::assertSame('Rodisko', $columns[12]->abbreviation());
        self::assertSame('Dát. prisťahovania', $columns[13]->abbreviation());
        self::assertSame('Odkiaľ', $columns[14]->abbreviation());
        self::assertSame('Príslušnosť', $columns[15]->abbreviation());
        self::assertSame('Národnosť', $columns[16]->abbreviation());
        self::assertSame('Náb.', $columns[17]->abbreviation());
        self::assertSame('Čít./Pís.', $columns[18]->abbreviation());
        self::assertSame('Povolanie', $columns[19]->abbreviation());
        self::assertSame('Postavenie', $columns[20]->abbreviation());
        self::assertSame('Závod', $columns[21]->abbreviation());
        self::assertSame('P. trv.', $columns[22]->abbreviation());
        self::assertSame('Byd. doč.', $columns[23]->abbreviation());
        self::assertSame('P. doč.', $columns[24]->abbreviation());
        self::assertSame('Vady', $columns[25]->abbreviation());
        self::assertSame('Poz.', $columns[26]->abbreviation());

        self::assertSame('Radové číslo bytu', $columns[0]->title());
        self::assertSame('Radové číslo osôb v dome', $columns[1]->title());
        self::assertSame('Priezvisko (meno rodinné)', $columns[2]->title());
        self::assertSame('Meno (krstné alebo rodné)', $columns[3]->title());
        self::assertSame('Príbuzenský alebo iný pomer k prednostovi domácnosti', $columns[4]->title());
        self::assertSame('Pohlavie (či mužské či ženské', $columns[5]->title());
        self::assertSame('Deň, mesiac a rok narodenia', $columns[6]->title());
        self::assertSame('Rodinný stav', $columns[7]->title());
        self::assertSame('U žien, ktoré sú alebo boly vydaté dátum posledného sňatku', $columns[8]->title());
        self::assertSame('u ovdov. žien dátum ovdovenia, u rozvedených a rozlúčených dátum rozvodu alebo rozluky', $columns[9]->title());
        self::assertSame('U žien, ktoré sú alebo boly vydaté počet všetkých žive narodených detí v poslednom manželstve', $columns[10]->title());
        self::assertSame('z nich zomrelo', $columns[11]->title());
        self::assertSame('a) rodná obec, b) pol. okres, c) krajina', $columns[12]->title());
        self::assertSame('Jestliže sčítaný nebýva v obci pobytu od narodenia, kedy sa prisťahoval', $columns[13]->title());
        self::assertSame('Jestliže sčítaný nebýva v obci pobytu od narodenia, odkiaľ sa prisťahoval', $columns[14]->title());
        self::assertSame('Státna príslušnosť, u čsl. štátnych príslušníkov mimotoho tiež domovská príslušnosť', $columns[15]->title());
        self::assertSame('Národnosť (materský jazyk', $columns[16]->title());
        self::assertSame('Náboženské vyznanie (cirkevná príslušnosť alebo bez vyznania', $columns[17]->title());
        self::assertSame('Znalosť čítania a písania len u osôb starších 6tich rokov', $columns[18]->title());
        self::assertSame('druh povolania', $columns[19]->title());
        self::assertSame('postavenie v povolaní', $columns[20]->title());
        self::assertSame('bližšie označenie závodu a miesta závodu', $columns[21]->title());
        self::assertSame('Či je sčítaný v obci prítomný trvale alebo len dočasne', $columns[22]->title());
        self::assertSame('Jestliže dočasne, nech uvedie svoje riadne bydlisko', $columns[23]->title());
        self::assertSame('Prítomný dočasne - do jedného mesiaca', $columns[24]->title());
        self::assertSame('Telesné vady - či sčítaný je slepý na obe oči, hluchý, nemý, hluchonemý, či nemá ruku alebo nohu', $columns[25]->title());
        self::assertSame('Poznámka', $columns[26]->title());
    }
}
