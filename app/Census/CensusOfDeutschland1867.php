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
class CensusOfDeutschland1867 extends CensusOfDeutschland implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '03 DEC 1867';
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
            new CensusColumnNull($this, '1.Nr.', 'Ordnungs-Nummer (1-15).'),
            new CensusColumnGivenNames($this, '2.Vorname', 'I. Vor- und Familien-Name jeder Person. Vorname'),
            new CensusColumnSurname($this, '3.Familienname', 'I. Vor- und Familien-Name jeder Person. Familienname.'),
            new CensusColumnNull($this, '4.männlich', 'II. Geschlecht männlich.'),
            new CensusColumnNull($this, '5.weiblich', 'II. Geschlecht weiblich.'),
            new CensusColumnBirthYear($this, '6.Geburtsjahr', 'III. Alter.'),
            new CensusColumnReligion($this, '7.Religion', 'IV. Religionsbekenntnis.'),
            new CensusColumnNull($this, '8.ledig', 'V. Familienstand. ledig.'),
            new CensusColumnNull($this, '9.verehelicht', 'V. Familienstand. verehelicht.'),
            new CensusColumnNull($this, '10.verwittwet', 'V. Familienstand. verwittwet.'),
            new CensusColumnNull($this, '11.geschieden', 'V. Familienstand. geschieden.'),
            new CensusColumnRelationToHeadGerman($this, '12.Stellung', 'V. Familienstand. Verhältnis der Familienglieder zum Haushaltungsvorstand.'),
            new CensusColumnOccupation($this, '13.Stand/Beruf', 'VI. Stand, Beruf oder Vorbereitung zum Beruf, Arbeits- und Dienstverhältnis.'),
            new CensusColumnNull($this, '14.StA_M-S', 'VII. Staatsangehörigkeit. Mecklenburg-Schwerinscher Unterthan.'),
            new CensusColumnNull($this, '15.StA', 'VII. Staatsangehörigkeit. Anderen Staaten angehörig. Welchem Staat?'),
            new CensusColumnNull($this, '16.', 'VIII. Art des Aufenthalts am Zählungsort. Norddeutscher und Zollvereins- See- und Flußschiffer.'),
            new CensusColumnNull($this, '17.', 'VIII. Art des Aufenthalts am Zählungsort. Reisender im Gasthof.'),
            new CensusColumnNull($this, '18.', 'VIII. Art des Aufenthalts am Zählungsort. Gast der Familie (zum Besuch aus).'),
            new CensusColumnNull($this, '19.', 'VIII. Art des Aufenthalts am Zählungsort. Alle übrigen Anwesenden.'),
            new CensusColumnNull($this, '20.blind', 'IX. Besondere Mängel einzelner Individuen. blind auf beiden Augen.'),
            new CensusColumnNull($this, '21.taubstumm', 'IX. Besondere Mängel einzelner Individuen. taubstumm.'),
            new CensusColumnNull($this, '22.blödsinnig', 'IX. Besondere Mängel einzelner Individuen. blödsinnig.'),
            new CensusColumnNull($this, '23.irrsinnig', 'IX. Besondere Mängel einzelner Individuen. irrsinnig.'),
        ];
    }
}
