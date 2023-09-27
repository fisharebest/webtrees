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
class CensusOfSlovakia1930 extends CensusOfSlovakia implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '01 DEC 1930';
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
            new CensusColumnNull($this, 'Č. os.', 'Radové číslo osôb v dome'),
            new CensusColumnSurname($this, 'Priezvisko', 'Priezvisko (meno rodinné)'),
            new CensusColumnGivenNames($this, 'Meno', 'Meno (krstné alebo rodné)'),
            new CensusColumnRelationToHead($this, 'Pomer', 'Príbuzenský alebo iný pomer k prednostovi domácnosti'),
            new CensusColumnSexMZ($this, 'Poh.', 'Pohlavie (či mužské či ženské'),
            new CensusColumnBirthDayDotMonthYear($this, 'Nar.', 'Deň, mesiac a rok narodenia'),
            new CensusColumnNull($this, 'Stav', 'Rodinný stav'),
            new CensusColumnNull($this, 'Dát. sňatku', 'U žien, ktoré sú alebo boly vydaté dátum posledného sňatku'),
            new CensusColumnNull($this, 'Dát. ovodv.', 'u ovdov. žien dátum ovdovenia, u rozvedených a rozlúčených dátum rozvodu alebo rozluky'),
            new CensusColumnNull($this, 'Poč. detí', 'U žien, ktoré sú alebo boly vydaté počet všetkých žive narodených detí v poslednom manželstve'),
            new CensusColumnNull($this, 'Zomrelo', 'z nich zomrelo'),
            new CensusColumnBirthPlace($this, 'Rodisko', 'a) rodná obec, b) pol. okres, c) krajina'),
            new CensusColumnNull($this, 'Dát. prisťahovania', 'Jestliže sčítaný nebýva v obci pobytu od narodenia, kedy sa prisťahoval'),
            new CensusColumnNull($this, 'Odkiaľ', 'Jestliže sčítaný nebýva v obci pobytu od narodenia, odkiaľ sa prisťahoval'),
            new CensusColumnNull($this, 'Príslušnosť', 'Státna príslušnosť, u čsl. štátnych príslušníkov mimotoho tiež domovská príslušnosť'),
            new CensusColumnNull($this, 'Národnosť', 'Národnosť (materský jazyk'),
            new CensusColumnReligion($this, 'Náb.', 'Náboženské vyznanie (cirkevná príslušnosť alebo bez vyznania'),
            new CensusColumnNull($this, 'Čít./Pís.', 'Znalosť čítania a písania len u osôb starších 6tich rokov'),
            new CensusColumnOccupation($this, 'Povolanie', 'druh povolania'),
            new CensusColumnNull($this, 'Postavenie', 'postavenie v povolaní'),
            new CensusColumnNull($this, 'Závod', 'bližšie označenie závodu a miesta závodu'),
            new CensusColumnNull($this, 'P. trv.', 'Či je sčítaný v obci prítomný trvale alebo len dočasne'),
            new CensusColumnNull($this, 'Byd. doč.', 'Jestliže dočasne, nech uvedie svoje riadne bydlisko'),
            new CensusColumnNull($this, 'P. doč.', 'Prítomný dočasne - do jedného mesiaca'),
            new CensusColumnNull($this, 'Vady', 'Telesné vady - či sčítaný je slepý na obe oči, hluchý, nemý, hluchonemý, či nemá ruku alebo nohu'),
            new CensusColumnNull($this, 'Poz.', 'Poznámka'),
        ];
    }
}
