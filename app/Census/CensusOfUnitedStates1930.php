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
class CensusOfUnitedStates1930 extends CensusOfUnitedStates implements CensusInterface {
	/**
	 * When did this census occur.
	 *
	 * @return string
	 */
	public function censusDate() {
		return 'APR 1930';
	}

	/**
	 * The columns of the census.
	 *
	 * @return CensusColumnInterface[]
	 */
	public function columns() {
		return array(
			new CensusColumnSurnameGivenNameInitial($this, 'Name', 'Name'),
			new CensusColumnRelationToHead($this, 'Relation', 'Relationship of each person to the head of the family'),
			new CensusColumnNull($this, 'Home', 'Home owned or rented'),
			new CensusColumnNull($this, 'Value/rent', 'Value of house, if owned, or monthly rental if rented'),
			new CensusColumnNull($this, 'Radio', 'Radio set'),
			new CensusColumnNull($this, 'Farm', 'Does this family live on a farm'),
			new CensusColumnSexMF($this, 'Sex', 'Sex'),
			new CensusColumnNull($this, 'Race', 'Color or race'),
			new CensusColumnAge($this, 'Age', 'Age at last birthday'),
			new CensusColumnNull($this, 'Condition', 'Whether single, married, widowed, or divorced'),
			new CensusColumnNull($this, 'Age married', 'Age at first marriage'),
			new CensusColumnNull($this, 'School', 'Attended school since Sept. 1, 1929'),
			new CensusColumnNull($this, 'Read/write', 'Whether able to read and write'),
			new CensusColumnBirthPlaceSimple($this, 'Birthplace', 'Place of birth'),
			new CensusColumnFatherBirthPlaceSimple($this, 'Father’s birthplace', 'Place of birth of father'),
			new CensusColumnMotherBirthPlaceSimple($this, 'Mother’s birthplace', 'Place of birth of mother'),
			new CensusColumnNull($this, 'Language', 'Language spoken in home before coming to the United States'),
			new CensusColumnNull($this, '?', 'Code'),
			new CensusColumnNull($this, '?', 'Code'),
			new CensusColumnNull($this, '?', 'Code'),
			new CensusColumnNull($this, 'Imm', 'Year of immigration to the United States'),
			new CensusColumnNull($this, 'Nat', 'Naturalization'),
			new CensusColumnNull($this, 'English', 'Whether able to speak English'),
			new CensusColumnOccupation($this, 'Occupation', 'Trade, profession, or particular kind of work done'),
			new CensusColumnNull($this, 'Industry', 'Industry, business of establishment in which at work'),
			new CensusColumnNull($this, 'Code', 'Industry code'),
			new CensusColumnNull($this, 'Emp', 'Class of worker'),
			new CensusColumnNull($this, 'Work', 'Whether normally at work yesterday or the last regular working day'),
			new CensusColumnNull($this, 'Unemp', 'If not, …'),
			new CensusColumnNull($this, 'Veteran', 'Whether a veteran of U.S. military or …'),
			new CensusColumnNull($this, 'War', 'What war or …'),
			new CensusColumnNull($this, '?', '…'),
		);
	}
}
