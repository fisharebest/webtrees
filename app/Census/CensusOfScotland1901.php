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
class CensusOfScotland1901 extends CensusOfScotland implements CensusInterface {
	/**
	 * When did this census occur.
	 *
	 * @return string
	 */
	public function censusDate() {
		return '31 MAR 1901';
	}

	/**
	 * The columns of the census.
	 *
	 * @return CensusColumnInterface[]
	 */
	public function columns() {
		return array(
			new CensusColumnFullName($this, '', ''),
			new CensusColumnRelationToHead($this, '', ''),
			new CensusColumnCondition($this, '', ''),
			new CensusColumnAgeMale($this, '', ''),
			new CensusColumnAgeFemale($this, '', ''),
			new CensusColumnOccupation($this, '', ''),
			new CensusColumnNull($this, '', ''), // Employer/Employed.
			new CensusColumnNull($this, '', ''), // Own account.
			new CensusColumnBirthPlace($this, '', ''),
			new CensusColumnNull($this, '', ''), // Language spoken.
			new CensusColumnNull($this, '', ''), // Infirm, etc.
			new CensusColumnNull($this, '', ''), // Rooms with windows.
		);
	}
}
