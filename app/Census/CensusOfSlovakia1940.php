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
class CensusOfSlovakia1940 extends CensusOfSlovakia implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '14 DEC 1940';
    }

    /**
     * The columns of the census.
     *
     * @return array<CensusColumnInterface>
     */
    public function columns(): array
    {
        return [
            new CensusColumnNull($this, 'Č. b.', 'Radové číslo bytu'),
            new CensusColumnNull($this, 'Č. os.', 'Radové číslo osôb v byte'),
            new CensusColumnSurname($this, 'Priezvisko', 'Priezvisko (meno rodinné)'),
            new CensusColumnGivenNames($this, 'Meno', 'Meno (krstné alebo rodné)'),
            new CensusColumnRelationToHead($this, 'Pomer', 'Príbuzenský alebo iný pomer k hlave domácnosti'),
            new CensusColumnSexMZ($this, 'Poh.', 'Pohlavie mužské alebo ženské'),
            new CensusColumnBirthDayDotMonthYear($this, 'Nar.', 'Deň, mesiac a rok narodenia'),
            new CensusColumnNull($this, 'Stav', 'Rodinný stav'),
            new CensusColumnBirthPlace($this, 'Rodisko', 'a) rodná obec, b) okres'),
            new CensusColumnNull($this, 'P. trv.', 'Je sčítaný v obci prítomný trvale?'),
            new CensusColumnNull($this, 'Byd. doč.', 'Ak dočasne, nech uvedie svoje riadne bydlisko'),
            new CensusColumnNull($this, 'Dát. prisťahovania', 'Ak sčítaný nebýva v obci pobytu od narodenia, kedy sa prisťahoval'),
            new CensusColumnNull($this, 'Odkiaľ', 'Ak sčítaný nebýva v obci pobytu od narodenia, odkiaľ sa prisťahoval'),
            new CensusColumnNull($this, 'Príslušnosť', 'Státna príslušnosť'),
            new CensusColumnNull($this, 'Národnosť', 'Národnosť'),
            new CensusColumnReligion($this, 'Náb.', 'Náboženstvo (cirkevná príslušnosť alebo bez vyznania'),
            new CensusColumnNull($this, 'Čít./Pís.', 'Znalosť čítania a písania len u osôb starších 6tich rokov'),
            new CensusColumnOccupation($this, 'Povolanie', 'Druh povolania'),
            new CensusColumnNull($this, 'Postavenie', 'Postavenie v povolaní'),
            new CensusColumnNull($this, 'Závod', 'Názov a sídlo závodu (firmy, úradu)'),
            new CensusColumnNull($this, 'Odvetvie', 'K akému odvetviu patrí závod (firma, úrad)'),
            new CensusColumnNull($this, 'Poz.', 'Poznámka'),
        ];
    }
}
