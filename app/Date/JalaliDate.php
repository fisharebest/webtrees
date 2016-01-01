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

use Fisharebest\ExtCalendar\PersianCalendar;
use Fisharebest\Webtrees\I18N;

/**
 * Definitions for the Jalali calendar
 */
class JalaliDate extends CalendarDate {
	/** @var int[] Convert GEDCOM month names to month numbers  */
	public static $MONTH_ABBREV = array('' => 0, 'FARVA' => 1, 'ORDIB' => 2, 'KHORD' => 3, 'TIR' => 4, 'MORDA' => 5, 'SHAHR' => 6, 'MEHR' => 7, 'ABAN' => 8, 'AZAR' => 9, 'DEY' => 10, 'BAHMA' => 11, 'ESFAN' => 12);

	/**
	 * Create a date from either:
	 * a Julian day number
	 * day/month/year strings from a GEDCOM date
	 * another CalendarDate object
	 *
	 * @param array|int|CalendarDate $date
	 */
	public function __construct($date) {
		$this->calendar = new PersianCalendar;
		parent::__construct($date);
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
				1  => /* I18N: 1st month in the Persian/Jalali calendar  */ I18N::translateContext('NOMINATIVE', 'Farvardin'),
				2  => /* I18N: 2nd month in the Persian/Jalali calendar  */ I18N::translateContext('NOMINATIVE', 'Ordibehesht'),
				3  => /* I18N: 3rd month in the Persian/Jalali calendar  */ I18N::translateContext('NOMINATIVE', 'Khordad'),
				4  => /* I18N: 4th month in the Persian/Jalali calendar  */ I18N::translateContext('NOMINATIVE', 'Tir'),
				5  => /* I18N: 5th month in the Persian/Jalali calendar  */ I18N::translateContext('NOMINATIVE', 'Mordad'),
				6  => /* I18N: 6th month in the Persian/Jalali calendar  */ I18N::translateContext('NOMINATIVE', 'Shahrivar'),
				7  => /* I18N: 7th month in the Persian/Jalali calendar  */ I18N::translateContext('NOMINATIVE', 'Mehr'),
				8  => /* I18N: 8th month in the Persian/Jalali calendar  */ I18N::translateContext('NOMINATIVE', 'Aban'),
				9  => /* I18N: 9th month in the Persian/Jalali calendar  */ I18N::translateContext('NOMINATIVE', 'Azar'),
				10 => /* I18N: 10th month in the Persian/Jalali calendar */ I18N::translateContext('NOMINATIVE', 'Dey'),
				11 => /* I18N: 11th month in the Persian/Jalali calendar */ I18N::translateContext('NOMINATIVE', 'Bahman'),
				12 => /* I18N: 12th month in the Persian/Jalali calendar */ I18N::translateContext('NOMINATIVE', 'Esfand'),
			);
		}

		return $translated_month_names[$month_number];
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
				1  => /* I18N: 1st month in the Persian/Jalali calendar  */ I18N::translateContext('GENITIVE', 'Farvardin'),
				2  => /* I18N: 2nd month in the Persian/Jalali calendar  */ I18N::translateContext('GENITIVE', 'Ordibehesht'),
				3  => /* I18N: 3rd month in the Persian/Jalali calendar  */ I18N::translateContext('GENITIVE', 'Khordad'),
				4  => /* I18N: 4th month in the Persian/Jalali calendar  */ I18N::translateContext('GENITIVE', 'Tir'),
				5  => /* I18N: 5th month in the Persian/Jalali calendar  */ I18N::translateContext('GENITIVE', 'Mordad'),
				6  => /* I18N: 6th month in the Persian/Jalali calendar  */ I18N::translateContext('GENITIVE', 'Shahrivar'),
				7  => /* I18N: 7th month in the Persian/Jalali calendar  */ I18N::translateContext('GENITIVE', 'Mehr'),
				8  => /* I18N: 8th month in the Persian/Jalali calendar  */ I18N::translateContext('GENITIVE', 'Aban'),
				9  => /* I18N: 9th month in the Persian/Jalali calendar  */ I18N::translateContext('GENITIVE', 'Azar'),
				10 => /* I18N: 10th month in the Persian/Jalali calendar */ I18N::translateContext('GENITIVE', 'Dey'),
				11 => /* I18N: 11th month in the Persian/Jalali calendar */ I18N::translateContext('GENITIVE', 'Bahman'),
				12 => /* I18N: 12th month in the Persian/Jalali calendar */ I18N::translateContext('GENITIVE', 'Esfand'),
			);
		}

		return $translated_month_names[$month_number];
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
				1  => /* I18N: 1st month in the Persian/Jalali calendar  */ I18N::translateContext('LOCATIVE', 'Farvardin'),
				2  => /* I18N: 2nd month in the Persian/Jalali calendar  */ I18N::translateContext('LOCATIVE', 'Ordibehesht'),
				3  => /* I18N: 3rd month in the Persian/Jalali calendar  */ I18N::translateContext('LOCATIVE', 'Khordad'),
				4  => /* I18N: 4th month in the Persian/Jalali calendar  */ I18N::translateContext('LOCATIVE', 'Tir'),
				5  => /* I18N: 5th month in the Persian/Jalali calendar  */ I18N::translateContext('LOCATIVE', 'Mordad'),
				6  => /* I18N: 6th month in the Persian/Jalali calendar  */ I18N::translateContext('LOCATIVE', 'Shahrivar'),
				7  => /* I18N: 7th month in the Persian/Jalali calendar  */ I18N::translateContext('LOCATIVE', 'Mehr'),
				8  => /* I18N: 8th month in the Persian/Jalali calendar  */ I18N::translateContext('LOCATIVE', 'Aban'),
				9  => /* I18N: 9th month in the Persian/Jalali calendar  */ I18N::translateContext('LOCATIVE', 'Azar'),
				10 => /* I18N: 10th month in the Persian/Jalali calendar */ I18N::translateContext('LOCATIVE', 'Dey'),
				11 => /* I18N: 11th month in the Persian/Jalali calendar */ I18N::translateContext('LOCATIVE', 'Bahman'),
				12 => /* I18N: 12th month in the Persian/Jalali calendar */ I18N::translateContext('LOCATIVE', 'Esfand'),
			);
		}

		return $translated_month_names[$month_number];
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
				1  => /* I18N: 1st month in the Persian/Jalali calendar  */ I18N::translateContext('INSTRUMENTAL', 'Farvardin'),
				2  => /* I18N: 2nd month in the Persian/Jalali calendar  */ I18N::translateContext('INSTRUMENTAL', 'Ordibehesht'),
				3  => /* I18N: 3rd month in the Persian/Jalali calendar  */ I18N::translateContext('INSTRUMENTAL', 'Khordad'),
				4  => /* I18N: 4th month in the Persian/Jalali calendar  */ I18N::translateContext('INSTRUMENTAL', 'Tir'),
				5  => /* I18N: 5th month in the Persian/Jalali calendar  */ I18N::translateContext('INSTRUMENTAL', 'Mordad'),
				6  => /* I18N: 6th month in the Persian/Jalali calendar  */ I18N::translateContext('INSTRUMENTAL', 'Shahrivar'),
				7  => /* I18N: 7th month in the Persian/Jalali calendar  */ I18N::translateContext('INSTRUMENTAL', 'Mehr'),
				8  => /* I18N: 8th month in the Persian/Jalali calendar  */ I18N::translateContext('INSTRUMENTAL', 'Aban'),
				9  => /* I18N: 9th month in the Persian/Jalali calendar  */ I18N::translateContext('INSTRUMENTAL', 'Azar'),
				10 => /* I18N: 10th month in the Persian/Jalali calendar */ I18N::translateContext('INSTRUMENTAL', 'Dey'),
				11 => /* I18N: 11th month in the Persian/Jalali calendar */ I18N::translateContext('INSTRUMENTAL', 'Bahman'),
				12 => /* I18N: 12th month in the Persian/Jalali calendar */ I18N::translateContext('INSTRUMENTAL', 'Esfand'),
			);
		}

		return $translated_month_names[$month_number];
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
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => I18N::translateContext('Abbreviation for Persian month: Farvardin', 'Far'),
				2  => I18N::translateContext('Abbreviation for Persian month: Ordibehesht', 'Ord'),
				3  => I18N::translateContext('Abbreviation for Persian month: Khordad', 'Khor'),
				4  => I18N::translateContext('Abbreviation for Persian month: Tir', 'Tir'),
				5  => I18N::translateContext('Abbreviation for Persian month: Mordad', 'Mor'),
				6  => I18N::translateContext('Abbreviation for Persian month: Shahrivar', 'Shah'),
				7  => I18N::translateContext('Abbreviation for Persian month: Mehr', 'Mehr'),
				8  => I18N::translateContext('Abbreviation for Persian month: Aban', 'Aban'),
				9  => I18N::translateContext('Abbreviation for Persian month: Azar', 'Azar'),
				10 => I18N::translateContext('Abbreviation for Persian month: Dey', 'Dey'),
				11 => I18N::translateContext('Abbreviation for Persian month: Bahman', 'Bah'),
				12 => I18N::translateContext('Abbreviation for Persian month: Esfand', 'Esf'),
			);
		}

		return $translated_month_names[$month_number];
	}
}
