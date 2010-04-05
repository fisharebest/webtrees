<?php
/**
 * Batch Update plugin for phpGedView - fix spacing in names, particularly that before/after the surname slashes
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2008 Greg Roach.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Module
 * $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class name_format_bu_plugin extends base_plugin {
	static function getName() {
		return i18n::translate('Fix name slashes and spaces');
	}

	static function getDescription() {
		return i18n::translate('Correct NAME records of the form \'John/DOE/\' or \'John /DOE\', as produced by older genealogy programs.');
	}
	
	static function doesRecordNeedUpdate($xref, $gedrec) {
		return
			preg_match('/^(?:1 NAME|2 _MARNM|2 _AKA) [^\/\n]*\/[^\/\n]*$/m', $gedrec) ||
			preg_match('/^(?:1 NAME|2 _MARNM|2 _AKA) [^\/\n]*[^\/ ]\//m', $gedrec);
	}

	static function updateRecord($xref, $gedrec) {
		return preg_replace(
			array(
				'/^((?:1 NAME|2 _MARNM|2 _AKA) [^\/\n]*\/[^\/\n]*)$/m',
				'/^((?:1 NAME|2 _MARNM|2 _AKA) [^\/\n]*[^\/ ])(\/)/m'
			),
			array(
				'$1/',
				'$1 $2'
			),
			$gedrec
		);
	}
}
