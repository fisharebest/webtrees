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

// Update the database schema from version 24-25
// - delete unused settings and update indexes

// Tree settings become site settings
Database::exec(
	"INSERT IGNORE INTO `##site_setting` (setting_name, setting_value)" .
	" SELECT setting_name, setting_value" .
	" FROM `##gedcom_setting`" .
	" WHERE setting_name IN ('SHOW_REGISTER_CAUTION', 'WELCOME_TEXT_CUST_HEAD') OR setting_name like 'WELCOME_TEXT_AUTH_MODE%'" .
	" GROUP BY setting_name"
);

Database::exec(
	"DELETE FROM `##gedcom_setting` WHERE setting_name IN ('ALLOW_EDIT_GEDCOM', 'SHOW_REGISTER_CAUTION', 'WELCOME_TEXT_CUST_HEAD') OR setting_name like 'WELCOME_TEXT_AUTH_MODE%'"
);

Database::exec(
	"DELETE FROM `##site_setting` WHERE setting_name IN ('STORE_MESSAGES')"
);

// Update the version to indicate success
Site::setPreference($schema_name, $next_version);
