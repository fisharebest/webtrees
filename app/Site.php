<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Illuminate\Database\Capsule\Manager as DB;

use function in_array;
use function mb_substr;

/**
 * Provide an interface to the wt_site_setting table.
 */
class Site
{
    // The following preferences contain sensitive data, and should not be logged.
    private const SENSITIVE_PREFERENCES = [
        'SMTP_AUTH_PASS'
    ];

    // The following preferences contain unimportant data, and should not be logged.
    private const UNIMPORTANT_PREFERENCES = [
        'next_xref'
    ];

    /**
     * Everything from the wt_site_setting table.
     *
     * @var array<string,string>
     */
    public static $preferences = [];

    /**
     * Get the site’s configuration settings
     *
     * @param string $setting_name
     * @param string $default
     *
     * @return string
     */
    public static function getPreference(string $setting_name, string $default = ''): string
    {
        // There are lots of settings, and we need to fetch lots of them on every page
        // so it is quicker to fetch them all in one go.
        if (self::$preferences === []) {
            self::$preferences = DB::table('site_setting')
                ->pluck('setting_value', 'setting_name')
                ->all();
        }

        return self::$preferences[$setting_name] ?? $default;
    }

    /**
     * Set the site’s configuration settings.
     *
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return void
     */
    public static function setPreference($setting_name, $setting_value): void
    {
        // The database column is only this long.
        $setting_value = mb_substr($setting_value, 0, 2000);

        if (self::getPreference($setting_name) !== $setting_value) {
            DB::table('site_setting')->updateOrInsert([
                'setting_name' => $setting_name,
            ], [
                'setting_value' => $setting_value,
            ]);

            self::$preferences[$setting_name] = $setting_value;

            if (in_array($setting_name, self::SENSITIVE_PREFERENCES, true)) {
                $setting_value = '********';
            }

            if (!in_array($setting_name, self::UNIMPORTANT_PREFERENCES)) {
                Log::addConfigurationLog('Site preference "' . $setting_name . '" set to "' . $setting_value . '"', null);
            }
        }
    }
}
