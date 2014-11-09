<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 Greg Roach
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use FishareBest\ExtCalendar\CalendarInterface;
use FishareBest\ExtCalendar\JewishCalendar;

/**
 * Class WT_Date_Calendar - Classes for Gedcom Date/Calendar functionality.
 *
 * WT_Date_Calendar is a base class for classes such as WT_Date_Gregorian, etc.
 * + All supported calendars have non-zero days/months/years.
 * + We store dates as both Y/M/D and Julian Days.
 * + For imprecise dates such as "JAN 2000" we store the start/end julian day.
 *
 * NOTE: Since different calendars start their days at different times, (civil
 * midnight, solar midnight, sunset, sunrise, etc.), we convert on the basis of
 * midday.
 */
class WT_Date_Calendar {
	const CALENDAR_ESCAPE = '@#DUNKNOWN@';
	const MONTHS_IN_YEAR = 12;
	const CAL_START_JD = 0; // @#DJULIAN@ 01 JAN 4713B.C.
	const CAL_END_JD = 99999999;
	const DAYS_IN_WEEK = 7;
	static $MONTH_ABBREV = array('' => 0, 'JAN' => 1, 'FEB' => 2, 'MAR' => 3, 'APR' => 4, 'MAY' => 5, 'JUN' => 6, 'JUL' => 7, 'AUG' => 8, 'SEP' => 9, 'OCT' => 10, 'NOV' => 11, 'DEC' => 12);

	/** @var string[] Convert numbers to/from roman numerals */
	private static $roman_numerals = array(1000 => 'M', 900 => 'CM', 500 => 'D', 400 => 'CD', 100 => 'C', 90 => 'XC', 50 => 'L', 40 => 'XL', 10 => 'X', 9 => 'IX', 5 => 'V', 4 => 'IV', 1 => 'I');

	/** @var CalendarInterface The calendar system used to represent this date */
	protected $calendar;

	public $y, $m, $d;     // Numeric year/month/day
	public $minJD, $maxJD; // Julian Day numbers

	/**
	 * Create a date from either:
	 * a Julian day number
	 * day/month/year strings from a GEDCOM date
	 * another WT_Date_Calendar object
	 *
	 * @param array|int|WT_Date_Calendar $date
	 */
	public function __construct($date) {
		// Construct from an integer (a julian day number)
		if (is_numeric($date)) {
			$this->minJD = $date;
			$this->maxJD = $date;
			list($this->y, $this->m, $this->d) = $this->calendar->jdToYmd($date);

			return;
		}

		// Construct from an array (of three gedcom-style strings: "1900", "FEB", "4")
		if (is_array($date)) {
			$this->d = (int)$date[2];
			if (array_key_exists($date[1], static::$MONTH_ABBREV)) {
				$this->m = static::$MONTH_ABBREV[$date[1]];
			} else {
				$this->m = 0;
				$this->d = 0;
			}
			$this->y = $this->extractYear($date[0]);

			// Our simple lookup table above does not take into account Adar and leap-years.
			if ($this->m === 6 && $this->calendar instanceof JewishCalendar && !$this->calendar->isLeapYear($this->y)) {
				$this->m = 7;
			}

			$this->setJdFromYmd();

			return;
		}

		// Construct from an equivalent xxxxDate object
		if (get_class($this) == get_class($date)) {
			$this->y     = $date->y;
			$this->m     = $date->m;
			$this->d     = $date->d;
			$this->minJD = $date->minJD;
			$this->maxJD = $date->maxJD;

			return;
		}

		// ...else construct an inequivalent xxxxDate object
		if ($date->y == 0) {
			// Incomplete date - convert on basis of anniversary in current year
			$today = $date->calendar->jdToYmd(unixtojd());
			$jd    = $date->calendar->ymdToJd($today[0], $date->m, $date->d == 0 ? $today[2] : $date->d);
		} else {
			// Complete date
			$jd = (int)(($date->maxJD + $date->minJD) / 2);
		}
		list($this->y, $this->m, $this->d) = $this->calendar->jdToYmd($jd);
		// New date has same precision as original date
		if ($date->y == 0) {
			$this->y = 0;
		}
		if ($date->m == 0) {
			$this->m = 0;
		}
		if ($date->d == 0) {
			$this->d = 0;
		}
		$this->setJdFromYmd();
	}

	/**
	 * What is this calendar called?
	 *
	 * @return string
	 */
	public static function calendarName() {
		return /* I18N: The French calendar */ WT_I18N::translate('French');
	}

	/**
	 * Is the current year a leap year?
	 *
	 * @return boolean
	 */
	function isLeapYear() {
		return $this->calendar->isLeapYear($this->y);
	}

	/**
	 * Set the object’s Julian day number from a potentially incomplete year/month/day
	 */
	public function setJdFromYmd() {
		if ($this->y == 0) {
			$this->minJD = 0;
			$this->maxJD = 0;
		} elseif ($this->m == 0) {
			$this->minJD = $this->calendar->ymdToJd($this->y, 1, 1);
			$this->maxJD = $this->calendar->ymdToJd($this->nextYear($this->y), 1, 1) - 1;
		} elseif ($this->d == 0) {
			list($ny, $nm) = $this->nextMonth();
			$this->minJD = $this->calendar->ymdToJd($this->y, $this->m, 1);
			$this->maxJD = $this->calendar->ymdToJd($ny, $nm, 1) - 1;
		} else {
			$this->minJD = $this->calendar->ymdToJd($this->y, $this->m, $this->d);
			$this->maxJD = $this->minJD;
		}
	}

	/**
	 * Full month name in nominative case.
	 *
	 * We put these in the base class, to save duplicating it in the Julian and Gregorian calendars.
	 *
	 * @param integer $month_number
	 * @param boolean $leap_year    Some calendars use leap months
	 *
	 * @return string
	 */
	public static function monthNameNominativeCase($month_number, $leap_year) {
		switch ($month_number) {
		case 1:
			return WT_I18N::translate_c('NOMINATIVE', 'January');
		case 2:
			return WT_I18N::translate_c('NOMINATIVE', 'February');
		case 3:
			return WT_I18N::translate_c('NOMINATIVE', 'March');
		case 4:
			return WT_I18N::translate_c('NOMINATIVE', 'April');
		case 5:
			return WT_I18N::translate_c('NOMINATIVE', 'May');
		case 6:
			return WT_I18N::translate_c('NOMINATIVE', 'June');
		case 7:
			return WT_I18N::translate_c('NOMINATIVE', 'July');
		case 8:
			return WT_I18N::translate_c('NOMINATIVE', 'August');
		case 9:
			return WT_I18N::translate_c('NOMINATIVE', 'September');
		case 10:
			return WT_I18N::translate_c('NOMINATIVE', 'October');
		case 11:
			return WT_I18N::translate_c('NOMINATIVE', 'November');
		case 12:
			return WT_I18N::translate_c('NOMINATIVE', 'December');
		default:
			return '';
		}
	}

	/**
	 * Full month name in genitive case.
	 *
	 * We put these in the base class, to save duplicating it in the Julian and Gregorian calendars.
	 *
	 * @param integer $month_number
	 * @param boolean $leap_year    Some calendars use leap months
	 *
	 * @return string
	 */
	protected static function monthNameGenitiveCase($month_number, $leap_year) {
		switch ($month_number) {
		case 1:
			return WT_I18N::translate_c('GENITIVE', 'January');
		case 2:
			return WT_I18N::translate_c('GENITIVE', 'February');
		case 3:
			return WT_I18N::translate_c('GENITIVE', 'March');
		case 4:
			return WT_I18N::translate_c('GENITIVE', 'April');
		case 5:
			return WT_I18N::translate_c('GENITIVE', 'May');
		case 6:
			return WT_I18N::translate_c('GENITIVE', 'June');
		case 7:
			return WT_I18N::translate_c('GENITIVE', 'July');
		case 8:
			return WT_I18N::translate_c('GENITIVE', 'August');
		case 9:
			return WT_I18N::translate_c('GENITIVE', 'September');
		case 10:
			return WT_I18N::translate_c('GENITIVE', 'October');
		case 11:
			return WT_I18N::translate_c('GENITIVE', 'November');
		case 12:
			return WT_I18N::translate_c('GENITIVE', 'December');
		default:
			return '';
		}
	}

	/**
	 * Full month name in locative case.
	 *
	 * We put these in the base class, to save duplicating it in the Julian and Gregorian calendars.
	 *
	 * @param integer $month_number
	 * @param boolean $leap_year    Some calendars use leap months
	 *
	 * @return string
	 */
	protected static function monthNameLocativeCase($month_number, $leap_year) {
		switch ($month_number) {
		case 1:
			return WT_I18N::translate_c('LOCATIVE', 'January');
		case 2:
			return WT_I18N::translate_c('LOCATIVE', 'February');
		case 3:
			return WT_I18N::translate_c('LOCATIVE', 'March');
		case 4:
			return WT_I18N::translate_c('LOCATIVE', 'April');
		case 5:
			return WT_I18N::translate_c('LOCATIVE', 'May');
		case 6:
			return WT_I18N::translate_c('LOCATIVE', 'June');
		case 7:
			return WT_I18N::translate_c('LOCATIVE', 'July');
		case 8:
			return WT_I18N::translate_c('LOCATIVE', 'August');
		case 9:
			return WT_I18N::translate_c('LOCATIVE', 'September');
		case 10:
			return WT_I18N::translate_c('LOCATIVE', 'October');
		case 11:
			return WT_I18N::translate_c('LOCATIVE', 'November');
		case 12:
			return WT_I18N::translate_c('LOCATIVE', 'December');
		default:
			return '';
		}
	}

	/**
	 * Full month name in instrumental case.
	 *
	 * We put these in the base class, to save duplicating it in the Julian and Gregorian calendars.
	 *
	 * @param integer $month_number
	 * @param boolean $leap_year    Some calendars use leap months
	 *
	 * @return string
	 */
	protected static function monthNameInstrumentalCase($month_number, $leap_year) {
		switch ($month_number) {
		case 1:
			return WT_I18N::translate_c('INSTRUMENTAL', 'January');
		case 2:
			return WT_I18N::translate_c('INSTRUMENTAL', 'February');
		case 3:
			return WT_I18N::translate_c('INSTRUMENTAL', 'March');
		case 4:
			return WT_I18N::translate_c('INSTRUMENTAL', 'April');
		case 5:
			return WT_I18N::translate_c('INSTRUMENTAL', 'May');
		case 6:
			return WT_I18N::translate_c('INSTRUMENTAL', 'June');
		case 7:
			return WT_I18N::translate_c('INSTRUMENTAL', 'July');
		case 8:
			return WT_I18N::translate_c('INSTRUMENTAL', 'August');
		case 9:
			return WT_I18N::translate_c('INSTRUMENTAL', 'September');
		case 10:
			return WT_I18N::translate_c('INSTRUMENTAL', 'October');
		case 11:
			return WT_I18N::translate_c('INSTRUMENTAL', 'November');
		case 12:
			return WT_I18N::translate_c('INSTRUMENTAL', 'December');
		default:
			return '';
		}
	}

	/**
	 * Abbreviated month name
	 *
	 * @param integer $month_number
	 * @param boolean $leap_year    Some calendars use leap months
	 *
	 * @return string
	 */
	protected static function monthNameAbbreviated($month_number, $leap_year) {
		switch ($month_number) {
		case 1:
			return WT_I18N::translate_c('Abbreviation for January', 'Jan');
		case 2:
			return WT_I18N::translate_c('Abbreviation for February', 'Feb');
		case 3:
			return WT_I18N::translate_c('Abbreviation for March', 'Mar');
		case 4:
			return WT_I18N::translate_c('Abbreviation for April', 'Apr');
		case 5:
			return WT_I18N::translate_c('Abbreviation for May', 'May');
		case 6:
			return WT_I18N::translate_c('Abbreviation for June', 'Jun');
		case 7:
			return WT_I18N::translate_c('Abbreviation for July', 'Jul');
		case 8:
			return WT_I18N::translate_c('Abbreviation for August', 'Aug');
		case 9:
			return WT_I18N::translate_c('Abbreviation for September', 'Sep');
		case 10:
			return WT_I18N::translate_c('Abbreviation for October', 'Oct');
		case 11:
			return WT_I18N::translate_c('Abbreviation for November', 'Nov');
		case 12:
			return WT_I18N::translate_c('Abbreviation for December', 'Dec');
		default:
			return '';
		}
	}

	/**
	 * Full day of th eweek
	 *
	 * @param integer $day_number
	 *
	 * @return string
	 */
	public static function dayNames($day_number) {
		switch ($day_number) {
		case 0:
			return WT_I18N::translate('Monday');
		case 1:
			return WT_I18N::translate('Tuesday');
		case 2:
			return WT_I18N::translate('Wednesday');
		case 3:
			return WT_I18N::translate('Thursday');
		case 4:
			return WT_I18N::translate('Friday');
		case 5:
			return WT_I18N::translate('Saturday');
		case 6:
			return WT_I18N::translate('Sunday');
		default:
			throw new InvalidArgumentException($day_number);
		}
	}

	/**
	 * Abbreviated day of the week
	 *
	 * @param integer $day_number
	 *
	 * @return string
	 */
	private static function dayNamesAbbreviated($day_number) {
		switch ($day_number) {
		case 0:
			return WT_I18N::translate('Mon');
		case 1:
			return WT_I18N::translate('Tue');
		case 2:
			return WT_I18N::translate('Wed');
		case 3:
			return WT_I18N::translate('Thu');
		case 4:
			return WT_I18N::translate('Fri');
		case 5:
			return WT_I18N::translate('Sat');
		case 6:
			return WT_I18N::translate('Sun');
		default:
			throw new InvalidArgumentException($day_number);
		}
	}

	/**
	 * Most years are 1 more than the previous, but not always (e.g. 1BC->1AD)
	 *
	 * @param integer $year
	 *
	 * @return integer
	 */
	protected static function nextYear($year) {
		return $year + 1;
	}

	/**
	 * Calendars that use suffixes, etc. (e.g. “B.C.”) or OS/NS notation should redefine this.
	 *
	 * @param string $year
	 *
	 * @return integer
	 */
	protected function extractYear($year) {
		return (int)$year;
	}

	/**
	 * Compare two dates, for sorting
	 *
	 * @param WT_Date_Calendar $d1
	 * @param WT_Date_Calendar $d2
	 *
	 * @return integer
	 */
	public static function compare(WT_Date_Calendar $d1, WT_Date_Calendar $d2) {
		if ($d1->maxJD < $d2->minJD) {
			return -1;
		} elseif ($d2->minJD > $d1->maxJD) {
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * How long between an event and a given julian day
	 * Return result as either a number of years or
	 * a gedcom-style age string.
	 *
	 * @todo WT_Date_Jewish needs to redefine this to cope with leap months
	 *
	 * @param boolean $full             true=gedcom style, false=just years
	 * @param integer $jd               date for calculation
	 * @param boolean $warn_on_negative show a warning triangle for negative ages
	 *
	 * @return string
	 */
	public function getAge($full, $jd, $warn_on_negative = true) {
		if ($this->y == 0 || $jd == 0) {
			return $full ? '' : '0';
		}
		if ($this->minJD < $jd && $this->maxJD > $jd) {
			return $full ? '' : '0';
		}
		if ($this->minJD == $jd) {
			return $full ? '' : '0';
		}
		if ($warn_on_negative && $jd < $this->minJD) {
			return '<i class="icon-warning"></i>';
		}
		list($y, $m, $d) = $this->calendar->jdToYmd($jd);
		$dy = $y - $this->y;
		$dm = $m - max($this->m, 1);
		$dd = $d - max($this->d, 1);
		if ($dd < 0) {
			$dm--;
		}
		if ($dm < 0) {
			$dm += static::MONTHS_IN_YEAR;
			$dy--;
		}
		// Not a full age?  Then just the years
		if (!$full) {
			return $dy;
		}
		// Age in years?
		if ($dy > 1) {
			return $dy . 'y';
		}
		$dm += $dy * static::MONTHS_IN_YEAR;
		// Age in months?
		if ($dm > 1) {
			return $dm . 'm';
		}

		// Age in days?
		return ($jd - $this->minJD) . 'd';
	}

	/**
	 * Convert a date from one calendar to another.
	 *
	 * @param string $calendar
	 *
	 * @return WT_Date_Calendar
	 */
	public function convertToCalendar($calendar) {
		switch ($calendar) {
		case 'gregorian':
			return new WT_Date_Gregorian($this);
		case 'julian':
			return new WT_Date_Julian($this);
		case 'jewish':
			return new WT_Date_Jewish($this);
		case 'french':
			return new WT_Date_French($this);
		case 'hijri':
			return new WT_Date_Hijri($this);
		case 'jalali':
			return new WT_Date_Jalali($this);
		default:
			return $this;
		}
	}

	/**
	 * Is this date within the valid range of the calendar
	 *
	 * @return boolean
	 */
	public function inValidRange() {
		return $this->minJD >= static::CAL_START_JD && $this->maxJD <= static::CAL_END_JD;
	}

	/**
	 * How many months in a year
	 *
	 * @return integer
	 */
	public function monthsInYear() {
		return static::MONTHS_IN_YEAR;
	}

	/**
	 * How many days in the current month
	 *
	 * @return integer
	 */
	public function daysInMonth() {
		try {
			return $this->calendar->daysInMonth($this->y, $this->m);
		} catch (InvalidArgumentException $ex) {
			// calendar.php calls this with "DD MMM" dates, for which we cannot calculate
			// the length of a month.  Should we validate this before calling this function?
			return 0;
		}
	}

	/**
	 * How many days in the current week
	 *
	 * @return integer
	 */
	public function daysInWeek() {
		return static::DAYS_IN_WEEK;
	}

	/**
	 * Format a date, using similar codes to the PHP date() function.
	 *
	 * @param string $format    See http://php.net/date
	 * @param string $qualifier GEDCOM qualifier, so we can choose the right case for the month name.
	 *
	 * @return string
	 */
	public function format($format, $qualifier = '') {
		// Don’t show exact details for inexact dates
		if (!$this->d) {
			// The comma is for US "M D, Y" dates
			$format = preg_replace('/%[djlDNSwz][,]?/', '', $format);
		}
		if (!$this->m) {
			$format = str_replace(array('%F', '%m', '%M', '%n', '%t'), '', $format);
		}
		if (!$this->y) {
			$format = str_replace(array('%t', '%L', '%G', '%y', '%Y'), '', $format);
		}
		// If we’ve trimmed the format, also trim the punctuation
		if (!$this->d || !$this->m || !$this->y) {
			$format = trim($format, ',. ;/-');
		}
		if ($this->d && preg_match('/%[djlDNSwz]/', $format)) {
			// If we have a day-number *and* we are being asked to display it, then genitive
			$case = 'GENITIVE';
		} else {
			switch ($qualifier) {
			case 'TO':
			case 'ABT':
			case 'FROM':
				$case = 'GENITIVE';
				break;
			case 'AFT':
				$case = 'LOCATIVE';
				break;
			case 'BEF':
			case 'BET':
			case 'AND':
				$case = 'INSTRUMENTAL';
				break;
			case '':
			case 'INT':
			case 'EST':
			case 'CAL':
			default: // There shouldn't be any other options...
				$case = 'NOMINATIVE';
				break;
			}
		}
		// Build up the formatted date, character at a time
		preg_match_all('/%[^%]/', $format, $matches);
		foreach ($matches[0] as $match) {
			switch ($match) {
			case '%d':
				$format = str_replace($match, $this->formatDayZeros(), $format);
				break;
			case '%j':
				$format = str_replace($match, $this->formatDay(), $format);
				break;
			case '%l':
				$format = str_replace($match, $this->formatLongWeekday(), $format);
				break;
			case '%D':
				$format = str_replace($match, $this->formatShortWeekday(), $format);
				break;
			case '%N':
				$format = str_replace($match, $this->formatIsoWeekday(), $format);
				break;
			case '%S':
				$format = str_replace($match, $this->formatOrdinalSuffix(), $format);
				break;
			case '%w':
				$format = str_replace($match, $this->formatNumericWeekday(), $format);
				break;
			case '%z':
				$format = str_replace($match, $this->formatDayOfYear(), $format);
				break;
			case '%F':
				$format = str_replace($match, $this->formatLongMonth($case), $format);
				break;
			case '%m':
				$format = str_replace($match, $this->formatMonthZeros(), $format);
				break;
			case '%M':
				$format = str_replace($match, $this->formatShortMonth(), $format);
				break;
			case '%n':
				$format = str_replace($match, $this->formatMonth(), $format);
				break;
			case '%t':
				$format = str_replace($match, $this->daysInMonth(), $format);
				break;
			case '%L':
				$format = str_replace($match, (int)$this->isLeapYear(), $format);
				break;
			case '%Y':
				$format = str_replace($match, $this->formatLongYear(), $format);
				break;
			case '%y':
				$format = str_replace($match, $this->formatShortYear(), $format);
				break;
				// These 4 extensions are useful for re-formatting gedcom dates.
			case '%@':
				$format = str_replace($match, static::CALENDAR_ESCAPE, $format);
				break;
			case '%A':
				$format = str_replace($match, $this->formatGedcomDay(), $format);
				break;
			case '%O':
				$format = str_replace($match, $this->formatGedcomMonth(), $format);
				break;
			case '%E':
				$format = str_replace($match, $this->formatGedcomYear(), $format);
				break;
			}
		}

		return $format;
	}

	/**
	 * Generate the %d format for a date.
	 *
	 * @return string
	 */
	protected function formatDayZeros() {
		if ($this->d > 9) {
			return WT_I18N::digits($this->d);
		} else {
			return WT_I18N::digits('0' . $this->d);
		}
	}

	/**
	 * Generate the %j format for a date.
	 *
	 * @return string
	 */
	protected function formatDay() {
		return WT_I18N::digits($this->d);
	}

	/**
	 * Generate the %l format for a date.
	 *
	 * @return string
	 */
	protected function formatLongWeekday() {
		return $this->dayNames($this->minJD % static::DAYS_IN_WEEK);
	}

	/**
	 * Generate the %D format for a date.
	 *
	 * @return string
	 */
	protected function formatShortWeekday() {
		return $this->dayNamesAbbreviated($this->minJD % static::DAYS_IN_WEEK);
	}

	/**
	 * Generate the %N format for a date.
	 *
	 * @return string
	 */
	protected function formatIsoWeekday() {
		return WT_I18N::digits($this->minJD % 7 + 1);
	}

	/**
	 * Generate the %S format for a date.
	 *
	 * @todo Should be functions in this class?
	 *
	 * @return string
	 */
	protected function formatOrdinalSuffix() {
		$func = 'ordinal_suffix_' . WT_LOCALE;
		if (function_exists($func)) {
			return $func($this->d);
		} else {
			return '';
		}
	}

	/**
	 * Generate the %w format for a date.
	 *
	 * @return string
	 */
	protected function formatNumericWeekday() {
		return WT_I18N::digits(($this->minJD + 1) % static::DAYS_IN_WEEK);
	}

	/**
	 * Generate the %z format for a date.
	 *
	 * @return string
	 */
	protected function formatDayOfYear() {
		return WT_I18N::digits($this->minJD - $this->calendar->ymdToJd($this->y, 1, 1));
	}

	/**
	 * Generate the %n format for a date.
	 *
	 * @return string
	 */
	protected function formatMonth() {
		return WT_I18N::digits($this->m);
	}

	/**
	 * Generate the %m format for a date.
	 *
	 * @return string
	 */
	protected function formatMonthZeros() {
		if ($this->m > 9) {
			return WT_I18N::digits($this->m);
		} else {
			return WT_I18N::digits('0' . $this->m);
		}
	}

	/**
	 * Generate the %F format for a date.
	 *
	 * @param string $case Which grammatical case shall we use
	 *
	 * @return string
	 */
	protected function formatLongMonth($case = 'NOMINATIVE') {
		switch ($case) {
		case 'GENITIVE':
			return $this->monthNameGenitiveCase($this->m, $this->isLeapYear());
		case 'NOMINATIVE':
			return $this->monthNameNominativeCase($this->m, $this->isLeapYear());
		case 'LOCATIVE':
			return $this->monthNameLocativeCase($this->m, $this->isLeapYear());
		case 'INSTRUMENTAL':
			return $this->monthNameInstrumentalCase($this->m, $this->isLeapYear());
		default:
			throw new InvalidArgumentException($case);
		}
	}

	/**
	 * Generate the %M format for a date.
	 *
	 * @return string
	 */
	protected function formatShortMonth() {
		return $this->monthNameAbbreviated($this->m, $this->isLeapYear());
	}

	/**
	 * Generate the %y format for a date.
	 *
	 * NOTE Short year is NOT a 2-digit year.  It is for calendars such as hebrew
	 * which have a 3-digit form of 4-digit years.
	 *
	 * @return string
	 */
	protected function formatShortYear() {
		return WT_I18N::digits($this->y);
	}

	/**
	 * Generate the %A format for a date.
	 *
	 * @return string
	 */
	protected function formatGedcomDay() {
		if ($this->d == 0) {
			return '';
		} else {
			return sprintf('%02d', $this->d);
		}
	}

	/**
	 * Generate the %O format for a date.
	 *
	 * @return string
	 */
	protected function formatGedcomMonth() {
		// Our simple lookup table doesn't work correctly for Adar on leap years
		if ($this->m == 7 && $this->calendar instanceof JewishCalendar && !$this->calendar->isLeapYear($this->y)) {
			return 'ADR';
		} else {
			return array_search($this->m, static::$MONTH_ABBREV);
		}
	}

	/**
	 * Generate the %E format for a date.
	 *
	 * @return string
	 */
	protected function formatGedcomYear() {
		if ($this->y == 0) {
			return '';
		} else {
			return sprintf('%04d', $this->y);
		}
	}

	/**
	 * Generate the %Y format for a date.
	 *
	 * @return string
	 */
	protected function formatLongYear() {
		return WT_I18N::digits($this->y);
	}

	/**
	 * Which months follows this one?  Calendars with leap-months should provide their own implementation.
	 *
	 * @return integer[]
	 */
	protected function nextMonth() {
		return array($this->m == static::MONTHS_IN_YEAR ? $this->nextYear($this->y) : $this->y, ($this->m % static::MONTHS_IN_YEAR) + 1);
	}

	/**
	 * Convert a decimal number to roman numerals
	 *
	 * @param integer $number
	 *
	 * @return string
	 */
	protected static function numberToRomanNumerals($number) {
		if ($number < 1) {
			// Cannot convert zero/negative numbers
			return (string)$number;
		}
		$roman = '';
		foreach (self::$roman_numerals as $key => $value) {
			while ($number >= $key) {
				$roman .= $value;
				$number -= $key;
			}
		}

		return $roman;
	}

	/**
	 * Convert a roman numeral to decimal
	 *
	 * @param string $roman
	 *
	 * @return integer
	 */
	protected static function romanNumeralsToNumber($roman) {
		$num = 0;
		foreach (self::$roman_numerals as $key => $value) {
			if (strpos($roman, $value) === 0) {
				$num += $key;
				$roman = substr($roman, strlen($value));
			}
		}

		return $num;
	}

	/**
	 * Get today’s date in the current calendar.
	 *
	 * @return integer[]
	 */
	public function todayYmd() {
		return $this->calendar->jdToYmd(unixtojd());
	}

	/**
	 * Convert to today’s date.
	 *
	 * @return WT_Date_Calendar
	 */
	public function today() {
		$tmp    = clone $this;
		$ymd    = $tmp->todayYmd();
		$tmp->y = $ymd[0];
		$tmp->m = $ymd[1];
		$tmp->d = $ymd[2];
		$tmp->setJdFromYmd();

		return $tmp;
	}

	/**
	 * Create a URL that links this date to the WT calendar
	 *
	 * @param string $date_format
	 *
	 * @return string
	 */
	public function calendarUrl($date_format) {
		$URL    = 'calendar.php?cal=' . rawurlencode(static::CALENDAR_ESCAPE);
		$action = 'year';
		if (strpos($date_format, 'Y') !== false || strpos($date_format, 'y') !== false) {
			$URL .= '&amp;year=' . $this->formatGedcomYear();
		}
		if (strpos($date_format, 'F') !== false || strpos($date_format, 'M') !== false || strpos($date_format, 'm') !== false || strpos($date_format, 'n') !== false) {
			$URL .= '&amp;month=' . $this->formatGedcomMonth();
			if ($this->m > 0) {
				$action = 'calendar';
			}
		}
		if (strpos($date_format, 'd') !== false || strpos($date_format, 'D') !== false || strpos($date_format, 'j') !== false) {
			$URL .= '&amp;day=' . $this->formatGedcomDay();
			if ($this->d > 0) {
				$action = 'today';
			}
		}

		return $URL . '&amp;action=' . $action;
	}
}
