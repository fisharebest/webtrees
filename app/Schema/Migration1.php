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
 * Upgrade the database schema from version 1 to version 2.
 */
class Migration1 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// Create the wt_session table to store session data in the database,
		// rather than in the filesystem.
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##session` (" .
			" session_id   CHAR(32)    NOT NULL," .
			" session_time TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP," .
			" user_id      INTEGER     NOT NULL," .
			" ip_address   VARCHAR(32) NOT NULL," .
			" session_data MEDIUMBLOB  NOT NULL," .
			" PRIMARY KEY     (session_id)," .
			"         KEY ix1 (session_time)," .
			"         KEY ix2 (user_id, ip_address)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
	}
}
