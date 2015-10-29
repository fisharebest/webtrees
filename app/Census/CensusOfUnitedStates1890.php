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
class CensusOfUnitedStates1890 extends CensusOfUnitedStates implements CensusInterface {
	/**
	 * When did this census occur.
	 *
	 * @return string
	 */
	public function censusDate() {
		return '02 JUN 1890';
	}

	/**
	 * The columns of the census.
	 *
	 * @return CensusColumnInterface[]
	 */
	public function columns() {
		return array(
			new CensusColumnGivenNameInitial($this, 'Name', 'Christian name in full, and initial of middle name'),
			new CensusColumnSurname($this, 'Surname', 'Surname'),
			new CensusColumnNull($this, 'CW', 'Whether a soldier, sailor or marine during the civil war (U.S. or Conf.), or widow of such person'),
			new CensusColumnRelationToHead($this, 'Relation', 'Relation to head of family'),
			new CensusColumnNull($this, 'Race', 'Whether white, black, mulatto, quadroon, octoroon, Chinese, Japanese, or Indian'),
			new CensusColumnSexMF($this, 'Sex', 'Sex'),
			new CensusColumnAge($this, 'Age', 'Age at nearest birthday.  If under one year, give age in months'),
			new CensusColumnNull($this, 'Condition', 'Whether single, married, widowed, or divorced'),
			new CensusColumnMonthIfMarriedWithinYear($this, 'Mar', 'Whether married duirng the census year (June 1, 1889, to May 31, 1890)'),
			new CensusColumnNull($this, 'Children', 'Mother of how many children, and number of these children living'),
			new CensusColumnBirthPlaceSimple($this, 'Birthplace', 'Place of birth'),
			new CensusColumnFatherBirthPlaceSimple($this, 'Father’s birthplace', 'Place of birth of father'),
			new CensusColumnFatherBirthPlaceSimple($this, 'Mother’s birthplace', 'Place of birth of mother'),
			new CensusColumnNull($this, 'US', 'Number of years in the United States'),
			new CensusColumnNull($this, 'Nat', 'Whether naturalized'),
			new CensusColumnNull($this, 'Papers', 'Whether naturalization papers have been taken out'),
			new CensusColumnOccupation($this, 'Occupation', 'Profession, trade, occupation'),
			new CensusColumnNull($this, 'Unemployed', 'Months unemployed during the census year (June 1, 1889, to May 31, 1890)'),
			new CensusColumnNull($this, 'Read', 'Able to read'),
			new CensusColumnNull($this, 'Write', 'Able to write'),
			new CensusColumnNull($this, 'English', 'Able to speak English.  If not the language or dialect spoken'),
			new CensusColumnNull($this, 'Disease', 'Whether suffering from acute or chronic disease, with name of disease and length of time afflicted'),
			new CensusColumnNull($this, 'Infirm', 'Whether defective in mind, sight, hearing, or speech, or whether crippled, maimed, or deformed, with name of defect'),
			new CensusColumnNull($this, 'Prisoner', 'Whether a prisoner, convict, homeless child, or pauper'),
		);
	}
}
