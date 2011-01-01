<?php
// Classes for Gedcom Date/Calendar functionality.
//
// Definitions for the Hebrew calendar.
// NOTE - this is the same as the Jewish Calendar, but displays dates in hebrew
// rather than the local language.
//
// NOTE: Since different calendars start their days at different times, (civil
// midnight, solar midnight, sunset, sunrise, etc.), we convert on the basis of
// midday.
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
// @author Greg Roach
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Date_Hebrew extends WT_Date_Jewish {
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
		case 9:  return 'אייר';
		case 10: return 'סיוון';
		case 11: return 'תמוז';
		case 12: return 'אב';
		case 13: return 'אלול';
		default: return '';
		}
	}

	static function NUM_TO_MONTH_GENITIVE($n, $leap_year) {
		// Hebrew does not have genitive forms
		return self::NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}

	static function NUM_TO_MONTH_LOCATIVE($n, $leap_year) {
		// Hebrew does not have locative forms
		return self::NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}

	static function NUM_TO_MONTH_INSTRUMENTAL($n, $leap_year) {
		// Hebrew does not have instrumental forms
		return self::NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}

	static function NUM_TO_SHORT_MONTH($n, $leap_year) {
		// TODO: Do these have short names?
		return self::NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
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
		if ($num % 1000 == 0) { // in year is 5000, 4000 etc
			$sb .= $jOnes[$thousands];
			$sb .= self::GERSH;
			$sb .= " ";
			$sb .= self::ALAFIM; //add # of thousands plus word thousand (overide alafim boolean)
		} else if ($DISPLAY_JEWISH_THOUSANDS) { // if alafim boolean display thousands
			$sb .= $jOnes[$thousands];
			$sb .= self::GERSH; //append thousands quote
			$sb .= " ";
		}
		$num = $num % 1000; //remove 1000s
		$hundreds = $num / 100; // # of hundreds
		$sb .= $jHundreds[$hundreds]; //add hundreds to String
		$num = $num % 100; //remove 100s
		if ($num == 15) { //special case 15
			$sb .= $tavTaz[0];
		} else if ($num == 16) { //special case 16
			$sb .= $tavTaz[1];
		} else {
			$tens = $num / 10;
			if ($num % 10 == 0) {                                    // if evenly divisable by 10
				if ($singleDigitYear == false) {
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
}
