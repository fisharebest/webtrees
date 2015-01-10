<?php
namespace Fisharebest\ExtCalendar;

/**
 * Class FrenchCalendar - calculations for the French Republican calendar.
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 Greg Roach
 * @license       This program is free software: you can redistribute it and/or modify
 *                it under the terms of the GNU General Public License as published by
 *                the Free Software Foundation, either version 3 of the License, or
 *                (at your option) any later version.
 *
 *                This program is distributed in the hope that it will be useful,
 *                but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                GNU General Public License for more details.
 *
 *                You should have received a copy of the GNU General Public License
 *                along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class FrenchCalendar extends AbstractCalendar implements CalendarInterface {
	/** See the GEDCOM specification */
	const GEDCOM_CALENDAR_ESCAPE = '@#DFRENCH R@';

	/** The earliest Julian Day number that can be converted into this calendar. */
	const JD_START = 2375840;

	/** The latest Julian Day number that can be converted into this calendar. */
	const JD_END = 2380952; // For compatibility with PHP, this is 0014-13-05

	/** The maximum number of months in any year */
	const MAX_MONTHS_IN_YEAR = 13;

	/**
	 * Month lengths for regular years and leap-years.
	 *
	 * @var integer[][]
	 */
	protected static $DAYS_IN_MONTH = array(
		0 => array(1 => 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 5),
		1 => array(1 => 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 6),
	);

	/**
	 * Determine whether a year is a leap year.
	 *
	 * Leap years were based on astronomical observations.  Only years 3, 7 and 11
	 * were ever observed.  Moves to a gregorian-like (fixed) system were proposed
	 * but never implemented.
	 *
	 * @param integer $year
	 *
	 * @return boolean
	 */
	public function isLeapYear($year) {
		return $year % 4 == 3;
	}

	/**
	 * Convert a Julian day number into a year/month/day.
	 *
	 * @param integer $julian_day
	 *
	 * @return integer[]
	 */
	public function jdToYmd($julian_day) {
		$year  = (int)(($julian_day - 2375109) * 4 / 1461) - 1;
		$month = (int)(($julian_day - 2375475 - $year * 365 - (int)($year / 4)) / 30) + 1;
		$day   = $julian_day - 2375444 - $month * 30 - $year * 365 - (int)($year / 4);

		return array($year, $month, $day);
	}

	/**
	 * Convert a year/month/day into a Julian day number
	 *
	 * @param integer $year
	 * @param integer $month
	 * @param integer $day
	 *
	 * @return integer
	 */
	public function ymdToJd($year, $month, $day) {
		return 2375444 + $day + $month * 30 + $year * 365 + (int)($year / 4);
	}
}
