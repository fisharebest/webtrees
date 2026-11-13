<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

use Fisharebest\Webtrees\DB;

/**
 * Populate the default_resn table
 */
class SeedDefaultResnTable implements SeedInterface
{
    /**
     *  Run the seeder.
     */
    public function run(): void
    {
        // Set default privacy settings for new trees
        DB::identityInsert(table: 'default_resn', callback: static function (): void {
            DB::query(table: 'default_resn')->insertOrUpdate([
                'default_resn_id' => 1,
            ], [
                'gedcom_id' => -1,
                'tag_type'  => 'SSN',
                'resn'      => 'confidential',
            ], ['resn' => 'confidential']);

            DB::query(table: 'default_resn')->insertOrUpdate([
                'default_resn_id' => 2,
            ], [
                'gedcom_id' => -1,
                'tag_type'  => 'SOUR',
                'resn'      => 'privacy',
            ], ['resn' => 'privacy']);

            DB::query(table: 'default_resn')->insertOrUpdate([
                'default_resn_id' => 3,
            ], [
                'gedcom_id' => -1,
                'tag_type'  => 'REPO',
                'resn'      => 'privacy',
            ], ['resn' => 'privacy']);

            DB::query(table: 'default_resn')->insertOrUpdate([
                'default_resn_id' => 4,
            ], [
                'gedcom_id' => -1,
                'tag_type'  => 'SUBM',
                'resn'      => 'confidential',
            ], ['resn' => 'confidential']);

            DB::query(table: 'default_resn')->insertOrUpdate([
                'default_resn_id' => 5,
            ], [
                'gedcom_id' => -1,
                'tag_type'  => 'SUBN',
                'resn'      => 'confidential',
            ], ['resn' => 'confidential']);
        });
    }
}
