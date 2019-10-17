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

namespace Fisharebest\Webtrees\Schema;

use Illuminate\Database\Capsule\Manager as DB;

/**
 * Populate the site_setting table
 */
class SeedSiteSettingTable implements SeedInterface
{
    private const SETTINGS = [
        'INDEX_DIRECTORY'         => 'data/',
        'USE_REGISTRATION_MODULE' => '1',
        'ALLOW_CHANGE_GEDCOM'     => '1',
        'SMTP_ACTIVE'             => 'internal',
        'SMTP_HOST'               => 'localhost',
        'SMTP_PORT'               => '25',
        'SMTP_AUTH'               => '1',
        'SMTP_SSL'                => 'none',
    ];

    /**
     *  Run the seeder.
     *
     * @return void
     */
    public function run(): void
    {
        foreach (self::SETTINGS as $setting_name => $setting_value) {
            DB::table('site_setting')->updateOrInsert([
                'setting_name'  => $setting_name,
            ], [
                'setting_value' => $setting_value,
            ]);
        }
    }
}
