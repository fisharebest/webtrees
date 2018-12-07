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
 * Test harness for the class CensusOfDeutschland1867
 */
class CensusOfDeutschland1867Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDeutschland1867
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfDeutschland1867();

        $this->assertSame('Mecklenburg-Schwerin, Deutschland', $census->censusPlace());
        $this->assertSame('03 DEC 1867', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDeutschland1867
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfDeutschland1867();
        $columns = $census->columns();

        $this->assertCount(23, $columns);
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
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnRelationToHeadGerman', $columns[11]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnOccupation', $columns[12]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[13]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[14]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[15]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[16]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[17]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[18]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[19]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[20]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[21]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[22]);

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
        $this->assertSame('12.Stellung', $columns[11]->abbreviation());
        $this->assertSame('13.Stand/Beruf', $columns[12]->abbreviation());
        $this->assertSame('14.StA_M-S', $columns[13]->abbreviation());
        $this->assertSame('15.StA', $columns[14]->abbreviation());
        $this->assertSame('16.', $columns[15]->abbreviation());
        $this->assertSame('17.', $columns[16]->abbreviation());
        $this->assertSame('18.', $columns[17]->abbreviation());
        $this->assertSame('19.', $columns[18]->abbreviation());
        $this->assertSame('20.blind', $columns[19]->abbreviation());
        $this->assertSame('21.taubstumm', $columns[20]->abbreviation());
        $this->assertSame('22.blödsinnig', $columns[21]->abbreviation());
        $this->assertSame('23.irrsinnig', $columns[22]->abbreviation());

        $this->assertSame('Ordnungs-Nummer (1-15).', $columns[0]->title());
        $this->assertSame('I. Vor- und Familien-Name jeder Person. Vorname', $columns[1]->title());
        $this->assertSame('I. Vor- und Familien-Name jeder Person. Familienname.', $columns[2]->title());
        $this->assertSame('II. Geschlecht männlich.', $columns[3]->title());
        $this->assertSame('II. Geschlecht weiblich.', $columns[4]->title());
        $this->assertSame('III. Alter.', $columns[5]->title());
        $this->assertSame('IV. Religionsbekenntnis.', $columns[6]->title());
        $this->assertSame('V. Familienstand. ledig.', $columns[7]->title());
        $this->assertSame('V. Familienstand. verehelicht.', $columns[8]->title());
        $this->assertSame('V. Familienstand. verwittwet.', $columns[9]->title());
        $this->assertSame('V. Familienstand. geschieden.', $columns[10]->title());
        $this->assertSame('V. Familienstand. Verhältnis der Familienglieder zum Haushaltungsvorstand.', $columns[11]->title());
        $this->assertSame('VI. Stand, Beruf oder Vorbereitung zum Beruf, Arbeits- und Dienstverhältnis.', $columns[12]->title());
        $this->assertSame('VII. Staatsangehörigkeit. Mecklenburg-Schwerinscher Unterthan.', $columns[13]->title());
        $this->assertSame('VII. Staatsangehörigkeit. Anderen Staaten angehörig. Welchem Staat?', $columns[14]->title());
        $this->assertSame('VIII. Art des Aufenthalts am Zählungsort. Norddeutscher und Zollvereins- See- und Flußschiffer.', $columns[15]->title());
        $this->assertSame('VIII. Art des Aufenthalts am Zählungsort. Reisender im Gasthof.', $columns[16]->title());
        $this->assertSame('VIII. Art des Aufenthalts am Zählungsort. Gast der Familie (zum Besuch aus).', $columns[17]->title());
        $this->assertSame('VIII. Art des Aufenthalts am Zählungsort. Alle übrigen Anwesenden.', $columns[18]->title());
        $this->assertSame('IX. Besondere Mängel einzelner Individuen. blind auf beiden Augen.', $columns[19]->title());
        $this->assertSame('IX. Besondere Mängel einzelner Individuen. taubstumm.', $columns[20]->title());
        $this->assertSame('IX. Besondere Mängel einzelner Individuen. blödsinnig.', $columns[21]->title());
        $this->assertSame('IX. Besondere Mängel einzelner Individuen. irrsinnig.', $columns[22]->title());
    }
}
