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
use Fisharebest\Webtrees\Place;

/**
 * Definitions for a census column
 */
class AbstractCensusColumn {
	/** @var Individual - the individual recorded on the census */
	protected $individul;

	/** @var Place - the place where the census took place */
	protected $place;

	/** @var Date - the date when the census took place */
	protected $date;

	/**
	 * Create a census column
	 *
	 * @param Individual $individual
	 * @param Place      $place
	 * @param Date       $date
	 */
	public function __construct(Individual $individual, Place $place, Date $date) {
		$this->individual = $individual;
		$this->place      = $place;
		$this->date       = $date;
	}
}
