<?php
namespace Fisharebest\ExtCalendar;

use InvalidArgumentException;

/**
 * Class JewishCalendar - calculations for the Jewish calendar.
 *
 * Hebrew characters in the code have either ISO-8859-8 or UTF_8 encoding.
 * Hebrew characters in the comments have UTF-8 encoding.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014-2015 Greg Roach
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
class JewishCalendar implements CalendarInterface {
	/** Optional behaviour for this calendar. */
	const EMULATE_BUG_54254 = 'EMULATE_BUG_54254';

	/** Place this symbol before the final letter of a sequence of numerals */
	const GERSHAYIM_ISO8859 = '"';
	const GERSHAYIM         = "\xd7\xb4";

	/** Place this symbol after a single numeral */
	const GERESH_ISO8859 = '\'';
	const GERESH         = "\xd7\xb3";

	/** The Hebrew word for thousand */
	const ALAFIM_ISO8859 = "\xe0\xec\xf4\xe9\xed";
	const ALAFIM         = "\xd7\x90\xd7\x9c\xd7\xa4\xd7\x99\xd7\x9d";

	/** A year that is one day shorter than normal. */
	const DEFECTIVE_YEAR = -1;

	/** A year that has the normal number of days. */
	const REGULAR_YEAR = 0;

	/** A year that is one day longer than normal. */
	const COMPLETE_YEAR = 1;

	/** @var string[] Hebrew numbers are represented by letters, similar to roman numerals. */
	private static $HEBREW_NUMERALS_ISO8859_8 = array(
		400 => "\xfa",
		300 => "\xf9",
		200 => "\xf8",
		100 => "\xf7",
		90  => "\xf6",
		80  => "\xf4",
		70  => "\xf2",
		60  => "\xf1",
		50  => "\xf0",
		40  => "\xee",
		30  => "\xec",
		20  => "\xeb",
		19  => "\xe9\xe8",
		18  => "\xe9\xe7",
		17  => "\xe9\xe6",
		16  => "\xe8\xe6",
		15  => "\xe8\xe5",
		10  => "\xe9",
		9   => "\xe8",
		8   => "\xe7",
		7   => "\xe6",
		6   => "\xe5",
		5   => "\xe4",
		4   => "\xe3",
		3   => "\xe2",
		2   => "\xe1",
		1   => "\xe0",
	);

	/** @var string[] Hebrew numbers are represented by letters, similar to roman numerals. */
	private static $HEBREW_NUMERALS_UTF8 = array(
		400 => "\xd7\xaa",
		300 => "\xd7\xa9",
		200 => "\xd7\xa8",
		100 => "\xd7\xa7",
		90  => "\xd7\xa6",
		80  => "\xd7\xa4",
		70  => "\xd7\xa2",
		60  => "\xd7\xa1",
		50  => "\xd7\xa0",
		40  => "\xd7\x9e",
		30  => "\xd7\x9c",
		20  => "\xd7\x9b",
		19  => "\xd7\x99\xd7\x98",
		18  => "\xd7\x99\xd7\x97",
		17  => "\xd7\x99\xd7\x96",
		16  => "\xd7\x98\xd7\x96",
		15  => "\xd7\x98\xd7\x95",
		10  => "\xd7\x99",
		9   => "\xd7\x98",
		8   => "\xd7\x97",
		7   => "\xd7\x96",
		6   => "\xd7\x95",
		5   => "\xd7\x94",
		4   => "\xd7\x93",
		3   => "\xd7\x92",
		2   => "\xd7\x91",
		1   => "\xd7\x90",
	);

	/** @var string[] Some letters have a different final form  */
	private static $FINAL_FORMS_UTF8 = array(
		"\xd7\x9b" => "\xd7\x9a",
		"\xd7\x9e" => "\xd7\x9d",
		"\xd7\xa0" => "\xd7\x9f",
		"\xd7\xa4" => "\xd7\xa3",
		"\xd7\xa6" => "\xd7\xa5",
	);

	/** @var integer[] These months have fixed lengths.  Others are variable. */
	private static $FIXED_MONTH_LENGTHS = array(
		1 => 30, 4 => 29, 5 => 30, 7 => 29, 8 => 30, 9 => 29, 10 => 30, 11 => 29, 12 => 30, 13 => 29
	);

	/**
	 * Cumulative number of days for each month in each type of year.
	 * First index is false/true (non-leap year, leap year)
	 * Second index is year type (-1, 0, 1)
	 * Third index is month number (1 ... 13)
	 *
	 * @var integer[][][]
	 */
	private static $CUMULATIVE_DAYS = array(
		0 => array( // Non-leap years
			self::DEFECTIVE_YEAR => array(
				1 => 0, 30, 59, 88, 117, 147, 147, 176, 206, 235, 265, 294, 324
			),
			self::REGULAR_YEAR  => array( // Regular years
				1 => 0, 30, 59, 89, 118, 148, 148, 177, 207, 236, 266, 295, 325
			),
			self::COMPLETE_YEAR  => array( // Complete years
				1 => 0, 30, 60, 90, 119, 149, 149, 178, 208, 237, 267, 296, 326
			),
		),
		1 => array( // Leap years
			self::DEFECTIVE_YEAR => array( // Deficient years
				1 => 0, 30, 59, 88, 117, 147, 177, 206, 236, 265, 295, 324, 354
			),
			self::REGULAR_YEAR  => array( // Regular years
				1 => 0, 30, 59, 89, 118, 148, 178, 207, 237, 266, 296, 325, 355
			),
			self::COMPLETE_YEAR  => array( // Complete years
				1 => 0, 30, 60, 90, 119, 149, 179, 208, 238, 267, 297, 326, 356
			),
		),
	);

	/** @var integer[] Rosh Hashanah cannot fall on a Sunday, Wednesday or Friday.  Move the year start accordingly. */
	private static $ROSH_HASHANAH = array(347998, 347997, 347997, 347998, 347997, 347998, 347997);

	/** @var mixed[] special behaviour for this calendar */
	protected $options = array(
		self::EMULATE_BUG_54254 => false,
	);

	/** @param mixed[] $options Some calendars have options that change their behaviour. */
	public function __construct($options = array()) {
		$this->options = array_merge($this->options, $options);
	}

	public function daysInMonth($year, $month) {
		if ($year < 1) {
			throw new InvalidArgumentException('Year ' . $year . ' is invalid for this calendar');
		} elseif ($month < 1 || $month > 13) {
			throw new InvalidArgumentException('Month ' . $month . ' is invalid for this calendar');
		} elseif ($month == 2) {
			return $this->daysInMonthHeshvan($year);
		} elseif ($month == 3) {
			return $this->daysInMonthKislev($year);
		} elseif ($month == 6) {
			return $this->daysInMonthAdarI($year);
		} else {
			return self::$FIXED_MONTH_LENGTHS[$month];
		}
	}


	public function daysInWeek() {
		return 7;
	}

	public function gedcomCalendarEscape() {
		return '@#DHEBREW@';
	}

	public function isLeapYear($year) {
		return (7 * $year + 1) % 19 < 7;
	}

	public function jdEnd() {
		return PHP_INT_MAX;
	}

	public function jdStart() {
		return 347998; // 1 Tishri 0001 AM
	}

	/**
	 * Convert a Julian day number into a year.
	 *
	 * @param integer $julian_day
	 *
	 * @return integer
	 */
	protected function jdToY($julian_day) {
		// Generate an approximate year - may be out by one either way.  Add one to it.
		$year = (int) (($julian_day - 347998) / 365) + 1;

		// Adjust by subtracting years;
		while ($this->yToJd($year) > $julian_day) {
			$year--;
		}

		return $year;
	}

	public function jdToYmd($julian_day) {
		// Find the year, by adding one month at a time to use up the remaining days.
		$year  = $this->jdToY($julian_day);
		$month = 1;
		$day   = $julian_day - $this->yToJd($year) + 1;

		while ($day > $this->daysInMonth($year, $month)) {
			$day -= $this->daysInMonth($year, $month);
			$month++;
		}

		// PHP 5.4 and earlier converted non leap-year Adar into month 6, instead of month 7.
		$month -= ($month === 7 && $this->options[self::EMULATE_BUG_54254] && !$this->isLeapYear($year)) ? 1 : 0;

		return array($year, $month, $day);
	}

	public function monthsInYear() {
		return 13;
	}

	/**
	 * Calculate the Julian Day number of the first day in a year.
	 *
	 * @param integer $year
	 *
	 * @return integer
	 */
	protected function yToJd($year) {
		$div19 = (int) (($year - 1) / 19);
		$mod19 = ($year - 1) % 19;

		$months      = 235 * $div19 + 12 * $mod19 + (int) ((7 * $mod19 + 1) / 19);
		$parts       = 204 + 793 * ($months % 1080);
		$hours       = 5 + 12 * $months + 793 * (int) ($months / 1080) + (int) ($parts / 1080);
		$conjunction = 1080 * ($hours % 24) + ($parts % 1080);
		$julian_day  = 1 + 29 * $months + (int) ($hours / 24);

		if (
			$conjunction >= 19440 ||
			$julian_day % 7 === 2 && $conjunction >= 9924 && !$this->isLeapYear($year) ||
			$julian_day % 7 === 1 && $conjunction >= 16789 && $this->isLeapYear($year - 1)
		) {
			$julian_day++;
		}

		// The actual year start depends on the day of the week
		return $julian_day + self::$ROSH_HASHANAH[$julian_day % 7];
	}

	public function ymdToJd($year, $month, $day) {
		return
			$this->yToJd($year) +
			self::$CUMULATIVE_DAYS[$this->isLeapYear($year)][$this->yearType($year)][$month] +
			$day - 1;
	}

	/**
	 * Determine whether a year is normal, defective or complete.
	 *
	 * @param integer $year
	 *
	 * @return integer defective (-1), normal (0) or complete (1)
	 */
	private function yearType($year) {
		$year_length = $this->yToJd($year + 1) - $this->yToJd($year);

		if ($year_length === 353 || $year_length === 383) {
			return self::DEFECTIVE_YEAR;
		} elseif ($year_length === 355 || $year_length === 385) {
			return self::COMPLETE_YEAR;
		} else {
			return self::REGULAR_YEAR;
		}
	}

	/**
	 * Calculate the number of days in Heshvan.
	 *
	 * @param integer $year
	 *
	 * @return integer
	 */
	private function daysInMonthHeshvan($year) {
		if ($this->yearType($year) === self::COMPLETE_YEAR) {
			return 30;
		} else {
			return 29;
		}
	}

	/**
	 * Calculate the number of days in Kislev.
	 *
	 * @param integer $year
	 *
	 * @return integer
	 */
	private function daysInMonthKislev($year) {
		if ($this->yearType($year) === self::DEFECTIVE_YEAR) {
			return 29;
		} else {
			return 30;
		}
	}

	/**
	 * Calculate the number of days in Adar I.
	 *
	 * @param integer $year
	 *
	 * @return integer
	 */
	private function daysInMonthAdarI($year) {
		if ($this->isLeapYear($year)) {
			return 30;
		} else {
			return 0;
		}
	}

	/**
	 * Hebrew month names.
	 *
	 * @link https://bugs.php.net/bug.php?id=54254
	 *
	 * @param integer $year
	 *
	 * @return string[]
	 */
	protected function hebrewMonthNames($year) {
		$leap_year = $this->isLeapYear($year);

		return array(
			1 => "\xfa\xf9\xf8\xe9", // Tishri - תשרי
			"\xe7\xf9\xe5\xef", // Heshvan - חשון
			"\xeb\xf1\xec\xe5", // Kislev - כסלו
			"\xe8\xe1\xfa", // Tevet - טבת
			"\xf9\xe1\xe8", // Shevat - שבט
			$leap_year ? ($this->options[self::EMULATE_BUG_54254] ? "\xe0\xe3\xf8" : "\xe0\xe3\xf8 \xe0'") : "\xe0\xe3\xf8", // Adar I - אדר - אדר א׳ - אדר
			$leap_year ? ($this->options[self::EMULATE_BUG_54254] ? "'\xe0\xe3\xf8 \xe1" : "\xe0\xe3\xf8 \xe1'") : "\xe0\xe3\xf8", // Adar II - 'אדר ב - אדר ב׳ - אדר
			"\xf0\xe9\xf1\xef", // Nisan - ניסן
			"\xe0\xe9\xe9\xf8", // Iyar - אייר
			"\xf1\xe9\xe5\xef", // Sivan - סיון
			"\xfa\xee\xe5\xe6", // Tammuz - תמוז
			"\xe0\xe1", // Av - אב
			"\xe0\xec\xe5\xec", // Elul - אלול
		);
	}

	/**
	 * The Hebrew name of a given month.
	 *
	 * @param integer $year
	 * @param integer $month
	 *
	 * @return string
	 */
	protected function hebrewMonthName($year, $month) {
		$months = $this->hebrewMonthNames($year);

		return $months[$month];
	}

	/**
	 * Add geresh (׳) and gershayim (״) punctuation to numeric values.
	 *
	 * Gereshayim is a contraction of “geresh” and “gershayim”.
	 *
	 * @param string $hebrew
	 *
	 * @return string
	 */
	protected function addGereshayim($hebrew) {
		switch (strlen($hebrew)) {
		case 0:
			// Zero, e.g. the zeros from the year 5,000
			return $hebrew;
		case 1:
			// Single digit - append a geresh
			return $hebrew . self::GERESH_ISO8859;
		default:
			// Multiple digits - insert a gershayim
			return substr_replace($hebrew, self::GERSHAYIM_ISO8859, -1, 0);
		}
	}

	/**
	 * Convert a number into a string, in the style of roman numerals
	 *
	 * @param integer  $number
	 * @param string[] $numerals
	 *
	 * @return string
	 */
	private function numberToNumerals($number, array $numerals) {
		$string = '';

		while ($number > 0) {
			foreach ($numerals as $n => $t) {
				if ($number >= $n) {
					$string .= $t;
					$number -= $n;
					break;
				}
			}
		}

		return $string;
	}

	/**
	 * Convert a number into Hebrew numerals using UTF8.
	 *
	 * @param integer $number
	 * @param boolean $show_thousands
	 *
	 * @return string
	 */
	public function numberToHebrewNumerals($number, $show_thousands) {
		// Years (e.g. "5782") may be written without the thousands (e.g. just "782"),
		// but since there is no zero, the number 5000 must be written as "5 thousand"
		if ($show_thousands || $number % 1000 === 0) {
			$thousands = (int)($number / 1000);
		} else {
			$thousands = 0;
		}
		$number = $number % 1000;

		$hebrew = $this->numberToNumerals($number, self::$HEBREW_NUMERALS_UTF8);

		// Two bytes per UTF8 character
		if (strlen($hebrew) === 2) {
			// Append a geresh after single-digit
			$hebrew .= self::GERESH;
		} elseif (strlen($hebrew) > 2) {
			// Some letters have a "final" form, when used at the end of a word.
			$hebrew = substr($hebrew, 0, -2) . strtr(substr($hebrew, -2), self::$FINAL_FORMS_UTF8);
			// Insert a gershayim before the final letter
			$hebrew = substr_replace($hebrew, self::GERSHAYIM, -2, 0);
		}

		if ($thousands) {
			if ($hebrew) {
				$hebrew = $this->numberToHebrewNumerals($thousands, $show_thousands) . $hebrew;
			} else {
				$hebrew = $this->numberToHebrewNumerals($thousands, $show_thousands) . ' ' . self::ALAFIM;
			}
		}

		return $hebrew;
	}

	/**
	 * Convert a number into Hebrew numerals using ISO8859-8.
	 *
	 * @param integer $number
	 * @param boolean $gereshayim Add punctuation to numeric values
	 *
	 * @return string
	 */
	protected function numberToHebrewNumeralsIso8859($number, $gereshayim) {
		$hebrew = $this->numberToNumerals($number, self::$HEBREW_NUMERALS_ISO8859_8);

		// Hebrew numerals are letters.  Add punctuation to prevent confusion with actual words.
		if ($gereshayim) {
			return $this->addGereshayim($hebrew);
		} else {
			return $hebrew;
		}
	}

	/**
	 * Format a year using Hebrew numerals.
	 *
	 * @param integer $year
	 * @param boolean $alafim_geresh Add a geresh  (׳) after thousands
	 * @param boolean $alafim        Add the word for thousands after the thousands
	 * @param boolean $gereshayim    Add geresh (׳) and gershayim (״) punctuation to numeric values
	 *
	 * @return string
	 */
	protected function yearToHebrewNumerals($year, $alafim_geresh, $alafim, $gereshayim) {
		if ($year < 1000) {
			return $this->numberToHebrewNumeralsIso8859($year, $gereshayim);
		} else {
			$thousands = $this->numberToHebrewNumeralsIso8859((int) ($year / 1000), false);
			if ($alafim_geresh) {
				$thousands .= self::GERESH_ISO8859;
			}
			if ($alafim) {
				$thousands .= ' ' . self::ALAFIM_ISO8859 . ' ';
			}

			return $thousands . $this->numberToHebrewNumeralsIso8859($year % 1000, $gereshayim);
		}
	}

	/**
	 * Convert a Julian Day number into a Hebrew date.
	 *
	 * @param integer $julian_day
	 * @param boolean $alafim_garesh
	 * @param boolean $alafim
	 * @param boolean $gereshayim
	 *
	 * @return string
	 */
	public function jdToHebrew($julian_day, $alafim_garesh, $alafim, $gereshayim) {
		list($year, $month, $day) = $this->jdToYmd($julian_day);

		return
			$this->numberToHebrewNumeralsIso8859($day, $gereshayim) . ' ' .
			$this->hebrewMonthName($year, $month) . ' ' .
			$this->yearToHebrewNumerals($year, $alafim_garesh, $alafim, $gereshayim);
	}
}
