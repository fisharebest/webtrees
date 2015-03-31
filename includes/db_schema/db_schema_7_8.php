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

// Update the database schema from version 7 to 8
// - update config data defining theme selection

Database::exec(
	"UPDATE `##gedcom_setting` SET setting_value=TRIM(LEADING 'themes/' FROM TRIM(TRAILING '/' FROM setting_value)) WHERE setting_name='THEME_DIR'"
);
Database::exec(
	"UPDATE `##user_setting` SET setting_value=TRIM(LEADING 'themes/' FROM TRIM(TRAILING '/' FROM setting_value)) WHERE setting_name='THEME_DIR'"
);
Database::exec(
	"UPDATE `##user_gedcom_setting` SET setting_value=TRIM(LEADING 'themes/' FROM TRIM(TRAILING '/' FROM setting_value)) WHERE setting_name='THEME_DIR'"
);

// Update the version to indicate success
Site::setPreference($schema_name, $next_version);
