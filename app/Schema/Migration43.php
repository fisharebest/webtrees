<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Schema;

use Illuminate\Database\Capsule\Manager as DB;

/**
 * Upgrade the database schema from version 43 to version 44.
 */
class Migration43 implements MigrationInterface
{
    /**
     * Upgrade to the next version
     *
     * @return void
     */
    public function upgrade(): void
    {
        // Language was previously a tree-setting.
        $language = DB::table('gedcom_setting')
            ->where('setting_name', '=', 'LANGUAGE')
            ->where('gedcom_id', '>', 0)
            ->value('setting_value');

        // Now it is a site-setting.
        DB::table('site_setting')->updateOrInsert([
            'setting_name' => 'LANGUAGE',
        ], [
            'setting_value' => $language ?? 'en-US',
        ]);

        // Cleanup
        DB::table('gedcom_setting')
            ->where('setting_name', '=', 'LANGUAGE')
            ->delete();
    }
}
