<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
 * Upgrade the database schema from version 37 to version 38.
 */
class Migration37 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {

		Database::prepare(
			"INSERT INTO `##site_setting` (setting_name, setting_value)" .
			" SELECT 'next_xref', MAX(next_id) FROM `##next_id`"
		)->execute();

		Database::prepare(
			"DELETE FROM `##gedcom_setting` WHERE setting_name in ('FAM_ID_PREFIX', 'GEDCOM_ID_PREFIX', 'MEDIA_ID_PREFIX', 'NOTE_ID_PREFIX', 'REPO_ID_PREFIX', 'SOURCE_ID_PREFIX')"
		)->execute();

		Database::prepare(
			"DROP TABLE IF EXISTS `##next_id`"
		)->execute();
	}
}
