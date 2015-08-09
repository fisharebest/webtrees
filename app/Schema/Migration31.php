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
use Fisharebest\Webtrees\Site;
use PDOException;

/**
 * Upgrade the database schema from version 31 to version 32.
 */
class Migration31 implements MigrationInterface {
	/** @var string[] Updated language codes */
	private $languages = array(
		'en_AU' => 'en-AU',
		'en_GB' => 'en-GB',
		'en_US' => 'en-US',
		'fr_CA' => 'fr-CA',
		'pt_BR' => 'pt-BR',
	);

	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		$index_dir = Site::getPreference('INDEX_DIRECTORY');

		// Due to the language code changes in 1.7.0, we need to update some other settings
		foreach ($this->languages as $old => $new) {
			try {
				Database::prepare(
					"UPDATE `##site_setting` SET setting_name = REPLACE(setting_name, :old, :new) " .
					"WHERE setting_name LIKE 'WELCOME_TEXT_AUTH_MODE_%'"
				)->execute(array(
					'old' => $old,
					'new' => $new,
				));
			} catch (PDOException $ex) {
				// Duplicate key?  Already done?
			}

			Database::prepare(
				"UPDATE `##block_setting` SET setting_value = REPLACE(setting_value, :old, :new) " .
				"WHERE setting_name = 'languages'"
			)->execute(array(
				'old' => $old,
				'new' => $new,
			));

			// Historical fact files
			if (file_exists($index_dir . 'histo.' . $old . '.php') && !file_exists($index_dir . 'histo.' . $new . '.php')) {
				rename($index_dir . 'histo.' . $old . '.php', $index_dir . 'histo.' . $new . '.php');
			}

			// Language files
			if (file_exists($index_dir . 'language/' . $old . '.php') && !file_exists($index_dir . 'language/' . $new . '.php')) {
				rename($index_dir . 'language/' . $old . '.php', $index_dir . 'language/' . $new . '.php');
			}

			if (file_exists($index_dir . 'language/' . $old . '.csv') && !file_exists($index_dir . 'language/' . $new . '.csv')) {
				rename($index_dir . 'language/' . $old . '.csv', $index_dir . 'language/' . $new . '.csv');
			}

			if (file_exists($index_dir . 'language/' . $old . '.mo') && !file_exists($index_dir . 'language/' . $new . '.mo')) {
				rename($index_dir . 'language/' . $old . '.mo', $index_dir . 'language/' . $new . '.mo');
			}
		}
	}
}
