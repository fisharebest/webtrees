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

/**
 * Upgrade the database schema from version 14 to version 15.
 */
class Migration14 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// Delete old config settings
		Database::exec("DELETE FROM `##gedcom_setting` WHERE setting_name IN('GEDCOM_DEFAULT_TAB', 'LINK_ICONS', 'ZOOM_BOXES')");
		Database::exec("DELETE FROM `##user_setting` WHERE setting_name='default'");

		// There is no way to add a RESN tag to NOTE objects
		Database::exec("UPDATE `##gedcom_setting` SET setting_value='SOUR,RESN' WHERE setting_name='NOTE_FACTS_ADD' AND setting_value='SOUR'");
	}
}
