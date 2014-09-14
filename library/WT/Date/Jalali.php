<?php
// Classes for Gedcom Date/Calendar functionality.
//
// Definitions for the Jalali calendar
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

use Fisharebest\ExtCalendar\PersianCalendar;

class WT_Date_Jalali extends WT_Date_Calendar {
	const CALENDAR_ESCAPE = '@#DJALALI@';
	const CAL_START_JD    = 1948321;
	static $MONTH_ABBREV  = array(
		''=>0, 'FARVA'=>1, 'ORDIB'=>2, 'KHORD'=>3, 'TIR'=>4, 'MORDA'=>5, 'SHAHR'=>6, 'MEHR'=>7, 'ABAN'=>8, 'AZAR'=>9, 'DEY'=>10, 'BAHMA'=>11, 'ESFAN'=>12
	);

	/**
	 * Create a new calendar date
	 *
	 * @param mixed $date
	 */
	public function __construct($date) {
		$this->calendar = new PersianCalendar;
		parent::__construct($date);
	}

	static function calendarName() {
		return /* I18N: The Persian/Jalali calendar */ WT_I18N::translate('Jalali');
	}

	static function monthNameNominativeCase($n, $leap_year) {
		switch ($n) {
		case 1:  return /* I18N:  1st month in the Persian/Jalali calendar */ WT_I18N::translate_c('NOMINATIVE', 'Farvardin'  );
		case 2:  return /* I18N:  2nd month in the Persian/Jalali calendar */ WT_I18N::translate_c('NOMINATIVE', 'Ordibehesht');
		case 3:  return /* I18N:  3rd month in the Persian/Jalali calendar */ WT_I18N::translate_c('NOMINATIVE', 'Khordad'    );
		case 4:  return /* I18N:  4th month in the Persian/Jalali calendar */ WT_I18N::translate_c('NOMINATIVE', 'Tir'        );
		case 5:  return /* I18N:  5th month in the Persian/Jalali calendar */ WT_I18N::translate_c('NOMINATIVE', 'Mordad'     );
		case 6:  return /* I18N:  6th month in the Persian/Jalali calendar */ WT_I18N::translate_c('NOMINATIVE', 'Shahrivar'  );
		case 7:  return /* I18N:  7th month in the Persian/Jalali calendar */ WT_I18N::translate_c('NOMINATIVE', 'Mehr'       );
		case 8:  return /* I18N:  8th month in the Persian/Jalali calendar */ WT_I18N::translate_c('NOMINATIVE', 'Aban'       );
		case 9:  return /* I18N:  9th month in the Persian/Jalali calendar */ WT_I18N::translate_c('NOMINATIVE', 'Azar'       );
		case 10: return /* I18N: 10th month in the Persian/Jalali calendar */ WT_I18N::translate_c('NOMINATIVE', 'Dey'        );
		case 11: return /* I18N: 11th month in the Persian/Jalali calendar */ WT_I18N::translate_c('NOMINATIVE', 'Bahman'     );
		case 12: return /* I18N: 12th month in the Persian/Jalali calendar */ WT_I18N::translate_c('NOMINATIVE', 'Esfand'     );
		default: return '';
		}
	}

	static function monthNameGenitiveCase($n, $leap_year) {
		switch ($n) {
		case 1:  return /* I18N:  1st month in the Persian/Jalali calendar */ WT_I18N::translate_c('GENITIVE', 'Farvardin'  );
		case 2:  return /* I18N:  2nd month in the Persian/Jalali calendar */ WT_I18N::translate_c('GENITIVE', 'Ordibehesht');
		case 3:  return /* I18N:  3rd month in the Persian/Jalali calendar */ WT_I18N::translate_c('GENITIVE', 'Khordad'    );
		case 4:  return /* I18N:  4th month in the Persian/Jalali calendar */ WT_I18N::translate_c('GENITIVE', 'Tir'        );
		case 5:  return /* I18N:  5th month in the Persian/Jalali calendar */ WT_I18N::translate_c('GENITIVE', 'Mordad'     );
		case 6:  return /* I18N:  6th month in the Persian/Jalali calendar */ WT_I18N::translate_c('GENITIVE', 'Shahrivar'  );
		case 7:  return /* I18N:  7th month in the Persian/Jalali calendar */ WT_I18N::translate_c('GENITIVE', 'Mehr'       );
		case 8:  return /* I18N:  8th month in the Persian/Jalali calendar */ WT_I18N::translate_c('GENITIVE', 'Aban'       );
		case 9:  return /* I18N:  9th month in the Persian/Jalali calendar */ WT_I18N::translate_c('GENITIVE', 'Azar'       );
		case 10: return /* I18N: 10th month in the Persian/Jalali calendar */ WT_I18N::translate_c('GENITIVE', 'Dey'        );
		case 11: return /* I18N: 11th month in the Persian/Jalali calendar */ WT_I18N::translate_c('GENITIVE', 'Bahman'     );
		case 12: return /* I18N: 12th month in the Persian/Jalali calendar */ WT_I18N::translate_c('GENITIVE', 'Esfand'     );
		default: return '';
		}
	}

	static function monthNameLocativeCase($n, $leap_year) {
		switch ($n) {
		case 1:  return /* I18N:  1st month in the Persian/Jalali calendar */ WT_I18N::translate_c('LOCATIVE', 'Farvardin'  );
		case 2:  return /* I18N:  2nd month in the Persian/Jalali calendar */ WT_I18N::translate_c('LOCATIVE', 'Ordibehesht');
		case 3:  return /* I18N:  3rd month in the Persian/Jalali calendar */ WT_I18N::translate_c('LOCATIVE', 'Khordad'    );
		case 4:  return /* I18N:  4th month in the Persian/Jalali calendar */ WT_I18N::translate_c('LOCATIVE', 'Tir'        );
		case 5:  return /* I18N:  5th month in the Persian/Jalali calendar */ WT_I18N::translate_c('LOCATIVE', 'Mordad'     );
		case 6:  return /* I18N:  6th month in the Persian/Jalali calendar */ WT_I18N::translate_c('LOCATIVE', 'Shahrivar'  );
		case 7:  return /* I18N:  7th month in the Persian/Jalali calendar */ WT_I18N::translate_c('LOCATIVE', 'Mehr'       );
		case 8:  return /* I18N:  8th month in the Persian/Jalali calendar */ WT_I18N::translate_c('LOCATIVE', 'Aban'       );
		case 9:  return /* I18N:  9th month in the Persian/Jalali calendar */ WT_I18N::translate_c('LOCATIVE', 'Azar'       );
		case 10: return /* I18N: 10th month in the Persian/Jalali calendar */ WT_I18N::translate_c('LOCATIVE', 'Dey'        );
		case 11: return /* I18N: 11th month in the Persian/Jalali calendar */ WT_I18N::translate_c('LOCATIVE', 'Bahman'     );
		case 12: return /* I18N: 12th month in the Persian/Jalali calendar */ WT_I18N::translate_c('LOCATIVE', 'Esfand'     );
		default: return '';
		}
	}

	static function monthNameInstrumentalCase($n, $leap_year) {
		switch ($n) {
		case 1:  return /* I18N:  1st month in the Persian/Jalali calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Farvardin'  );
		case 2:  return /* I18N:  2nd month in the Persian/Jalali calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Ordibehesht');
		case 3:  return /* I18N:  3rd month in the Persian/Jalali calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Khordad'    );
		case 4:  return /* I18N:  4th month in the Persian/Jalali calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Tir'        );
		case 5:  return /* I18N:  5th month in the Persian/Jalali calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Mordad'     );
		case 6:  return /* I18N:  6th month in the Persian/Jalali calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Shahrivar'  );
		case 7:  return /* I18N:  7th month in the Persian/Jalali calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Mehr'       );
		case 8:  return /* I18N:  8th month in the Persian/Jalali calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Aban'       );
		case 9:  return /* I18N:  9th month in the Persian/Jalali calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Azar'       );
		case 10: return /* I18N: 10th month in the Persian/Jalali calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Dey'        );
		case 11: return /* I18N: 11th month in the Persian/Jalali calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Bahman'     );
		case 12: return /* I18N: 12th month in the Persian/Jalali calendar */ WT_I18N::translate_c('INSTRUMENTAL', 'Esfand'     );
		default: return '';
		}
	}

	static function monthNameAbbreviated($n, $leap_year) {
		switch ($n) {
		case 1:  return WT_I18N::translate_c('Abbreviation for Persian month: Farvardin',   'Far' );
		case 2:  return WT_I18N::translate_c('Abbreviation for Persian month: Ordibehesht', 'Ord' );
		case 3:  return WT_I18N::translate_c('Abbreviation for Persian month: Khordad',     'Khor');
		case 4:  return WT_I18N::translate_c('Abbreviation for Persian month: Tir',         'Tir' );
		case 5:  return WT_I18N::translate_c('Abbreviation for Persian month: Mordad',      'Mor' );
		case 6:  return WT_I18N::translate_c('Abbreviation for Persian month: Shahrivar',   'Shah');
		case 7:  return WT_I18N::translate_c('Abbreviation for Persian month: Mehr',        'Mehr');
		case 8:  return WT_I18N::translate_c('Abbreviation for Persian month: Aban',        'Aban');
		case 9:  return WT_I18N::translate_c('Abbreviation for Persian month: Azar',        'Azar');
		case 10: return WT_I18N::translate_c('Abbreviation for Persian month: Dey',         'Dey' );
		case 11: return WT_I18N::translate_c('Abbreviation for Persian month: Bahman',      'Bah' );
		case 12: return WT_I18N::translate_c('Abbreviation for Persian month: Esfand',      'Esf' );
		default: return '';
		}
	}
}
