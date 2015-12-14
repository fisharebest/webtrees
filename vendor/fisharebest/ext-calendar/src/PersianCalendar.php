<?php
namespace Fisharebest\ExtCalendar;

/**
 * Class PersianCalendar - calculations for the Persian (Jalali) calendar.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014-2015 Greg Roach
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
class PersianCalendar implements CalendarInterface {
	/**
	 * In each 128 year cycle, the following years are leap years.
	 *
	 * @var integer[]
	 */
	private static $LEAP_YEAR_CYCLE = array(
		0, 5, 9, 13, 17, 21, 25, 29, 34, 38, 42, 46, 50, 54, 58, 62, 67, 71, 75, 79, 83, 87, 91, 95, 100, 104, 108, 112, 116, 120, 124
	);

	public function daysInMonth($year, $month) {
		if ($month <= 6) {
			return 31;
		} elseif ($month <= 11 || $this->isLeapYear($year)) {
			return 30;
		} else {
			return 29;
		}
	}

	public function daysInWeek() {
		return 7;
	}

	public function gedcomCalendarEscape() {
		return '@#DJALALI@';
	}

	public function isLeapYear($year) {
		return in_array((($year + 2346) % 2820) % 128, self::$LEAP_YEAR_CYCLE);
	}

	public function jdEnd() {
		return PHP_INT_MAX;
	}

	public function jdStart() {
		return 1948320; // 1 Farvardīn 0001 AP, 19 MAR 0622 AD
	}

	public function jdToYmd($julian_day) {
		$depoch = $julian_day - 2121447;
		$cycle  = (int) ($depoch / 1029983);
		$cyear  = $depoch % 1029983;
		if ($cyear == 1029982) {
			$ycycle = 2820;
		} else {
			$aux1   = (int) ($cyear / 366);
			$aux2   = $cyear % 366;
			$ycycle = (int) (((2134 * $aux1) + (2816 * $aux2) + 2815) / 1028522) + $aux1 + 1;
		}
		$year = $ycycle + (2820 * $cycle) + 474;

		// If we allowed negative years, we would deal with them here.
		$yday  = ($julian_day - $this->ymdToJd($year, 1, 1)) + 1;
		$month = ($yday <= 186) ? ceil($yday / 31) : ceil(($yday - 6) / 30);
		$day   = ($julian_day - $this->ymdToJd($year, $month, 1)) + 1;

		return array($year, (int) $month, (int) $day);
	}

	public function monthsInYear() {
		return 12;
	}

	public function ymdToJd($year, $month, $day) {
		$epbase = $year - (($year >= 0) ? 474 : 473);
		$epyear = 474 + $epbase % 2820;

		return
			$day +
			(($month <= 7) ? (($month - 1) * 31) : ((($month - 1) * 30) + 6)) +
			(int) ((($epyear * 682) - 110) / 2816) +
			($epyear - 1) * 365 +
			(int) ($epbase / 2820) * 1029983 +
			$this->jdStart();
	}
}
