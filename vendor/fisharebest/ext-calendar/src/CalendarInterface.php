<?php
namespace Fisharebest\ExtCalendar;

/**
 * Interface CalendarInterface - each calendar implementation needs to provide
 * these methods.
 *
 * Many of them are actually provided by the AbstractCalendar base class.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014-2017 Greg Roach
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
interface CalendarInterface {
	/**
	 * Determine the number of days in a specified month, allowing for leap years, etc.
	 *
	 * @param int $year
	 * @param int $month
	 *
	 * @return int
	 */
	public function daysInMonth($year, $month);

	/**
	 * Determine the number of days in a week.
	 *
	 * @return int
	 */
	public function daysInWeek();

	/**
	 * The escape sequence used to indicate this calendar in GEDCOM files.
	 *
	 * @return string
	 */
	public function gedcomCalendarEscape();

	/**
	 * Determine whether or not a given year is a leap-year.
	 *
	 * @param int $year
	 *
	 * @return bool
	 */
	public function isLeapYear($year);

	/**
	 * What is the highest Julian day number that can be converted into this calendar.
	 *
	 * @return int
	 */
	public function jdEnd();

	/**
	 * What is the lowest Julian day number that can be converted into this calendar.
	 *
	 * @return int
	 */
	public function jdStart();

	/**
	 * Convert a Julian day number into a year/month/day.
	 *
	 * @param int $julian_day
	 *
	 * @return int[]
	 */
	public function jdToYmd($julian_day);

	/**
	 * Determine the number of months in a year.
	 *
	 * @return int
	 */
	public function monthsInYear();

	/**
	 * Convert a year/month/day to a Julian day number.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 *
	 * @return int
	 */
	public function ymdToJd($year, $month, $day);
}
