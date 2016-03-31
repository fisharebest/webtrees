<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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

/**
 * Upgrade the database schema from version 33 to version 34.
 */
class Migration34 implements MigrationInterface {
	/**
	 * Upgrade to to the next version (Charts as modules, should be enabled by default)
	 */
	public function upgrade() {
		Database::exec(
			"INSERT IGNORE INTO `##module` (module_name, status, tab_order, menu_order, sidebar_order) VALUES" .
			" ('ancestors_chart', 'enabled', NULL, NULL, NULL),".
			" ('compact_tree_chart', 'enabled', NULL, NULL, NULL),".
			" ('descendancy_chart', 'enabled', NULL, NULL, NULL),".
			" ('family_book_chart', 'enabled', NULL, NULL, NULL),".
			" ('fan_chart', 'enabled', NULL, NULL, NULL),".
			" ('hourglass_chart', 'enabled', NULL, NULL, NULL),".
			" ('lifespans_chart', 'enabled', NULL, NULL, NULL),"
			" ('pedigree_chart', 'enabled', NULL, NULL, NULL),"
			" ('relationships_chart', 'enabled', NULL, NULL, NULL),"
			" ('statistics_chart', 'enabled', NULL, NULL, NULL),"
			" ('timeline_chart', 'enabled', NULL, NULL, NULL)"
		);
	}
}
