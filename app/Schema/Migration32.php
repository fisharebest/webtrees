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
 * Upgrade the database schema from version 32 to version 33.
 */
class Migration32 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		try {
			Database::prepare(
				"ALTER TABLE `##site_setting` CHANGE setting_value setting_value VARCHAR(2000) NOT NULL"
			)->execute();
		} catch (PDOException $ex) {
			// Already done?
		}

		Database::prepare(
			"DELETE FROM `##change` WHERE old_gedcom = '' AND new_gedcom = ''"
		)->execute();
	}
}
