<?php
// Update the news/blog module database schema from version 3 to 4
// add languages field
//
// The script should assume that it can be interrupted at
// any point, and be able to continue by re-running the script.
// Fatal errors, however, should be allowed to throw exceptions,
// which will be caught by the framework.
// It shouldn't do anything that might take more than a few
// seconds, for systems with low timeout values.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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

// Add the new column
try {
	WT_DB::exec(
		"ALTER TABLE `##news`" .
		" ADD COLUMN languages VARCHAR(255) NULL AFTER gedcom_id"
	);
} catch (PDOException $ex) {
	// Already updated?
}

// updates fields
// By default accept all languages
// Shift title from subject field to top of body and use markdown
// to make it a level 3 heading <H3>. Have to use REPEAT('#',3)
// as otherwise WT DB function converts the first two ## to the WT table prefix!
try {
	WT_DB::exec(
		"UPDATE `##news`" .
		" SET languages='" . implode(',', array_keys(WT_I18N::installed_languages())). "', body=CONCAT(REPEAT('#',3), ' ', subject, CHAR(13), CHAR(10), body)"
	);
} catch (PDOException $ex) {
	// Already updated?
}

// Uncomment this block to remove the subject column
//try {
//	WT_DB::exec(
//		"ALTER TABLE `##news`" .
//		" DROP COLUMN subject"
//	);
//} catch (PDOException $ex) {
//	// Already updated?
//}

try {
	// convert old block settings to new format
	WT_DB::exec(
		"UPDATE `##block_setting` JOIN `##block` USING (`block_id`)"
		. " SET `setting_value` = FIND_IN_SET(`setting_value`,'nolimit,date,count')-1"
		. " WHERE `module_name`='gedcom_news' AND `setting_name`='limit'");
	// deselect gedcom_news & user_jounal modules and select blog module
	WT_DB::exec("UPDATE `##block`"
		. " SET `module_name`='blog'"
		. " WHERE `module_name` IN ('gedcom_news','user_blog')");
} catch (PDOException $ex) {
	// Already updated?
}
// Update the version to indicate success
WT_Site::preference($schema_name, $next_version);
