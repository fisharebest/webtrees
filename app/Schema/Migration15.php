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
 * Upgrade the database schema from version 16 to version 17.
 */
class Migration15 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// Delete old config settings
		Database::exec("DELETE FROM `##gedcom_setting` WHERE setting_name IN('GEDCOM_DEFAULT_TAB', 'LINK_ICONS', 'ZOOM_BOXES', 'SHOW_LIST_PLACES', 'SHOW_CONTEXT_HELP')");
		Database::exec("DELETE FROM `##user_setting` WHERE setting_name='defaulttab'");

		// There is no way to add a RESN tag to NOTE objects
		Database::exec("UPDATE `##gedcom_setting` SET setting_value='SOUR,RESN' WHERE setting_name='NOTE_FACTS_ADD' AND setting_value='SOUR'");

		// This needs to be an absolute URL.  If not set, it defaults to the full path to login.php
		Database::exec("DELETE FROM `##site_setting` WHERE setting_name='LOGIN_URL' AND setting_value='login.php'");
		// No need for an empty value
		Database::exec("DELETE FROM `##site_setting` WHERE setting_name='SERVER_URL' AND setting_value=''");

		// Later PHP versions use session IDs longer than 32 chars.
		Database::exec("ALTER TABLE `##session` CHANGE session_id session_id CHAR(128) COLLATE utf8_unicode_ci NOT NULL");
	}
}
