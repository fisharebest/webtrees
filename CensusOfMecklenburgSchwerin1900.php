<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
 * Definitions for a census
 */
class CensusOfMecklenburgSchwerin1900 extends CensusOfMecklenburgSchwerin implements CensusInterface {
	/**
	 * When did this census occur.
	 *
	 * @return string
	 */
	public function censusDate() {
		return '01 DEC 1900';
	}

	/**
	 * The columns of the census.
	 *
	 * @return CensusColumnInterface[]
	 */
	public function columns() {
		return array(
			new CensusColumnNull($this, 'Lfd. Nr.', 'Laufende Nummer'),
			new CensusColumnGivenNames($this, 'Vorname', 'Vorname'),
			new CensusColumnSurname($this, 'Familienname', 'Familienname'),
			new CensusColumnRelationToHead($this, 'Verrwandtschaft oder Stellung', 'Verwandtschaft oder sonstige Stellung zum Haushaltungsvorstand'),
			new CensusColumnNull($this, 'männlich', 'Geschlecht männlich'),
			new CensusColumnNull($this, 'weiblich', 'Geschlecht weiblich'),
			new CensusColumnNull($this, 'Blind', 'Blind auf beiden Augen'),
			new CensusColumnNull($this, 'Taubstumm', 'Taubstumm'),
			new CensusColumnNull($this, 'Bemerkungen', 'Bemerkungen'),
			new CensusColumnNull($this, '', 'Nachfolgend die detaillierten Zählkartenangaben'),
			new CensusColumnFullName($this, 'Name', '1. Vor- und Familienname:'),
			new CensusColumnNull($this, 'Geschlecht', '2. Geschlecht:'),
			new CensusColumnNull($this, 'Familienstand', '3. Familienstand:'),
			new CensusColumnBirthDayDotMonthYear($this, 'Alter', '4. Alter: geboren den ... im Jahre ...'),
			new CensusColumnBirthPlace($this, 'Geburtsort', '5. Geburtsort: ... im Bezirk (Amt) ...'),
			new CensusColumnNull($this, 'Land/Provinz', 'für außerhalb des Großherzogthums Geborene auch Geburtsland, für in Preußen Geborene auch Provinz: ...'),
			new CensusColumnOccupation($this, 'Beruf/Stand', '6. Beruf, Stand, Erwerb, Gewerbe, Geschäft oder Nahrungszweig:'),
			new CensusColumnNull($this, 'Gemeinde Wohnort', '7.a. Gemeinde (Ortschaft), in welcher der Wohnort (Wohnung), bei verheiratheten Personen der Familienwohnsitz liegt:'),
			new CensusColumnNull($this, 'Land/Provinz', 'für außerhalb des Großherzogthums Wohnende auch Staat und für in Preußen Wohnende auch Provinz: ...'),
			new CensusColumnNull($this, 'Gemeinde Erwerbsort', '7.b. Gemeinde (Ortschaft), in welcher der Beruf (die Erwerbsthätigkeit) zur Zeit ausgeübt wird oder zuletzt ausgeübt wurde:'),
			new CensusColumnNull($this, 'Land/Provinz', 'für außerhalb des Großherzogthums Arbeitende auch Staat und für in Preußen Arbeitende auch Provinz: ...'),
			new CensusColumnReligion($this, 'Religion', '8. Religionsbekenntniß:'),
			new CensusColumnNull($this, 'Muttersprache', '9. Muttersprache (ob deutsch oder welche andere Sprache?):'),
			new CensusColumnNull($this, 'Staatsangehörigkeit', '10. Staatsangehörigkeit'),
			new CensusColumnNull($this, 'Dienstgrad', '11. Für Militärpersonen im aktiven Dienste: Dienstgrad:'),
			new CensusColumnNull($this, 'Einheit', 'Truppentheil, Kommando- oder Verwaltungsbehörde:'),
			new CensusColumnNull($this, 'Mängel und Gebrechen', '12. Etwaige körperliche Mängel und Gebrechen:'),
		);
	}
}
