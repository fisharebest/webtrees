<?php
// Classes for Gedcom Date/Calendar functionality.
//
// Definitions for the Arabic calendar.
// NOTE - this is the same as the Hijri Calendar, but displays dates in arabic
// rather than the local language.
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

class WT_Date_Arabic extends WT_Date_Hijri {
	static $ARABIC_DAYS=array("الأثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعه", "السبت", "الأحد");

	static function NUM_TO_MONTH_NOMINATIVE($n, $leap_year) {
		// Do not translate these - they are supposed to be arabic, whatever language is shown.
		switch ($n) {
		case 1:  return 'محرّم';
		case 2:  return 'صفر';
		case 3:  return 'ربيع الأول';
		case 4:  return 'ربيع الثانى';
		case 5:  return 'جمادى الأول';
		case 6:  return 'جمادى الثاني';
		case 7:  return 'رجب';
		case 8:  return 'شعبان';
		case 9:  return 'رمضان';
		case 10: return 'شوّال';
		case 11: return 'ذو القعدة';
		case 12: return 'ذو الحجة';
		default: return '';
		}
	}
	static function NUM_TO_MONTH_GENITIVE($n, $leap_year) {
		// Arabic does not have genitive forms
		return self::NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}
	static function NUM_TO_MONTH_LOCATIVE($n, $leap_year) {
		// Arabic does not have locative forms
		return self::NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}

	static function NUM_TO_MONTH_INSTRUMENTAL($n, $leap_year) {
		// Arabic does not have instrumental forms
		return self::NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}

	static function NUM_TO_SHORT_MONTH($n, $leap_year) {
		// TODO: Do these have short names?
		return self::NUM_TO_MONTH_NOMINATIVE($n, $leap_year);
	}

	function FormatLongWeekday() {
		return $this->$ARABIC_DAYS[$this->minJD % $this->NUM_DAYS_OF_WEEK()];
	}

	function FormatShortWeekday() {
		return $this->$ARABIC_DAYS[$this->minJD % $this->NUM_DAYS_OF_WEEK()];
	}
}
