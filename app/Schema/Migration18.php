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
 * Upgrade the database schema from version 18 to version 19.
 */
class Migration18 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// Update some indexes, based on analysis of slow-query-logs
		try {
			Database::exec(
				"ALTER TABLE `##places`" .
				" DROP       KEY ix1," .
				" DROP       KEY ix2," .
				" DROP       KEY ix3," .
				" DROP       KEY ix4," .
				" DROP       p_level," . // Not needed - implicit from p_parent
				" ADD        KEY ix1 (p_file, p_place)," . // autocomplete.php, find.php
				" ADD UNIQUE KEY ux1 (p_parent_id, p_file, p_place)" // placelist.php
			);
		} catch (PDOException $ex) {
			// Already done?
		}
	}
}
