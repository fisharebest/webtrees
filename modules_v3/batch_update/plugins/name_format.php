<?php
// Batch Update plugin for phpGedView - fix spacing in names, particularly that before/after the surname slashes
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

class name_format_bu_plugin extends base_plugin {
	static function getName() {
		return WT_I18N::translate('Fix name slashes and spaces');
	}

	static function getDescription() {
		return WT_I18N::translate('Correct NAME records of the form “John/DOE/” or “John /DOE”, as produced by older genealogy programs.');
	}

	static function doesRecordNeedUpdate($xref, $gedrec) {
		return
			preg_match('/^(?:1 NAME|2 (?:FONE|ROMN|_MARNM|_AKA|_HEB)) [^\/\n]*\/[^\/\n]*$/m', $gedrec) ||
			preg_match('/^(?:1 NAME|2 (?:FONE|ROMN|_MARNM|_AKA|_HEB)) [^\/\n]*[^\/ ]\//m', $gedrec);
	}

	static function updateRecord($xref, $gedrec) {
		return preg_replace(
			array(
				'/^((?:1 NAME|2 (?:FONE|ROMN|_MARNM|_AKA|_HEB)) [^\/\n]*\/[^\/\n]*)$/m',
				'/^((?:1 NAME|2 (?:FONE|ROMN|_MARNM|_AKA|_HEB)) [^\/\n]*[^\/ ])(\/)/m'
			),
			array(
				'$1/',
				'$1 $2'
			),
			$gedrec
		);
	}
}
