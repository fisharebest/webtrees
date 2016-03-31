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
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\I18N;

/**
 * Upgrade the database schema from version 33 to version 34.
 */
class Migration34 implements MigrationInterface {
	/**
	 * Upgrade to to the next version (Charts as modules, should be enabled by default)
	 */
	public function upgrade() {
		$names = array(
			"ancestors_chart", 
			"compact_tree_chart", 
			"descendancy_chart", 
			"family_book_chart", 
			"fan_chart", 
			"hourglass_chart", 
			"lifespans_chart", 
			"pedigree_chart", 
			"relationships_chart", 
			"statistics_chart", 
			"timeline_chart");
		
		$func = function($value) {
    	return " ('".$value."', 'enabled', NULL, NULL, NULL)";
		};
		$values = array_map($func, $names);
			
		Database::exec(
			"INSERT IGNORE INTO `##module` (module_name, status, tab_order, menu_order, sidebar_order) VALUES" .
			implode(',',$values)
		);
		
		foreach ($names as $name) {
			Database::prepare(
				"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" .
				" SELECT ?, gedcom_id, 'chart', 2" .
				" FROM `##gedcom`"
			)->execute(array($name));
		}
	}
}
