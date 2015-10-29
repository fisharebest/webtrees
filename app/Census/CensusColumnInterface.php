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
 * Definitions for a census column
 */
interface CensusColumnInterface {
	/**
	 * A short version of the column's name.
	 *
	 * @return string
	 */
	public function abbreviation();

	/**
	 * When did this census occur
	 *
	 * @return Date
	 */
	public function date();

	/**
	 * Generate the likely value of this census column, based on available information.
	 *
	 * @param Individual      $individual
	 * @param Individual|null $head
	 *
	 * @return string
	 */
	public function generate(Individual $individual, Individual $head = null);

	/**
	 * Where did this census occur
	 *
	 * @return string
	 */
	public function place();

	/**
	 * The full version of the column's name.
	 *
	 * @return string
	 */
	public function title();
}
