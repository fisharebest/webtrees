<?php
namespace Fisharebest\ExtCalendar;

/**
 * Class JulianCalendar - calculations for the Julian calendar.
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
class JulianCalendar extends AbstractCalendar implements CalendarInterface {
	/** See the GEDCOM specification */
	const GEDCOM_CALENDAR_ESCAPE = '@#DJULIAN@';

	/** Does the calendar start at year 1, or are we allowed negative (BCE) years. */
	const NEGATIVE_YEARS_ALLOWED = true;

	/**
	 * Month lengths for regular years and leap-years.
	 *
	 * @var integer[][]
	 */
	protected static $DAYS_IN_MONTH = array(
		0 => array(1 => 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31),
		1 => array(1 => 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31),
	);

	/**
	 * Determine whether a year is a leap year.
	 *
	 * @param integer $year
	 *
	 * @return boolean
	 */
	public function isLeapYear($year) {
		if ($year < 0) {
			$year++;
		}

		return $year % 4 == 0;
	}

	/**
	 * Convert a Julian day number into a year/month/day.
	 *
	 * @param $julian_day
	 *
	 * @return integer[]
	 */
	public function jdToYmd($julian_day) {
		$c = $julian_day + 32082;
		$d = (int)((4 * $c + 3) / 1461);
		$e = $c - (int)(1461 * $d / 4);
		$m = (int)((5 * $e + 2) / 153);

		$day   = $e - (int)((153 * $m + 2) / 5) + 1;
		$month = $m + 3 - 12 * (int)($m / 10);
		$year  = $d - 4800 + (int)($m / 10);
		if ($year < 1) {
			// 0 is 1 BCE, -1 is 2 BCE, etc.
			$year--;
		}

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
		if ($year < 0) {
			// 1 BCE is 0, 2 BCE is -1, etc.
			++$year;
		}
		$a     = (int)((14 - $month) / 12);
		$year  = $year + 4800 - $a;
		$month = $month + 12 * $a - 3;

		return $day + (int)((153 * $month + 2) / 5) + 365 * $year + (int)($year / 4) - 32083;
	}

	/**
	 * Get the number of days after March 21 that easter falls, for a given year.
	 *
	 * Uses the algorithm found in PHP’s ext/calendar/easter.c
	 *
	 * @param integer $year
	 *
	 * @return integer
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
		if ($pfm === 29 || $pfm === 28 && $golden > 11) {
			$pfm--;
		}

		$tmp = (4 - $pfm - $dom) % 7;
		if ($tmp < 0) {
			$tmp += 7;
		}

		return $pfm + $tmp + 1;
	}
}
