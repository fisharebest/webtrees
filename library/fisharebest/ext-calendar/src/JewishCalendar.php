<?php
namespace Fisharebest\ExtCalendar;

/**
 * class JewishCalendar - calculations for the Jewish calendar.
 *
 * Hebrew characters in this file have UTF-8 encoding.
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
class JewishCalendar extends Calendar implements CalendarInterface{
	/** Same as PHP’s ext/calendar extension */
	const PHP_CALENDAR_NAME = 'Jewish';

	/** Same as PHP’s ext/calendar extension */
	const PHP_CALENDAR_NUMBER = 2;

	/** Same as PHP’s ext/calendar extension */
	const PHP_CALENDAR_SYMBOL = 'CAL_JEWISH';

	/** See the GEDCOM specification */
	const GEDCOM_CALENDAR_ESCAPE = '@#DHEBREW@';

	/** The earliest Julian Day number that can be converted into this calendar. */
	const JD_START = 347998; // 1 Tishri 0001 AM

	/** The latest Julian Day number that can be converted into this calendar. */
	const JD_END = 324542846;

	/** The maximum number of days in any month. */
	const MAX_DAYS_IN_MONTH = 30;

	/** The maximum number of months in any year. */
	const MAX_MONTHS_IN_YEAR = 13;

	/** Place this symbol before the final letter of a sequence of numerals. */
	const GERSHAYIM = '״';

	/** Place this symbol after a single numeral. */
	const GERESH = '׳';

	/** Word for thousand. */
	const ALAFIM = 'אלפים';

	/** A year that is one day shorter than normal. */
	const DEFECTIVE_YEAR  = -1;

	/** A year that has the normal number of days. */
	const REGULAR_YEAR  = 0;

	/** A year that is one day longer than normal. */
	const COMPLETE_YEAR  = 1;

	/**
	 * Hebrew numbers are represented by letters, similar to roman numerals.
	 *
	 * @var string[]
	 */
	private static $HEBREW_NUMERALS = array(
		9000 => 'ט',
		8000 => 'ח',
		7000 => 'ז',
		6000 => 'ו',
		5000 => 'ה',
		4000 => 'ד',
		3000 => 'ג',
		2000 => 'ב',
		1000 => 'א',
		400 => 'ת',
		300 => 'ש',
		200 => 'ר',
		100 => 'ק',
		90 => 'צ',
		80 => 'פ',
		70 => 'ע',
		60 => 'ס',
		50 => 'נ',
		40 => 'מ',
		30 => 'ל',
		20 => 'כ',
		19 => 'יט',
		18 => 'יח',
		17 => 'יז',
		16 => 'טז',
		15 => 'טו',
		10 => 'י',
		9 => 'ט',
		8 => 'ח',
		7 => 'ז',
		6 => 'ו',
		5 => 'ה',
		4 => 'ד',
		3 => 'ג',
		2 => 'ב',
		1 => 'א',
	);

	/**
	 * Some numerals take a different form when they appear at the end of a number.
	 *
	 * @var string[]
	 */
	private static $HEBREW_NUMERALS_FINAL = array(
		90 => 'צ',
		80 => 'פ',
		70 => 'ע',
		60 => 'ס',
		50 => 'נ',
		40 => 'מ',
		30 => 'ל',
		20 => 'כ',
		10 => 'י',
	);

	/**
	 * These months have fixed lengths.  Others are variable.
	 *
	 * @var int[]
	 */
	private static $FIXED_MONTH_LENGTHS = array(
		1 => 30, 4 => 29, 5 => 30, 7 => 29, 8 => 30, 9 => 29, 10 => 30, 11 => 29, 12 => 30, 13 => 29
	);

	/**
	 * Cumulative number of days for each month in each type of year.
	 * First index is false/true (non-leap year, leap year)
	 * Second index is year type (-1, 0, 1)
	 * Third index is month number (1 ... 13)
	 *
	 * @var int[][][]
	 */
	private static $CUMULATIVE_DAYS = array(
		0 => array( // Non-leap years
			-1 => array( // Deficient years
				1 => 0, 30, 59, 88, 117, 147, 147, 176, 206, 235, 265, 294, 324
			),
			0 =>  array( // Regular years
				1 => 0, 30, 59, 89, 118, 148, 148, 177, 207, 236, 266, 295, 325
			),
			1 => array( // Complete years
				1 => 0, 30, 60, 90, 119, 149, 149, 178, 208, 237, 267, 296, 326
			),
		),
		1 => array( // Leap years
			-1 => array( // Deficient years
				1 => 0, 30, 59, 88, 117, 147, 177, 206, 236, 265, 295, 324, 354
			),
			0 =>  array( // Regular years
				1 => 0, 30, 59, 89, 118, 148, 178, 207, 237, 266, 296, 325, 355
			),
			1 => array( // Complete years
				1 => 0, 30, 60, 90, 119, 149, 179, 208, 238, 267, 297, 326, 356
			),
		),
	);


	/**
	 * Rosh Hashanah cannot fall on a Sunday, Wednesday or Friday.  Move the year start accordingly.
	 *
	 * @var int[]
	 */
	private static $ROSH_HASHANAH = array(347998, 347997, 347997, 347998, 347997, 347998, 347997);

	/**
	 * Determine whether a year is a leap year.
	 *
	 * @param  int  $year
	 * @return bool
	 */
	public function leapYear($year) {
		return (7 * $year + 1) % 19 < 7;
	}

	/**
	 * Convert a Julian day number into a year.
	 *
	 * @param int $jd
	 *
	 * @return int;
	 */
	protected function jdToY($jd) {
		// Generate an approximate year - may be out by one either way.  Add one to it.
		$year = (int)(($jd - 347998) / 365) + 1;

		// Adjust by subtracting years;
		while ($this->yToJd($year) > $jd) {
			$year--;
		}

		return $year;
	}

	/**
	 * Convert a Julian day number into a year/month/day.
	 *
	 * @param int $jd
	 *
	 * @return int[];
	 */
	public function jdToYmd($jd) {
		// Find the year
		$year = $this->jdToY($jd);

		// Add one month at a time, to use up the remaining days.
		$month = 1;
		$day = $jd - $this->yToJd($year) + 1;

		while ($day > $this->daysInMonth($year, $month)) {
			$day -= $this->daysInMonth($year, $month);
			$month += 1;
		}

		// PHP 5.4 and earlier converted non leap-year Adar into month 6, instead of month 7.
		$month -= (Shim::emulateBug54254() && $month == 7 && !$this->leapYear($year)) ? 1 : 0;

		return array($year, $month, $day);
	}

	/**
	 * Calculate the Julian Day number of the first day in a year.
	 *
	 * @param  int $year
	 *
	 * @return int
	 */
	protected function yToJd($year) {
		$div19 = (int)(($year - 1) / 19);
		$mod19 = ($year - 1) % 19;
		$months = 235 * $div19 + 12 * $mod19 + (int)((7 * $mod19 + 1) / 19);
		$parts = 204 + 793 * ($months % 1080);
		$hours = 5 + 12 * $months + 793 * (int)($months / 1080) + (int)($parts / 1080);
		$conjunction = 1080 * ($hours % 24) + ($parts % 1080);
		$jd = 1 + 29 * $months + (int)($hours / 24);

		if (
			$conjunction >= 19440 ||
			$jd % 7 === 2 && $conjunction >= 9924 && !$this->leapYear($year) ||
			$jd % 7 === 1 && $conjunction >= 16789 && $this->leapYear($year - 1)
		) {
			$jd++;
		}

		// The actual year start depends on the day of the week
		return $jd + self::$ROSH_HASHANAH[$jd % 7];
	}

	/**
	 * Convert a year/month/day into a Julian day number.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 *
	 * @return int
	 */
	public function ymdToJd($year, $month, $day) {
		return
			$this->yToJd($year) +
			self::$CUMULATIVE_DAYS[$this->leapYear($year)][$this->yearType($year)][$month] +
			$day - 1;
	}

	/**
	 * Determine whether a year is normal, defective or complete.
	 *
	 * @param $year
	 *
	 * @return int defective (-1), normal (0) or complete (1)
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
	 * @param int $year
	 *
	 * @return int
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
	 * @param int $year
	 *
	 * @return int
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
	 * @param int $year
	 *
	 * @return int
	 */
	private function daysInMonthAdarI($year) {
		if ($this->leapYear($year)) {
			return 30;
		} else {
			return 0;
		}
	}

	/**
	 * Calculate the number of days in a given month.
	 *
	 * @param  int $year
	 * @param  int $month
	 *
	 * @return int
	 */
	public function daysInMonth($year, $month) {
		if ($year === 0 || $month < 1 || $month > 13) {
			return trigger_error('invalid date.', E_USER_WARNING);
		} elseif ($month === 2) {
			return $this->daysInMonthHeshvan($year);
		} elseif ($month === 3) {
			return $this->daysInMonthKislev($year);
		} elseif ($month === 6) {
			return $this->daysInMonthAdarI($year);
		} else {
			return self::$FIXED_MONTH_LENGTHS[$month];
		}
	}

	/**
	 * Month names.
	 *
	 * @link https://bugs.php.net/bug.php?id=54254
	 *
	 * @return string[]
	 */
	public function monthNames() {
		return array(
			1 => 'Tishri', 'Heshvan', 'Kislev', 'Tevet', 'Shevat',
			Shim::emulateBug54254() ? 'AdarI' : 'Adar I',
			Shim::emulateBug54254() ? 'AdarII' : 'Adar II',
			'Nisan', 'Iyyar', 'Sivan', 'Tammuz', 'Av', 'Elul',
		);
	}

	/**
	 * Calculate the name of a month, for a specified Julian Day number.
	 *
	 * @param  $jd
	 *
	 * @return string
	 */
	public function jdMonthName($jd) {
		list($year, $month) = $this->jdToYmd($jd);
		$months = $this->monthNames();

		if (!$this->leapYear($year) && ($month === 6 || $month === 7)) {
			return Shim::emulateBug54254() ? 'AdarI' : 'Adar';
		} else {
			return $months[$month];
		}
	}

	/**
	 * Calculate the name of a month, for a specified Julian Day number.
	 *
	 * @param  int $jd
	 *
	 * @return string
	 */
	public function jdMonthNameAbbreviated($jd) {
		return $this->jdMonthName($jd);
	}


	/**
	 * Hebrew month names.
	 *
	 * @link https://bugs.php.net/bug.php?id=54254
	 *
	 * @param int $year
	 *
	 * @return string[]
	 */
	protected function hebrewMonthNames($year) {
		$leap_year = $this->leapYear($year);

		return array(
			1 => 'תשרי', 'חשון', 'כסלו', 'טבת', 'שבט',
			$leap_year ? (Shim::emulateBug54254() ? 'אדר' : 'אדר א׳') : 'אדר',
			$leap_year ? (Shim::emulateBug54254() ? '\'אדר ב' : 'אדר ב׳') : 'אדר',
			'ניסן', 'אייר', 'סיון', 'תמוז', 'אב', 'אלול',
		);
	}

	/**
	 * The Hebrew name of a given month.
	 *
	 * @param int $year
	 * @param int $month
	 *
	 * @return string
	 */
	protected function hebrewMonthName($year, $month) {
		$months = $this->hebrewMonthNames($year);

		return $months[$month];
	}

	/**
	 * Convert a number into Hebrew numerals.
	 * 
	 * @param int $number
	 * @param bool $alafim_garesh
	 * @param bool $alafim
	 * @param bool $gereshayim
	 *
	 * @return string
	 */
	protected function numToHebrew($number, $alafim_garesh, $alafim, $gereshayim) {
		$hebrew = '';
		while ($number > 0) {
			foreach (self::$HEBREW_NUMERALS_FINAL as $n => $h) {
				if ($number == $n) {
					$hebrew .= $h;
					break 2;
				}
			}
			foreach (self::$HEBREW_NUMERALS as $n => $h) {
				if ($number >= $n) {
					$hebrew .= $h;
					$number -= $n;
					break;
				}
			}
		}
		return $hebrew;
	}

	/**
	 * Convert a Julian Day number into a Hebrew date.
	 *
	 * @param int  $jd
	 * @param bool $alafim_garesh
	 * @param bool $alafim
	 * @param bool $gereshayim
	 *
	 * @return string $string
	 */
	public function jdToHebrew($jd, $alafim_garesh, $alafim, $gereshayim) {
		list($year, $month, $day) = $this->jdToYmd($jd);

		return
			$this->numToHebrew($day, $alafim_garesh, $alafim, $gereshayim) . ' ' .
			$this->hebrewMonthName($year, $month) . ' ' .
			$this->numToHebrew($year, $alafim_garesh, $alafim, $gereshayim);
	}
}
