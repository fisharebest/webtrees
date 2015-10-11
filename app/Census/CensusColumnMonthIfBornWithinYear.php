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

use Fisharebest\Webtrees\Individual;

/**
 * The month of birth, if within the last year.
 */
class CensusColumnMonthIfBornWithinYear extends AbstractCensusColumn implements CensusColumnInterface {
	/**
	 * Generate the likely value of this census column, based on available information.
	 *
	 * @param Individual      $individual
	 * @param Individual|null $head
	 *
	 * @return string
	 */
	public function generate(Individual $individual, Individual $head = null) {
		if ($individual->getBirthDate()->julianDay() + 365 >= $this->date()->julianDay()) {
			// Use the GEDCOM month, as we need this in English - for the US census
			return ucfirst(strtolower($individual->getBirthDate()->minimumDate()->format('%O')));
		} else {
			return '';
		}
	}
}
