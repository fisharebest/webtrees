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
class AbstractCensusColumn {
	/** @var CensusInterface */
	private $census;

	/** @var string */
	private $abbr;

	/** @var string */
	private $title;

	/**
	 * Create a column for a census
	 *
	 * @param CensusInterface $census - The census to which this column forms part.
	 * @param string          $abbr   - The abbrievated on-screen name "BiC"
	 * @param string          $title  - The full column heading "Born in the county"
	 */
	public function __construct(CensusInterface $census, $abbr, $title) {
		$this->census = $census;
		$this->abbr   = $abbr;
		$this->title  = $title;
	}

	/**
	 * A short version of the column's name.
	 *
	 * @return string
	 */
	public function abbreviation() {
		return $this->abbr;
	}

	/**
	 * Extract the country (last part) of a place name.
	 *
	 * @param string $place - e.g. "London, England"
	 *
	 * @return string - e.g. "England"
	 */
	protected function country($place) {
		$place = explode(', ', $place);

		return end($place);
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
	 * Find the father of an individual
	 *
	 * @return Individual $individual
	 */
	public function father(Individual $individual) {
		$family = $individual->getPrimaryChildFamily();

		if ($family) {
			return $family->getHusband();
		} else {
			return null;
		}
	}

	/**
	 * Find the mother of an individual
	 *
	 * @return Individual $individual
	 */
	public function mother(Individual $individual) {
		$family = $individual->getPrimaryChildFamily();

		if ($family) {
			return $family->getWife();
		} else {
			return null;
		}
	}

	/**
	 * Remove the country of a place name, where it is the same as the census place
	 *
	 * @param string $place - e.g. "London, England"
	 *
	 * @return string - e.g. "London" (for census of England) and "London, England" elsewhere
	 */
	protected function notCountry($place) {
		$parts = explode(', ', $place);

		if (end($parts) === $this->place()) {
			return implode(', ', array_slice($parts, 0, -1));
		} else {
			return $place;
		}
	}

	/**
	 * Where did this census occur
	 *
	 * @return string
	 */
	public function place() {
		return $this->census->censusPlace();
	}

	/**
	 * The full version of the column's name.
	 *
	 * @return string
	 */
	public function title() {
		return $this->title;
	}
}
