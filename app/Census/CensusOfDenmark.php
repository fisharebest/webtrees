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
class CensusOfDenmark extends Census implements CensusPlaceInterface {
	/**
	 * All available censuses for this census place.
	 *
	 * @return CensusInterface[]
	 */
	public function allCensusDates() {
		return array(
			new CensusOfDenmark1787(),
			new CensusOfDenmark1801(),
			new CensusOfDenmark1803(),
			new CensusOfDenmark1834(),
			new CensusOfDenmark1835(),
			new CensusOfDenmark1840(),
			new CensusOfDenmark1845(),
			new CensusOfDenmark1850(),
			new CensusOfDenmark1855(),
			new CensusOfDenmark1860(),
			new CensusOfDenmark1870(),
			new CensusOfDenmark1880(),
			new CensusOfDenmark1885(),
			new CensusOfDenmark1890(),
			new CensusOfDenmark1901(),
			new CensusOfDenmark1906(),
			new CensusOfDenmark1911(),
			new CensusOfDenmark1916(),
			new CensusOfDenmark1921(),
			new CensusOfDenmark1925(),
			new CensusOfDenmark1930(),
			new CensusOfDenmark1940(),
		);
	}

	/**
	 * Where did this census occur, in GEDCOM format.
	 *
	 * @return string
	 */
	public function censusPlace() {
		return 'Danmark';
	}
}
