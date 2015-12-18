<?php
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
namespace Fisharebest\Webtrees\Date;

use Fisharebest\ExtCalendar\JewishCalendar;
use Fisharebest\Webtrees\I18N;

/**
 * Definitions for the Jewish calendar
 */
class JewishDate extends CalendarDate {
	/** @var int[] Convert GEDCOM month names to month numbers  */
	public static $MONTH_ABBREV = array('' => 0, 'TSH' => 1, 'CSH' => 2, 'KSL' => 3, 'TVT' => 4, 'SHV' => 5, 'ADR' => 6, 'ADS' => 7, 'NSN' => 8, 'IYR' => 9, 'SVN' => 10, 'TMZ' => 11, 'AAV' => 12, 'ELL' => 13);

	/**
	 * Create a date from either:
	 * a Julian day number
	 * day/month/year strings from a GEDCOM date
	 * another CalendarDate object
	 *
	 * @param array|int|CalendarDate $date
	 */
	public function __construct($date) {
		$this->calendar = new JewishCalendar;
		parent::__construct($date);
	}

	/**
	 * Generate the %j format for a date.
	 *
	 * @return string
	 */
	protected function formatDay() {
		if (WT_LOCALE === 'he' || WT_LOCALE === 'yi') {
			return $this->calendar->numberToHebrewNumerals($this->d, true);
		} else {
			return $this->d;
		}
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
		if (WT_LOCALE === 'he' || WT_LOCALE === 'yi') {
			return $this->calendar->numberToHebrewNumerals($this->y, false);
		} else {
			return $this->y;
		}
	}

	/**
	 * Generate the %Y format for a date.
	 *
	 * @return string
	 */
	protected function formatLongYear() {
		if (WT_LOCALE === 'he' || WT_LOCALE === 'yi') {
			return $this->calendar->numberToHebrewNumerals($this->y, true);
		} else {
			return $this->y;
		}
	}

	/**
	 * Full month name in nominative case.
	 *
	 * @param int  $month_number
	 * @param bool $leap_year    Some calendars use leap months
	 *
	 * @return string
	 */
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

	/**
	 * Full month name in genitive case.
	 *
	 * @param int  $month_number
	 * @param bool $leap_year    Some calendars use leap months
	 *
	 * @return string
	 */
	protected function monthNameGenitiveCase($month_number, $leap_year) {
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

	/**
	 * Full month name in locative case.
	 *
	 * @param int  $month_number
	 * @param bool $leap_year    Some calendars use leap months
	 *
	 * @return string
	 */
	protected function monthNameLocativeCase($month_number, $leap_year) {
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

	/**
	 * Full month name in instrumental case.
	 *
	 * @param int  $month_number
	 * @param bool $leap_year    Some calendars use leap months
	 *
	 * @return string
	 */
	protected function monthNameInstrumentalCase($month_number, $leap_year) {
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

	/**
	 * Abbreviated month name
	 *
	 * @param int  $month_number
	 * @param bool $leap_year    Some calendars use leap months
	 *
	 * @return string
	 */
	protected function monthNameAbbreviated($month_number, $leap_year) {
		return self::monthNameNominativeCase($month_number, $leap_year);
	}

	/**
	 * Which months follows this one?  Calendars with leap-months should provide their own implementation.
	 *
	 * @return int[]
	 */
	protected function nextMonth() {
		if ($this->m == 6 && !$this->isLeapYear()) {
			return array($this->y, 8);
		} else {
			return array($this->y + ($this->m == 13 ? 1 : 0), ($this->m % 13) + 1);
		}
	}
}
