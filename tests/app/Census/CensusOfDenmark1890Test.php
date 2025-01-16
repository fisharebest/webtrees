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
 * Test harness for the class CensusOfDenmark1890
 */
class CensusOfDenmark1890Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDenmark1890
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfDenmark1890();

        self::assertSame('Danmark', $census->censusPlace());
        self::assertSame('01 FEB 1890', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDenmark1890
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns(): void
    {
        $census  = new CensusOfDenmark1890();
        $columns = $census->columns();

        self::assertCount(15, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnSexMK::class, $columns[1]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[2]);
        self::assertInstanceOf(CensusColumnConditionDanish::class, $columns[3]);
        self::assertInstanceOf(CensusColumnReligion::class, $columns[4]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[5]);
        self::assertInstanceOf(CensusColumnRelationToHead::class, $columns[6]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[7]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);

        self::assertSame('Navn', $columns[0]->abbreviation());
        self::assertSame('Køn', $columns[1]->abbreviation());
        self::assertSame('Alder', $columns[2]->abbreviation());
        self::assertSame('Civilstand', $columns[3]->abbreviation());
        self::assertSame('Trossamfund', $columns[4]->abbreviation());
        self::assertSame('Fødested', $columns[5]->abbreviation());
        self::assertSame('Stilling i familien', $columns[6]->abbreviation());
        self::assertSame('Erhverv', $columns[7]->abbreviation());
        self::assertSame('Erhvervsstedet', $columns[8]->abbreviation());
        self::assertSame('Døvstumme', $columns[9]->abbreviation());
        self::assertSame('Døve', $columns[10]->abbreviation());
        self::assertSame('Blinde', $columns[11]->abbreviation());
        self::assertSame('Idioter', $columns[12]->abbreviation());
        self::assertSame('Sindssyge', $columns[13]->abbreviation());
        self::assertSame('Anmærkninger', $columns[14]->abbreviation());

        self::assertSame('Samtlige Personers fulde Navn.', $columns[0]->title());
        self::assertSame('Kjønnet. Mandkøn (M.) eller Kvindekøn (Kv.).', $columns[1]->title());
        self::assertSame('Alder. Alderen anføres med det fyldte Aar, men for Børn, der ikke have fyldt 1 Aar, anføres „Under 1 Aar“ of Fødselsdagen.', $columns[2]->title());
        self::assertSame('Ægteskabelig Stillinge. Ugift (U.), Gift (G.), Enkemand eller Enke (E.), Separeret (S.), Fraskilt (F.).', $columns[3]->title());
        self::assertSame('Trossamfund („Folkekirken“ eller andetSamfund, saasom „det frilutheranske“, „det romersk katholske“, det „mosaiske“ o.s.v.).', $columns[4]->title());
        self::assertSame('Fødested, nemlig Sognets og Amtets eller Kjøbstadens (eller Handelpladsens) Navn, og for de i Bilandene Fødte samt for Udlændinge Landet, hvori de ere fødte.', $columns[5]->title());
        self::assertSame('Stilling i Familien (Husfader, Husmoder, Barn, Tjenestetyende, Logerende o.s.v.).', $columns[6]->title());
        self::assertSame('Erhverv (Embede, Forretning, Næringsvej og Titel, samt Vedkommendes Stilling som Hovedperson eller Medhjælper, Forvalter, Svend eller Dreng o.s.v.). - Arten af Erhvervet (Gaardejer, Husmand, Grovsmed, Vognfabrikant, Høker o.s.v.). - Under Fattigforsørgelse.', $columns[7]->title());
        self::assertSame('Erhvervsstedet (Beboelseskommunen eller hvilken anden Kommune).', $columns[8]->title());
        self::assertSame('Døvstumme.', $columns[9]->title());
        self::assertSame('Døve (Hørelson aldeles berøvet).', $columns[10]->title());
        self::assertSame('Blinde (Synet aldeles borsvet).', $columns[11]->title());
        self::assertSame('Uden Forstandsovner (Idioter).', $columns[12]->title());
        self::assertSame('Sindssyge.', $columns[13]->title());
        self::assertSame('Anmærkninger.', $columns[14]->title());
    }
}
