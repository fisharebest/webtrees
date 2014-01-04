<?php
// Classes for Gedcom Date/Calendar functionality.
//
// Definitions for the Julian Proleptic calendar
// (Proleptic means we extend it backwards, prior to its introduction in 46BC)
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Date_Julian extends WT_Date_Calendar {
	const CALENDAR_ESCAPE = '@#DJULIAN@';

	var $new_old_style=false;

	static function calendarName() {
		return /* I18N: The julian calendar */ WT_I18N::translate('Julian');
	}

	static function NextYear($y) {
		if ($y==-1)
			return 1;
		else
			return $y+1;
	}

	function IsLeapYear() {
		if ($this->y>0) {
			return $this->y%4==0;
		} else {
			return $this->y%4==-1;
		}
	}

	static function YMDtoJD($y, $m, $d) {
		if ($y<0) // 0=1BC, -1=2BC, etc.
			++$y;
		$a=(int)((14-$m)/12);
		$y=$y+4800-$a;
		$m=$m+12*$a-3;
		return $d+(int)((153*$m+2)/5)+365*$y+(int)($y/4)-32083;
	}

	static function JDtoYMD($j) {
		$c=$j+32082;
		$d=(int)((4*$c+3)/1461);
		$e=$c-(int)(1461*$d/4);
		$m=(int)((5*$e+2)/153);
		$day=$e-(int)((153*$m+2)/5)+1;
		$month=$m+3-12*(int)($m/10);
		$year=$d-4800+(int)($m/10);
		if ($year<1) {
			// 0=1BC, -1=2BC, etc.
			--$year;
		}
		return array($year, $month, $day);
	}

	// Process new-style/old-style years and years BC
	public function ExtractYear($year) {
		if (preg_match('/^(\d\d\d\d)\/\d{1,4}$/', $year, $match)) { // Assume the first year is correct
			$this->new_old_style=true;
			return $match[1]+1;
		} else
			if (preg_match('/^(\d+) B\.C\.$/', $year, $match))
				return -$match[1];
			else
				return (int)$year;
	}

	protected function FormatLongYear() {
		if ($this->y<0) {
			return /*  I18N: BCE=Before the Common Era, for Julian years < 0.  See http://en.wikipedia.org/wiki/Common_Era */ WT_I18N::translate('%s&nbsp;BCE', WT_I18N::digits(-$this->y));
		} else {
			if ($this->new_old_style) {
				return WT_I18N::translate('%s&nbsp;CE', WT_I18N::digits(sprintf('%d/%02d', $this->y-1, $this->y % 100)));
			} else
				return /* I18N: CE=Common Era, for Julian years > 0.  See http://en.wikipedia.org/wiki/Common_Era */ WT_I18N::translate('%s&nbsp;CE', WT_I18N::digits($this->y));
		}
	}

	protected function FormatGedcomYear() {
		if ($this->y<0) {
			return sprintf('%04d B.C.', -$this->y);
		} else {
			if ($this->new_old_style) {
				return sprintf('%04d/%02d', $this->y-1, $this->y % 100);
			} else {
				return sprintf('%04d', $this->y);
			}
		}
	}
}
