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
 * Upgrade the database schema from version 17 to version 18.
 */
class Migration17 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// Add table to control site access
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##site_access_rule` (" .
			" site_access_rule_id INTEGER          NOT NULL AUTO_INCREMENT," .
			" ip_address_start     INTEGER UNSIGNED NOT NULL DEFAULT 0," .
			" ip_address_end       INTEGER UNSIGNED NOT NULL DEFAULT 4294967295," .
			" user_agent_pattern   VARCHAR(255)     NOT NULL," .
			" rule                 ENUM('allow', 'deny', 'robot', 'unknown') NOT NULL DEFAULT 'unknown'," .
			" comment              VARCHAR(255)     NOT NULL," .
			" updated              TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP," .
			" PRIMARY KEY     (site_access_rule_id)" .
			") ENGINE=InnoDB COLLATE=utf8_unicode_ci"
		);

		Database::exec(
			"INSERT IGNORE INTO `##site_access_rule` (user_agent_pattern, rule, comment) VALUES" .
			" ('Mozilla/5.0 (%) Gecko/% %/%', 'allow', 'Gecko-based browsers')," .
			" ('Mozilla/5.0 (%) AppleWebKit/% (KHTML, like Gecko)%', 'allow', 'WebKit-based browsers')," .
			" ('Opera/% (%) Presto/% Version/%', 'allow', 'Presto-based browsers')," .
			" ('Mozilla/% (compatible; MSIE %', 'allow', 'Trident-based browsers')," .
			" ('Mozilla/5.0 (compatible; Konqueror/%', 'allow', 'Konqueror browser')"
		);
	}
}
