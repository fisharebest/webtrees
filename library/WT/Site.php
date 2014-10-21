<?php

use WT\Log;

/**
 * class WT_Site - Provide an interface to the wt_site_setting table.
 *
 * @copyright (c) 2014 webtrees development team
 * @license   This program is free software: you can redistribute it and/or modify
 *            it under the terms of the GNU General Public License as published by
 *            the Free Software Foundation, either version 2 of the License, or
 *            (at your option) any later version.
 *
 *            This program is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *            GNU General Public License for more details.
 *
 *            You should have received a copy of the GNU General Public License
 *            along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class WT_Site {
	/**
	 * Everything from the wt_site_setting table.
	 *
	 * @var array
	 */
	static $setting = null;

	/**
	 * Get the site’s configuration settings
	 *
	 * @param string $setting_name
	 *
	 * @return string|null
	 */
	public static function getPreference($setting_name) {
		// There are lots of settings, and we need to fetch lots of them on every page
		// so it is quicker to fetch them all in one go.
		if (self::$setting === null) {
			self::$setting = WT_DB::prepare(
				"SELECT SQL_CACHE setting_name, setting_value FROM `##site_setting`"
			)->fetchAssoc();
		}

		// A setting that hasn't yet been set?
		if (!array_key_exists($setting_name, self::$setting)) {
			self::$setting[$setting_name] = null;
		}

		return self::$setting[$setting_name];
	}

	/**
	 * Set the site’s configuration settings.
	 *
	 * @param string          $setting_name
	 * @param string|int|bool $setting_value
	 *
	 * @return void
	 */
	public static function setPreference($setting_name, $setting_value) {
		// Only need to update the database if the setting has actually changed.
		if (self::getPreference($setting_name) != $setting_value) {
			WT_DB::prepare(
				"REPLACE INTO `##site_setting` (setting_name, setting_value) VALUES (?, LEFT(?, 255))"
			)->execute(array($setting_name, $setting_value));

			self::$setting[$setting_name] = $setting_value;

			Log::addConfigurationLog('Site setting "' . $setting_name . '" set to "' . $setting_value . '"');
		}
	}
}
