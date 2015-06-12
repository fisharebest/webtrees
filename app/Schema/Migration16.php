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
 * Upgrade the database schema from version 16 to version 17.
 */
class Migration16 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// Add a "default" user, to store default settings
		Database::exec("INSERT IGNORE INTO `##user` (user_id, user_name, real_name, email, password) VALUES (-1, 'DEFAULT_USER', 'DEFAULT_USER', 'DEFAULT_USER', 'DEFAULT_USER')");

		// Add the initial default block settings
		Database::exec("INSERT IGNORE INTO `##block` (user_id, location, block_order, module_name) VALUES (-1, 'main', 1, 'todays_events'), (-1, 'main', 2, 'user_messages'), (-1, 'main', 3, 'user_favorites'), (-1, 'side', 1, 'user_welcome'), (-1, 'side', 2, 'random_media'), (-1, 'side', 3, 'upcoming_events'), (-1, 'side', 4, 'logged_in')");

		// Add a "default" tree, to store default settings
		Database::exec("INSERT IGNORE INTO `##gedcom` (gedcom_id, gedcom_name) VALUES (-1, 'DEFAULT_TREE')");

		// Add the initial default block settings
		Database::exec("INSERT IGNORE INTO `##block` (gedcom_id, location, block_order, module_name) VALUES (-1, 'main', 1, 'gedcom_stats'), (-1, 'main', 2, 'gedcom_news'), (-1, 'main', 3, 'gedcom_favorites'), (-1, 'main', 4, 'review_changes'), (-1, 'side', 1, 'gedcom_block'), (-1, 'side', 2, 'random_media'), (-1, 'side', 3, 'todays_events'), (-1, 'side', 4, 'logged_in')");

		// Some modules (e.g. sitemap) require larger settings
		Database::exec("ALTER TABLE `##module_setting` CHANGE setting_value setting_value MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL");
	}
}
