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
// Copyright (C) 2013 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Date_Calendar {
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
		if (get_class($this)==get_class($date)) {
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
			$jd=(int)(($date->maxJD+$date->minJD)/2);
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
		static $months=array(''=>0, 'JAN'=>1, 'FEB'=>2, 'MAR'=>3, 'APR'=>4, 'MAY'=>5, 'JUN'=>6, 'JUL'=>7, 'AUG'=>8, 'SEP'=>9, 'OCT'=>10, 'NOV'=>11, 'DEC'=>12);
		if (isset($months[$m])) {
			return $months[$m];
		} else {
			return null;
		}
	}
	// We put these in the base class, to save duplicating it in the Julian and Gregorian calendars
	static function NUM_TO_MONTH_NOMINATIVE($n, $leap_year) {
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
	static function NUM_TO_MONTH_GENITIVE($n, $leap_year) {
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
	static function NUM_TO_MONTH_LOCATIVE($n, $leap_year) {
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
	static function NUM_TO_MONTH_INSTRUMENTAL($n, $leap_year) {
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
	static function NUM_TO_SHORT_MONTH($n, $leap_year) {
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
		case 0: return WT_I18N::translate('Monday');
		case 1: return WT_I18N::translate('Tuesday');
		case 2: return WT_I18N::translate('Wednesday');
		case 3: return WT_I18N::translate('Thursday');
		case 4: return WT_I18N::translate('Friday');
		case 5: return WT_I18N::translate('Saturday');
		case 6: return WT_I18N::translate('Sunday');
		}
	}
	static function SHORT_DAYS_OF_WEEK($n) {
		switch ($n) {
		case 0: return WT_I18N::translate('Mon');
		case 1: return WT_I18N::translate('Tue');
		case 2: return WT_I18N::translate('Wed');
		case 3: return WT_I18N::translate('Thu');
		case 4: return WT_I18N::translate('Fri');
		case 5: return WT_I18N::translate('Sat');
		case 6: return WT_I18N::translate('Sun');
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
	// TODO: WT_Date_Jewish needs to redefine this to cope with leap months
	function GetAge($full, $jd, $warn_on_negative=true) {
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
		// Don't show exact details for inexact dates
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
		// If we've trimmed the format, also trim the punctuation
		if (!$this->d || !$this->m || !$this->y) {
			$format=trim($format, ',. ;/-');
		}
		if ($this->d && preg_match('/%[djlDNSwz]/', $format)) {
			// If we have a day-number *and* we are being asked to display it, then genitive
			$case='GENITIVE';
		} else {
			switch ($qualifier) {
			case '':
			case 'INT':
			case 'EST':
			case 'CAL': $case='NOMINATIVE'; break;
			case 'TO':
			case 'ABT':
			case 'FROM': $case='GENITIVE'; break;
			case 'AFT':  $case='LOCATIVE'; break;
			case 'BEF':
			case 'BET':
			case 'AND': $case='INSTRUMENTAL'; break;
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
		if ($this->d>9) {
			return WT_I18N::digits($this->d);
		} else {
			return WT_I18N::digits('0'.$this->d);
		}
	}

	function FormatDay() {
		return WT_I18N::digits($this->d);
	}

	function FormatLongWeekday() {
		return $this->LONG_DAYS_OF_WEEK($this->minJD % $this->NUM_DAYS_OF_WEEK());
	}

	function FormatShortWeekday() {
		return $this->SHORT_DAYS_OF_WEEK($this->minJD % $this->NUM_DAYS_OF_WEEK());
	}

	function FormatISOWeekday() {
		return WT_I18N::digits($this->minJD % 7 + 1);
	}

	function FormatOrdinalSuffix() {
		$func="ordinal_suffix_".WT_LOCALE;
		if (function_exists($func))
			return $func($this->d);
		else
			return '';
	}

	function FormatNumericWeekday() {
		return WT_I18N::digits(($this->minJD + 1) % $this->NUM_DAYS_OF_WEEK());
	}

	function FormatDayOfYear() {
		return WT_I18N::digits($this->minJD - $this->YMDtoJD($this->y, 1, 1));
	}

	function FormatMonth() {
		return WT_I18N::digits($this->m);
	}

	function FormatMonthZeros() {
		if ($this->m>9) {
			return WT_I18N::digits($this->m);
		} else {
			return WT_I18N::digits('0'.$this->m);
		}
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
		return WT_I18N::digits($this->y);
	}

	function FormatGedcomDay() {
		if ($this->d==0) {
			return '';
		} else {
			return sprintf('%02d', $this->d);
		}
	}

	function FormatGedcomMonth() {
		return $this->NUM_TO_GEDCOM_MONTH($this->m, $this->IsLeapYear());
	}

	function FormatGedcomYear() {
		if ($this->y==0) {
			return '';
		} else {
			return sprintf('%04d', $this->y);
		}
	}

	function FormatLongYear() {
		return WT_I18N::digits($this->y);
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
		return $this->JDtoYMD(WT_Date_Gregorian::YMDtoJD(date('Y'), date('n'), date('j')));
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

	// Create a URL that links this date to the WT calendar
	function CalendarURL($date_fmt="") {
		global $DATE_FORMAT;
		if (empty($date_fmt)) {
			$date_fmt=$DATE_FORMAT;
		}
		$URL='calendar.php?cal='.rawurlencode($this->CALENDAR_ESCAPE());
		$action="year";
		if (strpos($date_fmt, "Y")!==false
		||  strpos($date_fmt, "y")!==false) {
			$URL.='&amp;year='.$this->FormatGedcomYear();
		}
		if (strpos($date_fmt, "F")!==false
		||  strpos($date_fmt, "M")!==false
		||  strpos($date_fmt, "m")!==false
		||  strpos($date_fmt, "n")!==false) {
			$URL.='&amp;month='.$this->FormatGedcomMonth();
			if ($this->m>0)
				$action="calendar";
		}
		if (strpos($date_fmt, "d")!==false
		||  strpos($date_fmt, "D")!==false
		||  strpos($date_fmt, "j")!==false) {
			$URL.='&amp;day='.$this->FormatGedcomDay();
			if ($this->d>0)
				$action="today";
		}
		return $URL.'&amp;action='.$action;
	}
}
