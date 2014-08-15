<?php
// Provide an interface to the wt_site_setting table
//
// webtrees: Web based Family History software
// Copyright (c) 2014 webtrees development team
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Log;

class WT_Site {
	static $setting=null;

	// Get and Set the site’s configuration settings
	public static function preference($setting_name, $setting_value=null) {
		// There are lots of settings, and we need to fetch lots of them on every page
		// so it is quicker to fetch them all in one go.
		if (self::$setting===null) {
			self::$setting=WT_DB::prepare(
				"SELECT SQL_CACHE setting_name, setting_value FROM `##site_setting`"
			)->fetchAssoc();
		}

		// If $setting_value is null, then GET the setting
		if ($setting_value===null) {
			// If parameter two is not specified, GET the setting
			if (!array_key_exists($setting_name, self::$setting)) {
				self::$setting[$setting_name]=null;
			}
			return self::$setting[$setting_name];
		} else {
			// If parameter two is specified, then SET the setting
			if (self::preference($setting_name)!=$setting_value) {
				// Audit log of changes
				Log::addConfigurationLog('Site setting "' . $setting_name . '" set to "' . $setting_value . '"');
			}
			WT_DB::prepare(
				"REPLACE INTO `##site_setting` (setting_name, setting_value) VALUES (?, LEFT(?, 255))"
			)->execute(array($setting_name, $setting_value));
			self::$setting[$setting_name]=$setting_value;
		}
	}
}
