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
class CensusOfCzechRepublic1921 extends CensusOfCzechRepublic implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '15 FEB 1921';
    }

    /**
     * The columns of the census.
     *
     * @return CensusColumnInterface[]
     */
    public function columns(): array
    {
        return [
            new CensusColumnNull($this, 'Ř.č.', 'Řadové číslo'),
            new CensusColumnSurname($this, 'Příjmení', 'jméno rodinné'),
            new CensusColumnGivenNames($this, 'Jméno', 'Jméno (křestni)'),
            new CensusColumnRelationToHead($this, 'Vztah', ''),
            new CensusColumnSexMZ($this, 'Pohlaví', ''),
            new CensusColumnNull($this, 'Stav', 'Rodinný stav'),
            new CensusColumnBirthDayDotMonthYear($this, 'Narození', 'Datum narození'),
            new CensusColumnBirthPlace($this, 'Rodiště', 'Rodná obec, Soudní okres, Země'),
            new CensusColumnNull($this, 'Bydlí od', 'Od kdy bydlí zapsána osoba v obci?'),
            new CensusColumnNull($this, 'Přísluší', 'Domovské právo'),
            new CensusColumnNull($this, 'Národnost', 'Mateřský jazyk'),
            new CensusColumnReligion($this, 'Vyznání', ''),
            new CensusColumnNull($this, 'Gramotnost', 'Znalost čtení a psaní'),
            new CensusColumnOccupation($this, 'Povolání', 'Druh povolání'),
            new CensusColumnNull($this, 'Postavení', 'Postavení v zaměstnání'),
            new CensusColumnNull($this, 'Podnik', ''),
            new CensusColumnNull($this, 'Měl povolání 1914', ''),
            new CensusColumnNull($this, 'Povolání 1914', 'Druh povolání dne 16. července 1914'),
            new CensusColumnNull($this, 'Postavení 1914', 'Postavení v zaměstnání dne 16. července 1914'),
            new CensusColumnNull($this, 'Poznámka', ''),
        ];
    }
}
