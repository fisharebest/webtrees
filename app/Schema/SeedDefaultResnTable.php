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
 * Populate the default_resn table
 */
class SeedDefaultResnTable implements SeedInterface
{
    private const DEFAULT_RESTRICTIONS = [
        'SSN'  => 'confidential',
        'SOUR' => 'privacy',
        'REPO' => 'privacy',
        'SUBM' => 'confidential',
        'SUBN' => 'confidential',
    ];

    /**
     *  Run the seeder.
     *
     * @return void
     */
    public function run(): void
    {
        // Set default privacy settings for new trees
        foreach (self::DEFAULT_RESTRICTIONS as $tag_type => $resn) {
            DB::table('default_resn')->updateOrInsert([
                'gedcom_id' => -1,
                'tag_type'  => $tag_type,
            ], [
                'resn'      => $resn,
            ]);
        }
    }
}
