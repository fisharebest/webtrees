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
 * Populate the user table
 */
class SeedUserTable implements SeedInterface
{
    /**
     *  Run the seeder.
     *
     * @return void
     */
    public function run(): void
    {
        // Add a "default" user, to store default settings
        DB::table('user')->updateOrInsert([
            'user_id'   => -1,
        ], [
            'user_name' => 'DEFAULT_USER',
            'real_name' => 'DEFAULT_USER',
            'email'     => 'DEFAULT_USER',
            'password'  => 'DEFAULT_USER',
        ]);
    }
}
