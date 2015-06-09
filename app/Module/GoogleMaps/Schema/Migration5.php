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
 * Upgrade the database schema from version 5 to version 6.
 */
class Migration5 implements MigrationInterface {
	/** {@inheritDoc} */
	public function upgrade() {
		// Default settings
		Database::prepare(
			"INSERT IGNORE INTO `##module_setting` (module_name, setting_name, setting_value) VALUES " .
			" ('googlemap', 'GM_MAP_TYPE', 'G_NORMAL_MAP')," .
			" ('googlemap', 'GM_MAX_ZOOM', '15')," .
			" ('googlemap', 'GM_MIN_ZOOM', '2')," .
			" ('googlemap', 'GM_PRECISION_0', '0')," .
			" ('googlemap', 'GM_PRECISION_1', '1')," .
			" ('googlemap', 'GM_PRECISION_2', '2')," .
			" ('googlemap', 'GM_PRECISION_3', '3')," .
			" ('googlemap', 'GM_PRECISION_4', '4')," .
			" ('googlemap', 'GM_PRECISION_5', '5')," .
			" ('googlemap', 'GM_XSIZE', '600')," .
			" ('googlemap', 'GM_YSIZE', '400')," .
			" ('googlemap', 'GM_PH_XSIZE', '500')," .
			" ('googlemap', 'GM_PH_YSIZE', '350')," .
			" ('googlemap', 'GM_PH_MARKER', 'G_FLAG')," .
			" ('googlemap', 'GM_DISP_SHORT_PLACE', '0')"
		)->execute();
	}
}
