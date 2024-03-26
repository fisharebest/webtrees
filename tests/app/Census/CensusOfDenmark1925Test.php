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
use PHPUnit\Framework\Attributes\CoversClass;


#[CoversClass(CensusOfDenmark1925::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfDenmark1925Test extends TestCase
{
    /**
     * Test the census place and date
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfDenmark1925();

        self::assertSame('Danmark', $census->censusPlace());
        self::assertSame('05 NOV 1925', $census->censusDate());
    }

    /**
     * Test the census columns
     */
    public function testColumns(): void
    {
        $census  = new CensusOfDenmark1925();
        $columns = $census->columns();

        self::assertCount(11, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnSexMK::class, $columns[1]);
        self::assertInstanceOf(CensusColumnBirthDaySlashMonth::class, $columns[2]);
        self::assertInstanceOf(CensusColumnBirthYear::class, $columns[3]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[4]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[5]);
        self::assertInstanceOf(CensusColumnConditionDanish::class, $columns[6]);
        self::assertInstanceOf(CensusColumnRelationToHead::class, $columns[7]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[10]);

        self::assertSame('Navn', $columns[0]->abbreviation());
        self::assertSame('Køn', $columns[1]->abbreviation());
        self::assertSame('Fødselsdag', $columns[2]->abbreviation());
        self::assertSame('Fødselsaar', $columns[3]->abbreviation());
        self::assertSame('Fødested', $columns[4]->abbreviation());
        self::assertSame('Statsborgerforhold', $columns[5]->abbreviation());
        self::assertSame('Civilstand', $columns[6]->abbreviation());
        self::assertSame('Stilling i familien', $columns[7]->abbreviation());
        self::assertSame('Erhverv', $columns[8]->abbreviation());
        self::assertSame('Bopæl', $columns[9]->abbreviation());
        self::assertSame('Anmærkninger', $columns[10]->abbreviation());

        self::assertSame('Samtlige Personers Navn (ogsaa Fornavn). Ved Børn, endnu uden Navn, sættes „Dreng“ eller „Pige“. Midlertidig fraværerade Personer anføres ikke her, men paa Skemaeta Bagside)', $columns[0]->title());
        self::assertSame('Kjønnet. Mandkøn (M) eller Kvindekøn (K).', $columns[1]->title());
        self::assertSame('', $columns[2]->title());
        self::assertSame('', $columns[3]->title());
        self::assertSame('', $columns[4]->title());
        self::assertSame('', $columns[5]->title());
        self::assertSame('Ægteskabelig Stillinge. Ugift (U), Gift (G), Enkemand eller Enke (E), Separeret (S), Fraskilt (F).', $columns[6]->title());
        self::assertSame('Stilling i Familien: Husfader, Husmoder, Barn, Slangtning o.l., Tjenestetyende, Logerende, Pensioner', $columns[7]->title());
        self::assertSame('Erhverv eller Livsstilling', $columns[8]->title());
        self::assertSame('Bopæl den 5. Novbr. 1924', $columns[9]->title());
        self::assertSame('Anmærkninger', $columns[10]->title());
    }
}
