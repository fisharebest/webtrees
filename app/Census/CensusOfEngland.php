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
class CensusOfEngland extends Census implements CensusPlaceInterface {
	/**
	 * All available censuses for this census place.
	 *
	 * @return CensusInterface[]
	 */
	public function allCensusDates() {
		return array(
			new CensusOfEngland1841(),
			new CensusOfEngland1851(),
			new CensusOfEngland1861(),
			new CensusOfEngland1871(),
			new CensusOfEngland1881(),
			new CensusOfEngland1891(),
			new CensusOfEngland1901(),
			new CensusOfEngland1911(),
		);
	}

	/**
	 * Where did this census occur, in GEDCOM format.
	 *
	 * @return string
	 */
	public function censusPlace() {
		return 'England';
	}
}
