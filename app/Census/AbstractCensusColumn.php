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
 * Definitions for a census column
 */
class AbstractCensusColumn {
	/** @var CensusInterface - the place where the census took place */
	protected $census;

	/**
	 * Create a column for a census
	 *
	 * @param CensusInterface $census
	 */
	public function __construct(CensusInterface $census) {
		$this->census = $census;
	}

	/**
	 * When did this census occur
	 *
	 * @return Date
	 */
	public function date() {
		return new Date($this->census->censusDate());
	}

	/**
	 * Where did this census occur
	 *
	 * @return Date
	 */
	public function place() {
		return $this->census->censusPlace();
	}
}
