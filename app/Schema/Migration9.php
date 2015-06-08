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
 * Upgrade the database schema from version 9 to version 10.
 */
class Migration9 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// Change index on name table
		try {
			Database::exec(
				"ALTER TABLE `##dates` CHANGE d_type d_type ENUM('@#DGREGORIAN@', '@#DJULIAN@', '@#DHEBREW@', '@#DFRENCH R@', '@#DHIJRI@', '@#DROMAN@', '@#DJALALI@')"
			);
		} catch (PDOException $ex) {
			// Already been run?
		}

		try {
			// The INDILIST and FAMLIST scripts have been rewritten to use this index
			Database::exec(
				"ALTER TABLE `##name` DROP INDEX ix2, ADD INDEX ix2 (n_surn, n_file, n_type, n_id), ADD INDEX ix3 (n_givn, n_file, n_type, n_id)"
			);
		} catch (PDOException $ex) {
			// Already been run?
		}
	}
}
