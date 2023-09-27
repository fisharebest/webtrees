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
 * Test harness for the class CensusOfDenmark1930
 */
class CensusOfDenmark1930Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDenmark1930
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfDenmark1930();

        self::assertSame('Danmark', $census->censusPlace());
        self::assertSame('05 NOV 1930', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDenmark1930
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfDenmark1930();
        $columns = $census->columns();

        self::assertCount(18, $columns);
        self::assertInstanceOf(CensusColumnSurnameGivenNames::class, $columns[0]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[1]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[2]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[4]);
        self::assertInstanceOf(CensusColumnSexMK::class, $columns[5]);
        self::assertInstanceOf(CensusColumnBirthDaySlashMonth::class, $columns[6]);
        self::assertInstanceOf(CensusColumnBirthYear::class, $columns[7]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnConditionDanish::class, $columns[10]);
        self::assertInstanceOf(CensusColumnRelationToHead::class, $columns[11]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);

        self::assertSame('Navn', $columns[0]->abbreviation());
        self::assertSame('Bopæl', $columns[1]->abbreviation());
        self::assertSame('Andetsteds', $columns[2]->abbreviation());
        self::assertSame('Stede', $columns[3]->abbreviation());
        self::assertSame('Bopæl', $columns[4]->abbreviation());
        self::assertSame('Køn', $columns[5]->abbreviation());
        self::assertSame('Fødselsdag', $columns[6]->abbreviation());
        self::assertSame('Fødselsaar', $columns[7]->abbreviation());
        self::assertSame('Fødested', $columns[8]->abbreviation());
        self::assertSame('Statsborgerforhold', $columns[9]->abbreviation());
        self::assertSame('Civilstand', $columns[10]->abbreviation());
        self::assertSame('Stilling i familien', $columns[11]->abbreviation());
        self::assertSame('Erhverv', $columns[12]->abbreviation());
        self::assertSame('', $columns[13]->abbreviation());
        self::assertSame('', $columns[14]->abbreviation());
        self::assertSame('', $columns[15]->abbreviation());
        self::assertSame('', $columns[16]->abbreviation());
        self::assertSame('', $columns[17]->abbreviation());

        self::assertSame('', $columns[0]->title());
        self::assertSame('', $columns[1]->title());
        self::assertSame('Hvis den i Rubrik 3 opførte Person har fast Bopæl andetsteds, anføres her den faste Bopæl', $columns[2]->title());
        self::assertSame('Hvis den i Rubrik 3 opførte Person paa Tællingsdagen til Stede paa Tællingsstedet? Ja eller Nej.', $columns[3]->title());
        self::assertSame('Bopæl den 5. Novbr. 1929', $columns[4]->title());
        self::assertSame('Kjønnet. Mandkøn (M) eller Kvindekøn (K).', $columns[5]->title());
        self::assertSame('', $columns[6]->title());
        self::assertSame('', $columns[7]->title());
        self::assertSame('', $columns[8]->title());
        self::assertSame('', $columns[9]->title());
        self::assertSame('Ægteskabelig Stillinge. Ugift (U), Gift (G), Enkemand eller Enke (E), Separeret (S), Fraskilt (F).', $columns[10]->title());
        self::assertSame('Stilling i Familien: Husfader, Husmoder, Barn, Slangtning o.l., Tjenestetyende, Logerende, Pensioner', $columns[11]->title());
        self::assertSame('', $columns[12]->title());
        self::assertSame('', $columns[13]->title());
        self::assertSame('', $columns[14]->title());
        self::assertSame('', $columns[15]->title());
        self::assertSame('', $columns[16]->title());
        self::assertSame('', $columns[17]->title());
    }
}
