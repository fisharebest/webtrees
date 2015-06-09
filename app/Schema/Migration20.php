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
 * Upgrade the database schema from version 20 to version 21.
 */
class Migration20 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// Delete some old/unused configuration settings
		Database::exec("DELETE FROM `##gedcom_setting` WHERE setting_name IN ('MEDIA_EXTERNAL')");

		// Delete old table
		Database::exec("DROP TABLE IF EXISTS `##media_mapping`");

		// Make this table look like all the others
		try {
			Database::exec(
				"ALTER TABLE `##media`" .
				" DROP   m_id," .
				" CHANGE m_media   m_id       VARCHAR(20)  COLLATE utf8_unicode_ci NOT NULL," .
				" CHANGE m_file    m_filename VARCHAR(512) COLLATE utf8_unicode_ci DEFAULT NULL," .
				" CHANGE m_gedfile m_file     INTEGER                              NOT NULL," .
				" CHANGE m_gedrec  m_gedcom   MEDIUMTEXT   COLLATE utf8_unicode_ci DEFAULT NULL," .
				" ADD    m_type               VARCHAR(20)  COLLATE utf8_unicode_ci NULL AFTER m_ext," .
				" ADD    PRIMARY KEY     (m_file, m_id)," .
				" ADD            KEY ix2 (m_ext, m_type)," .
				" ADD            KEY ix3 (m_titl)"
			);
		} catch (PDOException $ex) {
			// Assume we've already done this
		}

		// Populate the new column
		Database::exec("UPDATE `##media` SET m_type = SUBSTRING_INDEX(SUBSTRING_INDEX(m_gedcom, '\n3 TYPE ', -1), '\n', 1) WHERE m_gedcom LIKE '%\n3 TYPE %'");
	}
}
