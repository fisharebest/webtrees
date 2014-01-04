<?php
// Classes for Gedcom Date/Calendar functionality.
//
// Definitions for the Roman calendar
// TODO The 5.5.1 gedcom spec mentions this calendar, but gives no details of
// how it is to be represented....  This class is just a place holder so that
// webtrees won’t compain if it receives one.
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

class WT_Date_Roman extends WT_Date_Calendar {
	const CALENDAR_ESCAPE = '@#DROMAN@';

	protected function FormatGedcomYear() {
		return sprintf('%04dAUC', $this->y);
	}

	protected function FormatLongYear() {
		return $this->y . 'AUC';
	}
}
