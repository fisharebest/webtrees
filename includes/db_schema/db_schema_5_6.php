<?php
// Update the database schema from version 5 to version 6
// - changes to the values for the gedcom setting SHOW_RELATIVES_EVENTS
//
// The script should assume that it can be interrupted at
// any point, and be able to continue by re-running the script.
// Fatal errors, however, should be allowed to throw exceptions,
// which will be caught by the framework.
// It shouldn't do anything that might take more than a few
// seconds, for systems with low timeout values.
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

$settings = WT_DB::prepare(
	"SELECT gedcom_id, setting_value FROM `##gedcom_setting` WHERE setting_name='SHOW_RELATIVES_EVENTS'"
)->fetchAssoc();

foreach ($settings as $gedcom_id=>$setting) {
	// Delete old settings
	$setting = preg_replace('/_(BIRT|MARR|DEAT)_(COUS|MSIB|FSIB|GGCH|NEPH|GGPA)/', '', $setting);
	$setting = preg_replace('/_FAMC_(RESI_EMIG)/', '', $setting);
	// Rename settings
	$setting = preg_replace('/_MARR_(MOTH|FATH|FAMC)/', '_MARR_PARE', $setting);
	$setting = preg_replace('/_DEAT_(MOTH|FATH)/', '_DEAT_PARE', $setting);
	// Remove duplicates
	preg_match_all('/[_A-Z]+/', $setting, $match);
	// And save
	WT_Tree::get($gedcom_id)->setPreference('SHOW_RELATIVES_EVENTS', implode(',', array_unique($match[0])));
}

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
