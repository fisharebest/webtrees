<?php
// Classes for Gedcom Date/Calendar functionality.
//
// Definitions for the Gregorian calendar
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

class WT_Date_Gregorian extends WT_Date_Calendar {
	static function CALENDAR_ESCAPE() {
		return '@#DGREGORIAN@';
	}
	static function CAL_START_JD() {
		return 2299161; // 15 OCT 1582
	}

	function IsLeapYear() {
		return $this->y%4==0 && $this->y%100!=0 || $this->y%400==0;
	}

	static function YMDtoJD($y, $m, $d) {
		if ($y<0) // 0=1BC, -1=2BC, etc.
			++$y;
		$a=floor((14-$m)/12);
		$y=$y+4800-$a;
		$m=$m+12*$a-3;
		return $d+floor((153*$m+2)/5)+365*$y+floor($y/4)-floor($y/100)+floor($y/400)-32045;
	}

	static function JDtoYMD($j) {
		$a=$j+32044;
		$b=floor((4*$a+3)/146097);
		$c=$a-floor($b*146097/4);
		$d=floor((4*$c+3)/1461);
		$e=$c-floor((1461*$d)/4);
		$m=floor((5*$e+2)/153);
		$day=$e-floor((153*$m+2)/5)+1;
		$month=$m+3-12*floor($m/10);
		$year=$b*100+$d-4800+floor($m/10);
		if ($year<1) // 0=1BC, -1=2BC, etc.
			--$year;
		return array($year, $month, $day);
	}
}
