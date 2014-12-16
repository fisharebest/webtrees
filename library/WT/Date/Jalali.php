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

use Fisharebest\ExtCalendar\PersianCalendar;

/**
 * Class WT_Date_Jalali - Definitions for the Jalali calendar
 */
class WT_Date_Jalali extends WT_Date_Calendar {
	const CALENDAR_ESCAPE = '@#DJALALI@';
	const CAL_START_JD = 1948321;
	static $MONTH_ABBREV = array('' => 0, 'FARVA' => 1, 'ORDIB' => 2, 'KHORD' => 3, 'TIR' => 4, 'MORDA' => 5, 'SHAHR' => 6, 'MEHR' => 7, 'ABAN' => 8, 'AZAR' => 9, 'DEY' => 10, 'BAHMA' => 11, 'ESFAN' => 12);

	/**
	 * {@inheritdoc}
	 */
	public function __construct($date) {
		$this->calendar = new PersianCalendar;
		parent::__construct($date);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function calendarName() {
		return /* I18N: The Persian/Jalali calendar */
			WT_I18N::translate('Jalali');
	}

	/**
	 * {@inheritdoc}
	 */
	public static function monthNameNominativeCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: 1st month in the Persian/Jalali calendar  */ WT_I18N::translate_c('NOMINATIVE', 'Farvardin'),
				2  => /* I18N: 2nd month in the Persian/Jalali calendar  */ WT_I18N::translate_c('NOMINATIVE', 'Ordibehesht'),
				3  => /* I18N: 3rd month in the Persian/Jalali calendar  */ WT_I18N::translate_c('NOMINATIVE', 'Khordad'),
				4  => /* I18N: 4th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('NOMINATIVE', 'Tir'),
				5  => /* I18N: 5th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('NOMINATIVE', 'Mordad'),
				6  => /* I18N: 6th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('NOMINATIVE', 'Shahrivar'),
				7  => /* I18N: 7th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('NOMINATIVE', 'Mehr'),
				8  => /* I18N: 8th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('NOMINATIVE', 'Aban'),
				9  => /* I18N: 9th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('NOMINATIVE', 'Azar'),
				10 => /* I18N: 10th month in the Persian/Jalali calendar */ WT_I18N::translate_c('NOMINATIVE', 'Dey'),
				11 => /* I18N: 11th month in the Persian/Jalali calendar */ WT_I18N::translate_c('NOMINATIVE', 'Bahman'),
				12 => /* I18N: 12th month in the Persian/Jalali calendar */ WT_I18N::translate_c('NOMINATIVE', 'Esfand'),
			);
		}

		return $translated_month_names[$month_number];
	}

	/**
	 * {@inheritdoc}
	 */
	static function monthNameGenitiveCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: 1st month in the Persian/Jalali calendar  */ WT_I18N::translate_c('GENITIVE', 'Farvardin'),
				2  => /* I18N: 2nd month in the Persian/Jalali calendar  */ WT_I18N::translate_c('GENITIVE', 'Ordibehesht'),
				3  => /* I18N: 3rd month in the Persian/Jalali calendar  */ WT_I18N::translate_c('GENITIVE', 'Khordad'),
				4  => /* I18N: 4th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('GENITIVE', 'Tir'),
				5  => /* I18N: 5th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('GENITIVE', 'Mordad'),
				6  => /* I18N: 6th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('GENITIVE', 'Shahrivar'),
				7  => /* I18N: 7th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('GENITIVE', 'Mehr'),
				8  => /* I18N: 8th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('GENITIVE', 'Aban'),
				9  => /* I18N: 9th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('GENITIVE', 'Azar'),
				10 => /* I18N: 10th month in the Persian/Jalali calendar */ WT_I18N::translate_c('GENITIVE', 'Dey'),
				11 => /* I18N: 11th month in the Persian/Jalali calendar */ WT_I18N::translate_c('GENITIVE', 'Bahman'),
				12 => /* I18N: 12th month in the Persian/Jalali calendar */ WT_I18N::translate_c('GENITIVE', 'Esfand'),
			);
		}

		return $translated_month_names[$month_number];
	}

	/**
	 * {@inheritdoc}
	 */
	static function monthNameLocativeCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: 1st month in the Persian/Jalali calendar  */ WT_I18N::translate_c('LOCATIVE', 'Farvardin'),
				2  => /* I18N: 2nd month in the Persian/Jalali calendar  */ WT_I18N::translate_c('LOCATIVE', 'Ordibehesht'),
				3  => /* I18N: 3rd month in the Persian/Jalali calendar  */ WT_I18N::translate_c('LOCATIVE', 'Khordad'),
				4  => /* I18N: 4th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('LOCATIVE', 'Tir'),
				5  => /* I18N: 5th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('LOCATIVE', 'Mordad'),
				6  => /* I18N: 6th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('LOCATIVE', 'Shahrivar'),
				7  => /* I18N: 7th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('LOCATIVE', 'Mehr'),
				8  => /* I18N: 8th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('LOCATIVE', 'Aban'),
				9  => /* I18N: 9th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('LOCATIVE', 'Azar'),
				10 => /* I18N: 10th month in the Persian/Jalali calendar */ WT_I18N::translate_c('LOCATIVE', 'Dey'),
				11 => /* I18N: 11th month in the Persian/Jalali calendar */ WT_I18N::translate_c('LOCATIVE', 'Bahman'),
				12 => /* I18N: 12th month in the Persian/Jalali calendar */ WT_I18N::translate_c('LOCATIVE', 'Esfand'),
			);
		}

		return $translated_month_names[$month_number];
	}

	/**
	 * {@inheritdoc}
	 */
	static function monthNameInstrumentalCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: 1st month in the Persian/Jalali calendar  */ WT_I18N::translate_c('INSTRUMENTAL', 'Farvardin'),
				2  => /* I18N: 2nd month in the Persian/Jalali calendar  */ WT_I18N::translate_c('INSTRUMENTAL', 'Ordibehesht'),
				3  => /* I18N: 3rd month in the Persian/Jalali calendar  */ WT_I18N::translate_c('INSTRUMENTAL', 'Khordad'),
				4  => /* I18N: 4th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('INSTRUMENTAL', 'Tir'),
				5  => /* I18N: 5th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('INSTRUMENTAL', 'Mordad'),
				6  => /* I18N: 6th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('INSTRUMENTAL', 'Shahrivar'),
				7  => /* I18N: 7th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('INSTRUMENTAL', 'Mehr'),
				8  => /* I18N: 8th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('INSTRUMENTAL', 'Aban'),
				9  => /* I18N: 9th month in the Persian/Jalali calendar  */ WT_I18N::translate_c('INSTRUMENTAL', 'Azar'),
				10 => /* I18N: 10th month in the Persian/Jalali calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Dey'),
				11 => /* I18N: 11th month in the Persian/Jalali calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Bahman'),
				12 => /* I18N: 12th month in the Persian/Jalali calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Esfand'),
			);
		}

		return $translated_month_names[$month_number];
	}

	/**
	 * {@inheritdoc}
	 */
	static function monthNameAbbreviated($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => WT_I18N::translate_c('Abbreviation for Persian month: Farvardin', 'Far'),
				2  => WT_I18N::translate_c('Abbreviation for Persian month: Ordibehesht', 'Ord'),
				3  => WT_I18N::translate_c('Abbreviation for Persian month: Khordad', 'Khor'),
				4  => WT_I18N::translate_c('Abbreviation for Persian month: Tir', 'Tir'),
				5  => WT_I18N::translate_c('Abbreviation for Persian month: Mordad', 'Mor'),
				6  => WT_I18N::translate_c('Abbreviation for Persian month: Shahrivar', 'Shah'),
				7  => WT_I18N::translate_c('Abbreviation for Persian month: Mehr', 'Mehr'),
				8  => WT_I18N::translate_c('Abbreviation for Persian month: Aban', 'Aban'),
				9  => WT_I18N::translate_c('Abbreviation for Persian month: Azar', 'Azar'),
				10 => WT_I18N::translate_c('Abbreviation for Persian month: Dey', 'Dey'),
				11 => WT_I18N::translate_c('Abbreviation for Persian month: Bahman', 'Bah'),
				12 => WT_I18N::translate_c('Abbreviation for Persian month: Esfand', 'Esf'),
			);
		}

		return $translated_month_names[$month_number];
	}
}
