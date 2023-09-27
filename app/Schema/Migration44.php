<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use PDOException;

/**
 * Upgrade the database schema from version 44 to version 45.
 */
class Migration44 implements MigrationInterface
{
    /**
     * Upgrade to the next version
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

            // SQL-server cannot cascade-delete/update on self-relations.
            // Users will need to delete all child locations before deleting the parent.
            if (DB::connection()->getDriverName() === 'sqlsrv') {
                // SQL-Server doesn't support 'RESTRICT'
                $action = 'NO ACTION';
            } else {
                $action = 'CASCADE';
            }

            DB::schema()->table('place_location', static function (Blueprint $table) use ($action): void {
                $table->foreign(['parent_id'])
                    ->references(['id'])
                    ->on('place_location')
                    ->onDelete($action)
                    ->onUpdate($action);
            });
        }

        // This table should only exist if we are upgrading an old installation, which would have been
        // created with MySQL.  Therefore we can safely use MySQL-specific SQL.
        if (DB::schema()->hasTable('placelocation')) {
            if (DB::connection()->getDriverName() === 'mysql') {
                DB::table('placelocation')
                    ->where('pl_lati', '=', '')
                    ->orWhere('pl_long', '=', '')
                    ->update([
                        'pl_lati' => null,
                        'pl_long' => null,
                    ]);

                // Missing/invalid parents?  Move them to the top level
                DB::table('placelocation AS pl1')
                    ->leftJoin('placelocation AS pl2', 'pl1.pl_parent_id', '=', 'pl2.pl_id')
                    ->whereNull('pl2.pl_id')
                    ->update([
                        'pl1.pl_parent_id' => 0,
                    ]);

                // Remove invalid values.
                DB::table('placelocation')
                    ->where('pl_lati', 'NOT REGEXP', '^[NS][0-9]+[.]?[0-9]*$')
                    ->orWhere('pl_long', 'NOT REGEXP', '^[EW][0-9]+[.]?[0-9]*$')
                    ->update([
                        'pl_lati' => null,
                        'pl_long' => null,
                    ]);

                // The existing data may have placenames that only differ after the first 120 chars.
                // Need to remove the constraint before we truncate/merge them.
                try {
                    DB::schema()->table('placelocation', static function (Blueprint $table): void {
                        $table->dropUnique(['pl_parent_id', 'pl_place']);
                    });
                } catch (PDOException) {
                    // Already deleted, or does not exist;
                }

                DB::table('placelocation')
                    ->update([
                        'pl_place' => new Expression('SUBSTRING(pl_place, 1, 120)'),
                    ]);

                // The lack of unique key constraints means that there may be duplicates...
                while (true) {
                    // Two places with the same name and parent...
                    $row = DB::table('placelocation')
                        ->select([
                            new Expression('MIN(pl_id) AS min'),
                            new Expression('MAX(pl_id) AS max'),
                        ])
                        ->groupBy(['pl_parent_id', 'pl_place'])
                        ->having(new Expression('COUNT(*)'), '>', '1')
                        ->first();

                    if ($row === null) {
                        break;
                    }

                    // ...move children to the first
                    DB::table('placelocation')
                        ->where('pl_parent_id', '=', $row->max)
                        ->update(['pl_parent_id' => $row->min]);

                    // ...delete the second
                    DB::table('placelocation')
                        ->where('pl_id', '=', $row->max)
                        ->delete();
                }

                // This is the SQL standard.  It works with MySQL 8.0 and higher
                $select1 = DB::table('placelocation')
                    ->leftJoin('place_location', 'id', '=', 'pl_id')
                    ->whereNull('id')
                    ->orderBy('pl_level')
                    ->orderBy('pl_id')
                    ->select([
                        'pl_id',
                        new Expression('CASE pl_parent_id WHEN 0 THEN NULL ELSE pl_parent_id END'),
                        'pl_place',
                        new Expression("CAST(REPLACE(REPLACE(pl_lati, 'S', '-'), 'N', '') AS FLOAT)"),
                        new Expression("CAST(REPLACE(REPLACE(pl_long, 'W', '-'), 'E', '') AS FLOAT)"),
                    ]);

                // This works for MySQL 5.7 and lower, which cannot cast to FLOAT
                $select2 = DB::table('placelocation')
                    ->leftJoin('place_location', 'id', '=', 'pl_id')
                    ->whereNull('id')
                    ->orderBy('pl_level')
                    ->orderBy('pl_id')
                    ->select([
                        'pl_id',
                        new Expression('CASE pl_parent_id WHEN 0 THEN NULL ELSE pl_parent_id END'),
                        'pl_place',
                        new Expression("REPLACE(REPLACE(pl_lati, 'S', '-'), 'N', '')"),
                        new Expression("REPLACE(REPLACE(pl_long, 'W', '-'), 'E', '')"),
                    ]);

                try {
                    DB::table('place_location')
                        ->insertUsing(['id', 'parent_id', 'place', 'latitude', 'longitude'], $select1);
                } catch (PDOException) {
                    DB::table('place_location')
                        ->insertUsing(['id', 'parent_id', 'place', 'latitude', 'longitude'], $select2);
                }
            }

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
