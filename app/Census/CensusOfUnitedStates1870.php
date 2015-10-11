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

use Fisharebest\Webtrees\Date;

/**
 * Definitions for a census
 */
class CensusOfUnitedStates1870 extends CensusOfUnitedStates implements CensusInterface {
	/**
	 * When did this census occur.
	 *
	 * @return string
	 */
	public function censusDate() {
		return 'JUN 1870';
	}

	/**
	 * The columns of the census.
	 *
	 * @return CensusColumnInterface[]
	 */
	public function columns() {
		return array(
			new CensusColumnFullName($this, '', ''),
			new CensusColumnAge($this, '', ''),
			new CensusColumnSexMF($this, '', ''),
			new CensusColumnNull($this, '', ''), // Race
			new CensusColumnOccupation($this, '', ''),
			new CensusColumnNull($this, '', ''), // Value of real estate owned
			new CensusColumnNull($this, '', ''), // Value of personal estate owned
			new CensusColumnBirthPlace($this, '', ''),
			new CensusColumnFatherForeign($this, '', ''),
			new CensusColumnMotherForeign($this, '', ''),
			new CensusColumnMonthIfBornWithinYear($this, '', ''),
			new CensusColumnMonthIfMarriedWithinYear($this, '', ''),
			new CensusColumnNull($this, '', ''), // Attended school within year
			new CensusColumnNull($this, '', ''), // Can read
			new CensusColumnNull($this, '', ''), // Can write
			new CensusColumnNull($this, '', ''), // Infirm
			new CensusColumnNull($this, '', ''), // US adult male citizen
			new CensusColumnNull($this, '', ''), // Disenfranchised
		);
	}
}
