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
class CensusOfUnitedStates1910 extends CensusOfUnitedStates implements CensusInterface {
	/**
	 * When did this census occur.
	 *
	 * @return string
	 */
	public function censusDate() {
		return '15 APR 1910';
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
			new CensusColumnSexMF($this, 'Sex', 'Sex'),
			new CensusColumnNull($this, 'Race', 'Color or race'),
			new CensusColumnAge($this, 'Age', 'Age at last birthday'),
			new CensusColumnNull($this, 'Condition', 'Whether single, married, widowed, or divorced'),
			new CensusColumnYearsMarried($this, 'Marr', 'Number of years of present marriage'),
			new CensusColumnChildrenBornAlive($this, 'Chil', 'Mother of how many children'),
			new CensusColumnChildrenLiving($this, 'Chil', 'Number of these children living'),
			new CensusColumnBirthPlaceSimple($this, 'Birthplace', 'Place of birth of this person'),
			new CensusColumnFatherBirthPlaceSimple($this, 'Father’s birthplace', 'Place of birth of father of this person'),
			new CensusColumnFatherBirthPlaceSimple($this, 'Mother’s birthplace', 'Place of birth of mother of this person'),
			new CensusColumnNull($this, 'Imm', 'Year of immigration to the United States'),
			new CensusColumnNull($this, 'Nat', 'Whether naturalized or alien'),
			new CensusColumnNull($this, 'Language', 'Whether able to speak English, of if not, give language spoken'),
			new CensusColumnOccupation($this, 'Occupation', 'Trade or profession of, or particular kind of work done by this person'),
			new CensusColumnNull($this, 'Ind', 'General nature of industry'),
			new CensusColumnNull($this, 'Emp', 'Whether an employer, employee, or work on own account'),
			new CensusColumnNull($this, 'Unemployed', 'Whether out of work on April 15, 1910'),
			new CensusColumnNull($this, 'Unemployed', 'Number of weeks out of work in 1909'),
			new CensusColumnNull($this, 'Read', 'Whether able to read'),
			new CensusColumnNull($this, 'Write', 'Whether able to write'),
			new CensusColumnNull($this, 'School', 'Attended school since September 1, 1909'),
			new CensusColumnNull($this, 'Home', 'Owned or rented'),
			new CensusColumnNull($this, 'Mort', 'Owned free or mortgaged'),
			new CensusColumnNull($this, 'Farm', 'Farm or house'),
			new CensusColumnNull($this, 'CW', 'Whether a survivor of the Union or Confederate Army or Navy'),
			new CensusColumnNull($this, 'Blind', 'Whether blind (both eyes)'),
			new CensusColumnNull($this, 'Deaf', 'Whether deaf and dumb'),
		);
	}
}
