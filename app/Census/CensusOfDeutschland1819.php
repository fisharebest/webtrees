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
class CensusOfDeutschland1819 extends CensusOfDeutschland implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return 'AUG 1819';
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
            new CensusColumnNull($this, 'Nr.', 'Laufende Num̅er.'),
            new CensusColumnNull($this, 'Geschlecht', 'Ob männlichen oder weiblichen Geschlechts.'),
            new CensusColumnFullName($this, 'Name', 'Vor- und Zuname.'),
            new CensusColumnBirthYear($this, 'Geburtsdatum', 'Jahr und Tag der Geburt.'),
            new CensusColumnBirthPlace($this, 'Geburtsort', 'Geburtsort.'),
            new CensusColumnNull($this, 'Kirchspiel', 'Kirchspiel, wohin der Geburtsort gehört.'),
            new CensusColumnNull($this, '', 'leere Spalte'),
            new CensusColumnOccupation($this, 'Stand/Beruf', 'Stand und Gewerbe.'),
            new CensusColumnNull($this, 'Besitz', 'Grundbesitz.'),
            new CensusColumnNull($this, 'hier seit', 'Wie lange er schon hier ist.'),
            new CensusColumnNull($this, 'Familienstand', 'Ob ledig oder verheirathet.'),
            new CensusColumnReligion($this, 'Religion', 'Religion.'),
            new CensusColumnNull($this, 'Bemerkungen', 'Allgemeine Bemerkungen.'),
        ];
    }
}
