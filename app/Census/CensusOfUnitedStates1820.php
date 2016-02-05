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
class CensusOfUnitedStates1820 extends CensusOfUnitedStates implements CensusInterface {
	/**
	 * When did this census occur.
	 *
	 * @return string
	 */
	public function censusDate() {
		return '07 AUG 1820';
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
			new CensusColumnAgeMaleUnder10Years($this, 'FWM-10', 'Free white males under 10'),
			new CensusColumnAgeMale10To16Years($this, 'FWM10-16', 'Free white males 10<16'),	
            new CensusColumnAgeMale16To18Years($this, 'FWM16-18', 'Free white males 10<16'),
			new CensusColumnAgeMale16To26Years($this, 'FWM16-26', 'Free white males 16<26, inc. head of family'),
			new CensusColumnAgeMale26To45Years($this, 'FWM26-45', 'Free white males 26<45, inc. head of family'),
			new CensusColumnAgeMale45UpYears($this, 'FWM45+', 'Free white males 45 up, inc. head of family'),
			new CensusColumnAgeFemaleUnder10Years($this, 'FWF-10', 'Free white females under 10'),
			new CensusColumnAgeFemale10To16Years($this, 'FWF10-16', 'Free white females 10<16'),
			new CensusColumnAgeFemale16To26Years($this, 'FWF16-26', 'Free white females 16<26, inc. head of family'),
			new CensusColumnAgeFemale26To45Years($this, 'FWF26-45', 'Free white females 26<45, inc. head of family'),
			new CensusColumnAgeFemale45UpYears($this, 'FWF45+', 'Free white females 45 up'),			
			new CensusColumnNull($this, 'FNR', 'Foreigners not naturalized'),
			new CensusColumnNull($this, 'AG', 'No. engaged in agriculture'),
			new CensusColumnNull($this, 'COM', 'No. engaged in commerce'),
            new CensusColumnNull($this, 'MNF', 'No. engaged in manufactures'),
			new CensusColumnNull($this, 'Slaves', 'Slaves'),
			new CensusColumnNull($this, 'FC', 'Free colored persons'),
			new CensusColumnNull($this, 'Other', 'All other persons except indians non taxed'),
						
		);
	}
}