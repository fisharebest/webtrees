<?php
/**
 * Classes for Gedcom Date/Calendar functionality.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2007 to 2010 Greg Roach
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @author Greg Roach
 * @version $Id$
 *
 * NOTE: Since different calendars start their days at different times, (civil
 * midnight, solar midnight, sunset, sunrise, etc.), we convert on the basis of
 * midday.
 *
 * NOTE: We assume that years start on the first day of the first month.  Where
 * this is not the case (e.g. England prior to 1752), we need to use modified
 * years or the OS/NS notation "4 FEB 1750/51".
 *
 * NOTE: PGV should only be using the GedcomDate class.  The other classes
 * are all for internal use only.
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLASS_DATE_PHP', '');

////////////////////////////////////////////////////////////////////////////////
//
// CalendarDate is a base class for classes such as GregorianDate, etc.
//
// + All supported calendars have non-zero days/months/years.
// + We store dates as both Y/M/D and Julian Days.
// + For imprecise dates such as "JAN 2000" we store the start/end julian day.
//
////////////////////////////////////////////////////////////////////////////////
class CalendarDate {
	var $y, $m, $d;     // Numeric year/month/day
	var $minJD, $maxJD; // Julian Day numbers

	function __construct($date) {
		// Construct from an integer (a julian day number)
		if (is_numeric($date)) {
			$this->minJD=$date;
			$this->maxJD=$date;
			list($this->y, $this->m, $this->d)=$this->JDtoYMD($date);
			return;
		}

		// Construct from an array (of three gedcom-style strings: "1900", "feb", "4")
		if (is_array($date)) {
			$this->d=(int)$date[2];
			if (!is_null($this->MONTH_TO_NUM($date[1]))) {
				$this->m=$this->MONTH_TO_NUM($date[1]);
			} else {
				$this->m=0;
				$this->d=0;
			}
			$this->y=$this->ExtractYear($date[0]);
			$this->SetJDfromYMD();
			return;
		}

		// Construct from an equivalent xxxxDate object
		if ($this->CALENDAR_ESCAPE()==$date->CALENDAR_ESCAPE()) {
			// NOTE - can't copy whole object - need to be able to copy Hebrew to Jewish, etc.
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
			$today=$date->TodayYMD();
			$jd=$date->YMDtoJD($today[0], $date->m, $date->d==0?$today[2]:$date->d);
		} else {
			// Complete date
			$jd=floor(($date->maxJD+$date->minJD)/2);
		}
		list($this->y, $this->m, $this->d)=$this->JDtoYMD($jd);
		// New date has same precision as original date
		if ($date->y==0) $this->y=0;
		if ($date->m==0) $this->m=0;
		if ($date->d==0) $this->d=0;
		$this->SetJDfromYMD();
	}

	// Set the object's JD from a potentially incomplete YMD
	function SetJDfromYMD() {
		if ($this->y==0) {
			$this->minJD=0;
			$this->maxJD=0;
		} else
			if ($this->m==0) {
				$this->minJD=$this->YMDtoJD($this->y, 1, 1);
				$this->maxJD=$this->YMDtoJD($this->NextYear($this->y), 1, 1)-1;
			} else {
				if ($this->d==0) {
					list($ny,$nm)=$this->NextMonth();
					$this->minJD=$this->YMDtoJD($this->y, $this->m,  1);
					$this->maxJD=$this->YMDtoJD($ny, $nm, 1)-1;
				} else {
					$this->minJD=$this->YMDtoJD($this->y, $this->m, $this->d);
					$this->maxJD=$this->minJD;
				}
			}
	}

	// Calendars are defined in terms of the following static functions.
	// They should redefine them as necessary.
	static function CALENDAR_ESCAPE() {
		return '@#DUNKNOWN@';
	}
	static function NUM_MONTHS() {
		return 12;
	}
	static function MONTH_TO_NUM($m) {
		static $months=array(''=>0, 'jan'=>1, 'feb'=>2, 'mar'=>3, 'apr'=>4, 'may'=>5, 'jun'=>6, 'jul'=>7, 'aug'=>8, 'sep'=>9, 'oct'=>10, 'nov'=>11, 'dec'=>12);
		if (isset($months[$m])) {
			return $months[$m];
		} else {
			return null;
		}
	}
	// We put these in the base class, to save duplicating it in the Julian and Gregorian calendars
	static function NUM_TO_MONTH_NOMINATIVE($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('NOMINATIVE', 'January');
		case 2:  return i18n::translate_c('NOMINATIVE', 'February');
		case 3:  return i18n::translate_c('NOMINATIVE', 'March');
		case 4:  return i18n::translate_c('NOMINATIVE', 'April');
		case 5:  return i18n::translate_c('NOMINATIVE', 'May');
		case 6:  return i18n::translate_c('NOMINATIVE', 'June');
		case 7:  return i18n::translate_c('NOMINATIVE', 'July');
		case 8:  return i18n::translate_c('NOMINATIVE', 'August');
		case 9:  return i18n::translate_c('NOMINATIVE', 'September');
		case 10: return i18n::translate_c('NOMINATIVE', 'October');
		case 11: return i18n::translate_c('NOMINATIVE', 'November');
		case 12: return i18n::translate_c('NOMINATIVE', 'December');
		default: return '';
		}
	}
	static function NUM_TO_MONTH_GENITIVE($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('GENITIVE', 'January');
		case 2:  return i18n::translate_c('GENITIVE', 'February');
		case 3:  return i18n::translate_c('GENITIVE', 'March');
		case 4:  return i18n::translate_c('GENITIVE', 'April');
		case 5:  return i18n::translate_c('GENITIVE', 'May');
		case 6:  return i18n::translate_c('GENITIVE', 'June');
		case 7:  return i18n::translate_c('GENITIVE', 'July');
		case 8:  return i18n::translate_c('GENITIVE', 'August');
		case 9:  return i18n::translate_c('GENITIVE', 'September');
		case 10: return i18n::translate_c('GENITIVE', 'October');
		case 11: return i18n::translate_c('GENITIVE', 'November');
		case 12: return i18n::translate_c('GENITIVE', 'December');
		default: return '';
		}
	}
	static function NUM_TO_MONTH_LOCATIVE($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('LOCATIVE', 'January');
		case 2:  return i18n::translate_c('LOCATIVE', 'February');
		case 3:  return i18n::translate_c('LOCATIVE', 'March');
		case 4:  return i18n::translate_c('LOCATIVE', 'April');
		case 5:  return i18n::translate_c('LOCATIVE', 'May');
		case 6:  return i18n::translate_c('LOCATIVE', 'June');
		case 7:  return i18n::translate_c('LOCATIVE', 'July');
		case 8:  return i18n::translate_c('LOCATIVE', 'August');
		case 9:  return i18n::translate_c('LOCATIVE', 'September');
		case 10: return i18n::translate_c('LOCATIVE', 'October');
		case 11: return i18n::translate_c('LOCATIVE', 'November');
		case 12: return i18n::translate_c('LOCATIVE', 'December');
		default: return '';
		}
	}
	static function NUM_TO_MONTH_INSTRUMENTAL($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('INSTRUMENTAL', 'January');
		case 2:  return i18n::translate_c('INSTRUMENTAL', 'February');
		case 3:  return i18n::translate_c('INSTRUMENTAL', 'March');
		case 4:  return i18n::translate_c('INSTRUMENTAL', 'April');
		case 5:  return i18n::translate_c('INSTRUMENTAL', 'May');
		case 6:  return i18n::translate_c('INSTRUMENTAL', 'June');
		case 7:  return i18n::translate_c('INSTRUMENTAL', 'July');
		case 8:  return i18n::translate_c('INSTRUMENTAL', 'August');
		case 9:  return i18n::translate_c('INSTRUMENTAL', 'September');
		case 10: return i18n::translate_c('INSTRUMENTAL', 'October');
		case 11: return i18n::translate_c('INSTRUMENTAL', 'November');
		case 12: return i18n::translate_c('INSTRUMENTAL', 'December');
		default: return '';
		}
	}
	static function NUM_TO_SHORT_MONTH($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate('Jan');
		case 2:  return i18n::translate('Feb');
		case 3:  return i18n::translate('Mar');
		case 4:  return i18n::translate('Apr');
		case 5:  return i18n::translate('May');
		case 6:  return i18n::translate('Jun');
		case 7:  return i18n::translate('Jul');
		case 8:  return i18n::translate('Aug');
		case 9:  return i18n::translate('Sep');
		case 10: return i18n::translate('Oct');
		case 11: return i18n::translate('Nov');
		case 12: return i18n::translate('Dec');
		default: return '';
		}
	}
	static function NUM_TO_GEDCOM_MONTH($n, $leap_year) {
		switch ($n) {
		case 1:  return 'JAN';
		case 2:  return 'FEB';
		case 3:  return 'MAR';
		case 4:  return 'APR';
		case 5:  return 'MAY';
		case 6:  return 'JUN';
		case 7:  return 'JUL';
		case 8:  return 'AUG';
		case 9:  return 'SEP';
		case 10: return 'OCT';
		case 11: return 'NOV';
		case 12: return 'DEC';
		default: return '';
		}
	}
	static function CAL_START_JD() {
		return 0; // @#DJULIAN@ 01 JAN 4713B.C.
	}
	static function CAL_END_JD() {
		return 99999999;
	}
	static function NUM_DAYS_OF_WEEK() {
		return 7;
	}
	static function LONG_DAYS_OF_WEEK($n) {
		switch ($n) {
		case 0: return i18n::translate('Monday');
		case 1: return i18n::translate('Tuesday');
		case 2: return i18n::translate('Wednesday');
		case 3: return i18n::translate('Thursday');
		case 4: return i18n::translate('Friday');
		case 5: return i18n::translate('Saturday');
		case 6: return i18n::translate('Sunday');
		}
	}
	static function SHORT_DAYS_OF_WEEK($n) {
		switch ($n) {
		case 0: return i18n::translate('Mon');
		case 1: return i18n::translate('Tue');
		case 2: return i18n::translate('Wed');
		case 3: return i18n::translate('Thu');
		case 4: return i18n::translate('Fri');
		case 5: return i18n::translate('Sat');
		case 6: return i18n::translate('Sun');
		}
	}
	static function YMDtoJD($y, $m, $d) {
		return 0;
	}
	static function JDtoYMD($j) {
		return array(0, 0, 0);
	}
	// Most years are 1 more than the previous, but not always (e.g. 1BC->1AD)
	static function NextYear($y) {
		return $y+1;
	}
	// Calendars that use suffixes, etc. (e.g. 'B.C.') or OS/NS notation should redefine this.
	function ExtractYear($year) {
		return (int)$year;
	}
	// Leap years may have extra days, extra months, etc.
	function IsLeapYear() {
		return false;
	}

	// Compare two dates - helper function for sorting by date
	static function Compare($d1, $d2) {
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
	// TODO: JewishDate needs to redefine this to cope with leap months
	function GetAge($full, $jd, $warn_on_negative=true) {
		if ($this->y==0 || $jd==0) {
			return '';
		}
		if ($this->minJD < $jd && $this->maxJD > $jd) {
			return '';
		}
		if ($this->minJD==$jd) {
			return $full?'':'0';
		}
		if ($warn_on_negative && $jd<$this->minJD) {
			return '<img alt="" src="images/warning.gif" />';
		}
		list($y,$m,$d)=$this->JDtoYMD($jd);
		$dy=$y-$this->y;
		$dm=$m-max($this->m,1);
		$dd=$d-max($this->d,1);
		if ($dd<0) {
			$dd+=$this->DaysInMonth();
			$dm--;
		}
		if ($dm<0) {
			$dm+=$this->NUM_MONTHS();
			$dy--;
		}
		// Not a full age?  Then just the years
		if (!$full)
			return $dy;
		// Age in years?
		if ($dy>1)
			return $dy.'y';
		$dm+=$dy*$this->NUM_MONTHS();
		// Age in months?
		if ($dm>1)
			return $dm.'m';
		// Age in days?
		return ($jd-$this->minJD)."d";
	}

	// Convert a date from one calendar to another.
	function convert_to_cal($calendar) {
  	switch ($calendar) {
		case 'gregorian':
			return new GregorianDate($this);
		case 'julian':
			return new JulianDate($this);
		case 'jewish':
			if (WT_LOCALE!='he')
				return new JewishDate($this);
			// no  break
		case 'hebrew':
			return new HebrewDate($this);
		case 'french':
			return new FrenchRDate($this);
		case 'arabic':
			if (WT_LOCALE!='ar')
				return new ArabicDate($this);
			// no  break
		case 'hijri':
			return new HijriDate($this);
		default:
			return $this;
		}
	}

	// Is this date within the valid range of the calendar
	function InValidRange() {
		return $this->minJD>=$this->CAL_START_JD() && $this->maxJD<=$this->CAL_END_JD();
	}

	// How many days in the current month
	function DaysInMonth() {
		list($ny,$nm)=$this->NextMonth();
		return $this->YMDtoJD($ny, $nm, 1) - $this->YMDtoJD($this->y, $this->m, 1);
	}

	// How many days in the current week
	function DaysInWeek() {
		return $this->NUM_DAYS_OF_WEEK();
	}

	// Format a date
	// $format - format string: the codes are specified in http://php.net/date
	function Format($format, $qualifier='') {
		// Legacy formats (DMY) become jFY
		if (preg_match('/^[DMY,. ;\/-]+$/', $format)) {
			$format=strtr($format, 'DM', 'jF');
		}
		// Don't show exact details for inexact dates
		if (!$this->d) {
			$format=str_replace(array('%d', '%j', '%l', '%D', '%N', '%S', '%w', '%z'), '', $format);
		}
		if (!$this->m) {
			$format=str_replace(array('%F', '%m', '%M', '%n', '%t'), '', $format);
		}
		if (!$this->y) {
			$format=str_replace(array('%t', '%L', '%G', '%y', '%Y'), '', $format);
		}
		// If we've trimmed the format, also trim the punctuation
		if (!$this->d || !$this->m || !$this->y) {
			$format=trim($format, ',. ;/-');
		}
		if ($this->d) {
			$case='GENITIVE';
		} else {
			switch ($qualifier) {
			case '':
			case 'int':
			case 'est':
	 		case 'cal': $case='NOMINATIVE'; break;
			case 'to':
			case 'abt':
			case 'from': $case='GENITIVE'; break;
			case 'aft':  $case='LOCATIVE'; break;
			case 'bef':
			case 'bet':
			case 'and': $case='INSTRUMENTAL'; break;
			}
		}
		// Build up the formated date, character at a time
		preg_match_all('/%[^%]/', $format, $matches);
		foreach ($matches[0] as $match) {
			switch ($match) {
			case '%d': $format=str_replace($match, $this->FormatDayZeros(),       $format); break;
			case '%j': $format=str_replace($match, $this->FormatDay(),            $format); break;
			case '%l': $format=str_replace($match, $this->FormatLongWeekday(),    $format); break;
			case '%D': $format=str_replace($match, $this->FormatShortWeekday(),   $format); break;
			case '%N': $format=str_replace($match, $this->FormatISOWeekday(),     $format); break;
			case '%S': $format=str_replace($match, $this->FormatOrdinalSuffix(),  $format); break;
			case '%w': $format=str_replace($match, $this->FormatNumericWeekday(), $format); break;
			case '%z': $format=str_replace($match, $this->FormatDayOfYear(),      $format); break;
			case '%F': $format=str_replace($match, $this->FormatLongMonth($case), $format); break;
			case '%m': $format=str_replace($match, $this->FormatMonthZeros(),     $format); break;
			case '%M': $format=str_replace($match, $this->FormatShortMonth(),     $format); break;
			case '%n': $format=str_replace($match, $this->FormatMonth(),          $format); break;
			case '%t': $format=str_replace($match, $this->DaysInMonth(),          $format); break;
			case '%L': $format=str_replace($match, (int)$this->IsLeapYear(),      $format); break;
			case '%Y': $format=str_replace($match, $this->FormatLongYear(),       $format); break;
			case '%y': $format=str_replace($match, $this->FormatShortYear(),      $format); break;
			// These 4 extensions are useful for re-formatting gedcom dates.
			case '%@': $format=str_replace($match, $this->CALENDAR_ESCAPE(),      $format); break;
			case '%A': $format=str_replace($match, $this->FormatGedcomDay(),      $format); break;
			case '%O': $format=str_replace($match, $this->FormatGedcomMonth(),    $format); break;
			case '%E': $format=str_replace($match, $this->FormatGedcomYear(),     $format); break;
			}
		}
		return $format;
	}

	// Functions to extract bits of the date in various formats.  Individual calendars
	// will want to redefine some of these.
	function FormatDayZeros() {
		if ($this->d<10)
			return '0'.$this->d;
		else
			return $this->d;
	}

	function FormatDay() {
		return $this->d;
	}

	function FormatLongWeekday() {
		return $this->LONG_DAYS_OF_WEEK($this->minJD % $this->NUM_DAYS_OF_WEEK());
	}

	function FormatShortWeekday() {
		return $this->SHORT_DAYS_OF_WEEK($this->minJD % $this->NUM_DAYS_OF_WEEK());
	}

	function FormatISOWeekday() {
		return $this->minJD % 7 + 1;
	}

	function FormatOrdinalSuffix() {
		$func="ordinal_suffix_".WT_LOCALE;
		if (function_exists($func))
			return $func($this->d);
		else
			return '';
	}

	function FormatNumericWeekday() {
		return ($this->minJD + 1) % $this->NUM_DAYS_OF_WEEK();
	}

	function FormatDayOfYear() {
		return $this->minJD - $this->YMDtoJD($this->y, 1, 1);
	}

	function FormatMonth() {
		return $this->m;
	}

	function FormatMonthZeros() {
		if ($this->m > 9)
			return $this->m;
		else
			return '0'.$this->m;
	}

	function FormatLongMonth($case='NOMINATIVE') {
		switch ($case) {
		case 'GENITIVE':     return $this->NUM_TO_MONTH_GENITIVE    ($this->m, $this->IsLeapYear());
		case 'NOMINATIVE':   return $this->NUM_TO_MONTH_NOMINATIVE  ($this->m, $this->IsLeapYear());
		case 'LOCATIVE':     return $this->NUM_TO_MONTH_LOCATIVE    ($this->m, $this->IsLeapYear());
		case 'INSTRUMENTAL': return $this->NUM_TO_MONTH_INSTRUMENTAL($this->m, $this->IsLeapYear());
		}
	}

	function FormatShortMonth() {
		return $this->NUM_TO_SHORT_MONTH($this->m, $this->IsLeapYear());
	}

	// NOTE Short year is NOT a 2-digit year.  It is for calendars such as hebrew
	// which have a 3-digit form of 4-digit years.
	function FormatShortYear() {
		return $this->y;
	}

	function FormatGedcomDay() {
		if ($this->d==0)
			return '';
		else
			return sprintf('%02d', $this->d);
	}

	function FormatGedcomMonth() {
		return $this->NUM_TO_GEDCOM_MONTH($this->m, $this->IsLeapYear());
	}

	function FormatGedcomYear() {
		if ($this->y==0)
			return '';
		else
			return sprintf('%04d', $this->y);
	}

	function FormatLongYear() {
		return $this->y;
	}

	// Calendars with leap-months should redefine this.
	function NextMonth() {
		return array(
			$this->m==$this->NUM_MONTHS() ? $this->NextYear($this->y) : $this->y,
			($this->m%$this->NUM_MONTHS())+1
		);
	}

	// Convert a decimal number to roman numerals
	static function NumToRoman($num) {
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
	static function RomanToNum($roman) {
		static $lookup=array(1000=>'M', '900'=>'CM', '500'=>'D', 400=>'CD', 100=>'C', 90=>'XC', 50=>'L', 40=>'XL', 10=>'X', 9=>'IX', 5=>'V', 4=>'IV', 1=>'I');
		$num=0;
		foreach ($lookup as $key=>$value)
			if (strpos($roman, $value)===0) {
				$num+=$key;
				$roman=substr($roman, strlen($value));
			}
		return $num;
	}

	// Get today's date in the current calendar
	function TodayYMD() {
		return $this->JDtoYMD(GregorianDate::YMDtoJD(date('Y'), date('n'), date('j')));
	}
	function Today() {
		$tmp=clone $this;
		$ymd=$tmp->TodayYMD();
		$tmp->y=$ymd[0];
		$tmp->m=$ymd[1];
		$tmp->d=$ymd[2];
		$tmp->SetJDfromYMD();
		return $tmp;
	}

	// Create a URL that links this date to the PGV calendar
	function CalendarURL($date_fmt="") {
		global $DATE_FORMAT;
		if (empty($date_fmt))
			$date_fmt=$DATE_FORMAT;
		$URL='calendar.php?cal='.$this->CALENDAR_ESCAPE();
		$action="year";
		if (strpos($date_fmt, "Y")!==false
		||  strpos($date_fmt, "y")!==false) {
			$URL.='&year='.$this->FormatGedcomYear();
		}
		if (strpos($date_fmt, "F")!==false
		||  strpos($date_fmt, "M")!==false
		||  strpos($date_fmt, "m")!==false
		||  strpos($date_fmt, "n")!==false) {
			$URL.='&month='.$this->FormatGedcomMonth();
			if ($this->m>0)
				$action="calendar";
		}
		if (strpos($date_fmt, "d")!==false
		||  strpos($date_fmt, "D")!==false
		||  strpos($date_fmt, "j")!==false) {
			$URL.='&day='.$this->FormatGedcomDay();
			if ($this->d>0)
				$action="today";
		}
		return encode_url($URL.'&action='.$action);
	}
} // class CalendarDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the Gregorian calendar
////////////////////////////////////////////////////////////////////////////////
class GregorianDate extends CalendarDate {
	static function CALENDAR_ESCAPE() {
		return '@#DGREGORIAN@';
	}
	static function CAL_START_JD() {
		return 2299161; // 15 OCT 1582
	}

	function IsLeapYear() {
		return $this->y%4==0 && $this->y%100!=0 || $this->y%400==0;
	}

	static function YMDtoJD($y, $m, $d) {
		if ($y<0) // 0=1BC, -1=2BC, etc.
			++$y;
		$a=floor((14-$m)/12);
		$y=$y+4800-$a;
		$m=$m+12*$a-3;
		return $d+floor((153*$m+2)/5)+365*$y+floor($y/4)-floor($y/100)+floor($y/400)-32045;
	}

	static function JDtoYMD($j) {
		$a=$j+32044;
		$b=floor((4*$a+3)/146097);
		$c=$a-floor($b*146097/4);
		$d=floor((4*$c+3)/1461);
		$e=$c-floor((1461*$d)/4);
		$m=floor((5*$e+2)/153);
		$day=$e-floor((153*$m+2)/5)+1;
		$month=$m+3-12*floor($m/10);
		$year=$b*100+$d-4800+floor($m/10);
		if ($year<1) // 0=1BC, -1=2BC, etc.
			--$year;
		return array($year, $month, $day);
	}

} // class GregorianDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the Julian Proleptic calendar
// (Proleptic means we extend it backwards, prior to its introduction in 46BC)
////////////////////////////////////////////////////////////////////////////////
class JulianDate extends CalendarDate {
	var $new_old_style=false;

	static function CALENDAR_ESCAPE() {
		return '@#DJULIAN@';
	}

	static function NextYear($y) {
		if ($y==-1)
			return 1;
		else
			return $y+1;
	}

	function IsLeapYear() {
		return $this->y%4==0;
	}

	static function YMDtoJD($y, $m, $d) {
		if ($y<0) // 0=1BC, -1=2BC, etc.
			++$y;
		$a=floor((14-$m)/12);
		$y=$y+4800-$a;
		$m=$m+12*$a-3;
		return $d+floor((153*$m+2)/5)+365*$y+floor($y/4)-32083;
	}

	static function JDtoYMD($j) {
		$c=$j+32082;
		$d=floor((4*$c+3)/1461);
		$e=$c-floor(1461*$d/4);
		$m=floor((5*$e+2)/153);
		$day=$e-floor((153*$m+2)/5)+1;
		$month=$m+3-12*floor($m/10);
		$year=$d-4800+floor($m/10);
		if ($year<1) // 0=1BC, -1=2BC, etc.
		--$year;
		return array($year, $month, $day);
	}

	// Process new-style/old-style years and years BC
	function ExtractYear($year) {
		if (preg_match('/^(\d\d\d\d) \/ \d{1,4}$/', $year, $match)) { // Assume the first year is correct
			$this->new_old_style=true;
			return $match[1]+1;
		} else
			if (preg_match('/^(\d+) b ?c$/', $year, $match))
				return -$match[1];
			else
				return (int)$year;
	}

	function FormatLongYear() {
		if ($this->y<0) {
			// I18N: Number of years "before christ"
			return i18n::translate('%d B.C.', -$this->y);
		} else {
			if ($this->new_old_style) {
				return sprintf('%d/%02d', $this->y-1, $this->y % 100);
			} else
				return $this->y;
		}
	}

	function FormatGedcomYear() {
		if ($this->y<0)
			return sprintf('%04dB.C.', -$this->y);
		else
			if ($this->new_old_style) {
				return sprintf('%04d/%02d', $this->y-1, $this->y % 100);
			} else
				return sprintf('%04d', $this->y);
	}
} // class JulianDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the Jewish calendar
////////////////////////////////////////////////////////////////////////////////
class JewishDate extends CalendarDate {
	static function CALENDAR_ESCAPE() {
		return '@#DHEBREW@';
	}

	static function MONTH_TO_NUM($m) {
		static $months=array(''=>0, 'tsh'=>1, 'csh'=>2, 'ksl'=>3, 'tvt'=>4, 'shv'=>5, 'adr'=>6, 'ads'=>7, 'nsn'=>8, 'iyr'=>9, 'svn'=>10, 'tmz'=>11, 'aav'=>12, 'ell'=>13);
		if (isset($months[$m])) {
			return $months[$m];
		} else {
			return null;
		}
	}
	static function NUM_TO_MONTH_NOMINATIVE($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('NOMINATIVE', 'Tishrei');
		case 2:  return i18n::translate_c('NOMINATIVE', 'Heshvan');
		case 3:  return i18n::translate_c('NOMINATIVE', 'Kislev');
		case 4:  return i18n::translate_c('NOMINATIVE', 'Tevet');
		case 5:  return i18n::translate_c('NOMINATIVE', 'Shevat');
		case 6:  if ($leap_year) return i18n::translate_c('NOMINATIVE', 'Adar'); else return i18n::translate_c('NOMINATIVE', 'Adar I');
		case 7:  return i18n::translate_c('NOMINATIVE', 'Adar II');
		case 8:  return i18n::translate_c('NOMINATIVE', 'Nissan');
		case 9:  return i18n::translate_c('NOMINATIVE', 'Iyar');
		case 10: return i18n::translate_c('NOMINATIVE', 'Sivan');
		case 11: return i18n::translate_c('NOMINATIVE', 'Tamuz');
		case 12: return i18n::translate_c('NOMINATIVE', 'Av');
		case 13: return i18n::translate_c('NOMINATIVE', 'Elul');
		default: return '';
		}
	}
	static function NUM_TO_MONTH_GENITIVE($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('GENITIVE', 'Tishrei');
		case 2:  return i18n::translate_c('GENITIVE', 'Heshvan');
		case 3:  return i18n::translate_c('GENITIVE', 'Kislev');
		case 4:  return i18n::translate_c('GENITIVE', 'Tevet');
		case 5:  return i18n::translate_c('GENITIVE', 'Shevat');
		case 6:  if ($leap_year) return i18n::translate_c('GENITIVE', 'Adar'); else return i18n::translate_c('GENITIVE', 'Adar I');
		case 7:  return i18n::translate_c('GENITIVE', 'Adar II');
		case 8:  return i18n::translate_c('GENITIVE', 'Nissan');
		case 9:  return i18n::translate_c('GENITIVE', 'Iyar');
		case 10: return i18n::translate_c('GENITIVE', 'Sivan');
		case 11: return i18n::translate_c('GENITIVE', 'Tamuz');
		case 12: return i18n::translate_c('GENITIVE', 'Av');
		case 13: return i18n::translate_c('GENITIVE', 'Elul');
		default: return '';
		}
	}
	static function NUM_TO_MONTH_LOCATIVE($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('LOCATIVE', 'Tishrei');
		case 2:  return i18n::translate_c('LOCATIVE', 'Heshvan');
		case 3:  return i18n::translate_c('LOCATIVE', 'Kislev');
		case 4:  return i18n::translate_c('LOCATIVE', 'Tevet');
		case 5:  return i18n::translate_c('LOCATIVE', 'Shevat');
		case 6:  if ($leap_year) return i18n::translate_c('LOCATIVE', 'Adar'); else return i18n::translate_c('LOCATIVE', 'Adar I');
		case 7:  return i18n::translate_c('LOCATIVE', 'Adar II');
		case 8:  return i18n::translate_c('LOCATIVE', 'Nissan');
		case 9:  return i18n::translate_c('LOCATIVE', 'Iyar');
		case 10: return i18n::translate_c('LOCATIVE', 'Sivan');
		case 11: return i18n::translate_c('LOCATIVE', 'Tamuz');
		case 12: return i18n::translate_c('LOCATIVE', 'Av');
		case 13: return i18n::translate_c('LOCATIVE', 'Elul');
		default: return '';
		}
	}
	static function NUM_TO_MONTH_INSTRUMENTAL($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('INSTRUMENTAL', 'Tishrei');
		case 2:  return i18n::translate_c('INSTRUMENTAL', 'Heshvan');
		case 3:  return i18n::translate_c('INSTRUMENTAL', 'Kislev');
		case 4:  return i18n::translate_c('INSTRUMENTAL', 'Tevet');
		case 5:  return i18n::translate_c('INSTRUMENTAL', 'Shevat');
		case 6:  if ($leap_year) return i18n::translate_c('INSTRUMENTAL', 'Adar'); else return i18n::translate_c('INSTRUMENTAL', 'Adar I');
		case 7:  return i18n::translate_c('INSTRUMENTAL', 'Adar II');
		case 8:  return i18n::translate_c('INSTRUMENTAL', 'Nissan');
		case 9:  return i18n::translate_c('INSTRUMENTAL', 'Iyar');
		case 10: return i18n::translate_c('INSTRUMENTAL', 'Sivan');
		case 11: return i18n::translate_c('INSTRUMENTAL', 'Tamuz');
		case 12: return i18n::translate_c('INSTRUMENTAL', 'Av');
		case 13: return i18n::translate_c('INSTRUMENTAL', 'Elul');
		default: return '';
		}
	}
	static function NUM_TO_SHORT_MONTH($n, $leap_year) {
		// TODO: Do these have short names?
		switch ($n) {
		case 1:  return i18n::translate('Tishrei');
		case 2:  return i18n::translate('Heshvan');
		case 3:  return i18n::translate('Kislev');
		case 4:  return i18n::translate('Tevet');
		case 5:  return i18n::translate('Shevat');
		case 6:  if ($leap_year) return i18n::translate('Adar'); else return i18n::translate('Adar I');
		case 7:  return i18n::translate('Adar II');
		case 8:  return i18n::translate('Nissan');
		case 9:  return i18n::translate('Iyar');
		case 10: return i18n::translate('Sivan');
		case 11: return i18n::translate('Tamuz');
		case 12: return i18n::translate('Av');
		case 13: return i18n::translate('Elul');
		default: return '';
		}
	}
	static function NUM_TO_GEDCOM_MONTH($n, $leap_year) {
		// TODO: Do these have short names in English?
		switch ($n) {
		case 1:  return 'TSH';
		case 2:  return 'CSH';
		case 3:  return 'KSL';
		case 4:  return 'TVT';
		case 5:  return 'SHV';
		case 6:  return 'ADR';
		case 7:  return 'ADS';
		case 8:  return 'NSN';
		case 9:  return 'IYR';
		case 10: return 'SVN';
		case 11: return 'TMZ';
		case 12: return 'AAV';
		case 13: return 'ELL';
		default: return '';
		}
	}
	static function NUM_MONTHS() {
		return 13;
	}
	static function CAL_START_JD() {
		return 347998; // 01 TSH 0001 = @#JULIAN@ 7 OCT 3761B.C.
	}

	function NextMonth() {
		if ($this->m==6 && !$this->IsLeapYear())
			return array($this->y, 8);
		else
			return array($this->y+($this->m==13?1:0), ($this->m%13)+1);
	}

	function IsLeapYear() {
		return ((7*$this->y+1)%19)<7;
	}

	// TODO implement this function locally
	static function YMDtoJD($y, $mh, $d) {
		if (function_exists('JewishToJD'))
			return JewishToJD($mh, $d, $y);
		else
			return 0;
	}

	// TODO implement this function locally
	static function JDtoYMD($j) {
		if (function_exists('JdToJewish'))
			list($m, $d, $y)=explode('/', JDToJewish($j));
		else
			list($m, $d, $y)=array(0, 0, 0);
		return array($y, $m, $d);
	}
} // class JewishDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the Hebrew calendar.
// NOTE - this is the same as the Jewish Calendar, but displays dates in hebrew
// rather than the local language.
////////////////////////////////////////////////////////////////////////////////
class HebrewDate extends JewishDate {
	const GERSHAYIM="״";
	const GERSH="׳";
	const ALAFIM="אלפים";
	
	function FormatDayZeros() {
		return $this->NumToHebrew($this->d);
	}

	function FormatDay() {
		return $this->NumToHebrew($this->d);
	}

	static function LONG_DAYS_OF_WEEK($n) {
		// Do not translate these - they are supposed to be hebrew, whatever language is shown.
		switch ($n) {
		case 0: return 'שני';
		case 1: return 'שלישי';
		case 2: return 'רביעי';
		case 3: return 'חמישי';
		case 4: return 'ששי';
		case 5: return 'שבת';
		case 6: return 'ראשון';
		}
	}
	static function SHORT_DAYS_OF_WEEK($n) {
		// TODO: Do these have short names?
		return LONG_DAYS_OF_WEEK($n);
	}

	static function NUM_TO_MONTH_NOMINATIVE($n, $leap_year) {
		// Do not translate these - they are supposed to be hebrew, whatever language is shown.
		switch ($n) {
		case 1:  return 'תשרי';
		case 2:  return 'חשוון';
		case 3:  return 'כסלו';
		case 4:  return 'טבת';
		case 5:  return 'שבט';
		case 6:  if ($leap_year) return 'אדר א׳'; else return 'אדר';
		case 7:  return 'אדר ב׳';
		case 8:  return 'ניסן';
		case 9:  return 'איי�';
		case 10: return 'סיוון';
		case 11: return 'תמוז';
		case 12: return 'אב';
		case 13: return 'אלול';
		default: return '';
		}
	}

	static function NUM_TO_MONTH_GENITIVE($n, $leap_year) {
		// Hebrew does not have genitive forms
		return $this->NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}
	
	static function NUM_TO_MONTH_LOCATIVE($n, $leap_year) {
		// Hebrew does not have locative forms
		return $this->NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}
	
	static function NUM_TO_MONTH_INSTRUMENTAL($n, $leap_year) {
		// Hebrew does not have instrumental forms
		return $this->NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}
	
	static function NUM_TO_SHORT_MONTH($n, $leap_year) {
		// TODO: Do these have short names?
		return $this->NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}

	function FormatShortYear() {
		return $this->NumToHebrew($this->y%1000);
	}

	function FormatLongYear() {
		return $this->NumToHebrew($this->y);
	}
	// Convert a decimal number to hebrew - like roman numerals, but with extra punctuation
	// and special rules.
	static function NumToHebrew($num) {
		global $DISPLAY_JEWISH_THOUSANDS;

		static $jHundreds = array("", "ק", "ר", "ש", "ת", "תק", "תר","תש", "תת", "תתק");
		static $jTens     = array("", "י", "כ", "ל", "מ", "נ", "ס", "ע", "פ", "צ");
		static $jTenEnds  = array("", "י", "ך", "ל", "ם", "ן", "ס", "ע", "ף", "ץ");
		static $tavTaz    = array("ט״ו", "ט״ז");
		static $jOnes     = array("", "א", "ב", "ג", "ד", "ה", "ו", "ז", "ח", "ט");

		$shortYear = $num %1000; //discard thousands
		//next check for all possible single Hebrew digit years
		$singleDigitYear=($shortYear < 11 || ($shortYear <100 && $shortYear % 10 == 0)  || ($shortYear <= 400 && $shortYear % 100 ==0));
		$thousands = $num / 1000; //get # thousands
		$sb = "";
		//append thousands to String
		if($num % 1000 == 0) { // in year is 5000, 4000 etc
			$sb .= $jOnes[$thousands];
			$sb .= self::GERSH;
			$sb .= " ";
			$sb .= self::ALAFIM; //add # of thousands plus word thousand (overide alafim boolean)
		} else if($DISPLAY_JEWISH_THOUSANDS) { // if alafim boolean display thousands
			$sb .= $jOnes[$thousands];
			$sb .= self::GERSH; //append thousands quote
			$sb .= " ";
		}
		$num = $num % 1000; //remove 1000s
		$hundreds = $num / 100; // # of hundreds
		$sb .= $jHundreds[$hundreds]; //add hundreds to String
		$num = $num % 100; //remove 100s
		if($num == 15) { //special case 15
			$sb .= $tavTaz[0];
		} else if($num == 16) { //special case 16
			$sb .= $tavTaz[1];
		} else {
			$tens = $num / 10;
			if($num % 10 == 0) {                                    // if evenly divisable by 10
				if($singleDigitYear == false) {
					$sb .= $jTenEnds[$tens]; // use end letters so that for example 5750 will end with an end nun
				} else {
					$sb .= $jTens[$tens]; // use standard letters so that for example 5050 will end with a regular nun
				}
			} else {
				$sb .= $jTens[$tens];
				$num = $num % 10;
				$sb .= $jOnes[$num];
			}
		}
		if ($singleDigitYear == true) {
			$sb .= self::GERSH; //append single quote
		} else { // append double quote before last digit
        	$pos1 = strlen($sb)-2;
 			$sb = substr($sb, 0, $pos1) . self::GERSHAYIM . substr($sb, $pos1);
			$sb = str_replace(self::GERSHAYIM . self::GERSHAYIM, self::GERSHAYIM, $sb); //replace double gershayim with single instance
		}
		return $sb;
	}

} // class HebrewDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the French Republican calendar
////////////////////////////////////////////////////////////////////////////////
class FrenchRDate extends CalendarDate {
	static function CALENDAR_ESCAPE() {
		return '@#DFRENCH R@';
	}

	static function MONTH_TO_NUM($m) {
		static $months=array(''=>0, 'vend'=>1, 'brum'=>2, 'frim'=>3, 'nivo'=>4, 'pluv'=>5, 'vent'=>6, 'germ'=>7, 'flor'=>8, 'prai'=>9, 'mess'=>10, 'ther'=>11, 'fruc'=>12, 'comp'=>13);
		if (isset($months[$m])) {
			return $months[$m];
		} else {
			return null;
		}
	}
	static function NUM_TO_MONTH_NOMINATIVE($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('NOMINATIVE', 'Vendémiaire');
		case 2:  return i18n::translate_c('NOMINATIVE', 'Brumaire');
		case 3:  return i18n::translate_c('NOMINATIVE', 'Frimaire');
		case 4:  return i18n::translate_c('NOMINATIVE', 'Nivôse');
		case 5:  return i18n::translate_c('NOMINATIVE', 'Pluviôse');
		case 6:  return i18n::translate_c('NOMINATIVE', 'Ventôse');
		case 7:  return i18n::translate_c('NOMINATIVE', 'Germinal');
		case 8:  return i18n::translate_c('NOMINATIVE', 'Floréal');
		case 9:  return i18n::translate_c('NOMINATIVE', 'Prairial');
		case 10: return i18n::translate_c('NOMINATIVE', 'Messidor');
		case 11: return i18n::translate_c('NOMINATIVE', 'Thermidor');
		case 12: return i18n::translate_c('NOMINATIVE', 'Fructidor');
		case 13: return i18n::translate_c('NOMINATIVE', 'jours complémentaires');
		}
	}
	static function NUM_TO_MONTH_GENITIVE($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('GENITIVE', 'Vendémiaire');
		case 2:  return i18n::translate_c('GENITIVE', 'Brumaire');
		case 3:  return i18n::translate_c('GENITIVE', 'Frimaire');
		case 4:  return i18n::translate_c('GENITIVE', 'Nivôse');
		case 5:  return i18n::translate_c('GENITIVE', 'Pluviôse');
		case 6:  return i18n::translate_c('GENITIVE', 'Ventôse');
		case 7:  return i18n::translate_c('GENITIVE', 'Germinal');
		case 8:  return i18n::translate_c('GENITIVE', 'Floréal');
		case 9:  return i18n::translate_c('GENITIVE', 'Prairial');
		case 10: return i18n::translate_c('GENITIVE', 'Messidor');
		case 11: return i18n::translate_c('GENITIVE', 'Thermidor');
		case 12: return i18n::translate_c('GENITIVE', 'Fructidor');
		case 13: return i18n::translate_c('GENITIVE', 'jours complémentaires');
		}
	}
	static function NUM_TO_MONTH_LOCATIVE($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('LOCATIVE', 'Vendémiaire');
		case 2:  return i18n::translate_c('LOCATIVE', 'Brumaire');
		case 3:  return i18n::translate_c('LOCATIVE', 'Frimaire');
		case 4:  return i18n::translate_c('LOCATIVE', 'Nivôse');
		case 5:  return i18n::translate_c('LOCATIVE', 'Pluviôse');
		case 6:  return i18n::translate_c('LOCATIVE', 'Ventôse');
		case 7:  return i18n::translate_c('LOCATIVE', 'Germinal');
		case 8:  return i18n::translate_c('LOCATIVE', 'Floréal');
		case 9:  return i18n::translate_c('LOCATIVE', 'Prairial');
		case 10: return i18n::translate_c('LOCATIVE', 'Messidor');
		case 11: return i18n::translate_c('LOCATIVE', 'Thermidor');
		case 12: return i18n::translate_c('LOCATIVE', 'Fructidor');
		case 13: return i18n::translate_c('LOCATIVE', 'jours complémentaires');
		}
	}
	static function NUM_TO_MONTH_INSTRUMENTAL($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('INSTRUMENTAL', 'Vendémiaire');
		case 2:  return i18n::translate_c('INSTRUMENTAL', 'Brumaire');
		case 3:  return i18n::translate_c('INSTRUMENTAL', 'Frimaire');
		case 4:  return i18n::translate_c('INSTRUMENTAL', 'Nivôse');
		case 5:  return i18n::translate_c('INSTRUMENTAL', 'Pluviôse');
		case 6:  return i18n::translate_c('INSTRUMENTAL', 'Ventôse');
		case 7:  return i18n::translate_c('INSTRUMENTAL', 'Germinal');
		case 8:  return i18n::translate_c('INSTRUMENTAL', 'Floréal');
		case 9:  return i18n::translate_c('INSTRUMENTAL', 'Prairial');
		case 10: return i18n::translate_c('INSTRUMENTAL', 'Messidor');
		case 11: return i18n::translate_c('INSTRUMENTAL', 'Thermidor');
		case 12: return i18n::translate_c('INSTRUMENTAL', 'Fructidor');
		case 13: return i18n::translate_c('INSTRUMENTAL', 'jours complémentaires');
		}
	}
	static function NUM_TO_SHORT_MONTH($n, $leap_year) {
		// TODO: Do these have short names?
		return $this->NUM_TO_MONTH_NOMINATIVE($n);
	}
	static function NUM_TO_GEDCOM_MONTH($n, $leap_year) {
		switch ($n) {
		case 1:  return 'VEND';
		case 2:  return 'BRUM';
		case 3:  return 'FRIM';
		case 4:  return 'NIVO';
		case 5:  return 'PLUV';
		case 6:  return 'VENT';
		case 7:  return 'GERM';
		case 8:  return 'FLOR';
		case 9:  return 'PRAI';
		case 10: return 'MESS';
		case 11: return 'THER';
		case 12: return 'FRUC';
		case 13: return 'COMP';
		}
	}
	static function NUM_MONTHS() {
		return 13;
	}
	static function LONG_DAYS_OF_WEEK($n) {
		switch ($n) {
		case 0: return i18n::translate('Primidi');
		case 1: return i18n::translate('Duodi');
		case 2: return i18n::translate('Tridi');
		case 3: return i18n::translate('Quartidi');
		case 4: return i18n::translate('Quintidi');
		case 5: return i18n::translate('Sextidi');
		case 6: return i18n::translate('Septidi');
		case 7: return i18n::translate('Octidi');
		case 8: return i18n::translate('Nonidi');
		case 9: return i18n::translate('Decidi');
		}
	}
	static function SHORT_DAYS_OF_WEEK($n) {
		// TODO: Do these have short names?
		return $this->LONG_DAYS_OF_WEEK($n);
	}
	static function NUM_DAYS_OF_WEEK() {
		return 10; // A "metric" week of 10 unimaginatively named days.
	}
	static function CAL_START_JD() {
		return 2375840; // 22 SEP 1792 = 01 VEND 0001
	}
	static function CAL_END_JD() {
		return 2380687; // 31 DEC 1805 = 10 NIVO 0014
	}

	// Leap years were based on astronomical observations.  Only years 3, 7 and 11
	// were ever observed.  Moves to a gregorian-like (fixed) system were proposed
	// but never implemented.  These functions are valid over the range years 1-14.
	function IsLeapYear() {
		return $this->y%4==3;
	}

	static function YMDtoJD($y, $m, $d) {
		return 2375444+$d+$m*30+$y*365+floor($y/4);
	}

	static function JDtoYMD($j) {
		$y=floor(($j-2375109)*4/1461)-1;
		$m=floor(($j-2375475-$y*365-floor($y/4))/30)+1;
		$d=$j-2375444-$m*30-$y*365-floor($y/4);
		return array($y, $m, $d);
	}

	// Years were written using roman numerals
	function FormatLongYear() {
		return $this->NumToRoman($this->y);
	}
} // class FrenchRDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the Hijri calendar.  Note that these are "theoretical" dates.
// "True" dates are based on local lunar observations, and can be a +/- one day.
////////////////////////////////////////////////////////////////////////////////
class HijriDate extends CalendarDate {
	static function CALENDAR_ESCAPE() {
		return '@#DHIJRI@';
	}
	static function MONTH_TO_NUM($m) {
		static $months=array(''=>0, 'muhar'=>1, 'safar'=>2, 'rabia'=>3, 'rabit'=>4, 'jumaa'=>5, 'jumat'=>6, 'rajab'=>7, 'shaab'=>8, 'ramad'=>9, 'shaww'=>10, 'dhuaq'=>11, 'dhuah'=>12);
		if (isset($months[$m])) {
			return $months[$m];
		} else {
			return null;
		}
	}
	static function NUM_TO_MONTH_NOMINATIVE($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('NOMINATIVE', 'Muharram');
		case 2:  return i18n::translate_c('NOMINATIVE', 'Safar');
		case 3:  return i18n::translate_c('NOMINATIVE', 'Rabi\' al-awwal');
		case 4:  return i18n::translate_c('NOMINATIVE', 'Rabi\' al-thani');
		case 5:  return i18n::translate_c('NOMINATIVE', 'Jumada al-awwal');
		case 6:  return i18n::translate_c('NOMINATIVE', 'Jumada al-thani');
		case 7:  return i18n::translate_c('NOMINATIVE', 'Rajab');
		case 8:  return i18n::translate_c('NOMINATIVE', 'Sha\'aban');
		case 9:  return i18n::translate_c('NOMINATIVE', 'Ramadan');
		case 10: return i18n::translate_c('NOMINATIVE', 'Shawwal');
		case 11: return i18n::translate_c('NOMINATIVE', 'Dhu al-Qi\'dah');
		case 12: return i18n::translate_c('NOMINATIVE', 'Dhu al-Hijjah');
		default: return '';
		}
	}
	static function NUM_TO_MONTH_GENITIVE($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('GENITIVE', 'Muharram');
		case 2:  return i18n::translate_c('GENITIVE', 'Safar');
		case 3:  return i18n::translate_c('GENITIVE', 'Rabi\' al-awwal');
		case 4:  return i18n::translate_c('GENITIVE', 'Rabi\' al-thani');
		case 5:  return i18n::translate_c('GENITIVE', 'Jumada al-awwal');
		case 6:  return i18n::translate_c('GENITIVE', 'Jumada al-thani');
		case 7:  return i18n::translate_c('GENITIVE', 'Rajab');
		case 8:  return i18n::translate_c('GENITIVE', 'Sha\'aban');
		case 9:  return i18n::translate_c('GENITIVE', 'Ramadan');
		case 10: return i18n::translate_c('GENITIVE', 'Shawwal');
		case 11: return i18n::translate_c('GENITIVE', 'Dhu al-Qi\'dah');
		case 12: return i18n::translate_c('GENITIVE', 'Dhu al-Hijjah');
		default: return '';
		}
	}
	static function NUM_TO_MONTH_LOCATIVE($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('LOCATIVE', 'Muharram');
		case 2:  return i18n::translate_c('LOCATIVE', 'Safar');
		case 3:  return i18n::translate_c('LOCATIVE', 'Rabi\' al-awwal');
		case 4:  return i18n::translate_c('LOCATIVE', 'Rabi\' al-thani');
		case 5:  return i18n::translate_c('LOCATIVE', 'Jumada al-awwal');
		case 6:  return i18n::translate_c('LOCATIVE', 'Jumada al-thani');
		case 7:  return i18n::translate_c('LOCATIVE', 'Rajab');
		case 8:  return i18n::translate_c('LOCATIVE', 'Sha\'aban');
		case 9:  return i18n::translate_c('LOCATIVE', 'Ramadan');
		case 10: return i18n::translate_c('LOCATIVE', 'Shawwal');
		case 11: return i18n::translate_c('LOCATIVE', 'Dhu al-Qi\'dah');
		case 12: return i18n::translate_c('LOCATIVE', 'Dhu al-Hijjah');
		default: return '';
		}
	}
	static function NUM_TO_MONTH_INSTRUMENTAL($n, $leap_year) {
		switch ($n) {
		case 1:  return i18n::translate_c('INSTRUMENTAL', 'Muharram');
		case 2:  return i18n::translate_c('INSTRUMENTAL', 'Safar');
		case 3:  return i18n::translate_c('INSTRUMENTAL', 'Rabi\' al-awwal');
		case 4:  return i18n::translate_c('INSTRUMENTAL', 'Rabi\' al-thani');
		case 5:  return i18n::translate_c('INSTRUMENTAL', 'Jumada al-awwal');
		case 6:  return i18n::translate_c('INSTRUMENTAL', 'Jumada al-thani');
		case 7:  return i18n::translate_c('INSTRUMENTAL', 'Rajab');
		case 8:  return i18n::translate_c('INSTRUMENTAL', 'Sha\'aban');
		case 9:  return i18n::translate_c('INSTRUMENTAL', 'Ramadan');
		case 10: return i18n::translate_c('INSTRUMENTAL', 'Shawwal');
		case 11: return i18n::translate_c('INSTRUMENTAL', 'Dhu al-Qi\'dah');
		case 12: return i18n::translate_c('INSTRUMENTAL', 'Dhu al-Hijjah');
		default: return '';
		}
	}
	static function NUM_TO_SHORT_MONTH($n, $leap_year) {
		// TODO: Do these have short names?
		return $this->NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}
	static function NUM_TO_GEDCOM_MONTH($n, $leap_year) {
		switch ($n) {
		case 1:  return 'MUHAR';
		case 2:  return 'SAFAR';
		case 3:  return 'RABIA';
		case 4:  return 'RABIT';
		case 5:  return 'JUMAA';
		case 6:  return 'JUMAT';
		case 7:  return 'RAJAB';
		case 8:  return 'SHAAB';
		case 9:  return 'RAMAD';
		case 10: return 'SHAWW';
		case 11: return 'DHUAQ';
		case 12: return 'DHUAH';
		default: return '';
		}
	}
	static function CAL_START_JD() {
		return 1948440; // @#DHIJRI@ 1 MUHAR 0001 = @#JULIAN@ 16 JUL 0622
	}

	function IsLeapYear() {
		return ((11*$this->y+14)%30)<11;
	}

	static function YMDtoJD($y, $m, $d) {
		return $d+29*($m-1)+floor((6*$m-1)/11)+$y*354+floor((3+11*$y)/30)+1948085;
	}

	static function JDtoYMD($j) {
		$y=floor((30*($j-1948440)+10646)/10631);
		$m=floor((11*($j-$y*354-floor((3+11*$y)/30)-1948086)+330)/325);
		$d=$j-29*($m-1)-floor((6*$m-1)/11)-$y*354-floor((3+11*$y)/30)-1948085;
		return array($y, $m, $d);
	}
} // class HijriDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the Arabic calendar.
// NOTE - this is the same as the Hijri Calendar, but displays dates in arabic
// rather than the local language.
////////////////////////////////////////////////////////////////////////////////
class ArabicDate extends HijriDate {
	static $ARABIC_DAYS=array("الأثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعه", "السبت", "الأحد");

	static function NUM_TO_MONTH_NOMINATIVE($n, $leap_year) {
		// Do not translate these - they are supposed to be arabic, whatever language is shown.
		switch ($n) {
		case 1:  return 'محرّم';
		case 2:  return 'صفر';
		case 3:  return 'ربيع الأول';
		case 4:  return 'ربيع الثانى';
		case 5:  return 'جمادى الأول';
		case 6:  return 'جمادى الثاني';
		case 7:  return 'رجب';
		case 8:  return 'شعبان';
		case 9:  return 'رمضان';
		case 10: return 'شوّال';
		case 11: return 'ذو القعدة';
		case 12: return 'ذو الحجة';
		default: return '';
		}
	}
	static function NUM_TO_MONTH_GENITIVE($n, $leap_year) {
		// Arabic does not have genitive forms
		return $this->NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}
	static function NUM_TO_MONTH_LOCATIVE($n, $leap_year) {
		// Arabic does not have locative forms
		return $this->NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}
	
	static function NUM_TO_MONTH_INSTRUMENTAL($n, $leap_year) {
		// Arabic does not have instrumental forms
		return $this->NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}
	
	static function NUM_TO_SHORT_MONTH($n, $leap_year) {
		// TODO: Do these have short names?
		return $this->NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}

	function FormatLongWeekday() {
		return $this->$ARABIC_DAYS[$this->minJD % $this->NUM_DAYS_OF_WEEK()];
	}

	function FormatShortWeekday() {
		return $this->$ARABIC_DAYS[$this->minJD % $this->NUM_DAYS_OF_WEEK()];
	}
} // class ArabicDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the Roman calendar
// TODO The 5.5.1 gedcom spec mentions this calendar, but gives no details of
// how it is to be represented....  This class is just a place holder so that
// webtrees won't compain if it receives one.
////////////////////////////////////////////////////////////////////////////////
class RomanDate extends CalendarDate {
	static function CALENDAR_ESCAPE() {
		return '@#DROMAN@';
	}

	function FormatGedcomYear() {
		return sprintf('%04dAUC',$this->y);
	}

	function FormatLongYear() {
		return $this->y.'AUC';
	}
} // class RomanDate

////////////////////////////////////////////////////////////////////////////////
//
// GedcomDate represents the date or date range from a gedcom DATE record.
//
////////////////////////////////////////////////////////////////////////////////
class GedcomDate {
	var $qual1=null; // Optional qualifier, such as BEF, FROM, ABT
	var $date1=null; // The first (or only) date
	var $qual2=null; // Optional qualifier, such as TO, AND
	var $date2=null; // Optional second date
	var $text =null; // Optional text, as included with an INTerpreted date

	function __construct($date) {
		// Extract any explanatory text
		if (preg_match('/^(.*)( ?\(.*)$/', $date, $match)) {
			$date=$match[1];
			$this->text=$match[2];
		}
		// Ignore punctuation and normalise whitespace
		$date=preg_replace(
			array('/(\d+|@#[^@]+@)/', '/[\s;:.,-]+/', '/^ /', '/ $/'),
			array(' $1 ', ' ', '', ''),
			strtolower($date)
		);
		if (preg_match('/^(from|bet) (.+) (and|to) (.+)/', $date, $match)) {
			$this->qual1=$match[1];
			$this->date1=$this->ParseDate($match[2]);
			$this->qual2=$match[3];
			$this->date2=$this->ParseDate($match[4]);
		} elseif (preg_match('/^(from|bet|to|and|bef|aft|cal|est|int|abt) (.+)/', $date, $match)) {
			$this->qual1=$match[1];
			$this->date1=$this->ParseDate($match[2]);
		} else {
			$this->date1=$this->ParseDate($date);
		}
	}

	// Need to "deep-clone" nested objects
	function __clone() {
		$this->date1=clone $this->date1;
		if (is_object($this->date2)) {
			$this->date2=clone $this->date2;
		}
	}

	// Convert an individual gedcom date string into a CalendarDate object
	static function ParseDate($date) {
		// Calendar escape specified? - use it
		if (preg_match('/^(@#[^@]+@) ?(.*)/', $date, $match)) {
			$cal=$match[1];
			$date=$match[2];
		} else {
			$cal='';
		}
		// A date with a month: DM, M, MY or DMY
		if (preg_match('/^(\d?\d?) ?(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|tsh|csh|ksl|tvt|shv|adr|ads|nsn|iyr|svn|tmz|aav|ell|vend|brum|frim|nivo|pluv|vent|germ|flor|prai|mess|ther|fruc|comp|muhar|safar|rabi[at]|juma[at]|rajab|shaab|ramad|shaww|dhuaq|dhuah) ?((?:\d+(?: b ?c)?|\d\d\d\d \/ \d{1,4})?)$/', $date, $match)) {
			$d=$match[1];
			$m=$match[2];
			$y=$match[3];
		} else
			// A date with just a year
			if (preg_match('/^(\d+(?: b ?c)?|\d\d\d\d \/ \d{1,4})$/', $date, $match)) {
				$d='';
				$m='';
				$y=$match[1];
			} else {
				// An invalid date - do the best we can.
				$d='';
				$m='';
				$y='';
				// Look for a 3/4 digit year anywhere in the date
				if (preg_match('/\b(\d{3,4})\b/', $date, $match)) {
					$y=$match[1];
				}
				// Look for a month anywhere in the date
				if (preg_match('/(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|tsh|csh|ksl|tvt|shv|adr|ads|nsn|iyr|svn|tmz|aav|ell|vend|brum|frim|nivo|pluv|vent|germ|flor|prai|mess|ther|fruc|comp|muhar|safar|rabi[at]|juma[at]|rajab|shaab|ramad|shaww|dhuaq|dhuah)/', $date, $match)) {
					$m=$match[1];
					// Look for a day number anywhere in the date
					if (preg_match('/\b(\d\d?)\b/', $date, $match))
						$d=$match[1];
				}
			}
		// Unambiguous dates - override calendar escape
		if (preg_match('/^(tsh|csh|ksl|tvt|shv|adr|ads|nsn|iyr|svn|tmz|aav|ell)$/', $m)) {
			$cal='@#dhebrew@';
		} else {
			if (preg_match('/^(vend|brum|frim|nivo|pluv|vent|germ|flor|prai|mess|ther|fruc|comp)$/', $m)) {
				$cal='@#dfrench r@';
			} else {
				if (preg_match('/^(muhar|safar|rabi[at]|juma[at]|rajab|shaab|ramad|shaww|dhuaq|dhuah)$/', $m)) {
					$cal='@#dhijri@'; // This is a PGV extension
				} elseif (preg_match('/^\d+( b ?c)|\d\d\d\d \/ \d{1,4}$/', $y)) {
					$cal='@#djulian@';
				}
			}
		}
		// Ambiguous dates - don't override calendar escape
		if ($cal=='') {
			if (preg_match('/^(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)$/', $m)) {
				$cal='@#dgregorian@';
			} else {
				if (preg_match('/^[345]\d\d\d$/', $y)) { // Year 3000-5999
					$cal='@#dhebrew@';
				} else {
					$cal='@#dgregorian@';
				}
			}
		}
		// Now construct an object of the correct type
		switch ($cal) {
		case '@#dgregorian@':
			return new GregorianDate(array($y, $m, $d));
		case '@#djulian@':
	 		return new JulianDate(array($y, $m, $d));
		case '@#dhebrew@':
			if (WT_LOCALE=='he')
	 			return new HebrewDate(array($y, $m, $d));
			else
	 			return new JewishDate(array($y, $m, $d));
		case '@#dhijri@':
			if (WT_LOCALE=='ar')
				return new ArabicDate(array($y, $m, $d));
			else
				return new HijriDate(array($y, $m, $d));
		case '@#dfrench r@':
		 	return new FrenchRDate(array($y, $m, $d));
		case '@#droman@':
			return new RomanDate(array($y, $m, $d));
		}
	}

	// Convert a date to the prefered format and calendar(s) display.
	// Optionally make the date a URL to the calendar.
	function Display($url=false, $date_fmt=null, $cal_fmts=null) {
		global $TEXT_DIRECTION, $DATE_FORMAT, $CALENDAR_FORMAT;

		// Convert dates to given calendars and given formats
		if (!$date_fmt) {
			$date_fmt=$DATE_FORMAT;
		}
		if (is_null($cal_fmts))
			$cal_fmts=explode('_and_', $CALENDAR_FORMAT);

		// Allow special processing for different languages
		$func="date_localisation_".WT_LOCALE;
		if (!function_exists($func))
			$func="DefaultDateLocalisation";

		// Two dates with text before, between and after
		$q1=$this->qual1;
		$d1=$this->date1->Format($date_fmt, $this->qual1);
		$q2=$this->qual2;
		if (is_null($this->date2))
			$d2='';
		else
			$d2=$this->date2->Format($date_fmt, $this->qual2);
		$q3='';
		$func($q1, $d1, $q2, $d2, $q3);
		// Convert to other calendars, if requested
		$conv1='';
		$conv2='';
		foreach ($cal_fmts as $cal_fmt)
			if ($cal_fmt!='none') {
				$d1conv=$this->date1->convert_to_cal($cal_fmt);
				if ($d1conv->InValidRange()) {
					$d1tmp=$d1conv->Format($date_fmt, $this->qual1);
				} else {
					$d1tmp='';
				}
				$q1tmp=$this->qual1;
				if (is_null($this->date2)) {
					$d2conv=null;
					$d2tmp='';
				} else {
					$d2conv=$this->date2->convert_to_cal($cal_fmt);
					if ($d2conv->InValidRange()) {
						$d2tmp=$d2conv->Format($date_fmt, $this->qual2);
					} else {
						$d2tmp='';
					}
				}
				$q2tmp=$this->qual2;
				$q3tmp='';
				// Localise the date
				$func($q1tmp, $d1tmp, $q2tmp, $d2tmp, $q3tmp);
				// If the date is different to the unconverted date, add it to the date string.
				if ($d1!=$d1tmp && $d1tmp!='') {
					if ($url) {
						if ($CALENDAR_FORMAT!="none") {
							$conv1.=' <span dir="'.$TEXT_DIRECTION.'">(<a href="'.$d1conv->CalendarURL($date_fmt).'">'.$d1tmp.'</a>)</span>';
						} else {
							$conv1.=' <span dir="'.$TEXT_DIRECTION.'"><br /><a href="'.$d1conv->CalendarURL($date_fmt).'">'.$d1tmp.'</a></span>';
						}
					} else {
						$conv1.=' <span dir="'.$TEXT_DIRECTION.'">('.$d1tmp.')</span>';
					}
				}
				if (!is_null($this->date2) && $d2!=$d2tmp && $d1tmp!='') {
					if ($url) {
						$conv2.=' <span dir="'.$TEXT_DIRECTION.'">(<a href="'.$d2conv->CalendarURL($date_fmt).'">'.$d2tmp.'</a>)</span>';
					} else {
						$conv2.=' <span dir="'.$TEXT_DIRECTION.'">('.$d2tmp.')</span>';
					}
				}
			}

		// Add URLs, if requested
		if ($url) {
			$d1='<a href="'.$this->date1->CalendarURL($date_fmt).'">'.$d1.'</a>';
			if (!is_null($this->date2))
				$d2='<a href="'.$this->date2->CalendarURL($date_fmt).'">'.$d2.'</a>';
		}

		// Localise the date
		// TODO, use separate translations for nominative, genitive, etc.
		switch ($q1.$q2) {
		case '':       $tmp=$d1.$conv1; break;
		case 'abt':    /* I18N: Gedcom ABT dates     */ $tmp=i18n::translate('about %s',            $d1.$conv1); break;
		case 'cal':    /* I18N: Gedcom CAL dates     */ $tmp=i18n::translate('calculated %s',       $d1.$conv1); break;
		case 'est':    /* I18N: Gedcom EST dates     */ $tmp=i18n::translate('estimated %s',        $d1.$conv1); break;
		case 'int':    /* I18N: Gedcom INT dates     */ $tmp=i18n::translate('interpreted %s (%s)', $d1.$conv1, $this->text); break;
		case 'bef':    /* I18N: Gedcom BEF dates     */ $tmp=i18n::translate('before %s',           $d1.$conv1); break;
		case 'aft':    /* I18N: Gedcom AFT dates     */ $tmp=i18n::translate('after %s',            $d1.$conv1); break;
		case 'from':   /* I18N: Gedcom FROM dates    */ $tmp=i18n::translate('from %s',             $d1.$conv1); break;
		case 'to':     /* I18N: Gedcom TO dates      */ $tmp=i18n::translate('to %s',               $d1.$conv1); break;
		case 'betand': /* I18N: Gedcom BET-AND dates */ $tmp=i18n::translate('between %s and %s',   $d1.$conv1, $d2.$conv2); break;
		case 'fromto': /* I18N: Gedcom FROM-TO dates */ $tmp=i18n::translate('from %s to %s',       $d1.$conv1, $d2.$conv2); break;
		default: $tmp=i18n::translate('Invalid date'); break; // e.g. BET without AND
		}

		// Return at least one printable character, for better formatting in tables.
		if (strip_tags($tmp)=='')
			return '&nbsp;';
		else
			return "<span class=\"date\">{$tmp}</span>";
	}

	// Get the earliest/latest date/JD from this date
	function MinDate() {
		return $this->date1;
	}
	function MaxDate() {
		if (is_null($this->date2))
			return $this->date1;
		else
			return $this->date2;
	}
	function MinJD() {
		$tmp=$this->MinDate();
		return $tmp->minJD;
	}
	function MaxJD() {
		$tmp=$this->MaxDate();
		return $tmp->maxJD;
	}
	function JD() {
		return floor(($this->MinJD()+$this->MaxJD())/2);
	}

	// Offset this date by N years, and round to the whole year
	function AddYears($n, $qual='') {
		$tmp=clone $this;
		$tmp->date1->y+=$n;
		$tmp->date1->m=0;
		$tmp->date1->d=0;
		$tmp->date1->SetJDfromYMD();
		$tmp->qual1=$qual;
		$tmp->qual2='';
		$tmp->date2=null;
		return $tmp;
	}

	// Calculate the number of full years between two events.
	// Return the result as either a number of years (for indi lists, etc.)
	static function GetAgeYears($d1, $d2=null, $warn_on_negative=true) {
		if (!is_object($d1)) return;
		if (!is_object($d2))
			return $d1->date1->GetAge(false, client_jd(), $warn_on_negative );
		else
			return $d1->date1->GetAge(false, $d2->MinJD(), $warn_on_negative);
	}

	// Calculate the years/months/days between two events
	// Return a gedcom style age string: "1y 2m 3d" (for fact details)
	static function GetAgeGedcom($d1, $d2=null, $warn_on_negative=true) {
		if (is_null($d2)) {
			return $d1->date1->GetAge(true, client_jd(), $warn_on_negative);
		} else {
			// If dates overlap, then can't calculate age.
			if (GedcomDate::Compare($d1, $d2)) {
				return $d1->date1->GetAge(true, $d2->MinJD(), $warn_on_negative);
			} if (GedcomDate::Compare($d1, $d2)==0 && $d1->date1->minJD==$d2->MinJD()) {
				return '0d';
			} else {
				return '';
			}
		}
	}

	// Static function to compare two dates.
	// return <0 if $a<$b
	// return >0 if $b>$a
	// return  0 if dates same/overlap
	// BEF/AFT sort as the day before/after.
	static function Compare($a, $b) {
		// Get min/max JD for each date.
		switch ($a->qual1) {
		case 'bef':
			$amin=$a->MinJD()-1;
			$amax=$amin;
			break;
		case 'aft':
			$amax=$a->MaxJD()+1;
			$amin=$amax;
			break;
		default:
			$amin=$a->MinJD();
			$amax=$a->MaxJD();
			break;
		}
		switch ($b->qual1) {
		case 'bef':
			$bmin=$b->MinJD()-1;
			$bmax=$bmin;
			break;
		case 'aft':
			$bmax=$b->MaxJD()+1;
			$bmin=$bmax;
			break;
		default:
			$bmin=$b->MinJD();
			$bmax=$b->MaxJD();
			break;
		}
		if ($amax<$bmin) {
			return -1;
		} else {
			if ($amin>$bmax && $bmax>0) {
				return 1;
			} else {
				if ($amin<$bmin && $amax<=$bmax) {
					return -1;
				} elseif ($amin>$bmin && $amax>=$bmax && $bmax>0) {
					return 1;
				} else {
					return 0;
				}
			}
		}
	}

	// Check whether a gedcom date contains usable calendar date(s).
	function isOK() {
		return $this->MinJD() && $this->MaxJD();
	}

	// Calculate the gregorian year for a date.  This should NOT be used internally
	// within PGV - we should keep the code "calendar neutral" to allow support for
	// jewish/arabic users.  This is only for interfacing with external entities,
	// such as the ancestry.com search interface or the dated fact icons.
	function gregorianYear() {
		if ($this->isOK()) {
			list($y)=GregorianDate::JDtoYMD($this->JD());
			return $y;
		} else {
			return 0;
		}
	}
}

// Localise a date.  This is a default function, and may be overridden in extras.xx.php
function DefaultDateLocalisation(&$q1, &$d1, &$q2, &$d2, &$q3) {
}
?>
