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
namespace Fisharebest\Webtrees\Date;

/**
 * Definitions for the Roman calendar
 *
 * The 5.5.1 gedcom spec mentions this calendar, but gives no details of
 * how it is to be represented....  This class is just a place holder so that
 * webtrees won’t compain if it receives one.
 */
class RomanDate extends JulianDate {
	/** {@inheritdoc} */
	protected function formatGedcomYear() {
		return sprintf('%04dAUC', $this->y);
	}

	/** {@inheritdoc} */
	protected function formatLongYear() {
		return $this->y . 'AUC';
	}
}
