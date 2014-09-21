<?php
// Classes for Gedcom Date/Calendar functionality.
//
// Definitions for the French Republican calendar
//
// NOTE: Since different calendars start their days at different times, (civil
// midnight, solar midnight, sunset, sunrise, etc.), we convert on the basis of
// midday.
//
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

class WT_Date_French extends WT_Date_Calendar {
	const CALENDAR_ESCAPE = '@#DFRENCH R@';
	const MONTHS_IN_YEAR  = 13;
	const CAL_START_JD    = 2375840; // 22 SEP 1792 = 01 VEND 0001
	const CAL_END_JD      = 2380687; // 31 DEC 1805 = 10 NIVO 0014
	const DAYS_IN_WEEK    = 10; // A metric week of 10 unimaginatively named days.
	static $MONTH_ABBREV  = array(
		''=>0, 'VEND'=>1, 'BRUM'=>2, 'FRIM'=>3, 'NIVO'=>4, 'PLUV'=>5, 'VENT'=>6, 'GERM'=>7, 'FLOR'=>8, 'PRAI'=>9, 'MESS'=>10, 'THER'=>11, 'FRUC'=>12, 'COMP'=>13
	);

	/**
	 * Create a new calendar date
	 *
	 * @param mixed $date
	 */
	public function __construct($date) {
		$this->calendar = new FrenchCalendar;
		parent::__construct($date);
	}

	static function calendarName() {
		return /* I18N: The French calendar */ WT_I18N::translate('French');
	}

	static function monthNameNominativeCase($n, $leap_year) {
		switch ($n) {
		case 1:
			return WT_I18N::translate_c('NOMINATIVE', 'Vendémiaire');
		case 2:
			return WT_I18N::translate_c('NOMINATIVE', 'Brumaire');
		case 3:
			return WT_I18N::translate_c('NOMINATIVE', 'Frimaire');
		case 4:
			return WT_I18N::translate_c('NOMINATIVE', 'Nivôse');
		case 5:
			return WT_I18N::translate_c('NOMINATIVE', 'Pluviôse');
		case 6:
			return WT_I18N::translate_c('NOMINATIVE', 'Ventôse');
		case 7:
			return WT_I18N::translate_c('NOMINATIVE', 'Germinal');
		case 8:
			return WT_I18N::translate_c('NOMINATIVE', 'Floréal');
		case 9:
			return WT_I18N::translate_c('NOMINATIVE', 'Prairial');
		case 10:
			return WT_I18N::translate_c('NOMINATIVE', 'Messidor');
		case 11:
			return WT_I18N::translate_c('NOMINATIVE', 'Thermidor');
		case 12:
			return WT_I18N::translate_c('NOMINATIVE', 'Fructidor');
		case 13:
			return WT_I18N::translate_c('NOMINATIVE', 'jours complémentaires');
		default:
			throw new InvalidArgumentException($n);
		}
	}

	static function monthNameGenitiveCase($n, $leap_year) {
		switch ($n) {
		case 1:
			return WT_I18N::translate_c('GENITIVE', 'Vendémiaire');
		case 2:
			return WT_I18N::translate_c('GENITIVE', 'Brumaire');
		case 3:
			return WT_I18N::translate_c('GENITIVE', 'Frimaire');
		case 4:
			return WT_I18N::translate_c('GENITIVE', 'Nivôse');
		case 5:
			return WT_I18N::translate_c('GENITIVE', 'Pluviôse');
		case 6:
			return WT_I18N::translate_c('GENITIVE', 'Ventôse');
		case 7:
			return WT_I18N::translate_c('GENITIVE', 'Germinal');
		case 8:
			return WT_I18N::translate_c('GENITIVE', 'Floréal');
		case 9:
			return WT_I18N::translate_c('GENITIVE', 'Prairial');
		case 10:
			return WT_I18N::translate_c('GENITIVE', 'Messidor');
		case 11:
			return WT_I18N::translate_c('GENITIVE', 'Thermidor');
		case 12:
			return WT_I18N::translate_c('GENITIVE', 'Fructidor');
		case 13:
			return WT_I18N::translate_c('GENITIVE', 'jours complémentaires');
		default:
			throw new InvalidArgumentException($n);
		}
	}

	static function monthNameLocativeCase($n, $leap_year) {
		switch ($n) {
		case 1:
			return WT_I18N::translate_c('LOCATIVE', 'Vendémiaire');
		case 2:
			return WT_I18N::translate_c('LOCATIVE', 'Brumaire');
		case 3:
			return WT_I18N::translate_c('LOCATIVE', 'Frimaire');
		case 4:
			return WT_I18N::translate_c('LOCATIVE', 'Nivôse');
		case 5:
			return WT_I18N::translate_c('LOCATIVE', 'Pluviôse');
		case 6:
			return WT_I18N::translate_c('LOCATIVE', 'Ventôse');
		case 7:
			return WT_I18N::translate_c('LOCATIVE', 'Germinal');
		case 8:
			return WT_I18N::translate_c('LOCATIVE', 'Floréal');
		case 9:
			return WT_I18N::translate_c('LOCATIVE', 'Prairial');
		case 10:
			return WT_I18N::translate_c('LOCATIVE', 'Messidor');
		case 11:
			return WT_I18N::translate_c('LOCATIVE', 'Thermidor');
		case 12:
			return WT_I18N::translate_c('LOCATIVE', 'Fructidor');
		case 13:
			return WT_I18N::translate_c('LOCATIVE', 'jours complémentaires');
		default:
			throw new InvalidArgumentException($n);
		}
	}

	static function monthNameInstrumentalCase($n, $leap_year) {
		switch ($n) {
		case 1:
			return WT_I18N::translate_c('INSTRUMENTAL', 'Vendémiaire');
		case 2:
			return WT_I18N::translate_c('INSTRUMENTAL', 'Brumaire');
		case 3:
			return WT_I18N::translate_c('INSTRUMENTAL', 'Frimaire');
		case 4:
			return WT_I18N::translate_c('INSTRUMENTAL', 'Nivôse');
		case 5:
			return WT_I18N::translate_c('INSTRUMENTAL', 'Pluviôse');
		case 6:
			return WT_I18N::translate_c('INSTRUMENTAL', 'Ventôse');
		case 7:
			return WT_I18N::translate_c('INSTRUMENTAL', 'Germinal');
		case 8:
			return WT_I18N::translate_c('INSTRUMENTAL', 'Floréal');
		case 9:
			return WT_I18N::translate_c('INSTRUMENTAL', 'Prairial');
		case 10:
			return WT_I18N::translate_c('INSTRUMENTAL', 'Messidor');
		case 11:
			return WT_I18N::translate_c('INSTRUMENTAL', 'Thermidor');
		case 12:
			return WT_I18N::translate_c('INSTRUMENTAL', 'Fructidor');
		case 13:
			return WT_I18N::translate_c('INSTRUMENTAL', 'jours complémentaires');
		default:
			throw new InvalidArgumentException($n);
		}
	}

	static function monthNameAbbreviated($n, $leap_year) {
		return self::monthNameNominativeCase($n, $leap_year);
	}

	static function dayNames($n) {
		switch ($n) {
		case 0:
			return WT_I18N::translate('Primidi');
		case 1:
			return WT_I18N::translate('Duodi');
		case 2:
			return WT_I18N::translate('Tridi');
		case 3:
			return WT_I18N::translate('Quartidi');
		case 4:
			return WT_I18N::translate('Quintidi');
		case 5:
			return WT_I18N::translate('Sextidi');
		case 6:
			return WT_I18N::translate('Septidi');
		case 7:
			return WT_I18N::translate('Octidi');
		case 8:
			return WT_I18N::translate('Nonidi');
		case 9:
			return WT_I18N::translate('Decidi');
		default:
			throw new InvalidArgumentException($n);
		}
	}

	static function dayNamesAbbreviated($n) {
		return self::dayNames($n);
	}

	// Years were written using roman numerals
	protected function formatLongYear() {
		return $this->numberToRomanNumerals($this->y);
	}
}
