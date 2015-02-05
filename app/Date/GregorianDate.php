<?php
namespace Fisharebest\Webtrees;

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

use Fisharebest\ExtCalendar\GregorianCalendar;

/**
 * Class GregorianDate - Definitions for the Gregorian calendar
 */
class GregorianDate extends CalendarDate {
	const CALENDAR_ESCAPE = '@#DGREGORIAN@';
	const CAL_START_JD = 2299161; // 15 OCT 1582

	/** {@inheritdoc} */
	public function __construct($date) {
		$this->calendar = new GregorianCalendar;
		parent::__construct($date);
	}

	/** {@inheritdoc} */
	public static function calendarName() {
		return /* I18N: The gregorian calendar */
			I18N::translate('Gregorian');
	}
}
