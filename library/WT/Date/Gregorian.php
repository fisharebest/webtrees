<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 Greg Roach
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use Fisharebest\ExtCalendar\GregorianCalendar;

/**
 * Class WT_Date_Gregorian - Definitions for the Gregorian calendar
 */
class WT_Date_Gregorian extends WT_Date_Calendar {
	const CALENDAR_ESCAPE = '@#DGREGORIAN@';
	const CAL_START_JD = 2299161; // 15 OCT 1582

	/**
	 * {@inheritdoc}
	 */
	public function __construct($date) {
		$this->calendar = new GregorianCalendar;
		parent::__construct($date);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function calendarName() {
		return /* I18N: The gregorian calendar */
			WT_I18N::translate('Gregorian');
	}
}
