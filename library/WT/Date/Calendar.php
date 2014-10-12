<?php
// Classes for Gedcom Date/Calendar functionality.
//
// WT_Date_Calendar is a base class for classes such as WT_Date_Gregorian, etc.
//
// + All supported calendars have non-zero days/months/years.
// + We store dates as both Y/M/D and Julian Days.
// + For imprecise dates such as "JAN 2000" we store the start/end julian day.
//
// NOTE: Since different calendars start their days at different times, (civil
// midnight, solar midnight, sunset, sunrise, etc.), we convert on the basis of
// midday.
//
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

class WT_Date_Calendar {
	const CALENDAR_ESCAPE = '@#DUNKNOWN@';
	const MONTHS_IN_YEAR  = 12;
	const CAL_START_JD    = 0; // @#DJULIAN@ 01 JAN 4713B.C.
	const CAL_END_JD      = 99999999;
	const DAYS_IN_WEEK    = 7;
	static $MONTH_ABBREV  = array(
		''=>0, 'JAN'=>1, 'FEB'=>2, 'MAR'=>3, 'APR'=>4, 'MAY'=>5, 'JUN'=>6, 'JUL'=>7, 'AUG'=>8, 'SEP'=>9, 'OCT'=>10, 'NOV'=>11, 'DEC'=>12
	);

	/**
	 * The calendar system used to represent this date
	 *
	 * @var CalendarInterface
	 */
	protected $calendar;

	public $y, $m, $d;     // Numeric year/month/day
	public $minJD, $maxJD; // Julian Day numbers

	public function __construct($date) {
		// Construct from an integer (a julian day number)
		if (is_numeric($date)) {
			$this->minJD=$date;
			$this->maxJD=$date;
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
			$this->y=$this->extractYear($date[0]);
			$this->setJdFromYmd();
			return;
		}

		// Construct from an equivalent xxxxDate object
		if (get_class($this)==get_class($date)) {
			$this->y=$date->y;
			$this->m=$date->m;
			$this->d=$date->d;
			$this->minJD=$date->minJD;
			$this->maxJD=$date->maxJD;
			return;
		}

		// ...else construct an inequivalent xxxxDate object
		if ($date->y==0) {
			// Incomplete date - convert on basis of anniversary in current year
			$today = $date->calendar->jdToYmd(unixtojd());
			$jd = $date->calendar->ymdToJd($today[0], $date->m, $date->d == 0 ? $today[2] : $date->d);
		} else {
			// Complete date
			$jd=(int)(($date->maxJD+$date->minJD)/2);
		}
		list($this->y, $this->m, $this->d) = $this->calendar->jdToYmd($jd);
		// New date has same precision as original date
		if ($date->y==0) $this->y=0;
		if ($date->m==0) $this->m=0;
		if ($date->d==0) $this->d=0;
		$this->setJdFromYmd();
	}

	function isLeapYear() {
		return $this->calendar->isLeapYear($this->y);
	}

	// Set the object’s JD from a potentially incomplete YMD
	public function setJdFromYmd() {
		if ($this->y==0) {
			$this->minJD=0;
			$this->maxJD=0;
		} else
			if ($this->m==0) {
				$this->minJD=$this->calendar->ymdToJd($this->y, 1, 1);
				$this->maxJD=$this->calendar->ymdToJd($this->nextYear($this->y), 1, 1)-1;
			} else {
				if ($this->d==0) {
					list($ny,$nm)=$this->nextMonth();
					$this->minJD=$this->calendar->ymdToJd($this->y, $this->m,  1);
					$this->maxJD=$this->calendar->ymdToJd($ny, $nm, 1)-1;
				} else {
					$this->minJD=$this->calendar->ymdToJd($this->y, $this->m, $this->d);
					$this->maxJD=$this->minJD;
				}
			}
	}

	// We put these in the base class, to save duplicating it in the Julian and Gregorian calendars
	static function monthNameNominativeCase($n, $leap_year) {
		switch ($n) {
		case 1:  return WT_I18N::translate_c('NOMINATIVE', 'January');
		case 2:  return WT_I18N::translate_c('NOMINATIVE', 'February');
		case 3:  return WT_I18N::translate_c('NOMINATIVE', 'March');
		case 4:  return WT_I18N::translate_c('NOMINATIVE', 'April');
		case 5:  return WT_I18N::translate_c('NOMINATIVE', 'May');
		case 6:  return WT_I18N::translate_c('NOMINATIVE', 'June');
		case 7:  return WT_I18N::translate_c('NOMINATIVE', 'July');
		case 8:  return WT_I18N::translate_c('NOMINATIVE', 'August');
		case 9:  return WT_I18N::translate_c('NOMINATIVE', 'September');
		case 10: return WT_I18N::translate_c('NOMINATIVE', 'October');
		case 11: return WT_I18N::translate_c('NOMINATIVE', 'November');
		case 12: return WT_I18N::translate_c('NOMINATIVE', 'December');
		default: return '';
		}
	}

	protected static function monthNameGenitiveCase($n, $leap_year) {
		switch ($n) {
		case 1:  return WT_I18N::translate_c('GENITIVE', 'January');
		case 2:  return WT_I18N::translate_c('GENITIVE', 'February');
		case 3:  return WT_I18N::translate_c('GENITIVE', 'March');
		case 4:  return WT_I18N::translate_c('GENITIVE', 'April');
		case 5:  return WT_I18N::translate_c('GENITIVE', 'May');
		case 6:  return WT_I18N::translate_c('GENITIVE', 'June');
		case 7:  return WT_I18N::translate_c('GENITIVE', 'July');
		case 8:  return WT_I18N::translate_c('GENITIVE', 'August');
		case 9:  return WT_I18N::translate_c('GENITIVE', 'September');
		case 10: return WT_I18N::translate_c('GENITIVE', 'October');
		case 11: return WT_I18N::translate_c('GENITIVE', 'November');
		case 12: return WT_I18N::translate_c('GENITIVE', 'December');
		default: return '';
		}
	}

	protected static function monthNameLocativeCase($n, $leap_year) {
		switch ($n) {
		case 1:  return WT_I18N::translate_c('LOCATIVE', 'January');
		case 2:  return WT_I18N::translate_c('LOCATIVE', 'February');
		case 3:  return WT_I18N::translate_c('LOCATIVE', 'March');
		case 4:  return WT_I18N::translate_c('LOCATIVE', 'April');
		case 5:  return WT_I18N::translate_c('LOCATIVE', 'May');
		case 6:  return WT_I18N::translate_c('LOCATIVE', 'June');
		case 7:  return WT_I18N::translate_c('LOCATIVE', 'July');
		case 8:  return WT_I18N::translate_c('LOCATIVE', 'August');
		case 9:  return WT_I18N::translate_c('LOCATIVE', 'September');
		case 10: return WT_I18N::translate_c('LOCATIVE', 'October');
		case 11: return WT_I18N::translate_c('LOCATIVE', 'November');
		case 12: return WT_I18N::translate_c('LOCATIVE', 'December');
		default: return '';
		}
	}

	protected static function monthNameInstrumentalCase($n, $leap_year) {
		switch ($n) {
		case 1:  return WT_I18N::translate_c('INSTRUMENTAL', 'January');
		case 2:  return WT_I18N::translate_c('INSTRUMENTAL', 'February');
		case 3:  return WT_I18N::translate_c('INSTRUMENTAL', 'March');
		case 4:  return WT_I18N::translate_c('INSTRUMENTAL', 'April');
		case 5:  return WT_I18N::translate_c('INSTRUMENTAL', 'May');
		case 6:  return WT_I18N::translate_c('INSTRUMENTAL', 'June');
		case 7:  return WT_I18N::translate_c('INSTRUMENTAL', 'July');
		case 8:  return WT_I18N::translate_c('INSTRUMENTAL', 'August');
		case 9:  return WT_I18N::translate_c('INSTRUMENTAL', 'September');
		case 10: return WT_I18N::translate_c('INSTRUMENTAL', 'October');
		case 11: return WT_I18N::translate_c('INSTRUMENTAL', 'November');
		case 12: return WT_I18N::translate_c('INSTRUMENTAL', 'December');
		default: return '';
		}
	}

	protected static function monthNameAbbreviated($n, $leap_year) {
		switch ($n) {
		case 1:  return WT_I18N::translate_c('Abbreviation for January',   'Jan');
		case 2:  return WT_I18N::translate_c('Abbreviation for February',  'Feb');
		case 3:  return WT_I18N::translate_c('Abbreviation for March',     'Mar');
		case 4:  return WT_I18N::translate_c('Abbreviation for April',     'Apr');
		case 5:  return WT_I18N::translate_c('Abbreviation for May',       'May');
		case 6:  return WT_I18N::translate_c('Abbreviation for June',      'Jun');
		case 7:  return WT_I18N::translate_c('Abbreviation for July',      'Jul');
		case 8:  return WT_I18N::translate_c('Abbreviation for August',    'Aug');
		case 9:  return WT_I18N::translate_c('Abbreviation for September', 'Sep');
		case 10: return WT_I18N::translate_c('Abbreviation for October',   'Oct');
		case 11: return WT_I18N::translate_c('Abbreviation for November',  'Nov');
		case 12: return WT_I18N::translate_c('Abbreviation for December',  'Dec');
		default: return '';
		}
	}

	static function dayNames($n) {
		switch ($n) {
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
			throw new InvalidArgumentException($n);
		}
	}

	static function dayNamesAbbreviated($n) {
		switch ($n) {
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
			throw new InvalidArgumentException($n);
		}
	}

	// Most years are 1 more than the previous, but not always (e.g. 1BC->1AD)
	static function nextYear($y) {
		return $y+1;
	}

	// Calendars that use suffixes, etc. (e.g. “B.C.”) or OS/NS notation should redefine this.
	public function extractYear($year) {
		return (int)$year;
	}

	// Compare two dates - helper function for sorting by date
	static function compare($d1, $d2) {
		if ($d1->maxJD < $d2->minJD)
			return -1;
		if ($d2->minJD > $d1->maxJD)
			return 1;
		return 0;
	}

	// How long between an event and a given julian day
	// Return result as either a number of years or
	// a gedcom-style age string.
	// bool $full: true=gedcom style, false=just years
	// int $jd: date for calculation
	// TODO: WT_Date_Jewish needs to redefine this to cope with leap months
	public function getAge($full, $jd, $warn_on_negative=true) {
		if ($this->y==0 || $jd==0) {
			return $full?'':'0';
		}
		if ($this->minJD < $jd && $this->maxJD > $jd) {
			return $full?'':'0';
		}
		if ($this->minJD==$jd) {
			return $full?'':'0';
		}
		if ($warn_on_negative && $jd<$this->minJD) {
			return '<i class="icon-warning"></i>';
		}
		list($y,$m,$d) = $this->calendar->jdToYmd($jd);
		$dy=$y-$this->y;
		$dm=$m-max($this->m,1);
		$dd=$d-max($this->d,1);
		if ($dd<0) {
			$dm--;
		}
		if ($dm<0) {
			$dm+=static::MONTHS_IN_YEAR;
			$dy--;
		}
		// Not a full age?  Then just the years
		if (!$full)
			return $dy;
		// Age in years?
		if ($dy>1)
			return $dy.'y';
		$dm+=$dy*static::MONTHS_IN_YEAR;
		// Age in months?
		if ($dm>1)
			return $dm.'m';
		// Age in days?
		return ($jd-$this->minJD)."d";
	}

	// Convert a date from one calendar to another.
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

	// Is this date within the valid range of the calendar
	public function inValidRange() {
		return $this->minJD >= static::CAL_START_JD && $this->maxJD <= static::CAL_END_JD;
	}

	// How many months in a year
	public function monthsInYear() {
		return static::MONTHS_IN_YEAR;
	}

	// How many days in the current month
	public function daysInMonth() {
		try {
			return $this->calendar->daysInMonth($this->y, $this->m);
		} catch (InvalidArgumentException $ex) {
			// calendar.php calls this with "DD MMM" dates, for which we cannot calculate
			// the length of a month.  Should we validate this before calling this function?
			return 0;
		}
	}

	// How many days in the current week
	public function daysInWeek() {
		return static::DAYS_IN_WEEK;
	}

	// Format a date
	// $format - format string: the codes are specified in http://php.net/date
	public function format($format, $qualifier='') {
		// Don’t show exact details for inexact dates
		if (!$this->d) {
			// The comma is for US "M D, Y" dates
			$format=preg_replace('/%[djlDNSwz][,]?/', '', $format);
		}
		if (!$this->m) {
			$format=str_replace(array('%F', '%m', '%M', '%n', '%t'), '', $format);
		}
		if (!$this->y) {
			$format=str_replace(array('%t', '%L', '%G', '%y', '%Y'), '', $format);
		}
		// If we’ve trimmed the format, also trim the punctuation
		if (!$this->d || !$this->m || !$this->y) {
			$format=trim($format, ',. ;/-');
		}
		if ($this->d && preg_match('/%[djlDNSwz]/', $format)) {
			// If we have a day-number *and* we are being asked to display it, then genitive
			$case='GENITIVE';
		} else {
			switch ($qualifier) {
			case 'TO':
			case 'ABT':
			case 'FROM':
				$case='GENITIVE';
				break;
			case 'AFT':
				$case='LOCATIVE';
				break;
			case 'BEF':
			case 'BET':
			case 'AND':
				$case='INSTRUMENTAL';
				break;
			case '':
			case 'INT':
			case 'EST':
			case 'CAL':
			default: // There shouldn't be any other options...
				$case='NOMINATIVE';
				break;
			}
		}
		// Build up the formatted date, character at a time
		preg_match_all('/%[^%]/', $format, $matches);
		foreach ($matches[0] as $match) {
			switch ($match) {
			case '%d': $format=str_replace($match, $this->formatDayZeros(),       $format); break;
			case '%j': $format=str_replace($match, $this->formatDay(),            $format); break;
			case '%l': $format=str_replace($match, $this->formatLongWeekday(),    $format); break;
			case '%D': $format=str_replace($match, $this->formatShortWeekday(),   $format); break;
			case '%N': $format=str_replace($match, $this->formatIsoWeekday(),     $format); break;
			case '%S': $format=str_replace($match, $this->formatOrdinalSuffix(),  $format); break;
			case '%w': $format=str_replace($match, $this->formatNumericWeekday(), $format); break;
			case '%z': $format=str_replace($match, $this->formatDayOfYear(),      $format); break;
			case '%F': $format=str_replace($match, $this->formatLongMonth($case), $format); break;
			case '%m': $format=str_replace($match, $this->formatMonthZeros(),     $format); break;
			case '%M': $format=str_replace($match, $this->formatShortMonth(),     $format); break;
			case '%n': $format=str_replace($match, $this->formatMonth(),          $format); break;
			case '%t': $format=str_replace($match, $this->daysInMonth(),          $format); break;
			case '%L': $format=str_replace($match, (int)$this->isLeapYear(),      $format); break;
			case '%Y': $format=str_replace($match, $this->formatLongYear(),       $format); break;
			case '%y': $format=str_replace($match, $this->formatShortYear(),      $format); break;
			// These 4 extensions are useful for re-formatting gedcom dates.
			case '%@': $format=str_replace($match, static::CALENDAR_ESCAPE,       $format); break;
			case '%A': $format=str_replace($match, $this->formatGedcomDay(),      $format); break;
			case '%O': $format=str_replace($match, $this->formatGedcomMonth(),    $format); break;
			case '%E': $format=str_replace($match, $this->formatGedcomYear(),     $format); break;
			}
		}
		return $format;
	}

	// Functions to extract bits of the date in various formats.  Individual calendars
	// will want to redefine some of these.
	protected function formatDayZeros() {
		if ($this->d>9) {
			return WT_I18N::digits($this->d);
		} else {
			return WT_I18N::digits('0'.$this->d);
		}
	}

	protected function formatDay() {
		return WT_I18N::digits($this->d);
	}

	protected function formatLongWeekday() {
		return $this->dayNames($this->minJD % static::DAYS_IN_WEEK);
	}

	protected function formatShortWeekday() {
		return $this->dayNamesAbbreviated($this->minJD % static::DAYS_IN_WEEK);
	}

	protected function formatIsoWeekday() {
		return WT_I18N::digits($this->minJD % 7 + 1);
	}

	protected function formatOrdinalSuffix() {
		$func="ordinal_suffix_".WT_LOCALE;
		if (function_exists($func))
			return $func($this->d);
		else
			return '';
	}

	protected function formatNumericWeekday() {
		return WT_I18N::digits(($this->minJD + 1) % static::DAYS_IN_WEEK);
	}

	protected function formatDayOfYear() {
		return WT_I18N::digits($this->minJD - $this->calendar->ymdToJd($this->y, 1, 1));
	}

	protected function formatMonth() {
		return WT_I18N::digits($this->m);
	}

	protected function formatMonthZeros() {
		if ($this->m>9) {
			return WT_I18N::digits($this->m);
		} else {
			return WT_I18N::digits('0'.$this->m);
		}
	}

	protected function formatLongMonth($case='NOMINATIVE') {
		switch ($case) {
		case 'GENITIVE':
			return $this->monthNameGenitiveCase    ($this->m, $this->isLeapYear());
		case 'NOMINATIVE':
			return $this->monthNameNominativeCase  ($this->m, $this->isLeapYear());
		case 'LOCATIVE':
			return $this->monthNameLocativeCase    ($this->m, $this->isLeapYear());
		case 'INSTRUMENTAL':
			return $this->monthNameInstrumentalCase($this->m, $this->isLeapYear());
		default:
			throw new InvalidArgumentException($case);
		}
	}

	protected function formatShortMonth() {
		return $this->monthNameAbbreviated($this->m, $this->isLeapYear());
	}

	// NOTE Short year is NOT a 2-digit year.  It is for calendars such as hebrew
	// which have a 3-digit form of 4-digit years.
	protected function formatShortYear() {
		return WT_I18N::digits($this->y);
	}

	protected function formatGedcomDay() {
		if ($this->d==0) {
			return '';
		} else {
			return sprintf('%02d', $this->d);
		}
	}

	protected function formatGedcomMonth() {
		return array_search($this->m, static::$MONTH_ABBREV);
	}

	protected function formatGedcomYear() {
		if ($this->y==0) {
			return '';
		} else {
			return sprintf('%04d', $this->y);
		}
	}

	protected function formatLongYear() {
		return WT_I18N::digits($this->y);
	}

	// Calendars with leap-months should redefine this.
	protected function nextMonth() {
		return array(
			$this->m==static::MONTHS_IN_YEAR ? $this->nextYear($this->y) : $this->y,
			($this->m % static::MONTHS_IN_YEAR)+1
		);
	}

	// Convert a decimal number to roman numerals
	static function numberToRomanNumerals($num) {
		static $lookup=array(1000=>'M', '900'=>'CM', '500'=>'D', 400=>'CD', 100=>'C', 90=>'XC', 50=>'L', 40=>'XL', 10=>'X', 9=>'IX', 5=>'V', 4=>'IV', 1=>'I');
		if ($num<1) return $num;
		$roman='';
		foreach ($lookup as $key=>$value)
			while ($num>=$key) {
				$roman.=$value;
				$num-=$key;
			}
		return $roman;
	}

	// Convert a roman numeral to decimal
	protected static function romanNumeralsToNumber($roman) {
		static $lookup=array(1000=>'M', '900'=>'CM', '500'=>'D', 400=>'CD', 100=>'C', 90=>'XC', 50=>'L', 40=>'XL', 10=>'X', 9=>'IX', 5=>'V', 4=>'IV', 1=>'I');
		$num=0;
		foreach ($lookup as $key=>$value)
			if (strpos($roman, $value)===0) {
				$num+=$key;
				$roman=substr($roman, strlen($value));
			}
		return $num;
	}

	// Get today’s date in the current calendar
	public function todayYmd() {
		return $this->calendar->jdToYmd(unixtojd());
	}

	public function today() {
		$tmp=clone $this;
		$ymd=$tmp->todayYmd();
		$tmp->y=$ymd[0];
		$tmp->m=$ymd[1];
		$tmp->d=$ymd[2];
		$tmp->setJdFromYmd();
		return $tmp;
	}

	// Create a URL that links this date to the WT calendar
	public function calendarUrl($date_fmt="") {
		global $DATE_FORMAT;
		if (empty($date_fmt)) {
			$date_fmt=$DATE_FORMAT;
		}
		$URL='calendar.php?cal='.rawurlencode(static::CALENDAR_ESCAPE);
		$action="year";
		if (strpos($date_fmt, "Y")!==false
		||  strpos($date_fmt, "y")!==false) {
			$URL.='&amp;year='.$this->formatGedcomYear();
		}
		if (strpos($date_fmt, "F")!==false
		||  strpos($date_fmt, "M")!==false
		||  strpos($date_fmt, "m")!==false
		||  strpos($date_fmt, "n")!==false) {
			$URL.='&amp;month='.$this->formatGedcomMonth();
			if ($this->m>0)
				$action="calendar";
		}
		if (strpos($date_fmt, "d")!==false
		||  strpos($date_fmt, "D")!==false
		||  strpos($date_fmt, "j")!==false) {
			$URL.='&amp;day='.$this->formatGedcomDay();
			if ($this->d>0)
				$action="today";
		}
		return $URL.'&amp;action='.$action;
	}
}
