<?php
namespace Fisharebest\ExtCalendar;

/**
 * class JulianCalendar - calculations for the Julian calendar.
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
class JulianCalendar extends Calendar implements CalendarInterface {
	/** Same as PHP’s ext/calendar extension */
	const PHP_CALENDAR_NAME = 'Julian';

	/** Same as PHP’s ext/calendar extension */
	const PHP_CALENDAR_NUMBER = 1;

	/** Same as PHP’s ext/calendar extension */
	const PHP_CALENDAR_SYMBOL = 'CAL_JULIAN';

	/** See the GEDCOM specification */
	const GEDCOM_CALENDAR_ESCAPE = '@#DJULIAN@';

	/**
	 * Month lengths for regular years and leap-years.
	 *
	 * @var int[][]
	 */
	protected static $DAYS_IN_MONTH = array(
		0 => array(1 => 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31),
		1 => array(1 => 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31),
	);

	/**
	 * English month names.
	 *
	 * @return string[]
	 */
	public function monthNames() {
		return array(
			1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',
		);
	}

	/**
	 * Abbreviated English month names.
	 *
	 * @return string[]
	 */
	public function monthNamesAbbreviated() {
		return array(
			1 => 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
		);
	}


	/**
	 * Determine whether a year is a leap year.
	 *
	 * @param  int  $year
	 * @return bool
	 */
	public function leapYear($year) {
		if ($year < 0) {
			$year++;
		}

		return $year % 4 == 0;
	}

	/**
	 * Convert a Julian day number into a year/month/day.
	 *
	 * @param $jd
	 *
	 * @return int[];
	 */
	public function jdToYmd($jd) {
		$c = $jd + 32082;
		$d = (int)((4 * $c + 3) / 1461);
		$e = $c - (int)(1461 * $d / 4);
		$m = (int)((5 * $e + 2) / 153);
		$day = $e - (int)((153 * $m + 2) / 5) + 1;
		$month = $m + 3 - 12 * (int)($m / 10);
		$year = $d - 4800 + (int)($m / 10);
		if ($year < 1) {
			// 0=1BC, -1=2BC, etc.
			$year--;
		}

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
		if ($year < 0) {
			// 1 B.C.E. => 0, 2 B.C.E> => 1, etc.
			++$year;
		}
		$a = (int)((14 - $month) / 12);
		$year = $year + 4800 - $a;
		$month = $month + 12 * $a - 3;

		return $day + (int)((153 * $month + 2) / 5) + 365 * $year + (int)($year / 4) - 32083;
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
		if ($year == 0 || $month < 1 || $month > 12) {
			return trigger_error('invalid date.', E_USER_WARNING);
		} else {
			return static::$DAYS_IN_MONTH[$this->leapYear($year)][$month];
		}
	}

	/**
	 * Get the number of days after March 21 that easter falls, for a given year.
	 *
	 * Uses the algorithm found in PHP’s ext/calendar/easter.c
	 *
	 * @param int $year
	 *
	 * @return int
	 */
	public function easterDays($year) {
		// The “golden” number
		$golden = 1 + $year % 19;

		// The “dominical” number (finding a Sunday)
		$dom = ($year + (int)($year / 4) + 5) % 7;
		if ($dom < 0) {
			$dom += 7;
		}

		// The uncorrected “Paschal full moon” date
		$pfm = (3 - 11 * $golden - 7) % 30;
		if ($pfm < 0) {
			$pfm += 30;
		}

		// The corrected “Paschal full moon” date
		if ($pfm == 29 || $pfm == 28 && $golden > 11) {
			$pfm--;
		}

		$tmp = (4 - $pfm - $dom) % 7;
		if ($tmp < 0) {
			$tmp += 7;
		}

		return $pfm + $tmp + 1;
	}
}
