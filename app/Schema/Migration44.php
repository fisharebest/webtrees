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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Schema;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
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
        // It is simpler to create a new table than to update the existing one.

        if (!DB::schema()->hasTable('place_location')) {
            DB::schema()->create('place_location', static function (Blueprint $table): void {
                $table->integer('id', true);
                $table->integer('parent_id')->nullable();
                $table->string('place', 120);
                $table->double('latitude')->nullable();
                $table->double('longitude')->nullable();

                $table->unique(['parent_id', 'place']);
                $table->unique(['place', 'parent_id']);

                $table->index(['latitude']);
                $table->index(['longitude']);
            });

            DB::schema()->table('place_location', static function (Blueprint $table): void {
                $table->foreign(['parent_id'])
                    ->references(['id'])
                    ->on('place_location')
                    ->onDelete('CASCADE')
                    ->onUpdate('CASCADE');
            });
        }

        if (DB::schema()->hasTable('placelocation')) {
            DB::table('placelocation')
                ->where('pl_lati', '=', '')
                ->orWhere('pl_long', '=', '')
                ->update([
                    'pl_lati' => null,
                    'pl_long' => null,
                ]);

            // Ideally, we would update the parent_id separately,
            $select = DB::table('placelocation')
                ->leftJoin('place_location', 'id', '=', 'pl_id')
                ->whereNull('id')
                ->orderBy('pl_id')
                ->select([
                    'pl_id',
                    new Expression('CASE pl_parent_id WHEN 0 THEN NULL ELSE pl_parent_id END'),
                    'pl_place',
                    new Expression("REPLACE(REPLACE(pl_lati, 'S', '-'), 'N', '')"),
                    new Expression("REPLACE(REPLACE(pl_long, 'W', '-'), 'E', '')"),
                ]);

            DB::table('place_location')
                ->insertUsing(['id', 'parent_id', 'place', 'latitude', 'longitude'], $select);

            //DB::table('place_location')
            //    ->join('placelocation', 'pl_id', '=', 'id')
            //    ->where('pl_parent_id', '<>', 0)
            //    ->update([
            //        'parent_id' => new Expression('pl_parent_id'),
            //    ]);

            DB::schema()->drop('placelocation');
        }

        // Earlier versions of webtrees used 0 and NULL interchangeably.
        // Assume 0 at the country-level and NULL at lower levels.
        DB::table('place_location')
            ->whereNotNull('parent_id')
            ->where('latitude', '=', 0)
            ->where('longitude', '=', 0)
            ->update([
                'latitude'  => null,
                'longitude' => null,
            ]);
    }
}
