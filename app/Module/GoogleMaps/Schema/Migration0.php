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
namespace Fisharebest\Webtrees\Module\GoogleMaps\Schema;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Schema\MigrationInterface;

/**
 * Upgrade the database schema from version 0 (empty database) to version 1.
 */
class Migration0 implements MigrationInterface {
	/** {@inheritDoc} */
	public function upgrade() {
		// Create the tables, as per PhpGedView 4.2.1

		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##placelocation` (" .
			" pl_id        INTEGER      NOT NULL," .
			" pl_parent_id INTEGER          NULL," .
			" pl_level     INTEGER          NULL," .
			" pl_place     VARCHAR(255)     NULL," .
			" pl_long      VARCHAR(30)      NULL," .
			" pl_lati      VARCHAR(30)      NULL," .
			" pl_zoom      INTEGER          NULL," .
			" pl_icon      VARCHAR(255)     NULL," .
			" PRIMARY KEY     (pl_id)," .
			"         KEY ix1 (pl_level)," .
			"         KEY ix2 (pl_long)," .
			"         KEY ix3 (pl_lati)," .
			"         KEY ix4 (pl_place)," .
			"         KEY ix5 (pl_parent_id)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
	}
}
