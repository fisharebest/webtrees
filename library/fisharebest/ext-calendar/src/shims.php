<?php
/**
 * Link the global functions and constants from ext/calendar interface to our
 * own shim functions.
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

use Fisharebest\ExtCalendar\Shim;

define('CAL_GREGORIAN', 0);
define('CAL_JULIAN', 1);
define('CAL_JEWISH', 2);
define('CAL_FRENCH', 3);
define('CAL_NUM_CALS', 4);
define('CAL_DOW_DAYNO', 0);
define('CAL_DOW_SHORT', Shim::shouldEmulateBug67960() ? 1 : 2);
define('CAL_DOW_LONG', Shim::shouldEmulateBug67960() ? 2 : 1);
define('CAL_MONTH_GREGORIAN_SHORT', 0);
define('CAL_MONTH_GREGORIAN_LONG', 1);
define('CAL_MONTH_JULIAN_SHORT', 2);
define('CAL_MONTH_JULIAN_LONG', 3);
define('CAL_MONTH_JEWISH', 4);
define('CAL_MONTH_FRENCH', 5);
define('CAL_EASTER_DEFAULT', 0);
define('CAL_EASTER_ROMAN', 1);
define('CAL_EASTER_ALWAYS_GREGORIAN', 2);
define('CAL_EASTER_ALWAYS_JULIAN', 3);
define('CAL_JEWISH_ADD_ALAFIM_GERESH', 2);
define('CAL_JEWISH_ADD_ALAFIM', 4);
define('CAL_JEWISH_ADD_GERESHAYIM', 8);

/**
 * @param int $calendar_id
 * @param int $month
 * @param int $year
 *
 * @return int
 */
function cal_days_in_month($calendar_id, $month, $year) {
	return Shim::calDaysInMonth($calendar_id, $month, $year);
}

/**
 * @param int $julian_day
 * @param int $calendar_id
 *
 * @return array
 */
function cal_from_jd($julian_day, $calendar_id) {
	return Shim::calFromJd($julian_day, $calendar_id);
}

/**
 * @param int $calendar_id
 *
 * @return array
 */
function cal_info($calendar_id = -1) {
	return Shim::calInfo($calendar_id);
}

/**
 * @param int $calendar_id
 * @param int $month
 * @param int $day
 * @param int $year
 *
 * @return int
 */
function cal_to_jd($calendar_id, $month, $day, $year) {
	return Shim::calToJd($calendar_id, $month, $day, $year);
}

/**
 * @param int $year
 *
 * @return int
 */
function easter_date($year = null) {
	return Shim::easterDate($year ? $year : date('Y'));
}

/**
 * @param int $year
 * @param int $method
 *
 * @return int
 */
function easter_days($year = null, $method = CAL_EASTER_DEFAULT) {
	return Shim::easterDays($year ? $year : date('Y'), $method);
}

/**
 * @param int $month
 * @param int $day
 * @param int $year
 *
 * @return int
 */
function FrenchToJD($month, $day, $year) {
	return Shim::FrenchToJD($month, $day, $year);
}

/**
 * @param int $month
 * @param int $day
 * @param int $year
 *
 * @return int
 */
function GregorianToJD($month, $day, $year) {
	return Shim::GregorianToJD($month, $day, $year);
}

/**
 * @param int $julian_day
 * @param int $mode
 *
 * @return mixed
 */
function JDDayOfWeek($julian_day, $mode = CAL_DOW_DAYNO) {
	return Shim::JDDayOfWeek($julian_day, $mode);
}

/**
 * @param int $julian_day
 * @param int $mode
 *
 * @return string
 */
function JDMonthName($julian_day, $mode) {
	return Shim::JDMonthName($julian_day, $mode);
}

/**
 * @param int $julian_day
 *
 * @return string
 */
function JDToFrench($julian_day) {
	return Shim::JDToFrench($julian_day);
}

/**
 * @param int $julian_day
 *
 * @return string
 */
function JDToGregorian($julian_day) {
	return Shim::JDToGregorian($julian_day);
}

/**
 * @param int  $julian_day
 * @param bool $hebrew
 * @param int  $flags
 *
 * @return string
 */
function jdtojewish($julian_day, $hebrew = false, $flags = 0) {
	return Shim::jdtojewish($julian_day, $hebrew, $flags);
}

/**
 * @param int $julian_day
 *
 * @return string
 */
function JDToJulian($julian_day) {
	return Shim::JDToJulian($julian_day);
}

/**
 * @param int $julian_day
 *
 * @return int
 */
function jdtounix($julian_day) {
	return Shim::jdtounix($julian_day);
}

/**
 * @param int $month
 * @param int $day
 * @param int $year
 *
 * @return int
 */
function JewishToJD($month, $day, $year) {
	return Shim::JewishToJD($month, $day, $year);
}

/**
 * @param int $month
 * @param int $day
 * @param int $year
 *
 * @return int
 */
function JulianToJD($month, $day, $year) {
	return Shim::JulianToJD($month, $day, $year);
}

/**
 * @param int $timestamp
 *
 * @return int
 */
function unixtojd($timestamp = null) {
	return Shim::unixtojd($timestamp ? $timestamp : time());
}
