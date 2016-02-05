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
class CensusOfUnitedStates1810 extends CensusOfUnitedStates implements CensusInterface {
	/**
	 * When did this census occur.
	 *
	 * @return string
	 */
	public function censusDate() {
		return '06 AUG 1810';
	}

	/**
	 * The columns of the census.
	 *
	 * @return CensusColumnInterface[]
	 */
	public function columns() {
		return array(
			new CensusColumnFullName($this, 'Name', 'Name'),
			new CensusColumnRelationToHead($this, 'Relation', 'Relation to head of household'),
			new CensusColumnOccupation($this, 'Occupation', 'Profession, occupation, or trade'),
			new CensusColumnMaleAgeUnder10Years($this, 'Male-10', 'Free white males under 10'),
			new CensusColumnAgeMale10To16Years($this, 'Male 10-16', 'Free white males 10 < 16'),
			new CensusColumnAgeMale16To26Years($this, 'Male 16-26', 'Free white males 16 < 26, inc. head of family'),
			new CensusColumnAgeMale26To45Years($this, 'Male 26-45', 'Free white males 26 < 45, inc. head of family'),
			new CensusColumnAgeMale45UpYears($this, 'Male 45+', 'Free white males 45 up, inc. head of family'),
    		new CensusColumnAgeFemaleUnder10Years($this, 'Female-10', 'Free white females under 10'),
			new CensusColumnAgeFemale10To16Years($this, 'Female 10-16', 'Free white females 10 < 16'),
			new CensusColumnAgeFemale16To26Years($this, 'Female 16-26', 'Free white females 16 < 26, inc. head of family'),
			new CensusColumnAgeFemale26To45Years($this, 'Female 26-45', 'Free white females 26 < 45, inc. head of family'),
			new CensusColumnAgeFemale45UpYears($this, 'Female 45+', 'Free white females 45 up'),			
			new CensusColumnNull($this, 'Other Free', 'All other free persons, except Indians not taxed'),
			new CensusColumnNull($this, 'Slaves', 'Number of slaves'),
			new CensusColumnNull($this, 'Total', 'Total number of individuals'),
			
		);
	}
}