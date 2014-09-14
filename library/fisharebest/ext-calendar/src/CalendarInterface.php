<?php
namespace Fisharebest\ExtCalendar;

/**
 * interface CalendarInterface - each calendar implementation needs to provide
 * these methods.
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
interface CalendarInterface {
	/**
	 * Convert a Julian day number into a year/month/day.
	 *
	 * @param $jd
	 *
	 * @return int[]
	 */
	public function jdToYmd($jd);

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

	/**
	 * Determine whether or not a given year is a leap-year.
	 *
	 * @param int $year
	 *
	 * @return bool
	 */
	public function leapYear($year);

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
	 * Provide a list of month names, as required by PHP::cal_info()
	 *
	 * @return string[]
	 */
	public function monthNames();

}
