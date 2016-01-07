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
class CensusOfDenmark1930 extends CensusOfDenmark implements CensusInterface {
	/**
	 * When did this census occur.
	 *
	 * @return string
	 */
	public function censusDate() {
		return '05 NOV 1930';
	}

	/**
	 * The columns of the census.
	 *
	 * @return CensusColumnInterface[]
	 */
	public function columns() {
		return array(
			new CensusColumnSurnameGivenNames($this, 'Navn', ''),
			new CensusColumnNull($this, 'Bopæl', ''),
			new CensusColumnNull($this, 'Andetsteds', 'Hvis den i Rubrik 3 opførte Person har fast Bopæl andetsteds, anføres her den faste Bopæl'),
			new CensusColumnNull($this, 'Stede', 'Hvis den i Rubrik 3 opførte Person paa Tællingsdagen til Stede paa Tællingsstedet? Ja eller Nej.'),
			new CensusColumnNull($this, 'Bopæl', 'Bopæl den 5. Novbr. 1929'),
			new CensusColumnSexMK($this, 'Køn', 'Kjønnet. Mandkøn (M) eller Kvindekøn (K).'),
			new CensusColumnBirthDaySlashMonth($this, 'Fødselsdag', ''),
			new CensusColumnBirthYear($this, 'Fødselsaar', ''),
			new CensusColumnBirthPlace($this, 'Fødested', ''),
			new CensusColumnNull($this, 'Statsbergerferhold', ''),
			new CensusColumnConditionDanish($this, 'Civilstand', 'Ægteskabelig Stillinge. Ugift (U), Gift (G), Enkemand eller Enke (E), Separeret (S), Fraskilt (F).'),
			new CensusColumnRelationToHead($this, 'Stilling i familien', 'Stilling i Familien: Husfader, Husmoder, Barn, Slangtning o.l., Tjenestetyende, Logerende, Pensioner'),
			new CensusColumnOccupation($this, 'Erhverv', ''),
			new CensusColumnNull($this, '', ''),
			new CensusColumnNull($this, '', ''),
			new CensusColumnNull($this, '', ''),
			new CensusColumnNull($this, '', ''),
			new CensusColumnNull($this, '', ''),
		);
	}
}
