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
class CensusOfDenmark1911 extends CensusOfDenmark implements CensusInterface {
	/**
	 * When did this census occur.
	 *
	 * @return string
	 */
	public function censusDate() {
		return '01 FEB 1911';
	}

	/**
	 * The columns of the census.
	 *
	 * @return CensusColumnInterface[]
	 */
	public function columns() {
		return array(
			new CensusColumnFullName($this, 'Navn', 'Samtlige Personers Navn (ogsaa Fornavn). Ved Børn, endnu uden Navn, sættes „Dreng“ eller „Pige“. Midlertidig fraværerade Personer anføres ikke her, men paa Skemaeta Bagside)'),
			new CensusColumnSexMK($this, 'Køn', 'Kjønnet. Mandkøn (M) eller Kvindekøn (K).'),
			new CensusColumnBirthDaySlashMonthYear($this, 'Fødselsdag', 'Føderlsaar og Føderladag.'),
			new CensusColumnConditionDanish($this, 'Civilstand', 'Ægteskabelig Stillinge. Ugift (U), Gift (G), Enkemand eller Enke (E), Separeret (S), Fraskilt (F).'),
			new CensusColumnReligion($this, 'Trossamfund', 'Trossamfund (Folkekirken eller Navnet paa det Trossamfund, man tilhører, eller „udenfor Trossamfund“).'),
			new CensusColumnBirthPlace($this, 'Fødested', ''),
			new CensusColumnNull($this, '', ''),
			new CensusColumnNull($this, '', ''),
			new CensusColumnRelationToHead($this, 'Stilling i familien', 'Stilling i Familien: Husfader, Husmoder, Barn, Slangtning o.l., Tjenestetyende, Logerende, Pensioner'),
			new CensusColumnOccupation($this, 'Erhverv', ''),
			new CensusColumnNull($this, '', ''),
			new CensusColumnNull($this, '', ''),
			new CensusColumnNull($this, '', ''),
			new CensusColumnNull($this, '', ''),
			new CensusColumnNull($this, 'Anmærkninger', 'Anmærkninger.'),
		);
	}
}
