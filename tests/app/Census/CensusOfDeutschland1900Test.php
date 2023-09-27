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
 * Test harness for the class CensusOfDeutschland1900
 */
class CensusOfDeutschland1900Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDeutschland1900
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfDeutschland1900();

        self::assertSame('Mecklenburg-Schwerin, Deutschland', $census->censusPlace());
        self::assertSame('01 DEC 1900', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDeutschland1900
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfDeutschland1900();
        $columns = $census->columns();

        self::assertCount(27, $columns);
        self::assertInstanceOf(CensusColumnNull::class, $columns[0]);
        self::assertInstanceOf(CensusColumnGivenNames::class, $columns[1]);
        self::assertInstanceOf(CensusColumnSurname::class, $columns[2]);
        self::assertInstanceOf(CensusColumnRelationToHeadGerman::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[4]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[5]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnBirthDayDotMonthYear::class, $columns[13]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[18]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[19]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[20]);
        self::assertInstanceOf(CensusColumnReligion::class, $columns[21]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[22]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[23]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[24]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[25]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[26]);

        self::assertSame('Lfd.Nr.', $columns[0]->abbreviation());
        self::assertSame('Vorname', $columns[1]->abbreviation());
        self::assertSame('Familienname', $columns[2]->abbreviation());
        self::assertSame('Stellung', $columns[3]->abbreviation());
        self::assertSame('männlich', $columns[4]->abbreviation());
        self::assertSame('weiblich', $columns[5]->abbreviation());
        self::assertSame('Blind', $columns[6]->abbreviation());
        self::assertSame('Taubstumm', $columns[7]->abbreviation());
        self::assertSame('Bemerkungen', $columns[8]->abbreviation());
        self::assertSame('', $columns[9]->abbreviation());
        self::assertSame('ZK 1.Name', $columns[10]->abbreviation());
        self::assertSame('ZK 2.Geschlecht', $columns[11]->abbreviation());
        self::assertSame('ZK 3.Familienstand', $columns[12]->abbreviation());
        self::assertSame('ZK 4.Alter', $columns[13]->abbreviation());
        self::assertSame('ZK 5.Geburtsort', $columns[14]->abbreviation());
        self::assertSame('ZK 5.Land/Provinz', $columns[15]->abbreviation());
        self::assertSame('ZK 6.Beruf/Stand', $columns[16]->abbreviation());
        self::assertSame('ZK 7a.Gemeinde Wohnort', $columns[17]->abbreviation());
        self::assertSame('ZK 7a.Land/Provinz', $columns[18]->abbreviation());
        self::assertSame('ZK 7b.Gemeinde Erwerbsort', $columns[19]->abbreviation());
        self::assertSame('ZK 7b.Land/Provinz', $columns[20]->abbreviation());
        self::assertSame('ZK 8.Religion', $columns[21]->abbreviation());
        self::assertSame('ZK 9.Muttersprache', $columns[22]->abbreviation());
        self::assertSame('ZK 10.StA', $columns[23]->abbreviation());
        self::assertSame('ZK 11.Dienstgrad', $columns[24]->abbreviation());
        self::assertSame('ZK 11.Einheit', $columns[25]->abbreviation());
        self::assertSame('ZK 12.Gebrechen', $columns[26]->abbreviation());

        self::assertSame('Laufende Nummer', $columns[0]->title());
        self::assertSame('Vorname', $columns[1]->title());
        self::assertSame('Familienname', $columns[2]->title());
        self::assertSame('Verwandtschaft oder sonstige Stellung zum Haushaltungsvorstand', $columns[3]->title());
        self::assertSame('Geschlecht männlich', $columns[4]->title());
        self::assertSame('Geschlecht weiblich', $columns[5]->title());
        self::assertSame('Blind auf beiden Augen', $columns[6]->title());
        self::assertSame('Taubstumm', $columns[7]->title());
        self::assertSame('Bemerkungen', $columns[8]->title());
        self::assertSame('Nachfolgend die detaillierten Zählkartenangaben', $columns[9]->title());
        self::assertSame('1. Vor- und Familienname:', $columns[10]->title());
        self::assertSame('2. Geschlecht:', $columns[11]->title());
        self::assertSame('3. Familienstand:', $columns[12]->title());
        self::assertSame('4. Alter: geboren den ... im Jahre ...', $columns[13]->title());
        self::assertSame('5. Geburtsort: ... im Bezirk (Amt) ...', $columns[14]->title());
        self::assertSame('für außerhalb des Großherzogthums Geborene auch Geburtsland, für in Preußen Geborene auch Provinz: ...', $columns[15]->title());
        self::assertSame('6. Beruf, Stand, Erwerb, Gewerbe, Geschäft oder Nahrungszweig:', $columns[16]->title());
        self::assertSame('7.a. Gemeinde (Ortschaft), in welcher der Wohnort (Wohnung), bei verheiratheten Personen der Familienwohnsitz liegt:', $columns[17]->title());
        self::assertSame('für außerhalb des Großherzogthums Wohnende auch Staat und für in Preußen Wohnende auch Provinz: ...', $columns[18]->title());
        self::assertSame('7.b. Gemeinde (Ortschaft), in welcher der Beruf (die Erwerbsthätigkeit) zur Zeit ausgeübt wird oder zuletzt ausgeübt wurde:', $columns[19]->title());
        self::assertSame('für außerhalb des Großherzogthums Arbeitende auch Staat und für in Preußen Arbeitende auch Provinz: ...', $columns[20]->title());
        self::assertSame('8. Religionsbekenntnis:', $columns[21]->title());
        self::assertSame('9. Muttersprache (ob deutsch oder welche andere Sprache?):', $columns[22]->title());
        self::assertSame('10. Staatsangehörigkeit:', $columns[23]->title());
        self::assertSame('11. Für Militärpersonen im aktiven Dienste: Dienstgrad:', $columns[24]->title());
        self::assertSame('Truppentheil, Kommando- oder Verwaltungsbehörde:', $columns[25]->title());
        self::assertSame('12. Etwaige körperliche Mängel und Gebrechen:', $columns[26]->title());
    }
}
