<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

/**
 * Definitions for a census
 */
class CensusOfDeutschland1900 extends CensusOfDeutschland implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '01 DEC 1900';
    }

    /**
     * Where did this census occur, in GEDCOM format.
     *
     * @return string
     */
    public function censusPlace(): string
    {
        return 'Mecklenburg-Schwerin, Deutschland';
    }

    /**
     * The columns of the census.
     *
     * @return array<CensusColumnInterface>
     */
    public function columns(): array
    {
        return [
            new CensusColumnNull($this, 'Lfd.Nr.', 'Laufende Nummer'),
            new CensusColumnGivenNames($this, 'Vorname', 'Vorname'),
            new CensusColumnSurname($this, 'Familienname', 'Familienname'),
            new CensusColumnRelationToHeadGerman($this, 'Stellung', 'Verwandtschaft oder sonstige Stellung zum Haushaltungsvorstand'),
            new CensusColumnNull($this, 'männlich', 'Geschlecht männlich'),
            new CensusColumnNull($this, 'weiblich', 'Geschlecht weiblich'),
            new CensusColumnNull($this, 'Blind', 'Blind auf beiden Augen'),
            new CensusColumnNull($this, 'Taubstumm', 'Taubstumm'),
            new CensusColumnNull($this, 'Bemerkungen', 'Bemerkungen'),
            new CensusColumnNull($this, '', 'Nachfolgend die detaillierten Zählkartenangaben'),
            new CensusColumnFullName($this, 'ZK 1.Name', '1. Vor- und Familienname:'),
            new CensusColumnNull($this, 'ZK 2.Geschlecht', '2. Geschlecht:'),
            new CensusColumnNull($this, 'ZK 3.Familienstand', '3. Familienstand:'),
            new CensusColumnBirthDayDotMonthYear($this, 'ZK 4.Alter', '4. Alter: geboren den ... im Jahre ...'),
            new CensusColumnBirthPlace($this, 'ZK 5.Geburtsort', '5. Geburtsort: ... im Bezirk (Amt) ...'),
            new CensusColumnNull($this, 'ZK 5.Land/Provinz', 'für außerhalb des Großherzogthums Geborene auch Geburtsland, für in Preußen Geborene auch Provinz: ...'),
            new CensusColumnOccupation($this, 'ZK 6.Beruf/Stand', '6. Beruf, Stand, Erwerb, Gewerbe, Geschäft oder Nahrungszweig:'),
            new CensusColumnNull($this, 'ZK 7a.Gemeinde Wohnort', '7.a. Gemeinde (Ortschaft), in welcher der Wohnort (Wohnung), bei verheiratheten Personen der Familienwohnsitz liegt:'),
            new CensusColumnNull($this, 'ZK 7a.Land/Provinz', 'für außerhalb des Großherzogthums Wohnende auch Staat und für in Preußen Wohnende auch Provinz: ...'),
            new CensusColumnNull($this, 'ZK 7b.Gemeinde Erwerbsort', '7.b. Gemeinde (Ortschaft), in welcher der Beruf (die Erwerbsthätigkeit) zur Zeit ausgeübt wird oder zuletzt ausgeübt wurde:'),
            new CensusColumnNull($this, 'ZK 7b.Land/Provinz', 'für außerhalb des Großherzogthums Arbeitende auch Staat und für in Preußen Arbeitende auch Provinz: ...'),
            new CensusColumnReligion($this, 'ZK 8.Religion', '8. Religionsbekenntnis:'),
            new CensusColumnNull($this, 'ZK 9.Muttersprache', '9. Muttersprache (ob deutsch oder welche andere Sprache?):'),
            new CensusColumnNull($this, 'ZK 10.StA', '10. Staatsangehörigkeit:'),
            new CensusColumnNull($this, 'ZK 11.Dienstgrad', '11. Für Militärpersonen im aktiven Dienste: Dienstgrad:'),
            new CensusColumnNull($this, 'ZK 11.Einheit', 'Truppentheil, Kommando- oder Verwaltungsbehörde:'),
            new CensusColumnNull($this, 'ZK 12.Gebrechen', '12. Etwaige körperliche Mängel und Gebrechen:'),
        ];
    }
}
