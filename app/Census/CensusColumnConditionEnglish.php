<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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
class CensusColumnConditionEnglish extends AbstractCensusColumn implements CensusColumnInterface {
	/* Text to display for married individuals */
	protected $married = 'Mar';

	/* Text to display for unmarried individuals */
	protected $unmarried = 'Unm';

	/* Text to display for divorced individuals */
	protected $divorced = 'Div';

	/* Text to display for widowed individuals (not yet implemented) */
	protected $wid = 'Wid';

	/**
	 * Generate the likely value of this census column, based on available information.
	 *
	 * @param Individual      $individual
	 * @param Individual|null $head
	 *
	 * @return string
	 */
	public function generate(Individual $individual, Individual $head = null) {
		$family = $this->spouseFamily($individual);

		if ($family === null || count($family->getFacts('_NMR')) > 0) {
			return $this->unmarried;
		} elseif (count($family->getFacts('DIV')) > 0) {
			return $this->divorced;
		} else {
			return $this->married;
		}
	}
}
