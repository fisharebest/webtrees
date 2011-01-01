<?php
// Classes for Gedcom Date/Calendar functionality.
//
// Definitions for the Hijri calendar.  Note that these are "theoretical" dates.
// "True" dates are based on local lunar observations, and can be a +/- one day.
//
// NOTE: Since different calendars start their days at different times, (civil
// midnight, solar midnight, sunset, sunrise, etc.), we convert on the basis of
// midday.
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// @author Greg Roach
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Date_Hijri extends WT_Date_Calendar {
	static function CALENDAR_ESCAPE() {
		return '@#DHIJRI@';
	}
	static function MONTH_TO_NUM($m) {
		static $months=array(''=>0, 'muhar'=>1, 'safar'=>2, 'rabia'=>3, 'rabit'=>4, 'jumaa'=>5, 'jumat'=>6, 'rajab'=>7, 'shaab'=>8, 'ramad'=>9, 'shaww'=>10, 'dhuaq'=>11, 'dhuah'=>12);
		if (isset($months[$m])) {
			return $months[$m];
		} else {
			return null;
		}
	}
	static function NUM_TO_MONTH_NOMINATIVE($n, $leap_year) {
		switch ($n) {
		case 1:  return WT_I18N::translate_c('NOMINATIVE', 'Muharram');
		case 2:  return WT_I18N::translate_c('NOMINATIVE', 'Safar');
		case 3:  return WT_I18N::translate_c('NOMINATIVE', 'Rabi\' al-awwal');
		case 4:  return WT_I18N::translate_c('NOMINATIVE', 'Rabi\' al-thani');
		case 5:  return WT_I18N::translate_c('NOMINATIVE', 'Jumada al-awwal');
		case 6:  return WT_I18N::translate_c('NOMINATIVE', 'Jumada al-thani');
		case 7:  return WT_I18N::translate_c('NOMINATIVE', 'Rajab');
		case 8:  return WT_I18N::translate_c('NOMINATIVE', 'Sha\'aban');
		case 9:  return WT_I18N::translate_c('NOMINATIVE', 'Ramadan');
		case 10: return WT_I18N::translate_c('NOMINATIVE', 'Shawwal');
		case 11: return WT_I18N::translate_c('NOMINATIVE', 'Dhu al-Qi\'dah');
		case 12: return WT_I18N::translate_c('NOMINATIVE', 'Dhu al-Hijjah');
		default: return '';
		}
	}
	static function NUM_TO_MONTH_GENITIVE($n, $leap_year) {
		switch ($n) {
		case 1:  return WT_I18N::translate_c('GENITIVE', 'Muharram');
		case 2:  return WT_I18N::translate_c('GENITIVE', 'Safar');
		case 3:  return WT_I18N::translate_c('GENITIVE', 'Rabi\' al-awwal');
		case 4:  return WT_I18N::translate_c('GENITIVE', 'Rabi\' al-thani');
		case 5:  return WT_I18N::translate_c('GENITIVE', 'Jumada al-awwal');
		case 6:  return WT_I18N::translate_c('GENITIVE', 'Jumada al-thani');
		case 7:  return WT_I18N::translate_c('GENITIVE', 'Rajab');
		case 8:  return WT_I18N::translate_c('GENITIVE', 'Sha\'aban');
		case 9:  return WT_I18N::translate_c('GENITIVE', 'Ramadan');
		case 10: return WT_I18N::translate_c('GENITIVE', 'Shawwal');
		case 11: return WT_I18N::translate_c('GENITIVE', 'Dhu al-Qi\'dah');
		case 12: return WT_I18N::translate_c('GENITIVE', 'Dhu al-Hijjah');
		default: return '';
		}
	}
	static function NUM_TO_MONTH_LOCATIVE($n, $leap_year) {
		switch ($n) {
		case 1:  return WT_I18N::translate_c('LOCATIVE', 'Muharram');
		case 2:  return WT_I18N::translate_c('LOCATIVE', 'Safar');
		case 3:  return WT_I18N::translate_c('LOCATIVE', 'Rabi\' al-awwal');
		case 4:  return WT_I18N::translate_c('LOCATIVE', 'Rabi\' al-thani');
		case 5:  return WT_I18N::translate_c('LOCATIVE', 'Jumada al-awwal');
		case 6:  return WT_I18N::translate_c('LOCATIVE', 'Jumada al-thani');
		case 7:  return WT_I18N::translate_c('LOCATIVE', 'Rajab');
		case 8:  return WT_I18N::translate_c('LOCATIVE', 'Sha\'aban');
		case 9:  return WT_I18N::translate_c('LOCATIVE', 'Ramadan');
		case 10: return WT_I18N::translate_c('LOCATIVE', 'Shawwal');
		case 11: return WT_I18N::translate_c('LOCATIVE', 'Dhu al-Qi\'dah');
		case 12: return WT_I18N::translate_c('LOCATIVE', 'Dhu al-Hijjah');
		default: return '';
		}
	}
	static function NUM_TO_MONTH_INSTRUMENTAL($n, $leap_year) {
		switch ($n) {
		case 1:  return WT_I18N::translate_c('INSTRUMENTAL', 'Muharram');
		case 2:  return WT_I18N::translate_c('INSTRUMENTAL', 'Safar');
		case 3:  return WT_I18N::translate_c('INSTRUMENTAL', 'Rabi\' al-awwal');
		case 4:  return WT_I18N::translate_c('INSTRUMENTAL', 'Rabi\' al-thani');
		case 5:  return WT_I18N::translate_c('INSTRUMENTAL', 'Jumada al-awwal');
		case 6:  return WT_I18N::translate_c('INSTRUMENTAL', 'Jumada al-thani');
		case 7:  return WT_I18N::translate_c('INSTRUMENTAL', 'Rajab');
		case 8:  return WT_I18N::translate_c('INSTRUMENTAL', 'Sha\'aban');
		case 9:  return WT_I18N::translate_c('INSTRUMENTAL', 'Ramadan');
		case 10: return WT_I18N::translate_c('INSTRUMENTAL', 'Shawwal');
		case 11: return WT_I18N::translate_c('INSTRUMENTAL', 'Dhu al-Qi\'dah');
		case 12: return WT_I18N::translate_c('INSTRUMENTAL', 'Dhu al-Hijjah');
		default: return '';
		}
	}
	static function NUM_TO_SHORT_MONTH($n, $leap_year) {
		// TODO: Do these have short names?
		return self::NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}
	static function NUM_TO_GEDCOM_MONTH($n, $leap_year) {
		switch ($n) {
		case 1:  return 'MUHAR';
		case 2:  return 'SAFAR';
		case 3:  return 'RABIA';
		case 4:  return 'RABIT';
		case 5:  return 'JUMAA';
		case 6:  return 'JUMAT';
		case 7:  return 'RAJAB';
		case 8:  return 'SHAAB';
		case 9:  return 'RAMAD';
		case 10: return 'SHAWW';
		case 11: return 'DHUAQ';
		case 12: return 'DHUAH';
		default: return '';
		}
	}
	static function CAL_START_JD() {
		return 1948440; // @#DHIJRI@ 1 MUHAR 0001 = @#JULIAN@ 16 JUL 0622
	}

	function IsLeapYear() {
		return ((11*$this->y+14)%30)<11;
	}

	static function YMDtoJD($y, $m, $d) {
		return $d+29*($m-1)+floor((6*$m-1)/11)+$y*354+floor((3+11*$y)/30)+1948085;
	}

	static function JDtoYMD($j) {
		$y=floor((30*($j-1948440)+10646)/10631);
		$m=floor((11*($j-$y*354-floor((3+11*$y)/30)-1948086)+330)/325);
		$d=$j-29*($m-1)-floor((6*$m-1)/11)-$y*354-floor((3+11*$y)/30)-1948085;
		return array($y, $m, $d);
	}
}
