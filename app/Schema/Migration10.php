<?php
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
namespace Fisharebest\Webtrees\Schema;

use Fisharebest\Webtrees\Database;

/**
 * Upgrade the database schema from version 10 to version 11.
 */
class Migration10 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// Delete old configuration setting
		Database::exec("DELETE FROM `##gedcom_setting` WHERE setting_name IN ('SEARCH_FACTS_DEFAULT', 'DISPLAY_JEWISH_GERESHAYIM', 'DISPLAY_JEWISH_THOUSANDS')");

		// Increase the password column from 64 to 128 characters
		Database::exec("ALTER TABLE `##user` CHANGE password password VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL");
	}
}
