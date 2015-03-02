<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

use Fisharebest\ExtCalendar\JewishCalendar;

/**
 * Class JewishDate - Definitions for the Jewish calendar
 */
class JewishDate extends CalendarDate {
	const CALENDAR_ESCAPE = '@#DHEBREW@';
	const MONTHS_IN_YEAR = 13;
	const CAL_START_JD = 347998; // 01 TSH 0001 = @#JULIAN@ 7 OCT 3761B.C.
	const GERSHAYIM = '״';
	const GERSH = '׳';
	const ALAFIM = 'אלפים';

	/** {@inheritdoc} */
	public static $MONTH_ABBREV = array('' => 0, 'TSH' => 1, 'CSH' => 2, 'KSL' => 3, 'TVT' => 4, 'SHV' => 5, 'ADR' => 6, 'ADS' => 7, 'NSN' => 8, 'IYR' => 9, 'SVN' => 10, 'TMZ' => 11, 'AAV' => 12, 'ELL' => 13);

	/** {@inheritdoc} */
	public function __construct($date) {
		$this->calendar = new JewishCalendar;
		parent::__construct($date);
	}

	/** {@inheritdoc} */
	public static function calendarName() {
		return /* I18N: The Hebrew/Jewish calendar */
			I18N::translate('Jewish');
	}

	/** {@inheritdoc} */
	function formatDayZeros() {
		if (WT_LOCALE == 'he') {
			return $this->numberToHebrewNumerals($this->d);
		} else {
			return $this->d;
		}
	}

	/** {@inheritdoc} */
	function formatDay() {
		if (WT_LOCALE == 'he') {
			return $this->numberToHebrewNumerals($this->d);
		} else {
			return $this->d;
		}
	}

	/** {@inheritdoc} */
	function formatShortYear() {
		if (WT_LOCALE == 'he') {
			return $this->numberToHebrewNumerals($this->y % 1000);
		} else {
			return $this->y;
		}
	}

	/** {@inheritdoc} */
	function formatLongYear() {
		if (WT_LOCALE == 'he') {
			return $this->numberToHebrewNumerals($this->y);
		} else {
			return $this->y;
		}
	}

	/** {@inheritdoc} */
	public static function monthNameNominativeCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('NOMINATIVE', 'Tishrei'),
				2  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('NOMINATIVE', 'Heshvan'),
				3  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('NOMINATIVE', 'Kislev'),
				4  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('NOMINATIVE', 'Tevet'),
				5  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('NOMINATIVE', 'Shevat'),
				6  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('NOMINATIVE', 'Adar I'),
				7  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('NOMINATIVE', 'Adar'),
				-7 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('NOMINATIVE', 'Adar II'),
				8  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('NOMINATIVE', 'Nissan'),
				9  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('NOMINATIVE', 'Iyar'),
				10 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('NOMINATIVE', 'Sivan'),
				11 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('NOMINATIVE', 'Tamuz'),
				12 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('NOMINATIVE', 'Av'),
				13 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('NOMINATIVE', 'Elul'),
			);
		}

		if ($month_number === 7 && $leap_year) {
			return $translated_month_names[-7];
		} else {
			return $translated_month_names[$month_number];
		}
	}

	/** {@inheritdoc} */
	static function monthNameGenitiveCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('GENITIVE', 'Tishrei'),
				2  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('GENITIVE', 'Heshvan'),
				3  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('GENITIVE', 'Kislev'),
				4  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('GENITIVE', 'Tevet'),
				5  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('GENITIVE', 'Shevat'),
				6  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('GENITIVE', 'Adar I'),
				7  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('GENITIVE', 'Adar'),
				-7 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('GENITIVE', 'Adar II'),
				8  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('GENITIVE', 'Nissan'),
				9  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('GENITIVE', 'Iyar'),
				10 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('GENITIVE', 'Sivan'),
				11 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('GENITIVE', 'Tamuz'),
				12 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('GENITIVE', 'Av'),
				13 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('GENITIVE', 'Elul'),
			);
		}

		if ($month_number === 7 && $leap_year) {
			return $translated_month_names[-7];
		} else {
			return $translated_month_names[$month_number];
		}
	}

	/** {@inheritdoc} */
	protected static function monthNameLocativeCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('LOCATIVE', 'Tishrei'),
				2  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('LOCATIVE', 'Heshvan'),
				3  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('LOCATIVE', 'Kislev'),
				4  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('LOCATIVE', 'Tevet'),
				5  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('LOCATIVE', 'Shevat'),
				6  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('LOCATIVE', 'Adar I'),
				7  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('LOCATIVE', 'Adar'),
				-7 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('LOCATIVE', 'Adar II'),
				8  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('LOCATIVE', 'Nissan'),
				9  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('LOCATIVE', 'Iyar'),
				10 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('LOCATIVE', 'Sivan'),
				11 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('LOCATIVE', 'Tamuz'),
				12 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('LOCATIVE', 'Av'),
				13 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('LOCATIVE', 'Elul'),
			);
		}

		if ($month_number === 7 && $leap_year) {
			return $translated_month_names[-7];
		} else {
			return $translated_month_names[$month_number];
		}
	}

	/** {@inheritdoc} */
	protected static function monthNameInstrumentalCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('INSTRUMENTAL', 'Tishrei'),
				2  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('INSTRUMENTAL', 'Heshvan'),
				3  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('INSTRUMENTAL', 'Kislev'),
				4  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('INSTRUMENTAL', 'Tevet'),
				5  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('INSTRUMENTAL', 'Shevat'),
				6  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('INSTRUMENTAL', 'Adar I'),
				7  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('INSTRUMENTAL', 'Adar'),
				-7 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('INSTRUMENTAL', 'Adar II'),
				8  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('INSTRUMENTAL', 'Nissan'),
				9  => /* I18N: a month in the Jewish calendar */ I18N::translateContext('INSTRUMENTAL', 'Iyar'),
				10 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('INSTRUMENTAL', 'Sivan'),
				11 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('INSTRUMENTAL', 'Tamuz'),
				12 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('INSTRUMENTAL', 'Av'),
				13 => /* I18N: a month in the Jewish calendar */ I18N::translateContext('INSTRUMENTAL', 'Elul'),
			);
		}

		if ($month_number === 7 && $leap_year) {
			return $translated_month_names[-7];
		} else {
			return $translated_month_names[$month_number];
		}
	}

	/** {@inheritdoc} */
	protected static function monthNameAbbreviated($month_number, $leap_year) {
		return self::monthNameNominativeCase($month_number, $leap_year);
	}

	/** {@inheritdoc} */
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
		$thousands       = (int) ($num / 1000); //get # thousands
		$sb              = "";
		//append thousands to String
		if ($num % 1000 == 0) {
			// in year is 5000, 4000 etc
			$sb .= $jOnes[$thousands];
			$sb .= self::GERSH;
			$sb .= " ";
			$sb .= self::ALAFIM; //add # of thousands plus word thousand (overide alafim boolean)
		} elseif ($DISPLAY_JEWISH_THOUSANDS) {
			// if alafim boolean display thousands
			$sb .= $jOnes[$thousands];
			$sb .= self::GERSH; //append thousands quote
			$sb .= " ";
		}
		$num      = $num % 1000; //remove 1000s
		$hundreds = (int) ($num / 100); // # of hundreds
		$sb .= $jHundreds[$hundreds]; //add hundreds to String
		$num = $num % 100; //remove 100s
		if ($num == 15) {
			$sb .= $tavTaz[0];
		} else if ($num == 16) {
			$sb .= $tavTaz[1];
		} else {
			$tens = (int) ($num / 10);
			if ($num % 10 == 0) {
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
			// Append single quote
			$sb .= self::GERSH;
		} else {
			// Append double quote before last digit
			$pos1 = strlen($sb) - 2;
			$sb   = substr($sb, 0, $pos1) . self::GERSHAYIM . substr($sb, $pos1);
			// Replace double gershayim with single instance
			$sb   = str_replace(self::GERSHAYIM . self::GERSHAYIM, self::GERSHAYIM, $sb);
		}

		return $sb;
	}
}
