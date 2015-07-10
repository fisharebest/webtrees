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
 * Upgrade the database schema from version 23 to version 24.
 */
class Migration23 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// - media table columns should be not null, so we can find
		// media objects with missing files
		Database::exec(
			"ALTER TABLE `##media`" .
			" CHANGE m_ext      m_ext      VARCHAR(6)   COLLATE utf8_unicode_ci NOT NULL," .
			" CHANGE m_type     m_type     VARCHAR(20)  COLLATE utf8_unicode_ci NOT NULL," .
			" CHANGE m_filename m_filename VARCHAR(512) COLLATE utf8_unicode_ci NOT NULL," .
			" CHANGE m_titl     m_titl     VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL," .
			" CHANGE m_gedcom   m_gedcom   MEDIUMTEXT   COLLATE utf8_unicode_ci NOT NULL"
		);
	}
}
