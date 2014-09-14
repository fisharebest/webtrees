<?php
namespace Fisharebest\ExtCalendar;

/**
 * class FrenchCalendar - calculations for the French Republican calendar.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 Greg Roach
 * @license   This program is free software: you can redistribute it and/or modify
 *            it under the terms of the GNU General Public License as published by
 *            the Free Software Foundation, either version 3 of the License, or
 *            (at your option) any later version.
 *
 *            This program is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *            GNU General Public License for more details.
 *
 *            You should have received a copy of the GNU General Public License
 *            along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class FrenchCalendar extends Calendar implements CalendarInterface {
	/** Same as PHP’s ext/calendar extension */
	const PHP_CALENDAR_NAME = 'French';

	/** Same as PHP’s ext/calendar extension */
	const PHP_CALENDAR_NUMBER = 3;

	/** Same as PHP’s ext/calendar extension */
	const PHP_CALENDAR_SYMBOL = 'CAL_FRENCH';

	/** See the GEDCOM specification */
	const GEDCOM_CALENDAR_ESCAPE = '@#DFRENCH R@';

	/** The earliest Julian Day number that can be converted into this calendar. */
	const JD_START = 2375840;

	/** The latest Julian Day number that can be converted into this calendar. */
	const JD_END = 2380952; // For compatibility with PHP, this is 0014-13-05

	/** The maximum number of months in any year */
	const MAX_MONTHS_IN_YEAR = 13;

	/** The maximum number of days in any month */
	const MAX_DAYS_IN_MONTH = 30;

	/**
	 * Determine whether a year is a leap year.
	 *
	 * Leap years were based on astronomical observations.  Only years 3, 7 and 11
	 * were ever observed.  Moves to a gregorian-like (fixed) system were proposed
	 * but never implemented.
	 *
	 * @param  int  $year
	 * @return bool
	 */
	public function leapYear($year) {
		return $year % 4 == 3;
	}

	/**
	 * Convert a Julian day number into a year/month/day.
	 *
	 * @param $jd
	 *
	 * @return int[];
	 */
	public function jdToYmd($jd) {
		$year = (int)(($jd - 2375109) * 4 / 1461) - 1;
		$month = (int)(($jd - 2375475 - $year * 365 - (int)($year / 4)) / 30) + 1;
		$day = $jd - 2375444 - $month * 30 - $year * 365 - (int)($year / 4);

		return array($year, $month, $day);
	}

	/**
	 * Convert a year/month/day into a Julian day number
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 *
	 * @return int
	 */
	public function ymdToJd($year, $month, $day) {
		return 2375444 + $day + $month * 30 + $year * 365 + (int)($year / 4);
	}

	/**
	 * Month names for the calendar.
	 *
	 * @return string[]
	 */
	public function monthNames() {
		return array(
			1 => 'Vendemiaire', 'Brumaire', 'Frimaire', 'Nivose', 'Pluviose', 'Ventose',
			'Germinal', 'Floreal', 'Prairial', 'Messidor', 'Thermidor', 'Fructidor', 'Extra',
		);
	}

	/**
	 * Calculate the number of days in a month.
	 *
	 * @param  int $year
	 * @param  int $month
	 *
	 * @return int
	 */
	public function daysInMonth($year, $month) {
		if ($year < 1 || $year > 14) {
			return trigger_error('invalid date.', E_USER_WARNING);
		} elseif ($month >=1 && $month <= 12) {
			return 30;
		} elseif ($month == 13) {
			if ($this->leapYear($year)) {
				return 6;
			} else {
				return $year == 14 ? -2380948 : 5; // Emulate a bug in PHP
			}
		} else {
			return trigger_error('invalid date.', E_USER_WARNING);
		}
	}
}
