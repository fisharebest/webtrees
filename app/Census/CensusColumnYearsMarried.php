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
use Fisharebest\Webtrees\Individual;

/**
 * For how many years has the individual been married.
 */
class CensusColumnYearsMarried extends AbstractCensusColumn implements CensusColumnInterface {
	/**
	 * Generate the likely value of this census column, based on available information.
	 *
	 * @param Individual      $individual
	 * @param Individual|null $head
	 *
	 * @return string
	 */
	public function generate(Individual $individual, Individual $head = null) {
		$marriage_date = null;

		foreach ($individual->getSpouseFamilies() as $family) {
			foreach ($family->getFacts('MARR', true) as $fact) {
				if ($fact->getDate()->isOK() && Date::compare($fact->getDate(), $this->date()) <= 0) {
					$marriage_date = $fact->getDate();
				}
			}
		}

		if ($marriage_date === null) {
			return '';
		} else {
			return (string) Date::getAge($marriage_date, $this->date(), 0);
		}
	}
}
