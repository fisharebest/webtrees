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
 * Test harness for the class CensusOfDeutschlandNL1867
 */
class CensusOfDeutschlandNL1867Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDeutschlandNL1867
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfDeutschlandNL1867();

        $this->assertSame('Mecklenburg-Schwerin (Nachtragsliste), Deutschland', $census->censusPlace());
        $this->assertSame('03 DEC 1867', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDeutschlandNL1867
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfDeutschlandNL1867();
        $columns = $census->columns();

        $this->assertCount(18, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnGivenNames', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnSurname', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthYear', $columns[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnReligion', $columns[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[7]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[8]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[9]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[10]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[11]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[12]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[13]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[14]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[15]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[16]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[17]);

        $this->assertSame('1.Nr.', $columns[0]->abbreviation());
        $this->assertSame('2.Vorname', $columns[1]->abbreviation());
        $this->assertSame('3.Familienname', $columns[2]->abbreviation());
        $this->assertSame('4.männlich', $columns[3]->abbreviation());
        $this->assertSame('5.weiblich', $columns[4]->abbreviation());
        $this->assertSame('6.Geburtsjahr', $columns[5]->abbreviation());
        $this->assertSame('7.Religion', $columns[6]->abbreviation());
        $this->assertSame('8.ledig', $columns[7]->abbreviation());
        $this->assertSame('9.verehelicht', $columns[8]->abbreviation());
        $this->assertSame('10.verwittwet', $columns[9]->abbreviation());
        $this->assertSame('11.geschieden', $columns[10]->abbreviation());
        $this->assertSame('12.StA_M-S', $columns[11]->abbreviation());
        $this->assertSame('13.StA', $columns[12]->abbreviation());
        $this->assertSame('14.', $columns[13]->abbreviation());
        $this->assertSame('15.', $columns[14]->abbreviation());
        $this->assertSame('16.', $columns[15]->abbreviation());
        $this->assertSame('17.', $columns[16]->abbreviation());
        $this->assertSame('18.Aufenthaltsort', $columns[17]->abbreviation());

        $this->assertSame('Ordnungs-Nummer.', $columns[0]->title());
        $this->assertSame('I. Vor- und Familienname jeder Person. Vorname.', $columns[1]->title());
        $this->assertSame('I. Vor- und Familienname jeder Person. Familienname.', $columns[2]->title());
        $this->assertSame('II. Geschlecht männlich.', $columns[3]->title());
        $this->assertSame('II. Geschlecht weiblich.', $columns[4]->title());
        $this->assertSame('III. Alter.', $columns[5]->title());
        $this->assertSame('IV. Religionsbekenntnis.', $columns[6]->title());
        $this->assertSame('V. Familienstand. ledig.', $columns[7]->title());
        $this->assertSame('V. Familienstand. verehelicht.', $columns[8]->title());
        $this->assertSame('V. Familienstand. verwittwet.', $columns[9]->title());
        $this->assertSame('V. Familienstand. geschieden.', $columns[10]->title());
        $this->assertSame('VI. Staatsangehörigkeit. Mecklenburg-Schwerinscher Unterthan.', $columns[11]->title());
        $this->assertSame('VI. Staatsangehörigkeit. Anderen Staaten angehörig. Welchem Staat?', $columns[12]->title());
        $this->assertSame('VII. Art des Abwesenheit vom Zählungsorte. Nicht über ein Jahr Abwesende als See- oder Flußschiffer.', $columns[13]->title());
        $this->assertSame('VII. Art des Abwesenheit vom Zählungsorte. Nicht über ein Jahr Abwesende auf Land- oder Seereisen.', $columns[14]->title());
        $this->assertSame('VII. Art des Abwesenheit vom Zählungsorte. Nicht über ein Jahr Abwesende auf Besuch außerhalb des Orts.', $columns[15]->title());
        $this->assertSame('VII. Art des Aufenthalts am Zählungsort. Ueber ein Jahr, oder in anderer Art als nach Spalte 14 bis 16 Abwesende.', $columns[16]->title());
        $this->assertSame('VIII. Vermuthlicher Aufenthaltsort zur Zählungszeit.', $columns[17]->title());
    }
}
