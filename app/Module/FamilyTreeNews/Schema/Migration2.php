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
namespace Fisharebest\Webtrees\Module\FamilyTreeNews\Schema;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Schema\MigrationInterface;
use PDOException;

/**
 * Upgrade the database schema from version 2 to version 3.
 */
class Migration2 implements MigrationInterface {
	/** {@inheritDoc} */
	public function upgrade() {
		// Delete any data that might violate the new constraints
		Database::exec(
			"DELETE FROM `##news`" .
			" WHERE user_id   NOT IN (SELECT user_id   FROM `##user`  )" .
			" OR    gedcom_id NOT IN (SELECT gedcom_id FROM `##gedcom`)"
		);

		// Add the new constraints
		try {
			Database::exec(
				"ALTER TABLE `##news`" .
				" ADD FOREIGN KEY news_fk1 (user_id  ) REFERENCES `##user`   (user_id)   ON DELETE CASCADE," .
				" ADD FOREIGN KEY news_fk2 (gedcom_id) REFERENCES `##gedcom` (gedcom_id) ON DELETE CASCADE"
			);
		} catch (PDOException $ex) {
			// Already updated?
		}
	}
}
