<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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

use Fisharebest\ExtCalendar\FrenchCalendar;
use Fisharebest\Webtrees\I18N;

/**
 * Definitions for the French Republican calendar
 */
class FrenchDate extends CalendarDate {
	/** @var int[] Convert GEDCOM month names to month numbers  */
	public static $MONTH_ABBREV = array('' => 0, 'VEND' => 1, 'BRUM' => 2, 'FRIM' => 3, 'NIVO' => 4, 'PLUV' => 5, 'VENT' => 6, 'GERM' => 7, 'FLOR' => 8, 'PRAI' => 9, 'MESS' => 10, 'THER' => 11, 'FRUC' => 12, 'COMP' => 13);

	/**
	 * Create a date from either:
	 * a Julian day number
	 * day/month/year strings from a GEDCOM date
	 * another CalendarDate object
	 *
	 * @param array|int|CalendarDate $date
	 */
	public function __construct($date) {
		$this->calendar = new FrenchCalendar;
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
				1  => /* I18N: a month in the French republican calendar */ I18N::translateContext('NOMINATIVE', 'Vendemiaire'),
				2  => /* I18N: a month in the French republican calendar */ I18N::translateContext('NOMINATIVE', 'Brumaire'),
				3  => /* I18N: a month in the French republican calendar */ I18N::translateContext('NOMINATIVE', 'Frimaire'),
				4  => /* I18N: a month in the French republican calendar */ I18N::translateContext('NOMINATIVE', 'Nivose'),
				5  => /* I18N: a month in the French republican calendar */ I18N::translateContext('NOMINATIVE', 'Pluviose'),
				6  => /* I18N: a month in the French republican calendar */ I18N::translateContext('NOMINATIVE', 'Ventose'),
				7  => /* I18N: a month in the French republican calendar */ I18N::translateContext('NOMINATIVE', 'Germinal'),
				8  => /* I18N: a month in the French republican calendar */ I18N::translateContext('NOMINATIVE', 'Floreal'),
				9  => /* I18N: a month in the French republican calendar */ I18N::translateContext('NOMINATIVE', 'Prairial'),
				10 => /* I18N: a month in the French republican calendar */ I18N::translateContext('NOMINATIVE', 'Messidor'),
				11 => /* I18N: a month in the French republican calendar */ I18N::translateContext('NOMINATIVE', 'Thermidor'),
				12 => /* I18N: a month in the French republican calendar */ I18N::translateContext('NOMINATIVE', 'Fructidor'),
				13 => /* I18N: a month in the French republican calendar */ I18N::translateContext('NOMINATIVE', 'jours complementaires'),
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
				1  => /* I18N: a month in the French republican calendar */ I18N::translateContext('GENITIVE', 'Vendemiaire'),
				2  => /* I18N: a month in the French republican calendar */ I18N::translateContext('GENITIVE', 'Brumaire'),
				3  => /* I18N: a month in the French republican calendar */ I18N::translateContext('GENITIVE', 'Frimaire'),
				4  => /* I18N: a month in the French republican calendar */ I18N::translateContext('GENITIVE', 'Nivose'),
				5  => /* I18N: a month in the French republican calendar */ I18N::translateContext('GENITIVE', 'Pluviose'),
				6  => /* I18N: a month in the French republican calendar */ I18N::translateContext('GENITIVE', 'Ventose'),
				7  => /* I18N: a month in the French republican calendar */ I18N::translateContext('GENITIVE', 'Germinal'),
				8  => /* I18N: a month in the French republican calendar */ I18N::translateContext('GENITIVE', 'Floreal'),
				9  => /* I18N: a month in the French republican calendar */ I18N::translateContext('GENITIVE', 'Prairial'),
				10 => /* I18N: a month in the French republican calendar */ I18N::translateContext('GENITIVE', 'Messidor'),
				11 => /* I18N: a month in the French republican calendar */ I18N::translateContext('GENITIVE', 'Thermidor'),
				12 => /* I18N: a month in the French republican calendar */ I18N::translateContext('GENITIVE', 'Fructidor'),
				13 => /* I18N: a month in the French republican calendar */ I18N::translateContext('GENITIVE', 'jours complementaires'),
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
				1  => /* I18N: a month in the French republican calendar */ I18N::translateContext('LOCATIVE', 'Vendemiaire'),
				2  => /* I18N: a month in the French republican calendar */ I18N::translateContext('LOCATIVE', 'Brumaire'),
				3  => /* I18N: a month in the French republican calendar */ I18N::translateContext('LOCATIVE', 'Frimaire'),
				4  => /* I18N: a month in the French republican calendar */ I18N::translateContext('LOCATIVE', 'Nivose'),
				5  => /* I18N: a month in the French republican calendar */ I18N::translateContext('LOCATIVE', 'Pluviose'),
				6  => /* I18N: a month in the French republican calendar */ I18N::translateContext('LOCATIVE', 'Ventose'),
				7  => /* I18N: a month in the French republican calendar */ I18N::translateContext('LOCATIVE', 'Germinal'),
				8  => /* I18N: a month in the French republican calendar */ I18N::translateContext('LOCATIVE', 'Floreal'),
				9  => /* I18N: a month in the French republican calendar */ I18N::translateContext('LOCATIVE', 'Prairial'),
				10 => /* I18N: a month in the French republican calendar */ I18N::translateContext('LOCATIVE', 'Messidor'),
				11 => /* I18N: a month in the French republican calendar */ I18N::translateContext('LOCATIVE', 'Thermidor'),
				12 => /* I18N: a month in the French republican calendar */ I18N::translateContext('LOCATIVE', 'Fructidor'),
				13 => /* I18N: a month in the French republican calendar */ I18N::translateContext('LOCATIVE', 'jours complementaires'),
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
				1  => /* I18N: a month in the French republican calendar */ I18N::translateContext('INSTRUMENTAL', 'Vendemiaire'),
				2  => /* I18N: a month in the French republican calendar */ I18N::translateContext('INSTRUMENTAL', 'Brumaire'),
				3  => /* I18N: a month in the French republican calendar */ I18N::translateContext('INSTRUMENTAL', 'Frimaire'),
				4  => /* I18N: a month in the French republican calendar */ I18N::translateContext('INSTRUMENTAL', 'Nivose'),
				5  => /* I18N: a month in the French republican calendar */ I18N::translateContext('INSTRUMENTAL', 'Pluviose'),
				6  => /* I18N: a month in the French republican calendar */ I18N::translateContext('INSTRUMENTAL', 'Ventose'),
				7  => /* I18N: a month in the French republican calendar */ I18N::translateContext('INSTRUMENTAL', 'Germinal'),
				8  => /* I18N: a month in the French republican calendar */ I18N::translateContext('INSTRUMENTAL', 'Floreal'),
				9  => /* I18N: a month in the French republican calendar */ I18N::translateContext('INSTRUMENTAL', 'Prairial'),
				10 => /* I18N: a month in the French republican calendar */ I18N::translateContext('INSTRUMENTAL', 'Messidor'),
				11 => /* I18N: a month in the French republican calendar */ I18N::translateContext('INSTRUMENTAL', 'Thermidor'),
				12 => /* I18N: a month in the French republican calendar */ I18N::translateContext('INSTRUMENTAL', 'Fructidor'),
				13 => /* I18N: a month in the French republican calendar */ I18N::translateContext('INSTRUMENTAL', 'jours complementaires'),
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
		return self::monthNameNominativeCase($month_number, $leap_year);
	}

	/**
	 * Full day of th eweek
	 *
	 * @param int $day_number
	 *
	 * @return string
	 */
	public function dayNames($day_number) {
		static $translated_day_names;

		if ($translated_day_names === null) {
			$translated_day_names = array(
				0  => /* I18N: The first day in the French republican calendar */ I18N::translate('Primidi'),
				1  => /* I18N: The second day in the French republican calendar */ I18N::translate('Duodi'),
				2  => /* I18N: The third day in the French republican calendar */ I18N::translate('Tridi'),
				3  => /* I18N: The fourth day in the French republican calendar */ I18N::translate('Quartidi'),
				4  => /* I18N: The fifth day in the French republican calendar */ I18N::translate('Quintidi'),
				5  => /* I18N: The sixth day in the French republican calendar */ I18N::translate('Sextidi'),
				6  => /* I18N: The seventh day in the French republican calendar */ I18N::translate('Septidi'),
				7  => /* I18N: The eighth day in the French republican calendar */ I18N::translate('Octidi'),
				8  => /* I18N: The ninth day in the French republican calendar */ I18N::translate('Nonidi'),
				9  => /* I18N: The tenth day in the French republican calendar */ I18N::translate('Decidi'),
			);
		}

		return $translated_day_names[$day_number];
	}

	/**
	 * Abbreviated day of the week
	 *
	 * @param int $day_number
	 *
	 * @return string
	 */
	protected function dayNamesAbbreviated($day_number) {
		return $this->dayNames($day_number);
	}

	/**
	 * Generate the %Y format for a date.
	 *
	 * @return string
	 */
	protected function formatLongYear() {
		return $this->numberToRomanNumerals($this->y);
	}
}
