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
 * Marital status.
 */
class CensusColumnConditionDanish extends AbstractCensusColumn implements CensusColumnInterface {
	/**
	 * Generate the likely value of this census column, based on available information.
	 *
	 * @param Individual $individual
	 *
	 * @return string
	 */
	public function generate(Individual $individual) {
		$family = $this->spouseFamily($individual);

		if ($family === null) {
			return 'Ugift'; // unmarried
		} else {
			if (empty($family->getFacts('_NMR'))) {
				return 'Ugift'; // unmarried
			} elseif (empty($family->getFacts('DIV'))) {
				return 'Ugift'; // unmarried
			} else {
				return 'Gift'; // married
			}
		}
	}
}
