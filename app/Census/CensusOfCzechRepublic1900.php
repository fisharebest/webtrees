<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
class CensusOfCzechRepublic1900 extends CensusOfCzechRepublic implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '31 DEC 1900';
    }

    /**
     * The columns of the census.
     *
     * @return CensusColumnInterface[]
     */
    public function columns(): array
    {
        return [
            new CensusColumnFullName($this, 'Jméno', ''),
            new CensusColumnRelationToHead($this, 'Vztah', ''),
            new CensusColumnSexMZ($this, 'Pohlaví', ''),
            new CensusColumnBirthDaySlashMonthYear($this, 'Narození', 'Datum narození'),
            new CensusColumnBirthPlace($this, 'Rodiště', 'Místo narození'),
            new CensusColumnNull($this, 'Přísluší', 'Domovské právo'),
            new CensusColumnReligion($this, 'Vyznání', ''),
            new CensusColumnNull($this, 'Stav', 'Rodinný stav'),
            new CensusColumnNull($this, 'Jazyk', 'Jazyk v obcování'),
            new CensusColumnOccupation($this, 'Povolání', ''),
            new CensusColumnNull($this, 'Postavení', 'Postavení v zaměstnání'),
            new CensusColumnNull($this, 'Gramotnost', 'Znalost čtení a psaní'),
            new CensusColumnNull($this, 'Druh pobytu', 'Pobyt dočasný nebo trvalý'),
            new CensusColumnNull($this, 'Od roku', 'Počátek pobytu'),
        ];
    }
}
