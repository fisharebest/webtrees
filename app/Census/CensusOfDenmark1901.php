<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
declare(strict_types=1);


namespace Fisharebest\Webtrees\Census;

/**
 * Definitions for a census
 */
class CensusOfDenmark1901 extends CensusOfDenmark implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '01 FEB 1901';
    }

    /**
     * The columns of the census.
     *
     * @return CensusColumnInterface[]
     */
    public function columns(): array
    {
        return [
            new CensusColumnFullName($this, 'Navn', 'Samtlige Personers Navn (ogsaa Fornavn). Ved Børn, endnu uden Navn, sættes „Dreng“ eller „Pige“.'),
            new CensusColumnSexMK($this, 'Køn', 'Kjønnet. Mandkøn (M.) eller Kvindekøn (Kv.).'),
            new CensusColumnBirthDaySlashMonthYear($this, 'Fødselsdag', 'Føderlsaar og Føderladag.'),
            new CensusColumnConditionDanish($this, 'Civilstand', 'Ægteskabelig Stillinge. Ugift (U.), Gift (G.), Enkemand eller Enke (E.), Separeret (S.), Fraskilt (F.).'),
            new CensusColumnReligion($this, 'Trossamfund', 'Trossamfund (Folkekirken eller Navnet paa det Trossamfund, man tilhører, eller „uden for Trossamfund“).'),
            new CensusColumnBirthPlace($this, 'Fødested', 'Fødested 1) Indenlandsk Fødested: Kebstadens, Handelspladsens eller Sogneta og Amtets Navn (kan Amtet ikke angives, sættes vedkommende Landsdel, f. Eks. Fyn, Jlland osv.), 2) Fedt i Bilandene eller Udlandet: Landets Navn.'),
            new CensusColumnNull($this, '', ''),
            new CensusColumnNull($this, '', ''),
            new CensusColumnRelationToHead($this, 'Stilling i familien', 'Stilling i Familien (Husfader, Husmoder, Barn, Slangtning o.l., Tjenestetyende (naar vedkommende har Skudsmaalsbog), Pensioner, logerende.'),
            new CensusColumnOccupation($this, 'Erhverv', ''),
            new CensusColumnNull($this, '', ''),
            new CensusColumnNull($this, '', ''),
            new CensusColumnNull($this, '', ''),
            new CensusColumnNull($this, '', ''),
            new CensusColumnNull($this, '', ''),
            new CensusColumnNull($this, '', ''),
            new CensusColumnNull($this, 'Anmærkninger', 'Anmærkninger.'),
        ];
    }
}
