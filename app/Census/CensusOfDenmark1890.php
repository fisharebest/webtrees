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
			new CensusColumnFullName($this, 'TBC', 'To be confirmed'),
			new CensusColumnAge($this, 'TBC', 'To be confirmed'),
			new CensusColumnSexMF($this, 'TBC', 'To be confirmed'),
			new CensusColumnCondition($this, 'TBC', 'To be confirmed'),
			new CensusColumnRelationToHead($this, 'TBC', 'To be confirmed'),
			new CensusColumnOccupation($this, 'TBC', 'To be confirmed'),
			new CensusColumnBirthPlace($this, 'TBC', 'To be confirmed'),
			new CensusColumnNull($this, 'TBC', 'To be confirmed'), // religion
			new CensusColumnNull($this, 'TBC', 'To be confirmed'), // handicaps
		);
	}
}
