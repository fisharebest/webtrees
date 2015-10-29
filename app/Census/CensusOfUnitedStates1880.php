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
class CensusOfUnitedStates1880 extends CensusOfUnitedStates implements CensusInterface {
	/**
	 * When did this census occur.
	 *
	 * @return string
	 */
	public function censusDate() {
		return 'JUN 1880';
	}

	/**
	 * The columns of the census.
	 *
	 * @return CensusColumnInterface[]
	 */
	public function columns() {
		return array(
			new CensusColumnFullName($this, 'Name', 'Name'),
			new CensusColumnAge($this, 'Age', 'Age'),
			new CensusColumnSexMF($this, 'Sex', 'Sex'),
			new CensusColumnMonthIfBornWithinYear($this, 'Mon', 'If born within the year, state month'),
			new CensusColumnRelationToHead($this, 'Relation', 'Relation to head of household'),
			new CensusColumnNull($this, 'Single', 'Single'),
			new CensusColumnNull($this, 'Married', 'Married'),
			new CensusColumnNull($this, 'Write', 'Widowed, Divorced'),
			new CensusColumnNull($this, 'MY', 'Married during census year'),
			new CensusColumnOccupation($this, 'Occupation', 'Profession, occupation, or trade'),
			new CensusColumnNull($this, 'Un', 'Number of months the person has been unemployed during the census year'),
			new CensusColumnNull($this, 'Sick', 'Sickness or disability'),
			new CensusColumnNull($this, 'Blind', 'Blind'),
			new CensusColumnNull($this, 'DD', 'Deaf and dumb'),
			new CensusColumnNull($this, 'Idiotic', 'Idiotic'),
			new CensusColumnNull($this, 'Insane', 'Insane'),
			new CensusColumnNull($this, 'Disabled', 'Maimed, crippled, bedridden or otherwise disabled'),
			new CensusColumnNull($this, 'School', 'Attended school within the census year'),
			new CensusColumnNull($this, 'Read', 'Cannot read'),
			new CensusColumnNull($this, 'Write', 'Cannot write'),
			new CensusColumnBirthPlaceSimple($this, 'Birthplace', 'Place of birth, naming the state, territory, or country'),
			new CensusColumnFatherBirthPlaceSimple($this, 'Father’s birthplace', 'Place of birth of father, naming the state, territory, or country'),
			new CensusColumnMotherBirthPlaceSimple($this, 'Mother’s birthplace', 'Place of birth of mother, naming the state, territory, or country'),
		);
	}
}
