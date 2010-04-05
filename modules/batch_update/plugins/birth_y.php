<?php
/**
 * Batch Update plugin for phpGedView - add missing 1 BIRT/DEAT Y
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

class birth_y_bu_plugin extends base_plugin {
	static function getName() {
		return i18n::translate('Add missing birth records');
	}

	static function getDescription() {
		return i18n::translate('You can improve the performance of PGV by ensuring that all individuals have a &laquo;start of life&raquo; event.');
	}
	
	static function doesRecordNeedUpdate($xref, $gedrec) {
		return !preg_match('/^1\s+'.WT_EVENTS_BIRT.'\b/m', $gedrec);
	}

	static function updateRecord($xref, $gedrec) {
		return $gedrec."\n1 BIRT Y";
	}
}
