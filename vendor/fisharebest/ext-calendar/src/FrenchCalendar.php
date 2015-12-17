<?php
namespace Fisharebest\ExtCalendar;

use InvalidArgumentException;

/**
 * Class FrenchCalendar - calculations for the French Republican calendar.
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
class FrenchCalendar implements CalendarInterface {
	public function daysInMonth($year, $month) {
		if ($year <= 0) {
			throw new InvalidArgumentException('Year ' . $year . ' is invalid for this calendar');
		} elseif ($month < 1 || $month > 13) {
			throw new InvalidArgumentException('Month ' . $month . ' is invalid for this calendar');
		} elseif ($month !== 13) {
			return 30;
		} elseif ($this->isLeapYear($year)) {
			return 6;
		} else {
			return 5;
		}
	}

	public function daysInWeek() {
		return 10;
	}

	public function gedcomCalendarEscape() {
		return '@#DFRENCH R@';
	}

	/**
	 * Leap years were based on astronomical observations.  Only years 3, 7 and 11
	 * were ever observed.  Moves to a gregorian-like (fixed) system were proposed
	 * but never implemented.
	 *
	 */
	public function isLeapYear($year) {
		return $year % 4 == 3;
	}

	public function jdEnd() {
		return 2380687; // 31 DEC 1805 = 10 NIVO 0014
	}

	public function jdStart() {
		return 2375840; // 22 SEP 1792 = 01 VEND 0001
	}

	public function jdToYmd($julian_day) {
		$year  = (int) (($julian_day - 2375109) * 4 / 1461) - 1;
		$month = (int) (($julian_day - 2375475 - $year * 365 - (int) ($year / 4)) / 30) + 1;
		$day   = $julian_day - 2375444 - $month * 30 - $year * 365 - (int) ($year / 4);

		return array($year, $month, $day);
	}

	public function monthsInYear() {
		return 13;
	}

	public function ymdToJd($year, $month, $day) {
		return 2375444 + $day + $month * 30 + $year * 365 + (int) ($year / 4);
	}
}
