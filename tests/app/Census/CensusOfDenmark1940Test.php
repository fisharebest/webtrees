<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
 * Test harness for the class CensusOfDenmark1930
 */
class CensusOfDenmark1940Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDenmark1940
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfDenmark1940();

        $this->assertSame('Danmark', $census->censusPlace());
        $this->assertSame('05 NOV 1940', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDenmark1940
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfDenmark1940();
        $columns = $census->columns();

        $this->assertCount(15, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnSurnameGivenNames', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnSexMK', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthDaySlashMonth', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthYear', $columns[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthPlace', $columns[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[7]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnConditionDanish', $columns[8]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[9]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnRelationToHead', $columns[10]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnOccupation', $columns[11]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[12]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[13]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[14]);

        $this->assertSame('Navn', $columns[0]->abbreviation());
        $this->assertSame('Nærværende', $columns[1]->abbreviation());
        $this->assertSame('Fraværende', $columns[2]->abbreviation());
        $this->assertSame('Køn', $columns[3]->abbreviation());
        $this->assertSame('Fødselsdag', $columns[4]->abbreviation());
        $this->assertSame('Fødselsaar', $columns[5]->abbreviation());
        $this->assertSame('Fødested', $columns[6]->abbreviation());
        $this->assertSame('Statsbergerferhold', $columns[7]->abbreviation());
        $this->assertSame('Civilstand', $columns[8]->abbreviation());
        $this->assertSame('Indgaaelse', $columns[9]->abbreviation());
        $this->assertSame('Stilling i familien', $columns[10]->abbreviation());
        $this->assertSame('Erhverv', $columns[11]->abbreviation());
        $this->assertSame('Virksomhedens', $columns[12]->abbreviation());
        $this->assertSame('Hustruen', $columns[13]->abbreviation());
        $this->assertSame('Døtre', $columns[14]->abbreviation());

        $this->assertSame('', $columns[0]->title());
        $this->assertSame('Hvis den i Rubrik 1 opførte Person er midleritidg nærværende d.v.s. har fast Bopæl ????? (er optaget under en anden Address i Folkeregistret), anføres her den faste Bopæls Adresse (Kommunens Navn og den fuldstændige Adresse i denne; for Udlændinge dog kun Landets Navn).', $columns[1]->title());
        $this->assertSame('Hvis den i Rubrik 1 opførte Person er midleritidg fraværende d.v.s. har fast Bopæl paa Tællingsstedet (er optaget underdenne Address i Folkeregistret), men den 5. Novemer ikke er til Stede paa Tællingsstedet, anføres her „fraværende“ og Adressen paa det midlertidige Opholdssted (ved Ophold i Udlandet anføres jun Landets Navn).', $columns[2]->title());
        $this->assertSame('Køn Mand (M) Kvinde (K)', $columns[3]->title());
        $this->assertSame('', $columns[4]->title());
        $this->assertSame('', $columns[5]->title());
        $this->assertSame('', $columns[6]->title());
        $this->assertSame('', $columns[7]->title());
        $this->assertSame('Ægteskabelig Stillinge. Ugift (U), Gift (G), Enkemand eller Enke (E), Separeret (S), Fraskilt (F).', $columns[8]->title());
        $this->assertSame('Date for det nuværende Ægteskabs Indgaaelse. NB." RUbrikken udfyldes ikke al Enkemaend, Enker, Separerede eller Fraskilte.', $columns[9]->title());
        $this->assertSame('', $columns[10]->title());
        $this->assertSame('', $columns[11]->title());
        $this->assertSame('Virksomhedens (Branchens) Art', $columns[12]->title());
        $this->assertSame('Besvares kun af Hustruen og hjemmeboende Børn over 14 Aar', $columns[13]->title());
        $this->assertSame('Besvares kun af hjemmeboende Døtre over 14 Aar', $columns[14]->title());
    }
}
