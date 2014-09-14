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

use Fisharebest\ExtCalendar\FrenchCalendar;
use Fisharebest\ExtCalendar\GregorianCalendar;
use Fisharebest\ExtCalendar\JewishCalendar;
use Fisharebest\ExtCalendar\JulianCalendar;
use Fisharebest\ExtCalendar\Shim;

define(FrenchCalendar::PHP_CALENDAR_NAME, FrenchCalendar::PHP_CALENDAR_NUMBER);
define(GregorianCalendar::PHP_CALENDAR_NAME, GregorianCalendar::PHP_CALENDAR_NUMBER);
define(JewishCalendar::PHP_CALENDAR_NAME, JewishCalendar::PHP_CALENDAR_NUMBER);
define(JulianCalendar::PHP_CALENDAR_NAME, JulianCalendar::PHP_CALENDAR_NUMBER);
define('CAL_NUM_CALS', 4);
define('CAL_DOW_DAYNO', 0);
define('CAL_DOW_SHORT', Shim::emulateBug67960() ? 1 : 2);
define('CAL_DOW_LONG', Shim::emulateBug67960() ? 2 : 1);
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
define('CAL_JEWISH_ADD_GERESHAYIM ', 8);

function cal_days_in_month($calendar, $month, $year) {
	return Shim::calDaysInMonth($calendar, $month, $year);
}

function cal_from_jd($jd, $calendar) {
	return Shim::calFromJd($jd, $calendar);
}

function cal_info($calendar = -1) {
	return Shim::calInfo($calendar);
}

function cal_to_jd($calendar, $month, $day, $year) {
	return Shim::calToJd($calendar, $month, $day, $year);
}

function easter_date($year = null) {
	return Shim::easterDate($year);
}

function easter_days($year=null, $method=CAL_EASTER_DEFAULT) {
	return Shim::easterDays($year ? $year : date('Y'), $method);
}

function FrenchToJD($month, $day, $year) {
	return Shim::FrenchToJD($month, $day, $year);
}

function GregorianToJD($month, $day, $year) {
	return Shim::GregorianToJD($month, $day, $year);
}

function JDDayOfWeek($julianday, $mode = CAL_DOW_DAYNO) {
	return Shim::JDDayOfWeek($julianday, $mode);
}

function JDMonthName($julianday, $mode) {
	return Shim::JDMonthName($julianday, $mode);
}

function JDToFrench($juliandaycount) {
	return Shim::JDToFrench($juliandaycount);
}

function JDToGregorian($julianday) {
	return Shim::JDToGregorian($julianday);
}

function jdtojewish($juliandaycount, $hebrew = false, $fl = 0) {
	return Shim::jdtojewish($juliandaycount, $hebrew, $fl);
}

function JDToJulian($julianday) {
	return Shim::JDToJulian($julianday);
}

function jdtounix($jday) {
	return Shim::jdtounix($jday);
}

function JewishToJD($month, $day, $year) {
	return Shim::JewishToJD($month, $day, $year);
}

function JulianToJD($month, $day, $year) {
	return Shim::JulianToJD($month, $day, $year);
}

function unixtojd($timestamp = null) {
	return Shim::unixtojd($timestamp ? $timestamp : time());
}
