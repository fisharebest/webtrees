<?php
namespace Fisharebest\ExtCalendar;

/**
 * class Shim - PHP implementations of functions from the PHP calendar extension.
 *
 * @link      http://php.net/manual/en/book.calendar.php
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
class Shim {
	/**
	 * Create the necessary shims to emulate the ext/calendar packate.
	 *
	 * @return void
	 */
	public static function create() {
		if (!function_exists('cal_info')) {
			require __DIR__ . '/shims.php';
		}
	}

	/**
	 * Do we need to emulate PHP bug #54254?
	 *
	 * This bug relates to the names used for months 6 and 7 in the Jewish calendar.
	 *
	 * It was fixed in PHP 5.5.0
	 *
	 * @link https://bugs.php.net/bug.php?id=54254
	 *
	 * @return bool
	 */
	public static function emulateBug54254() {
		return version_compare(PHP_VERSION, '5.5.0', '<');
	}

	/**
	 * Do we need to emulate PHP bug #67960?
	 *
	 * This bug relates to the constants CAL_DOW_SHORT and CAL_DOW_LONG.
	 *
	 * It will hopefully be fixed in PHP 5.6.1
	 *
	 * @link https://bugs.php.net/bug.php?id=67960
	 * @link https://github.com/php/php-src/pull/806
	 *
	 * @return bool
	 */
	public static function emulateBug67960() {
		return true;
	}

	/**
	 * Return the number of days in a month for a given year and calendar.
	 *
	 * Shim implementation of \cal_days_in_month()
	 *
	 * @link https://php.net/cal_days_in_month
	 *
	 * @param $calendar
	 * @param $month
	 * @param $year
	 *
	 * @return int The number of days in the specified month
	 */
	public static function calDaysInMonth($calendar, $month, $year) {
		switch ($calendar) {
			case CAL_GREGORIAN:
				$gregorian = new GregorianCalendar;
				return $gregorian->daysInMonth($year, $month);

			case CAL_JULIAN:
				$julian= new JulianCalendar;
				return $julian->daysInMonth($year, $month);

			case CAL_FRENCH:
				$french = new FrenchCalendar();
				return $french->daysInMonth($year, $month);

			case CAL_JEWISH:
				$jewish = new JewishCalendar();
				return $jewish->daysInMonth($year, $month);

			default:
				return trigger_error('invalid calendar ID ' . $calendar . '.', E_USER_WARNING);
		}
	}

	/**
	 * Converts from Julian Day Count to a supported calendar.
	 *
	 * Shim implementation of \cal_from_jd()
	 *
	 * @link https://php.net/cal_from_jd
	 *
	 * @param int $jd       Julian Day number
	 * @param int $calendar Calendar constant
	 *
	 * @return array
	 */
	public static function calFromJd($jd, $calendar) {
		switch ($calendar) {
		case CAL_FRENCH:
			$french = new FrenchCalendar;
			return $french->calFromJd($jd);

		case CAL_GREGORIAN:
			$gregorian = new GregorianCalendar;
			return $gregorian->calFromJd($jd);

		case CAL_JEWISH:
			$jewish = new JewishCalendar;
			return $jewish->calFromJd($jd);

		case CAL_JULIAN:
			$julian = new JulianCalendar;
			return $julian->calFromJd($jd);

		default:
			return trigger_error('invalid calendar ID ' . $calendar, E_USER_WARNING);
		}
	}

	/**
	 * Returns information about a particular calendar.
	 *
	 * Shim implementation of \cal_info()
	 *
	 * @link https://php.net/cal_info
	 *
	 * @param int $calendar
	 *
	 * @return array
	 */
	public static function calInfo($calendar) {
		switch ($calendar) {
			case -1:
				return array(
					CAL_GREGORIAN => static::calInfo(CAL_GREGORIAN),
					CAL_JULIAN => static::calInfo(CAL_JULIAN),
					CAL_JEWISH => static::calInfo(CAL_JEWISH),
					CAL_FRENCH => static::calInfo(CAL_FRENCH),
				);
			case CAL_FRENCH:
				$french = new FrenchCalendar;
				return $french->phpCalInfo();

			case CAL_GREGORIAN:
				$gregorian = new GregorianCalendar;
				return $gregorian->phpCalInfo();

			case CAL_JEWISH:
				$jewish = new JewishCalendar;
				return $jewish->phpCalInfo();

			case CAL_JULIAN:
				$julian = new JulianCalendar;
				return $julian->phpCalInfo();

			default:
				return trigger_error('invalid calendar ID ' . $calendar . '.', E_USER_WARNING);
		}
	}

	/**
	 *  Converts from a supported calendar to Julian Day Count
	 *
	 * Shim implementation of \cal_to_jd()
	 *
	 * @link https://php.net/cal_to_jd
	 *
	 * @param int $calendar
	 * @param int $month
	 * @param int $day
	 * @param int $year
	 *
	 * @return int
	 */
	public static function calToJd($calendar, $month, $day, $year) {
		switch ($calendar) {
		case CAL_FRENCH:
			return Shim::frenchToJd($month, $day, $year);

		case CAL_GREGORIAN:
			return Shim::gregorianToJd($month, $day, $year);

		case CAL_JEWISH:
			return Shim::jewishToJd($month, $day, $year);

		case CAL_JULIAN:
			return Shim::julianToJd($month, $day, $year);

		default:
			return trigger_error('invalid calendar ID ' . $calendar . '.', E_USER_WARNING);
		}
	}

	/**
	 * Get Unix timestamp for midnight on Easter of a given year.
	 *
	 * Shim implementation of \easter_date()
	 *
	 * @link https://php.net/easter_date
	 *
	 * @param int $year
	 *
	 * @return int
	 */
	public static function easterDate($year=null) {
		if ($year === null) {
			$year = date('Y');
		} elseif ($year < 1970 || $year > 2037) {
			return trigger_error('This function is only valid for years between 1970 and 2037 inclusive', E_USER_WARNING);
		}
		$gregorian = new GregorianCalendar;
		$days = $gregorian->easterDays($year);

        // Calculate time-zone offset
        $date_time = new \DateTime('now', new \DateTimeZone(date_default_timezone_get()));
        $offset_seconds = $date_time->format('Z');

		if ($days < 11) {
			return jdtounix($gregorian->ymdToJd($year, 3, $days + 21)) - $offset_seconds;
		} else {
			return jdtounix($gregorian->ymdToJd($year, 4, $days - 10)) - $offset_seconds;
		}
	}

	/**
	 * Get number of days after March 21 on which Easter falls for a given year.
	 *
	 * Shim implementation of \easter_days()
	 *
	 * @link https://php.net/easter_days
	 *
	 * @param int $year
	 * @param int $method   Use the Julian or Gregorian calendar
	 *
	 * @return int
	 */
	public static function easterDays($year, $method) {
		$julian = new JulianCalendar;
		$gregorian = new GregorianCalendar;

		switch ($method) {
			case CAL_EASTER_ROMAN:
				if ($year <= 1582) {
					return $julian->easterDays($year);
				} else {
					return $gregorian->easterDays($year);
				}
			case CAL_EASTER_ALWAYS_GREGORIAN:
				return $gregorian->easterDays($year);

			case CAL_EASTER_ALWAYS_JULIAN:
				return $julian->easterDays($year);

			default: // CAL_EASTER_DEFAULT or any other value
				if ($year <= 1752) {
					return $julian->easterDays($year);
				} else {
					return $gregorian->easterDays($year);
				}
		}
	}

	/**
	 * Converts a date from the French Republican Calendar to a Julian Day Count.
	 *
	 * Shim implementation of \FrenchToJD()
	 *
	 * @link https://php.net/FrenchToJD
	 *
	 * @param int $month
	 * @param int $day
	 * @param int $year
	 *
	 * @return int
	 */
	public static function frenchToJd($month, $day, $year) {
		if ($year == 0) {
			return 0;
		} else {
			$french = new FrenchCalendar;

			return $french->ymdToJd($year, $month, $day);
		}
	}

	/**
	 * Converts a Gregorian date to Julian Day Count.
	 *
	 * Shim implementation of \GregorianToJD()
	 *
	 * @link https://php.net/GregorianToJD
	 *
	 * @param int $month
	 * @param int $day
	 * @param int $year
	 *
	 * @return int
	 */
	public static function gregorianToJd($month, $day, $year) {
		if ($year == 0) {
			return 0;
		} else {
			$gregorian = new GregorianCalendar;

			return $gregorian->ymdToJd($year, $month, $day);
		}
	}

	/**
	 * Returns the day of the week.
	 *
	 * Shim implementation of \JDDayOfWeek()
	 *
	 * @link https://php.net/JDDayOfWeek
	 * @link https://bugs.php.net/bug.php?id=67960
	 *
	 * @param int $julianday
	 * @param int $mode
	 *
	 * @return mixed
	 */
	public static function jdDayOfWeek($julianday, $mode) {
		$julian = new JulianCalendar;
		$dow = $julian->dayOfWeek($julianday);

		switch ($mode) {
			case 1: // 1, not CAL_DOW_LONG - see bug 67960
				return $julian->dayName($dow);
			case 2: // 2, not CAL_DOW_SHORT - see bug 67960
				return $julian->dayNameAbbreviated($dow);
			default: // CAL_DOW_DAYNO or anything else
				return $dow;
		}
	}

	/**
	 * Returns a month name.
	 *
	 * Shim implementation of \JDMonthName()
	 *
	 * @link https://php.net/JDMonthName
	 *
	 * @param int $julianday
	 * @param int $mode
	 *
	 * @return mixed
	 */
	public static function jdMonthName($julianday, $mode) {
		switch ($mode) {
			case CAL_MONTH_GREGORIAN_LONG:
				$gregorian = new GregorianCalendar;
				return $gregorian->jdMonthName($julianday);

			case CAL_MONTH_JULIAN_LONG:
				$julian = new JulianCalendar;
				return $julian->jdMonthName($julianday);

			case CAL_MONTH_JULIAN_SHORT:
				$julian = new JulianCalendar;
				return $julian->jdMonthNameAbbreviated($julianday);

			case CAL_MONTH_JEWISH:
				$jewish = new JewishCalendar;
				return $jewish->jdMonthName($julianday);

			case CAL_MONTH_FRENCH:
				$french = new FrenchCalendar;
				return $french->jdMonthName($julianday);

			default: // CAL_MONTH_GREGORIAN_SHORT and anything else
				$gregorian = new GregorianCalendar;
				return $gregorian->jdMonthNameAbbreviated($julianday);
		}
	}

	/**
	 * Converts a Julian Day Count to the French Republican Calendar.
	 *
	 * Shim implementation of \JDToFrench()
	 *
	 * @link https://php.net/JDToFrench
	 *
	 * @param int $juliandaycount A Julian Day number
	 *
	 * @return string A string of the form "month/day/year"
	 */
	public static function jdToFrench($juliandaycount) {
		if ($juliandaycount >= FrenchCalendar::JD_START && $juliandaycount <= FrenchCalendar::JD_END) {
			$french = new FrenchCalendar;
			$cal = $french->calFromJd($juliandaycount);

			return $cal['date'];
		} else {
			return '0/0/0';
		}
	}

	/**
	 * Converts Julian Day Count to Gregorian date.
	 *
	 * Shim implementation of \JDToGregorian()
	 *
	 * @link https://php.net/JDToGregorian
	 *
	 * @param int $julianday A Julian Day number
	 *
	 * @return string A string of the form "month/day/year"
	 */
	public static function jdToGregorian($julianday) {
		$gregorian = new GregorianCalendar;
		$cal = $gregorian->calFromJd($julianday);

		return $cal['date'];
	}

	/**
	 * Converts a Julian day count to a Jewish calendar date.
	 *
	 * Shim implementation of \JdtoJjewish()
	 *
	 * @link https://php.net/JdtoJewish
	 *
	 * @param int $juliandaycount A Julian Day number
	 * @param bool $hebrew        If set, the date is returned in Hebrew text
	 * @param int $fl             If set, then add alafim and gereshayim to the text
	 *
	 * @return string A string of the form "month/day/year"
	 */
	public static function jdToJewish($juliandaycount, $hebrew = false, $fl = 0) {
		$jewish = new JewishCalendar;

		if ($hebrew) {
			$hebrew = $jewish->jdToHebrew($juliandaycount, $fl & CAL_JEWISH_ADD_ALAFIM_GERESH, $fl & CAL_JEWISH_ADD_ALAFIM, $fl & CAL_JEWISH_ADD_GERESHAYIM);
			// Our code generates Hebrew punctuation.  PHP uses ASCII punctuation.
			$hebrew = strtr($hebrew, array(JewishCalendar::GERESH => "'", JewishCalendar::GERSHAYIM => '"'));
			// Our code generates UTF-8.  PHP generates ISO-8859=8
			$hebrew = mb_convert_encoding($hebrew, 'ISO-8859-8', 'UTF-8');

			return $hebrew;
		} else {
			list($year, $month, $day) = $jewish->jdToYmd($juliandaycount);

			return $month . '/' . $day . '/' . $year;
		}
	}

	/**
	 * Converts a Julian Day Count to a Julian Calendar Date.
	 *
	 * Shim implementation of \JDToJulian()
	 *
	 * @link https://php.net/JDToJulian
	 *
	 * @param int $julianday A Julian Day number
	 *
	 * @return string A string of the form "month/day/year"
	 */
	public static function jdToJulian($julianday) {
		$julian = new JulianCalendar;
		$cal = $julian->calFromJd($julianday);

		return $cal['date'];
	}

	/**
	 * Convert Julian Day to Unix timestamp.
	 *
	 * Shim implementation of \jdtounix()
	 *
	 * @link https://php.net/jdtounix
	 *
	 * @param int $jday
	 *
	 * @return int
	 */
	public static function jdToUnix($jday) {
		if ($jday >= 2440588 && $jday <= 2465343) {
			return (int)($jday - 2440588) * 86400;
		} else {
			return false;
		}
	}

	/**
	 * Converts a date in the Jewish Calendar to Julian Day Count.
	 *
	 * Shim implementation of \JewishToJD()
	 *
	 * @link https://php.net/JewishToJD
	 *
	 * @param int $month
	 * @param int $day
	 * @param int $year
	 *
	 * @return int
	 */
	public static function jewishToJD($month, $day, $year) {
		if ($year == 0) {
			return 0;
		} else {
			$jewish = new JewishCalendar;

			return $jewish->ymdToJd($year, $month, $day);
		}
	}

	/**
	 * Converts a Julian Calendar date to Julian Day Count.
	 *
	 * Shim implementation of \JdToJulian()
	 *
	 * @link https://php.net/JdToJulian
	 *
	 * @param int $month
	 * @param int $day
	 * @param int $year
	 *
	 * @return int
	 */
	public static function julianToJd($month, $day, $year) {
		if ($year == 0) {
			return 0;
		} else {
			$julian = new JulianCalendar;

			return $julian->ymdToJd($year, $month, $day);
		}
	}

	/**
	 * Convert Unix timestamp to Julian Day.
	 *
	 * Shim implementation of \unixtojd()
	 *
	 * @link https://php.net/unixtojd
	 *
	 * @param int $timestamp
	 *
	 * @return int
	 */
	public static function unixToJd($timestamp) {
		if ($timestamp < 0) {
			return false;
		} else {
			// Convert timestamp based on local timezone
			return static::GregorianToJd(gmdate('n', $timestamp), gmdate('j', $timestamp), gmdate('Y', $timestamp));
		}
	}
}
