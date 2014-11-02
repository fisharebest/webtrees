<?php
namespace Fisharebest\ExtCalendar;

/**
 * Class ArabicCalendar - calculations for the Arabic (Hijri) calendar.
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
class ArabicCalendar extends AbstractCalendar implements CalendarInterface {
	/** See the GEDCOM specification */
	const GEDCOM_CALENDAR_ESCAPE = '@#DHIJRI@';

	/** The earliest Julian Day number that can be converted into this calendar. */
	const JD_START = 1948440; // 1 Muharram 1 AH = 16 JUL 0622 (Julian)

	/**
	 * Month lengths for regular years and leap-years.
	 *
	 * @var integer[][]
	 */
	protected static $DAYS_IN_MONTH = array(
		0 => array(1 => 30, 28, 30, 29, 30, 29, 30, 29, 30, 29, 30, 29),
		1 => array(1 => 30, 28, 30, 29, 30, 29, 30, 29, 30, 29, 30, 30),
	);

	/**
	 * Determine whether a year is a leap year.
	 *
	 * @param integer $year
	 *
	 * @return boolean
	 */
	public function isLeapYear($year) {
		return ((11 * $year + 14) % 30) < 11;
	}

	/**
	 * Convert a Julian day number into a year/month/day.
	 *
	 * @param integer $julian_day
	 *
	 * @return integer[]
	 */
	public function jdToYmd($julian_day) {
		$year  = (int)((30 * ($julian_day - 1948439) + 10646) / 10631);
		$month = (int)((11 * ($julian_day - $year * 354 - (int)((3 + 11 * $year) / 30) - 1948085) + 330) / 325);
		$day   = $julian_day - 29 * ($month - 1) - (int)((6 * $month - 1) / 11) - $year * 354 - (int)((3 + 11 * $year) / 30) - 1948084;

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
		return $day + 29 * ($month - 1) + (int)((6 * $month - 1) / 11) + $year * 354 + (int)((3 + 11 * $year) / 30) + 1948084;
	}
}
