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

use Fisharebest\ExtCalendar\JewishCalendar;

/**
 * Class WT_Date_Jewish - Definitions for the Jewish calendar
 */
class WT_Date_Jewish extends WT_Date_Calendar {
	const CALENDAR_ESCAPE = '@#DHEBREW@';
	const MONTHS_IN_YEAR = 13;
	const CAL_START_JD = 347998; // 01 TSH 0001 = @#JULIAN@ 7 OCT 3761B.C.
	const GERSHAYIM = '״';
	const GERSH = '׳';
	const ALAFIM = 'אלפים';

	static $MONTH_ABBREV = array('' => 0, 'TSH' => 1, 'CSH' => 2, 'KSL' => 3, 'TVT' => 4, 'SHV' => 5, 'ADR' => 6, 'ADS' => 7, 'NSN' => 8, 'IYR' => 9, 'SVN' => 10, 'TMZ' => 11, 'AAV' => 12, 'ELL' => 13);

	/**
	 * {@inheritdoc}
	 */
	public function __construct($date) {
		$this->calendar = new JewishCalendar;
		parent::__construct($date);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function calendarName() {
		return /* I18N: The Hebrew/Jewish calendar */
			WT_I18N::translate('Jewish');
	}

	/**
	 * {@inheritdoc}
	 */
	function formatDayZeros() {
		if (WT_LOCALE == 'he') {
			return $this->numberToHebrewNumerals($this->d);
		} else {
			return $this->d;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	function formatDay() {
		if (WT_LOCALE == 'he') {
			return $this->numberToHebrewNumerals($this->d);
		} else {
			return $this->d;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	function formatShortYear() {
		if (WT_LOCALE == 'he') {
			return $this->numberToHebrewNumerals($this->y % 1000);
		} else {
			return $this->y;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	function formatLongYear() {
		if (WT_LOCALE == 'he') {
			return $this->numberToHebrewNumerals($this->y);
		} else {
			return $this->y;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public static function monthNameNominativeCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('NOMINATIVE', 'Tishrei'),
				2  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('NOMINATIVE', 'Heshvan'),
				3  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('NOMINATIVE', 'Kislev'),
				4  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('NOMINATIVE', 'Tevet'),
				5  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('NOMINATIVE', 'Shevat'),
				6  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('NOMINATIVE', 'Adar I'),
				7  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('NOMINATIVE', 'Adar'),
				-7 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('NOMINATIVE', 'Adar II'),
				8  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('NOMINATIVE', 'Nissan'),
				9  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('NOMINATIVE', 'Iyar'),
				10 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('NOMINATIVE', 'Sivan'),
				11 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('NOMINATIVE', 'Tamuz'),
				12 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('NOMINATIVE', 'Av'),
				13 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('NOMINATIVE', 'Elul'),
			);
		}

		if ($month_number === 7 && $leap_year) {
			return $translated_month_names[-7];
		} else {
			return $translated_month_names[$month_number];
		}
	}

	/**
	 * {@inheritdoc}
	 */
	static function monthNameGenitiveCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('GENITIVE', 'Tishrei'),
				2  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('GENITIVE', 'Heshvan'),
				3  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('GENITIVE', 'Kislev'),
				4  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('GENITIVE', 'Tevet'),
				5  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('GENITIVE', 'Shevat'),
				6  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('GENITIVE', 'Adar I'),
				7  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('GENITIVE', 'Adar'),
				-7 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('GENITIVE', 'Adar II'),
				8  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('GENITIVE', 'Nissan'),
				9  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('GENITIVE', 'Iyar'),
				10 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('GENITIVE', 'Sivan'),
				11 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('GENITIVE', 'Tamuz'),
				12 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('GENITIVE', 'Av'),
				13 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('GENITIVE', 'Elul'),
			);
		}

		if ($month_number === 7 && $leap_year) {
			return $translated_month_names[-7];
		} else {
			return $translated_month_names[$month_number];
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected static function monthNameLocativeCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('LOCATIVE', 'Tishrei'),
				2  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('LOCATIVE', 'Heshvan'),
				3  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('LOCATIVE', 'Kislev'),
				4  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('LOCATIVE', 'Tevet'),
				5  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('LOCATIVE', 'Shevat'),
				6  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('LOCATIVE', 'Adar I'),
				7  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('LOCATIVE', 'Adar'),
				-7 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('LOCATIVE', 'Adar II'),
				8  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('LOCATIVE', 'Nissan'),
				9  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('LOCATIVE', 'Iyar'),
				10 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('LOCATIVE', 'Sivan'),
				11 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('LOCATIVE', 'Tamuz'),
				12 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('LOCATIVE', 'Av'),
				13 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('LOCATIVE', 'Elul'),
			);
		}

		if ($month_number === 7 && $leap_year) {
			return $translated_month_names[-7];
		} else {
			return $translated_month_names[$month_number];
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected static function monthNameInstrumentalCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Tishrei'),
				2  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Heshvan'),
				3  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Kislev'),
				4  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Tevet'),
				5  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Shevat'),
				6  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Adar I'),
				7  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Adar'),
				-7 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Adar II'),
				8  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Nissan'),
				9  => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Iyar'),
				10 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Sivan'),
				11 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Tamuz'),
				12 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Av'),
				13 => /* I18N: a month in the Jewish calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Elul'),
			);
		}

		if ($month_number === 7 && $leap_year) {
			return $translated_month_names[-7];
		} else {
			return $translated_month_names[$month_number];
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected static function monthNameAbbreviated($month_number, $leap_year) {
		return self::monthNameNominativeCase($month_number, $leap_year);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function nextMonth() {
		if ($this->m == 6 && !$this->isLeapYear()) {
			return array($this->y, 8);
		} else {
			return array($this->y + ($this->m == 13 ? 1 : 0), ($this->m % 13) + 1);
		}
	}

	/**
	 * Convert a decimal number to hebrew - like roman numerals, but with extra punctuation and special rules.
	 *
	 * @param integer $num
	 *
	 * @return string
	 */
	protected static function numberToHebrewNumerals($num) {
		$DISPLAY_JEWISH_THOUSANDS = false;

		static $jHundreds = array("", "ק", "ר", "ש", "ת", "תק", "תר", "תש", "תת", "תתק");
		static $jTens = array("", "י", "כ", "ל", "מ", "נ", "ס", "ע", "פ", "צ");
		static $jTenEnds = array("", "י", "ך", "ל", "ם", "ן", "ס", "ע", "ף", "ץ");
		static $tavTaz = array("ט״ו", "ט״ז");
		static $jOnes = array("", "א", "ב", "ג", "ד", "ה", "ו", "ז", "ח", "ט");

		$shortYear = $num % 1000; //discard thousands
		//next check for all possible single Hebrew digit years
		$singleDigitYear = ($shortYear < 11 || ($shortYear < 100 && $shortYear % 10 == 0) || ($shortYear <= 400 && $shortYear % 100 == 0));
		$thousands       = (int)($num / 1000); //get # thousands
		$sb              = "";
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
		$num      = $num % 1000; //remove 1000s
		$hundreds = (int)($num / 100); // # of hundreds
		$sb .= $jHundreds[$hundreds]; //add hundreds to String
		$num = $num % 100; //remove 100s
		if ($num == 15) { //special case 15
			$sb .= $tavTaz[0];
		} else if ($num == 16) { //special case 16
			$sb .= $tavTaz[1];
		} else {
			$tens = (int)($num / 10);
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
			$pos1 = strlen($sb) - 2;
			$sb   = substr($sb, 0, $pos1) . self::GERSHAYIM . substr($sb, $pos1);
			$sb   = str_replace(self::GERSHAYIM . self::GERSHAYIM, self::GERSHAYIM, $sb); //replace double gershayim with single instance
		}

		return $sb;
	}
}
