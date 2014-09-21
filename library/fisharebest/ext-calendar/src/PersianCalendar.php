<?php
namespace Fisharebest\ExtCalendar;

/**
 * class PersianCalendar - calculations for the Persian (Jalali) calendar.
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
class PersianCalendar extends Calendar implements CalendarInterface {
	/** Same as PHP’s ext/calendar extension */
	const PHP_CALENDAR_NAME = 'Persian';

	/** Same as PHP’s ext/calendar extension */
	const PHP_CALENDAR_NUMBER = 5; // PHP uses 0-3

	/** Same as PHP’s ext/calendar extension */
	const PHP_CALENDAR_SYMBOL = 'CAL_PERSIAN';

	/** See the GEDCOM specification */
	const GEDCOM_CALENDAR_ESCAPE = '@#DJALALI@';

	/** The earliest Julian Day number that can be converted into this calendar. */
	const JD_START = 1948321; // 1 Farvardīn 0001 AP = ?? ??? ???? (Julian)

	/**
	 * Month lengths for regular years and leap-years.
	 *
	 * @var int[][]
	 */
	protected static $DAYS_IN_MONTH = array(
		0 => array(1 => 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29),
		1 => array(1 => 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 30),
	);

	/**
	 * In each 128 year cycle, the following years are leap years.
	 *
	 * @var int[]
	 */
	private static $LEAP_YEAR_CYCLE = array(
		0, 5, 9, 13, 17, 21, 25, 29, 34, 38, 42, 46, 50, 54, 58, 62, 67, 71, 75, 79, 83, 87, 91, 95, 100, 104, 108, 112, 116, 120, 124
	);

	/**
	 * Determine whether a year is a leap year.
	 *
	 * @param  int  $year
	 * @return bool
	 */
	public function leapYear($year) {
		return in_array((($year + 2346) % 2820) % 128, self::$LEAP_YEAR_CYCLE);
	}

	/**
	 * Convert a Julian day number into a year/month/day.
	 *
	 * @param $jd
	 *
	 * @return int[];
	 */
	public function jdToYmd($jd) {
		$depoch = $jd - $this->ymdToJd(475, 1, 1);
		$cycle = (int)($depoch / 1029983);
		$cyear = $depoch % 1029983;
		if ($cyear == 1029982) {
			$ycycle = 2820;
		} else {
			$aux1 = (int)($cyear / 366);
			$aux2 = $cyear % 366;
			$ycycle = (int)(((2134 * $aux1) + (2816 * $aux2) + 2815) / 1028522) +
				$aux1 + 1;
		}
		$year = $ycycle + (2820 * $cycle) + 474;
		if ($year <= 0) {
			$year--;
		}
		$yday = ($jd - $this->ymdToJd($year, 1, 1)) + 1;
		$month = ($yday <= 186) ? ceil($yday / 31) : ceil(($yday - 6) / 30);
		$day = ($jd - $this->ymdToJd($year, $month, 1)) + 1;

		return array($year, (int)$month, (int)$day);
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
		$epbase = $year - (($year >= 0) ? 474 : 473);
		$epyear = 474 + $epbase % 2820;

		return
			$day +
			(($month <= 7) ? (($month - 1) * 31) : ((($month - 1) * 30) + 6)) +
			(int)((($epyear * 682) - 110) / 2816) +
			($epyear - 1) * 365 +
			(int)($epbase / 2820) * 1029983 +
			(self::JD_START - 1);
	}

	/**
	 * Month names for the calendar.
	 *
	 * @return string[]
	 */
	public function monthNames() {
		return array(
			1 => 'Farvardin', 'Ordibehesht', 'Khordad', 'Tir', 'Mordad', 'Shahrivar',
			'Mehr', 'Aban', 'Azar', 'Dey', 'Bahman', 'Esfand',
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
		if ($year == 0 || $month < 1 || $month > self::MAX_MONTHS_IN_YEAR) {
			return trigger_error('invalid date.', E_USER_WARNING);
		} else {
			return static::$DAYS_IN_MONTH[$this->leapYear($year)][$month];
		}
	}
}
