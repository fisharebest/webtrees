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
 * Test harness for the class CensusOfDeutschland1919
 */
class CensusOfDeutschland1919Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDeutschland1919
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfDeutschland1919();

        $this->assertSame('Mecklenburg-Schwerin, Deutschland', $census->censusPlace());
        $this->assertSame('08 OCT 1919', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDeutschland1919
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfDeutschland1919();
        $columns = $census->columns();

        $this->assertCount(17, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnGivenNames', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnSurname', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnRelationToHeadGerman', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthDay', $columns[7]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthMonth', $columns[8]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthYear', $columns[9]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthPlace', $columns[10]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[11]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[12]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[13]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[14]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[15]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[16]);

        $this->assertSame('Nummer', $columns[0]->abbreviation());
        $this->assertSame('Vorname', $columns[1]->abbreviation());
        $this->assertSame('Familienname', $columns[2]->abbreviation());
        $this->assertSame('Stellung im Haushalt', $columns[3]->abbreviation());
        $this->assertSame('männlich', $columns[4]->abbreviation());
        $this->assertSame('weiblich', $columns[5]->abbreviation());
        $this->assertSame('Familienstand', $columns[6]->abbreviation());
        $this->assertSame('Geburts-Tag', $columns[7]->abbreviation());
        $this->assertSame('Geburts-Monat', $columns[8]->abbreviation());
        $this->assertSame('Geburts-Jahr', $columns[9]->abbreviation());
        $this->assertSame('Geburtsort', $columns[10]->abbreviation());
        $this->assertSame('Amt, Kreis, Bezirk', $columns[11]->abbreviation());
        $this->assertSame('StA', $columns[12]->abbreviation());
        $this->assertSame('Gemeinde Brotversorgung', $columns[13]->abbreviation());
        $this->assertSame('Wohn-/ Aufenthaltsort', $columns[14]->abbreviation());
        $this->assertSame('Dienstgrad', $columns[15]->abbreviation());
        $this->assertSame('Kriegsgefangener', $columns[16]->abbreviation());

        $this->assertSame('Laufende Nummer', $columns[0]->title());
        $this->assertSame('Vorname', $columns[1]->title());
        $this->assertSame('Familienname', $columns[2]->title());
        $this->assertSame('Stellung im Haushalt', $columns[3]->title());
        $this->assertSame('Geschlecht männlich', $columns[4]->title());
        $this->assertSame('Geschlecht weiblich', $columns[5]->title());
        $this->assertSame('Familienstand', $columns[6]->title());
        $this->assertSame('Geburts-Tag', $columns[7]->title());
        $this->assertSame('Geburts-Monat', $columns[8]->title());
        $this->assertSame('Geburts-Jahr', $columns[9]->title());
        $this->assertSame('Name des Geburtsorts', $columns[10]->title());
        $this->assertSame('Amt, Kreis oder sonstiger obrigkeitlicher Bezirk', $columns[11]->title());
        $this->assertSame('Staatsangehörigkeit', $columns[12]->title());
        $this->assertSame('Gemeinde der Brotversorgung', $columns[13]->title());
        $this->assertSame('Wohnort bei nur vorübergehend Anwesenden. Aufenthaltsort bei vorübergehend Abwesenden', $columns[14]->title());
        $this->assertSame('Für Militärpersonen: Angabe des Dienstgrades', $columns[15]->title());
        $this->assertSame('Angabe ob Kriegsgefangener', $columns[16]->title());
    }
}
