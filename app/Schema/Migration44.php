<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;

/**
 * Upgrade the database schema from version 44 to version 45.
 */
class Migration44 implements MigrationInterface
{
    /**
     * Upgrade to to the next version
     *
     * @return void
     */
    public function upgrade(): void
    {
        if (!DB::schema()->hasColumn('placelocation', 'pl_latitude')) {
            DB::schema()->table('placelocation', static function (Blueprint $table): void {
                $table->float('pl_latitude')->nullable()->after('pl_lati');
                $table->float('pl_longitude')->nullable()->after('pl_long');
            });
        }

        DB::table('placelocation')
            ->where('pl_lati', 'LIKE', 'N%')
            ->update(['pl_latitude' => new Expression('CAST(SUBSTR(pl_lati, 2) AS FLOAT)')]);

        DB::table('placelocation')
            ->where('pl_lati', 'LIKE', 'S%')
            ->update(['pl_latitude' => new Expression('- CAST(SUBSTR(pl_lati, 2) AS FLOAT)')]);

        DB::table('placelocation')
            ->where('pl_long', 'LIKE', 'E%')
            ->update(['pl_longitude' => new Expression('CAST(SUBSTR(pl_long, 2) AS FLOAT)')]);

        DB::table('placelocation')
            ->where('pl_long', 'LIKE', 'W%')
            ->update(['pl_longitude' => new Expression('- CAST(SUBSTR(pl_long, 2) AS FLOAT)')]);

        DB::schema()->table('placelocation', static function (Blueprint $table): void {
            $table->dropColumn('pl_lati');
            $table->dropColumn('pl_long');
            $table->dropColumn('pl_zoom');
            $table->dropColumn('pl_icon');
            $table->dropColumn('pl_level');
        });
    }
}
