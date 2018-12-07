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
 * Test harness for the class CensusOfDeutschland1900
 */
class CensusOfDeutschland1900Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDeutschland1900
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfDeutschland1900();

        $this->assertSame('Mecklenburg-Schwerin, Deutschland', $census->censusPlace());
        $this->assertSame('01 DEC 1900', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDeutschland1900
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfDeutschland1900();
        $columns = $census->columns();

        $this->assertCount(27, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnGivenNames', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnSurname', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnRelationToHeadGerman', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[7]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[8]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[9]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnFullName', $columns[10]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[11]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[12]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthDayDotMonthYear', $columns[13]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthPlace', $columns[14]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[15]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnOccupation', $columns[16]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[17]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[18]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[19]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[20]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnReligion', $columns[21]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[22]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[23]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[24]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[25]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[26]);

        $this->assertSame('Lfd.Nr.', $columns[0]->abbreviation());
        $this->assertSame('Vorname', $columns[1]->abbreviation());
        $this->assertSame('Familienname', $columns[2]->abbreviation());
        $this->assertSame('Stellung', $columns[3]->abbreviation());
        $this->assertSame('männlich', $columns[4]->abbreviation());
        $this->assertSame('weiblich', $columns[5]->abbreviation());
        $this->assertSame('Blind', $columns[6]->abbreviation());
        $this->assertSame('Taubstumm', $columns[7]->abbreviation());
        $this->assertSame('Bemerkungen', $columns[8]->abbreviation());
        $this->assertSame('', $columns[9]->abbreviation());
        $this->assertSame('ZK 1.Name', $columns[10]->abbreviation());
        $this->assertSame('ZK 2.Geschlecht', $columns[11]->abbreviation());
        $this->assertSame('ZK 3.Familienstand', $columns[12]->abbreviation());
        $this->assertSame('ZK 4.Alter', $columns[13]->abbreviation());
        $this->assertSame('ZK 5.Geburtsort', $columns[14]->abbreviation());
        $this->assertSame('ZK 5.Land/Provinz', $columns[15]->abbreviation());
        $this->assertSame('ZK 6.Beruf/Stand', $columns[16]->abbreviation());
        $this->assertSame('ZK 7a.Gemeinde Wohnort', $columns[17]->abbreviation());
        $this->assertSame('ZK 7a.Land/Provinz', $columns[18]->abbreviation());
        $this->assertSame('ZK 7b.Gemeinde Erwerbsort', $columns[19]->abbreviation());
        $this->assertSame('ZK 7b.Land/Provinz', $columns[20]->abbreviation());
        $this->assertSame('ZK 8.Religion', $columns[21]->abbreviation());
        $this->assertSame('ZK 9.Muttersprache', $columns[22]->abbreviation());
        $this->assertSame('ZK 10.StA', $columns[23]->abbreviation());
        $this->assertSame('ZK 11.Dienstgrad', $columns[24]->abbreviation());
        $this->assertSame('ZK 11.Einheit', $columns[25]->abbreviation());
        $this->assertSame('ZK 12.Gebrechen', $columns[26]->abbreviation());

        $this->assertSame('Laufende Nummer', $columns[0]->title());
        $this->assertSame('Vorname', $columns[1]->title());
        $this->assertSame('Familienname', $columns[2]->title());
        $this->assertSame('Verwandtschaft oder sonstige Stellung zum Haushaltungsvorstand', $columns[3]->title());
        $this->assertSame('Geschlecht männlich', $columns[4]->title());
        $this->assertSame('Geschlecht weiblich', $columns[5]->title());
        $this->assertSame('Blind auf beiden Augen', $columns[6]->title());
        $this->assertSame('Taubstumm', $columns[7]->title());
        $this->assertSame('Bemerkungen', $columns[8]->title());
        $this->assertSame('Nachfolgend die detaillierten Zählkartenangaben', $columns[9]->title());
        $this->assertSame('1. Vor- und Familienname:', $columns[10]->title());
        $this->assertSame('2. Geschlecht:', $columns[11]->title());
        $this->assertSame('3. Familienstand:', $columns[12]->title());
        $this->assertSame('4. Alter: geboren den ... im Jahre ...', $columns[13]->title());
        $this->assertSame('5. Geburtsort: ... im Bezirk (Amt) ...', $columns[14]->title());
        $this->assertSame('für außerhalb des Großherzogthums Geborene auch Geburtsland, für in Preußen Geborene auch Provinz: ...', $columns[15]->title());
        $this->assertSame('6. Beruf, Stand, Erwerb, Gewerbe, Geschäft oder Nahrungszweig:', $columns[16]->title());
        $this->assertSame('7.a. Gemeinde (Ortschaft), in welcher der Wohnort (Wohnung), bei verheiratheten Personen der Familienwohnsitz liegt:', $columns[17]->title());
        $this->assertSame('für außerhalb des Großherzogthums Wohnende auch Staat und für in Preußen Wohnende auch Provinz: ...', $columns[18]->title());
        $this->assertSame('7.b. Gemeinde (Ortschaft), in welcher der Beruf (die Erwerbsthätigkeit) zur Zeit ausgeübt wird oder zuletzt ausgeübt wurde:', $columns[19]->title());
        $this->assertSame('für außerhalb des Großherzogthums Arbeitende auch Staat und für in Preußen Arbeitende auch Provinz: ...', $columns[20]->title());
        $this->assertSame('8. Religionsbekenntnis:', $columns[21]->title());
        $this->assertSame('9. Muttersprache (ob deutsch oder welche andere Sprache?):', $columns[22]->title());
        $this->assertSame('10. Staatsangehörigkeit:', $columns[23]->title());
        $this->assertSame('11. Für Militärpersonen im aktiven Dienste: Dienstgrad:', $columns[24]->title());
        $this->assertSame('Truppentheil, Kommando- oder Verwaltungsbehörde:', $columns[25]->title());
        $this->assertSame('12. Etwaige körperliche Mängel und Gebrechen:', $columns[26]->title());
    }
}
