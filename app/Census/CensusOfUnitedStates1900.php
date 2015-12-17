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
class CensusOfUnitedStates1900 extends CensusOfUnitedStates implements CensusInterface {
	/**
	 * When did this census occur.
	 *
	 * @return string
	 */
	public function censusDate() {
		return '01 JUN 1900';
	}

	/**
	 * The columns of the census.
	 *
	 * @return CensusColumnInterface[]
	 */
	public function columns() {
		return array(
			new CensusColumnFullName($this, 'Name', 'Name'),
			new CensusColumnRelationToHead($this, 'Relation', 'Relationship of each person to the head of the family'),
			new CensusColumnNull($this, 'Race', 'Color or race'),
			new CensusColumnSexMF($this, 'Sex', 'Sex'),
			new CensusColumnBirthMonth($this, 'Month', 'Month of birth'),
			new CensusColumnBirthYear($this, 'Year', 'Year of birth'),
			new CensusColumnAge($this, 'Age', 'Age at last birthday'),
			new CensusColumnNull($this, 'Condition', 'Whether single, married, widowed, or divorced'),
			new CensusColumnYearsMarried($this, 'Marr', 'Number of years married'),
			new CensusColumnChildrenBornAlive($this, 'Chil', 'Mother of how many children'),
			new CensusColumnChildrenLiving($this, 'Chil', 'Number of these children living'),
			new CensusColumnBirthPlaceSimple($this, 'Birthplace', 'Place of birth of this person'),
			new CensusColumnFatherBirthPlaceSimple($this, 'Father’s birthplace', 'Place of birth of father of this person'),
			new CensusColumnFatherBirthPlaceSimple($this, 'Mother’s birthplace', 'Place of birth of mother of this person'),
			new CensusColumnNull($this, 'Imm', 'Year of immigration to the United States'),
			new CensusColumnNull($this, 'US', 'Number of years in the United States'),
			new CensusColumnNull($this, 'Nat', 'Naturalization'),
			new CensusColumnOccupation($this, 'Occupation', 'Occupation, trade of profession'),
			new CensusColumnNull($this, 'Unemployed', 'Months not unemployed'),
			new CensusColumnNull($this, 'School', 'Attended school (in months)'),
			new CensusColumnNull($this, 'Read', 'Can read'),
			new CensusColumnNull($this, 'Write', 'Can write'),
			new CensusColumnNull($this, 'English', 'Can speak English'),
			new CensusColumnNull($this, 'Home', 'Owned or rented'),
			new CensusColumnNull($this, 'Mort', 'Owned free or mortgaged'),
			new CensusColumnNull($this, 'Farm', 'Farm or house'),
		);
	}
}
