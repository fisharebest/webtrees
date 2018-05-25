<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

namespace Fisharebest\Webtrees\Module\OpenStreetMap\Schema;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\Schema\MigrationInterface;
use PDOException;

/**
 * Upgrade the database schema from version 3 to version 4.
 */

class Migration3 implements MigrationInterface {
	/** {@inheritDoc} */

	public function upgrade() {
		$queries = [
			// Remove streetview fields & update indexes
			"ALTER TABLE `##placelocation`" .
			" DROP COLUMN pl_media," .
			" DROP COLUMN sv_long," .
			" DROP COLUMN sv_lati," .
			" DROP COLUMN sv_bearing," .
			" DROP COLUMN sv_elevation,".
			" DROP COLUMN sv_zoom," .
			" DROP INDEX ix1," .
			" DROP INDEX ix2," .
			" DROP INDEX ix3," .
			" DROP INDEX ix4," .
			" DROP INDEX ix5," .
			" ADD UNIQUE INDEX ix1 (pl_parent_id, pl_place)," .
			" ADD INDEX ix2 (pl_parent_id)," .
			" ADD INDEX ix3 (pl_place)",
			// Reset fields to default empty value
			"UPDATE `##placelocation` SET" .
			" pl_long = IF(pl_long IN ('', 'E0'), NULL, pl_long)," .
			" pl_lati = IF(pl_lati IN ('', 'N0'), NULL, pl_lati)," .
			" pl_zoom = IF(pl_zoom = '', NULL, pl_zoom)," .
			" pl_icon = IF(pl_icon = '', NULL, pl_icon)",
			// Clear out earlier versions of settings (if any)
			"DELETE FROM `##module_setting` WHERE module_name='openstreetmap' AND setting_name=''"
			];

		foreach($queries as $query) {
			try {
				Database::exec($query);
			} catch (PDOException $ex) {
				DebugBar::addThrowable($ex);

				// Already done this?
			}
		}
	}
}
