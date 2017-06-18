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
namespace Fisharebest\Webtrees;

/**
 * Provide an interface to the wt_site_setting table.
 */
class Site {
	/**
	 * Everything from the wt_site_setting table.
	 *
	 * @var array
	 */
	private static $preferences = [];

	/**
	 * Get the site’s configuration settings
	 *
	 * @param string $setting_name
	 * @param string $default
	 *
	 * @return string
	 */
	public static function getPreference($setting_name, $default = '') {
		// There are lots of settings, and we need to fetch lots of them on every page
		// so it is quicker to fetch them all in one go.
		if (empty(self::$preferences)) {
			self::$preferences = Database::prepare(
				"SELECT SQL_CACHE setting_name, setting_value FROM `##site_setting`"
			)->fetchAssoc();
		}

		if (!array_key_exists($setting_name, self::$preferences)) {
			self::$preferences[$setting_name] = $default;
		}

		return self::$preferences[$setting_name];
	}

	/**
	 * Set the site’s configuration settings.
	 *
	 * @param string $setting_name
	 * @param string $setting_value
	 */
	public static function setPreference($setting_name, $setting_value) {
		if (self::getPreference($setting_name) !== $setting_value) {
			Database::prepare(
				"REPLACE INTO `##site_setting` (setting_name, setting_value)" .
				" VALUES (:setting_name, LEFT(:setting_value, 2000))"
			)->execute([
				'setting_name'  => $setting_name,
				'setting_value' => $setting_value,
			]);

			self::$preferences[$setting_name] = $setting_value;

			Log::addConfigurationLog('Site preference "' . $setting_name . '" set to "' . $setting_value . '"');
		}
	}
}
