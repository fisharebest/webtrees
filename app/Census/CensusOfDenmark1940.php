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
class CensusOfDenmark1940 extends CensusOfDenmark implements CensusInterface {
	/**
	 * When did this census occur.
	 *
	 * @return string
	 */
	public function censusDate() {
		return '05 NOV 1940';
	}

	/**
	 * The columns of the census.
	 *
	 * @return CensusColumnInterface[]
	 */
	public function columns() {
		return array(
			new CensusColumnSurnameGivenNames($this, 'Navn', ''),
			new CensusColumnNull($this, 'Nærværende', 'Hvis den i Rubrik 1 opførte Person er midleritidg nærværende d.v.s. har fast Bopæl ????? (er optaget under en anden Address i Folkeregistret), anføres her den faste Bopæls Adresse (Kommunens Navn og den fuldstændige Adresse i denne; for Udlændinge dog kun Landets Navn).'),
			new CensusColumnNull($this, 'Fraværende', 'Hvis den i Rubrik 1 opførte Person er midleritidg fraværende d.v.s. har fast Bopæl paa Tællingsstedet (er optaget underdenne Address i Folkeregistret), men den 5. Novemer ikke er til Stede paa Tællingsstedet, anføres her  „fraværende“ og Adressen paa det midlertidige Opholdssted (ved Ophold i Udlandet anføres jun Landets Navn).'),
			new CensusColumnSexMK($this, 'Køn', 'Køn Mand (M) Kvinde (K)'),
			new CensusColumnBirthDaySlashMonth($this, 'Fødselsdag', ''),
			new CensusColumnBirthYear($this, 'Fødselsaar', ''),
			new CensusColumnBirthPlace($this, 'Fødested', ''),
			new CensusColumnNull($this, 'Statsbergerferhold', ''),
			new CensusColumnConditionDanish($this, 'Civilstand', 'Ægteskabelig Stillinge. Ugift (U), Gift (G), Enkemand eller Enke (E), Separeret (S), Fraskilt (F).'),
			new CensusColumnNull($this, 'Indgaaelse', 'Date for det nuværende Ægteskabs Indgaaelse.  NB." RUbrikken udfyldes ikke al Enkemaend, Enker, Separerede eller Fraskilte.'),
			new CensusColumnRelationToHead($this, 'Stilling i familien', ''),
			new CensusColumnOccupation($this, 'Erhverv', ''),
			new CensusColumnNull($this, 'Virksomhedens', 'Virksomhedens (Branchens) Art'),
			new CensusColumnNull($this, 'Hustruen', 'Besvares kun af Hustruen og hjemmeboende Børn over 14 Aar'),
			new CensusColumnNull($this, 'Døtre', 'Besvares kun af hjemmeboende Døtre over 14 Aar'),
		);
	}
}
