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
class CensusOfSlovakia1869 extends CensusOfSlovakia implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '31 DEC 1869';
    }

    /**
     * The columns of the census.
     *
     * @return array<CensusColumnInterface>
     */
    public function columns(): array
    {
        return [
            new CensusColumnNull($this, 'Č. b.', 'Poradové číslo bytu'),
            new CensusColumnNull($this, 'Č. os.', 'Poradové číslo osoby'),
            new CensusColumnFullName($this, 'Meno', 'Priezvisko a krstné meno, titul'),
            new CensusColumnRelationToHead($this, 'Vzťah', 'Postavenie (rodinný vzťah k hlave domácnosti)'),
            new CensusColumnSexMZ($this, 'Poh.', 'Pohlavie'),
            new CensusColumnBirthYear($this, 'Nar.', 'Rok narodenia'),
            new CensusColumnReligion($this, 'Náb.', 'Náboženstvo'),
            new CensusColumnNull($this, 'Stav', 'Rodinský stav'),
            new CensusColumnOccupation($this, 'Povolanie', 'Povolanie'),
            new CensusColumnNull($this, 'Zamestnanie', 'Okolnosti zamestnania'),
            new CensusColumnBirthPlace($this, 'Rodisko', 'Rodisko - štát/krajina, stolica/okres/sídlo/vidiek, mesto/obec'),
            new CensusColumnNull($this, 'Dom.', 'Príslušnosť k obci - zdejší'),
            new CensusColumnNull($this, 'Cudz.', 'Príslušnosť k obci - cudzí'),
            new CensusColumnNull($this, 'P. doč.', 'Prítomný dočasne - do jedného mesiaca'),
            new CensusColumnNull($this, 'P. trv.', 'Prítomný trvalo'),
            new CensusColumnNull($this, 'Vz. doč.', 'Vzdialený dočasne - do jedného mesiaca'),
            new CensusColumnNull($this, 'Vz. dlho.', 'Vzdialený dlhodobo - nad jeden mesiac'),
            new CensusColumnNull($this, 'Čít.', 'Osoba vie čítať'),
            new CensusColumnNull($this, 'Pís.', 'Osoba vie čítať a písať'),
            new CensusColumnNull($this, 'Poz.', 'Poznámka'),
        ];
    }
}
