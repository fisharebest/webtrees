<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

use PDOException;

// Add the new column
try {
	Database::exec(
		"ALTER TABLE `##news`" .
		" ADD COLUMN languages VARCHAR(255) NULL AFTER gedcom_id"
	);
} catch (PDOException $ex) {
	// Already updated?
}

// updates fields
// By default accept all languages
// Prepend subject field to body and use markdown to make it a level 3 heading <H3>.
// Have to use REPEAT('#',3) to stop database function converting the first two # to the WT table prefix

try {
	Database::exec(
		"UPDATE `##news`" .
		" SET languages='" . BlogModule::getActiveLanguageTagsAsString() . "', body=CONCAT(REPEAT('#',3), subject, '\r\n', body)"
	);
} catch (PDOException $ex) {
	// Already updated?
}

// Uncomment this block to remove the subject column
//try {
//	Database::exec(
//		"ALTER TABLE `##news`" .
//		" DROP COLUMN subject"
//	);
//} catch (PDOException $ex) {
//	// Already updated?
//}

try {
	// convert old block settings to new format
	Database::exec(
		"UPDATE `##block_setting` JOIN `##block` USING (`block_id`)"
		. " SET `setting_value` = FIND_IN_SET(`setting_value`,'nolimit,date,count')-1"
		. " WHERE `module_name`='gedcom_news' AND `setting_name`='limit'");
	// deselect gedcom_news & user_journal modules and select blog module
	Database::exec("UPDATE `##block`"
		. " SET `module_name`='blog'"
		. " WHERE `module_name` IN ('gedcom_news','user_blog')");
} catch (PDOException $ex) {
	// Already updated?
}

// Update the version to indicate success
Site::setPreference($schema_name, $next_version);
