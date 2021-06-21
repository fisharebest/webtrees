<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
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
        'SMTP_AUTH_PASS',
    ];

    // The following preferences contain unimportant data, and should not be logged.
    private const UNIMPORTANT_PREFERENCES = [
        'next_xref',
    ];

    // Default values for some site preferences.
    protected const DEFAULT_PREFERENCES = [
        'ALLOW_CHANGE_GEDCOM'     => '1',
        'HIDE_ADDR_FAX'           => '1',
        'HIDE_ADDR_PHON'          => '1',
        'HIDE_ADDR_WWW'           => '1',
        'HIDE_AFN'                => '1',
        'HIDE_ALIA'               => '1',
        'HIDE_ANCI'               => '1',
        'HIDE_ANUL'               => '1',
        'HIDE_ASSO'               => '1',
        'HIDE_BARM'               => '1',
        'HIDE_BIRT_FAMC'          => '1',
        'HIDE_CHR'                => '1',
        'HIDE_DIVF'               => '1',
        'HIDE_ENGA'               => '1',
        'HIDE_FAM_CENS'           => '1',
        'HIDE_FAM_RESI'           => '1',
        'HIDE_FCOM'               => '1',
        'HIDE_IDNO'               => '1',
        'HIDE_LDS'                => '1',
        'HIDE_MARC'               => '1',
        'HIDE_MARL'               => '1',
        'HIDE_MARS'               => '1',
        'HIDE_NAME_FONE'          => '1',
        'HIDE_NAME_NPFX'          => '1',
        'HIDE_NAME_NSFX'          => '1',
        'HIDE_NAME_ROMN'          => '1',
        'HIDE_NAME_SOUR'          => '1',
        'HIDE_ORDN'               => '1',
        'HIDE_PLAC_FONE'          => '1',
        'HIDE_PLAC_FORM'          => '1',
        'HIDE_PLAC_MAP'           => '1',
        'HIDE_PLAC_NOTE'          => '1',
        'HIDE_PLAC_ROMN'          => '1',
        'HIDE_REFN'               => '1',
        'HIDE_RFN'                => '1',
        'HIDE_RIN'                => '1',
        'HIDE_SOUR_DATA'          => '1',
        'HIDE_SOUR_DATE'          => '1',
        'HIDE_SOUR_EVEN'          => '1',
        'HIDE_SOUR_NOTE'          => '1',
        'HIDE_SOUR_QUAY'          => '1',
        'HIDE_SSN'                => '1',
        'HIDE_SUBM'               => '1',
        'INDEX_DIRECTORY'         => Webtrees::DATA_DIR,
        'LANGUAGE'                => 'en-US',
        'MULTIPLE_TREE_THRESHOLD' => '500',
        'SMTP_ACTIVE'             => 'internal',
        'SMTP_AUTH'               => '1',
        'SMTP_HOST'               => 'localhost',
        'SMTP_PORT'               => '25',
        'SMTP_SSL'                => 'none',
        'THEME_DIR'               => 'webtrees',
        'TIMEZONE'                => 'UTC',
        'USE_REGISTRATION_MODULE' => '1',
    ];

    /**
     * Everything from the wt_site_setting table.
     *
     * @var array<string,string>
     */
    public static array $preferences = [];

    /**
     * Set the site’s configuration settings.
     *
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return void
     */
    public static function setPreference(string $setting_name, string $setting_value): void
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

            if (!in_array($setting_name, self::UNIMPORTANT_PREFERENCES, true)) {
                Log::addConfigurationLog('Site preference "' . $setting_name . '" set to "' . $setting_value . '"', null);
            }
        }
    }

    /**
     * Get the site’s configuration settings
     *
     * @param string $setting_name
     *
     * @return string
     */
    public static function getPreference(string $setting_name): string
    {
        // There are lots of settings, and we need to fetch lots of them on every page
        // so it is quicker to fetch them all in one go.
        if (self::$preferences === []) {
            self::$preferences = DB::table('site_setting')
                ->pluck('setting_value', 'setting_name')
                ->all();
        }

        return self::$preferences[$setting_name] ?? self::DEFAULT_PREFERENCES[$setting_name] ?? '';
    }
}
