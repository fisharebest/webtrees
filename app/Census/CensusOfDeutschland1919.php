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
class CensusOfDeutschland1919 extends CensusOfDeutschland implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate()
    {
        return '08 OCT 1919';
    }

    /**
     * Where did this census occur, in GEDCOM format.
     *
     * @return string
     */
    public function censusPlace()
    {
        return 'Mecklenburg-Schwerin, Deutschland';
    }

    /**
     * The columns of the census.
     *
     * @return CensusColumnInterface[]
     */
    public function columns()
    {
        return array(
            new CensusColumnNull($this, 'Nummer', 'Laufende Nummer'),
            new CensusColumnGivenNames($this, 'Vorname', 'Vorname'),
            new CensusColumnSurname($this, 'Familienname', 'Familienname'),
            new CensusColumnRelationToHeadGerman($this, 'Stellung im Haushalt', 'Stellung im Haushalt'),
            new CensusColumnNull($this, 'männlich', 'Geschlecht männlich'),
            new CensusColumnNull($this, 'weiblich', 'Geschlecht weiblich'),
            new CensusColumnNull($this, 'Familienstand', 'Familienstand'),
            new CensusColumnBirthDay($this, 'Geburts-Tag', 'Geburts-Tag'),
            new CensusColumnBirthMonth($this, 'Geburts-Monat', 'Geburts-Monat'),
            new CensusColumnBirthYear($this, 'Geburts-Jahr', 'Geburts-Jahr'),
            new CensusColumnBirthPlace($this, 'Geburtsort', 'Name des Geburtsorts'),
            new CensusColumnNull($this, 'Amt, Kreis, Bezirk', 'Amt, Kreis oder sonstiger obrigkeitlicher Bezirk'),
            new CensusColumnNull($this, 'StA', 'Staatsangehörigkeit'),
            new CensusColumnNull($this, 'Gemeinde Brotversorgung', 'Gemeinde der Brotversorgung'),
            new CensusColumnNull($this, 'Wohn-/ Aufenthaltsort', 'Wohnort bei nur vorübergehend Anwesenden. Aufenthaltsort bei vorübergehend Abwesenden'),
            new CensusColumnNull($this, 'Dienstgrad', 'Für Militärpersonen: Angabe des Dienstgrades'),
            new CensusColumnNull($this, 'Kriegsgefangener', 'Angabe ob Kriegsgefangener'),
        );
    }
}
