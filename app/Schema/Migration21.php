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
 * Upgrade the database schema from version 21 to version 22.
 */
class Migration21 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// Data fix for bug #1072477
		Database::exec("UPDATE `##default_resn` SET xref     = NULL WHERE xref     = ''");
		Database::exec("UPDATE `##default_resn` SET tag_type = NULL WHERE tag_type = ''");

		// Delete old settings
		Database::exec("DELETE FROM `##gedcom_setting` WHERE setting_name IN ('AUTO_GENERATE_THUMBS', 'POSTAL_CODE', 'MEDIA_DIRECTORY_LEVELS', 'USE_MEDIA_VIEWER')");

		// Delete old settings
		Database::exec("DELETE FROM `##module_setting` WHERE module_name='lightbox'");

		// Very old versions of phpGedView allowed media paths beginning “./”
		// Remove these
		Database::exec(
			"UPDATE `##media` m" .
			" SET" .
			"  m_filename = TRIM(LEADING './' FROM m_filename)," .
			"  m_gedcom   = REPLACE(m_gedcom, '\n1 FILE ./', '\n1 FILE ')"
		);
		Database::exec(
			"UPDATE `##change` c" .
			" SET new_gedcom = REPLACE(new_gedcom, '\n1 FILE ./', '\n1 FILE ')" .
			" WHERE status = 'pending'"
		);

		// Previous versions of webtrees included the MEDIA_DIRECTORY setting in the
		// FILE tag of the OBJE records.  Remove it…
		Database::exec(
			"UPDATE `##media` m" .
			" JOIN `##gedcom_setting` gs ON (m.m_file = gs.gedcom_id AND gs.setting_name = 'MEDIA_DIRECTORY')" .
			" SET" .
			"  m_filename = TRIM(LEADING gs.setting_value FROM m_filename)," .
			"  m_gedcom   = REPLACE(m_gedcom, CONCAT('\n1 FILE ', gs.setting_value), '\n1 FILE ')"
		);
		// …don’t forget pending changes
		Database::exec(
			"UPDATE `##change` c" .
			" JOIN `##gedcom_setting` gs ON (c.gedcom_id = gs.gedcom_id AND gs.setting_name = 'MEDIA_DIRECTORY')" .
			" SET new_gedcom = REPLACE(new_gedcom, CONCAT('\n1 FILE ', gs.setting_value), '\n1 FILE ')" .
			" WHERE status = 'pending'"
		);
	}
}
