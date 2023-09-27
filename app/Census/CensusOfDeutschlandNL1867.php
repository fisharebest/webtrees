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

/**
 * Definitions for a census
 */
class CensusOfDeutschlandNL1867 extends CensusOfDeutschland implements CensusInterface
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
        return 'Mecklenburg-Schwerin (Nachtragsliste), Deutschland';
    }

    /**
     * The columns of the census.
     *
     * @return array<CensusColumnInterface>
     */
    public function columns(): array
    {
        return [
            new CensusColumnNull($this, '1.Nr.', 'Ordnungs-Nummer.'),
            new CensusColumnGivenNames($this, '2.Vorname', 'I. Vor- und Familienname jeder Person. Vorname.'),
            new CensusColumnSurname($this, '3.Familienname', 'I. Vor- und Familienname jeder Person. Familienname.'),
            new CensusColumnNull($this, '4.männlich', 'II. Geschlecht männlich.'),
            new CensusColumnNull($this, '5.weiblich', 'II. Geschlecht weiblich.'),
            new CensusColumnBirthYear($this, '6.Geburtsjahr', 'III. Alter.'),
            new CensusColumnReligion($this, '7.Religion', 'IV. Religionsbekenntnis.'),
            new CensusColumnNull($this, '8.ledig', 'V. Familienstand. ledig.'),
            new CensusColumnNull($this, '9.verehelicht', 'V. Familienstand. verehelicht.'),
            new CensusColumnNull($this, '10.verwittwet', 'V. Familienstand. verwittwet.'),
            new CensusColumnNull($this, '11.geschieden', 'V. Familienstand. geschieden.'),
            new CensusColumnNull($this, '12.StA_M-S', 'VI. Staatsangehörigkeit. Mecklenburg-Schwerinscher Unterthan.'),
            new CensusColumnNull($this, '13.StA', 'VI. Staatsangehörigkeit. Anderen Staaten angehörig. Welchem Staat?'),
            new CensusColumnNull($this, '14.', 'VII. Art des Abwesenheit vom Zählungsorte. Nicht über ein Jahr Abwesende als See- oder Flußschiffer.'),
            new CensusColumnNull($this, '15.', 'VII. Art des Abwesenheit vom Zählungsorte. Nicht über ein Jahr Abwesende auf Land- oder Seereisen.'),
            new CensusColumnNull($this, '16.', 'VII. Art des Abwesenheit vom Zählungsorte. Nicht über ein Jahr Abwesende auf Besuch außerhalb des Orts.'),
            new CensusColumnNull($this, '17.', 'VII. Art des Aufenthalts am Zählungsort. Ueber ein Jahr, oder in anderer Art als nach Spalte 14 bis 16 Abwesende.'),
            new CensusColumnNull($this, '18.Aufenthaltsort', 'VIII. Vermuthlicher Aufenthaltsort zur Zählungszeit.'),
        ];
    }
}
