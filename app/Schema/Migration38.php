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

namespace Fisharebest\Webtrees\Schema;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

/**
 * Upgrade the database schema from version 38 to version 39.
 */
class Migration38 implements MigrationInterface
{
    /**
     * Upgrade to to the next version
     *
     * @return void
     */
    public function upgrade(): void
    {
        // This table was previously created by the googlemap module in 1.7.9.
        // These migrations are now part of the core code.

        if (!DB::schema()->hasTable('placelocation')) {
            DB::schema()->create('placelocation', static function (Blueprint $table): void {
                $table->integer('pl_id')->primary();
                $table->integer('pl_parent_id');
                $table->integer('pl_level');
                $table->string('pl_place', 255);
                $table->string('pl_long', 30)->nullable();
                $table->string('pl_lati', 30)->nullable();
                $table->integer('pl_zoom')->nullable();
                $table->string('pl_icon', 255)->nullable();

                $table->index('pl_place');
                $table->unique(['pl_parent_id', 'pl_place']);
            });
        }

        if (DB::schema()->hasColumn('placelocation', 'pl_media')) {
            DB::schema()->table('placelocation', static function (Blueprint $table): void {
                $table->dropColumn('pl_media');
                $table->dropColumn('sv_long');
                $table->dropColumn('sv_lati');
                $table->dropColumn('sv_bearing');
                $table->dropColumn('sv_elevation');
                $table->dropColumn('sv_zoom');
            });
        }
    }
}
