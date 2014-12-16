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

use Fisharebest\ExtCalendar\FrenchCalendar;

/**
 * Class WT_Date_French - Definitions for the French Republican calendar
 */
class WT_Date_French extends WT_Date_Calendar {
	const CALENDAR_ESCAPE = '@#DFRENCH R@';
	const MONTHS_IN_YEAR = 13;
	const CAL_START_JD = 2375840; // 22 SEP 1792 = 01 VEND 0001
	const CAL_END_JD = 2380687; // 31 DEC 1805 = 10 NIVO 0014
	const DAYS_IN_WEEK = 10; // A metric week of 10 unimaginatively named days.
	static $MONTH_ABBREV = array('' => 0, 'VEND' => 1, 'BRUM' => 2, 'FRIM' => 3, 'NIVO' => 4, 'PLUV' => 5, 'VENT' => 6, 'GERM' => 7, 'FLOR' => 8, 'PRAI' => 9, 'MESS' => 10, 'THER' => 11, 'FRUC' => 12, 'COMP' => 13);

	/**
	 * {@inheritdoc}
	 */
	public function __construct($date) {
		$this->calendar = new FrenchCalendar;
		parent::__construct($date);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function calendarName() {
		return /* I18N: The French calendar */ WT_I18N::translate('French');
	}

	/**
	 * {@inheritdoc}
	 */
	public static function monthNameNominativeCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('NOMINATIVE', 'Vendémiaire'),
				2  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('NOMINATIVE', 'Brumaire'),
				3  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('NOMINATIVE', 'Frimaire'),
				4  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('NOMINATIVE', 'Nivôse'),
				5  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('NOMINATIVE', 'Pluviôse'),
				6  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('NOMINATIVE', 'Ventôse'),
				7  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('NOMINATIVE', 'Germinal'),
				8  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('NOMINATIVE', 'Floréal'),
				9  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('NOMINATIVE', 'Prairial'),
				10 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('NOMINATIVE', 'Messidor'),
				11 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('NOMINATIVE', 'Thermidor'),
				12 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('NOMINATIVE', 'Fructidor'),
				13 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('NOMINATIVE', 'jours complémentaires'),
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
				1  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('GENITIVE', 'Vendémiaire'),
				2  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('GENITIVE', 'Brumaire'),
				3  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('GENITIVE', 'Frimaire'),
				4  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('GENITIVE', 'Nivôse'),
				5  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('GENITIVE', 'Pluviôse'),
				6  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('GENITIVE', 'Ventôse'),
				7  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('GENITIVE', 'Germinal'),
				8  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('GENITIVE', 'Floréal'),
				9  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('GENITIVE', 'Prairial'),
				10 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('GENITIVE', 'Messidor'),
				11 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('GENITIVE', 'Thermidor'),
				12 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('GENITIVE', 'Fructidor'),
				13 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('GENITIVE', 'jours complémentaires'),
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
				1  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('LOCATIVE', 'Vendémiaire'),
				2  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('LOCATIVE', 'Brumaire'),
				3  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('LOCATIVE', 'Frimaire'),
				4  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('LOCATIVE', 'Nivôse'),
				5  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('LOCATIVE', 'Pluviôse'),
				6  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('LOCATIVE', 'Ventôse'),
				7  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('LOCATIVE', 'Germinal'),
				8  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('LOCATIVE', 'Floréal'),
				9  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('LOCATIVE', 'Prairial'),
				10 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('LOCATIVE', 'Messidor'),
				11 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('LOCATIVE', 'Thermidor'),
				12 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('LOCATIVE', 'Fructidor'),
				13 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('LOCATIVE', 'jours complémentaires'),
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
				1  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Vendémiaire'),
				2  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Brumaire'),
				3  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Frimaire'),
				4  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Nivôse'),
				5  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Pluviôse'),
				6  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Ventôse'),
				7  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Germinal'),
				8  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Floréal'),
				9  => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Prairial'),
				10 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Messidor'),
				11 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Thermidor'),
				12 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Fructidor'),
				13 => /* I18N: a month in the French republican calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'jours complémentaires'),
			);
		}

		return $translated_month_names[$month_number];
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
	public static function dayNames($day_number) {
		static $translated_day_names;

		if ($translated_day_names === null) {
			$translated_day_names = array(
				0  => /* I18N: a day in the French republican calendar */ WT_I18N::translate('Primidi'),
				1  => /* I18N: a day in the French republican calendar */ WT_I18N::translate('Duodi'),
				2  => /* I18N: a day in the French republican calendar */ WT_I18N::translate('Tridi'),
				3  => /* I18N: a day in the French republican calendar */ WT_I18N::translate('Quartidi'),
				4  => /* I18N: a day in the French republican calendar */ WT_I18N::translate('Quintidi'),
				5  => /* I18N: a day in the French republican calendar */ WT_I18N::translate('Sextidi'),
				6  => /* I18N: a day in the French republican calendar */ WT_I18N::translate('Septidi'),
				7  => /* I18N: a day in the French republican calendar */ WT_I18N::translate('Octidi'),
				8  => /* I18N: a day in the French republican calendar */ WT_I18N::translate('Nonidi'),
				9  => /* I18N: a day in the French republican calendar */ WT_I18N::translate('Decidi'),
			);
		}

		return $translated_day_names[$day_number];
	}

	/**
	 * {@inheritdoc}
	 */
	protected static function dayNamesAbbreviated($day_number) {
		return self::dayNames($day_number);
	}

	/**
	 * Years were written using roman numerals
	 *
	 * {@inheritdoc}
	 */
	protected function formatLongYear() {
		return $this->numberToRomanNumerals($this->y);
	}
}
