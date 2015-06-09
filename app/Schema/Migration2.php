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
 * Upgrade the database schema from version 2 to version 2.
 */
class Migration2 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// - create the wt_gedcom_chunk table to import gedcoms in
		// blocks of data smaller than the max_allowed_packet restriction.

		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##gedcom_chunk` (" .
			" gedcom_chunk_id INTEGER AUTO_INCREMENT NOT NULL," .
			" gedcom_id       INTEGER                NOT NULL," .
			" chunk_data      MEDIUMBLOB             NOT NULL," .
			" imported        BOOLEAN                NOT NULL DEFAULT FALSE," .
			" PRIMARY KEY     (gedcom_chunk_id)," .
			"         KEY ix1 (gedcom_id, imported)," .
			" FOREIGN KEY fk1 (gedcom_id) REFERENCES `##gedcom` (gedcom_id)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);

		try {
			Database::exec(
				"ALTER TABLE `##gedcom` DROP import_gedcom, DROP import_offset"
			);
		} catch (PDOException $ex) {
			// Perhaps we have already deleted these columns?
		}
	}
}
