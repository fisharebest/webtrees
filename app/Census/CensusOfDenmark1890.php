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
class CensusOfDenmark1890 extends CensusOfDenmark implements CensusInterface {
	/**
	 * When did this census occur.
	 *
	 * @return string
	 */
	public function censusDate() {
		return '01 FEB 1890';
	}

	/**
	 * The columns of the census.
	 *
	 * @return CensusColumnInterface[]
	 */
	public function columns() {
		return array(
			new CensusColumnFullName($this, 'Navn', 'Samtlige Personers fulde Navn.'),
			new CensusColumnSexMK($this, 'Køn', 'Kjønnet. Mandkøn (M.) eller Kvindekøn (Kv.).'),
			new CensusColumnAge($this, 'Alder', 'Alder.  Alderen anføres med det fyldte Aar, men for Børn, der ikke have fyldt 1 Aar, anføres „Under 1 Aar“ of Fødselsdagen.'),
			new CensusColumnConditionDanish($this, 'Civilstand', 'Ægteskabelig Stillinge. Ugift (U.), Gift (G.), Enkemand eller Enke (E.), Separeret (S.), Fraskilt (F.).'),
			new CensusColumnReligion($this, 'Trossamfund', 'Trossamfund („Folkekirken“ eller andetSamfund, saasom „det frilutheranske“, „det romersk katholske“, det „mosaiske“ o.s.v.).'),
			new CensusColumnBirthPlace($this, 'Fødested', 'Fødested, nemlig Sognets og Amtets eller Kjøbstadens (eller Handelpladsens) Navn, og for de i Bilandene Fødte samt for Udlændinge Landet, hvori de ere fødte.'),
			new CensusColumnRelationToHead($this, 'Stilling i familien', 'Stilling i Familien (Husfader, Husmoder, Barn, Tjenestetyende, Logerende o.s.v.).'),
			new CensusColumnOccupation($this, 'Erhverv', 'Erhverv (Embede, Forretning, Næringsvej og Titel, samt Vedkommendes Stilling som Hovedperson eller Medhjælper, Forvalter, Svend eller Dreng o.s.v.). - Arten af Erhvervet (Gaardejer, Husmand, Grovsmed, Vognfabrikant, Høker o.s.v.). - Under Fattigforsørgelse.'),
			new CensusColumnNull($this, 'Erhvervsstedet', 'Erhvervsstedet (Beboelseskommunen eller hvilken anden Kommune).'),
			new CensusColumnNull($this, 'Døvstumme', 'Døvstumme.'),
			new CensusColumnNull($this, 'Døve', 'Døve (Hørelson aldeles berøvet).'),
			new CensusColumnNull($this, 'Blinde', 'Blinde (Synet aldeles borsvet).'),
			new CensusColumnNull($this, 'Idioter', 'Uden Forstandsovner (Idioter).'),
			new CensusColumnNull($this, 'Sindssyge', 'Sindssyge.'),
			new CensusColumnNull($this, 'Anmærkninger', 'Anmærkninger.'),
		);
	}
}
