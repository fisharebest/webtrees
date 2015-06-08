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
 * Upgrade the database schema from version 29 to version 30.
 */
class Migration29 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// Originally migrated from PhpGedView, but never used.
		Database::exec("DROP TABLE IF EXISTS `##ip_address`");

		// No longer used
		Database::exec("DELETE FROM `##user_setting` WHERE setting_name IN ('editaccount')");
		Database::exec("DELETE FROM `##gedcom_setting` WHERE setting_name IN ('SHOW_STATS')");
		Database::exec("DELETE FROM `##site_setting` WHERE setting_name IN ('REQUIRE_ADMIN_AUTH_REGISTRATION')");

		// https://bugs.launchpad.net/webtrees/+bug/1405672
		Database::exec(
			"UPDATE `##site_access_rule` SET user_agent_pattern = 'Mozilla/5.0 (% Konqueror/%'" .
			" WHERE user_agent_pattern='Mozilla/5.0 (compatible; Konqueror/%'"
		);

		// Embedded variables are based on function names - which were renamed for PSR2
		Database::exec(
			"UPDATE `##block_setting` " .
			" JOIN `##block` USING (block_id)" .
			" SET setting_value = REPLACE(setting_value, '#WT_VERSION#', '#webtreesVersion#')" .
			" WHERE setting_name = 'html' AND module_name = 'html'"
		);
		Database::exec(
			"UPDATE `##block_setting` " .
			" JOIN `##block` USING (block_id)" .
			" SET setting_value = REPLACE(setting_value, '#browserTime24#', '#browserTime#')" .
			" WHERE setting_name = 'html' AND module_name = 'html'"
		);

		// Language settings have changed from locale (en_GB) to language tag (en-GB)
		Database::exec(
			"UPDATE `##gedcom_setting` SET setting_value = REPLACE(setting_value, '_', '-') WHERE setting_name = 'language'"
		);
		Database::exec(
			"UPDATE `##site_setting` SET setting_value = REPLACE(setting_value, '_', '-') WHERE setting_name = 'language'"
		);
		Database::exec(
			"UPDATE `##user_setting` SET setting_value = REPLACE(setting_value, '_', '-') WHERE setting_name = 'language'"
		);
		Database::exec(
			"UPDATE `##block_setting` SET setting_value = REPLACE(setting_value, '_', '-') WHERE setting_name = 'languages'"
		);
	}
}
