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
class CensusOfFrance extends Census implements CensusPlaceInterface {
	/**
	 * All available censuses for this census place.
	 *
	 * @return CensusInterface[]
	 */
	public function allCensusDates() {
		return array(
			new CensusOfFrance1836(),
			new CensusOfFrance1841(),
			new CensusOfFrance1846(),
			new CensusOfFrance1851(),
			new CensusOfFrance1856(),
			new CensusOfFrance1861(),
			new CensusOfFrance1866(),
			new CensusOfFrance1872(),
			new CensusOfFrance1876(),
			new CensusOfFrance1881(),
			new CensusOfFrance1886(),
			new CensusOfFrance1891(),
			new CensusOfFrance1896(),
			new CensusOfFrance1901(),
			new CensusOfFrance1906(),
			new CensusOfFrance1911(),
		);
	}

	/**
	 * Where did this census occur, in GEDCOM format.
	 *
	 * @return string
	 */
	public function censusPlace() {
		return 'France';
	}
}
