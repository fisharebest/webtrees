<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
 * Populate the block table
 */
class SeedBlockTable implements SeedInterface
{
    private const DEFAULT_TREE_PAGE_BLOCKS = [
        'main' => [
            1 => 'gedcom_stats',
            2 => 'gedcom_news',
            3 => 'gedcom_favorites',
            4 => 'review_changes',
        ],
        'side' => [
            1 => 'gedcom_block',
            2 => 'random_media',
            3 => 'todays_events',
            4 => 'logged_in',
        ],
    ];

    private const DEFAULT_USER_PAGE_BLOCKS = [
        'main' => [
            1 => 'todays_events',
            2 => 'user_messages',
            3 => 'user_favorites',
        ],
        'side' => [
            1 => 'user_welcome',
            2 => 'random_media',
            3 => 'upcoming_events',
            4 => 'logged_in',
        ],
    ];

    /**
     *  Run the seeder.
     *
     * @return void
     */
    public function run(): void
    {
        // Set default blocks for new trees
        foreach (self::DEFAULT_TREE_PAGE_BLOCKS as $location => $blocks) {
            foreach ($blocks as $block_order => $module_name) {
                DB::table('block')->insert([
                    'gedcom_id'   => -1,
                    'location'    => $location,
                    'block_order' => $block_order,
                    'module_name' => $module_name,
                ]);
            }
        }

        // Set default blocks for new user
        foreach (self::DEFAULT_USER_PAGE_BLOCKS as $location => $blocks) {
            foreach ($blocks as $block_order => $module_name) {
                DB::table('block')->insert([
                    'user_id'     => -1,
                    'location'    => $location,
                    'block_order' => $block_order,
                    'module_name' => $module_name,
                ]);
            }
        }
    }
}
