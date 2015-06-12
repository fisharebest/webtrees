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
use PDOException;

/**
 * Upgrade the database schema from version 27 to version 28.
 */
class Migration27 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// Delete old/unused settings
		Database::exec(
			"DELETE FROM `##gedcom_setting` WHERE setting_name IN ('USE_GEONAMES')"
		);

		try {
			// Indexes created by setup.php or schema update 17-18
			Database::exec("ALTER TABLE `##site_access_rule` DROP INDEX ix1, DROP INDEX ix2, DROP INDEX ix3");
			// Indexes created by schema update 17-18
			Database::exec("ALTER TABLE `##site_access_rule` DROP INDEX ix4, DROP INDEX ix5, DROP INDEX ix6");
		} catch (PDOException $ex) {
			// Already done?
		}

		// User data may contains duplicates - these will prevent us from creating the new indexes
		Database::exec(
			"DELETE t1 FROM `##site_access_rule` AS t1 JOIN (SELECT MIN(site_access_rule_id) AS site_access_rule_id, ip_address_end, ip_address_start, user_agent_pattern FROM `##site_access_rule`) AS t2 ON t1.ip_address_end = t2.ip_address_end AND t1.ip_address_start = t2.ip_address_start AND t1.user_agent_pattern = t2.user_agent_pattern AND t1.site_access_rule_id <> t2.site_access_rule_id"
		);

		// ix1 - covering index for visitor lookup
		// ix2 - for total counts in admin page
		try {
			Database::exec(
				"ALTER TABLE `##site_access_rule`" .
				" ADD UNIQUE INDEX `##site_access_rule_ix1` (ip_address_end, ip_address_start, user_agent_pattern, rule)," .
				" ADD        INDEX `##site_access_rule_ix2` (rule)"
			);
		} catch (PDOException $ex) {
			// Already done?
		}
	}
}
