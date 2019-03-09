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
namespace Fisharebest\Webtrees\Census;

/**
 * Test harness for the class CensusOfDenmark1890
 */
class CensusOfDenmark1890Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDenmark1890
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfDenmark1890();

        $this->assertSame('Danmark', $census->censusPlace());
        $this->assertSame('01 FEB 1890', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDenmark1890
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfDenmark1890();
        $columns = $census->columns();

        $this->assertCount(15, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnFullName', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnSexMK', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnAge', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnConditionDanish', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnReligion', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthPlace', $columns[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnRelationToHead', $columns[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnOccupation', $columns[7]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[8]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[9]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[10]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[11]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[12]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[13]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[14]);

        $this->assertSame('Navn', $columns[0]->abbreviation());
        $this->assertSame('Køn', $columns[1]->abbreviation());
        $this->assertSame('Alder', $columns[2]->abbreviation());
        $this->assertSame('Civilstand', $columns[3]->abbreviation());
        $this->assertSame('Trossamfund', $columns[4]->abbreviation());
        $this->assertSame('Fødested', $columns[5]->abbreviation());
        $this->assertSame('Stilling i familien', $columns[6]->abbreviation());
        $this->assertSame('Erhverv', $columns[7]->abbreviation());
        $this->assertSame('Erhvervsstedet', $columns[8]->abbreviation());
        $this->assertSame('Døvstumme', $columns[9]->abbreviation());
        $this->assertSame('Døve', $columns[10]->abbreviation());
        $this->assertSame('Blinde', $columns[11]->abbreviation());
        $this->assertSame('Idioter', $columns[12]->abbreviation());
        $this->assertSame('Sindssyge', $columns[13]->abbreviation());
        $this->assertSame('Anmærkninger', $columns[14]->abbreviation());

        $this->assertSame('Samtlige Personers fulde Navn.', $columns[0]->title());
        $this->assertSame('Kjønnet. Mandkøn (M.) eller Kvindekøn (Kv.).', $columns[1]->title());
        $this->assertSame('Alder. Alderen anføres med det fyldte Aar, men for Børn, der ikke have fyldt 1 Aar, anføres „Under 1 Aar“ of Fødselsdagen.', $columns[2]->title());
        $this->assertSame('Ægteskabelig Stillinge. Ugift (U.), Gift (G.), Enkemand eller Enke (E.), Separeret (S.), Fraskilt (F.).', $columns[3]->title());
        $this->assertSame('Trossamfund („Folkekirken“ eller andetSamfund, saasom „det frilutheranske“, „det romersk katholske“, det „mosaiske“ o.s.v.).', $columns[4]->title());
        $this->assertSame('Fødested, nemlig Sognets og Amtets eller Kjøbstadens (eller Handelpladsens) Navn, og for de i Bilandene Fødte samt for Udlændinge Landet, hvori de ere fødte.', $columns[5]->title());
        $this->assertSame('Stilling i Familien (Husfader, Husmoder, Barn, Tjenestetyende, Logerende o.s.v.).', $columns[6]->title());
        $this->assertSame('Erhverv (Embede, Forretning, Næringsvej og Titel, samt Vedkommendes Stilling som Hovedperson eller Medhjælper, Forvalter, Svend eller Dreng o.s.v.). - Arten af Erhvervet (Gaardejer, Husmand, Grovsmed, Vognfabrikant, Høker o.s.v.). - Under Fattigforsørgelse.', $columns[7]->title());
        $this->assertSame('Erhvervsstedet (Beboelseskommunen eller hvilken anden Kommune).', $columns[8]->title());
        $this->assertSame('Døvstumme.', $columns[9]->title());
        $this->assertSame('Døve (Hørelson aldeles berøvet).', $columns[10]->title());
        $this->assertSame('Blinde (Synet aldeles borsvet).', $columns[11]->title());
        $this->assertSame('Uden Forstandsovner (Idioter).', $columns[12]->title());
        $this->assertSame('Sindssyge.', $columns[13]->title());
        $this->assertSame('Anmærkninger.', $columns[14]->title());
    }
}
