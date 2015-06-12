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
 * Upgrade the database schema from version 13 to version 14.
 */
class Migration13 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// Remove the i_isdead column
		try {
			Database::exec("ALTER TABLE `##individuals` DROP i_isdead");
		} catch (PDOException $ex) {
			// Already done this?
		}
	}
}
