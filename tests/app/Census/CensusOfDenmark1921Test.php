<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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
 * Test harness for the class CensusOfDenmark1921
 */
class CensusOfDenmark1921Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDenmark1921
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfDenmark1921();

        self::assertSame('Danmark', $census->censusPlace());
        self::assertSame('01 FEB 1921', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDenmark1921
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfDenmark1921();
        $columns = $census->columns();

        self::assertCount(14, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnSexMK::class, $columns[1]);
        self::assertInstanceOf(CensusColumnBirthDaySlashMonth::class, $columns[2]);
        self::assertInstanceOf(CensusColumnBirthYear::class, $columns[3]);
        self::assertInstanceOf(CensusColumnConditionDanish::class, $columns[4]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[5]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);
        self::assertInstanceOf(CensusColumnReligion::class, $columns[8]);
        self::assertInstanceOf(CensusColumnRelationToHead::class, $columns[9]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);

        self::assertSame('Navn', $columns[0]->abbreviation());
        self::assertSame('Køn', $columns[1]->abbreviation());
        self::assertSame('Fødselsdag', $columns[2]->abbreviation());
        self::assertSame('Fødselsaar', $columns[3]->abbreviation());
        self::assertSame('Civilstand', $columns[4]->abbreviation());
        self::assertSame('Fødested', $columns[5]->abbreviation());
        self::assertSame('', $columns[6]->abbreviation());
        self::assertSame('', $columns[7]->abbreviation());
        self::assertSame('Trossamfund', $columns[8]->abbreviation());
        self::assertSame('Stilling i familien', $columns[9]->abbreviation());
        self::assertSame('Erhverv', $columns[10]->abbreviation());
        self::assertSame('', $columns[11]->abbreviation());
        self::assertSame('', $columns[12]->abbreviation());
        self::assertSame('Anmærkninger', $columns[13]->abbreviation());

        self::assertSame('Samtlige Personers Navn (ogsaa Fornavn). Ved Børn, endnu uden Navn, sættes „Dreng“ eller „Pige“. Midlertidig fraværerade Personer anføres ikke her, men paa Skemaeta Bagside)', $columns[0]->title());
        self::assertSame('Kjønnet. Mandkøn (M) eller Kvindekøn (K).', $columns[1]->title());
        self::assertSame('', $columns[2]->title());
        self::assertSame('', $columns[3]->title());
        self::assertSame('Ægteskabelig Stillinge. Ugift (U), Gift (G), Enkemand eller Enke (E), Separeret (S), Fraskilt (F).', $columns[4]->title());
        self::assertSame('', $columns[5]->title());
        self::assertSame('', $columns[6]->title());
        self::assertSame('', $columns[7]->title());
        self::assertSame('Trossamfund (Folkekirken eller Navnet paa det Trossamfund, man tilhører, eller „udenfor Trossamfund“).', $columns[8]->title());
        self::assertSame('Stilling i Familien: Husfader, Husmoder, Barn, Slangtning o.l., Tjenestetyende, Logerende, Pensioner', $columns[9]->title());
        self::assertSame('', $columns[10]->title());
        self::assertSame('', $columns[11]->title());
        self::assertSame('', $columns[12]->title());
        self::assertSame('Anmærkninger.', $columns[13]->title());
    }
}
