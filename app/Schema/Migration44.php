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

use Fisharebest\Database\Driver;
use Fisharebest\Database\Query\JoinOn;
use Fisharebest\Database\Query\QueryBuilder;
use Fisharebest\Webtrees\DB;
use Illuminate\Database\Schema\Blueprint;
use PDOException;

/**
 * Upgrade the database schema from version 44 to version 45.
 */
class Migration44 implements MigrationInterface
{
    private function legacyPlacesToImport(): QueryBuilder
    {
        $roots = DB::query()
            ->select('pl_id')
            ->from('placelocation')
            ->whereNull('pl_parent_id')
            ->orWhereEqual('pl_parent_id', 0);

        $children = DB::query()
            ->select('child.pl_id')
            ->from('placelocation AS child')
            ->join('valid_places AS parent', static fn (JoinOn $j): JoinOn => $j->on('parent.pl_id', 'child.pl_parent_id'));

        return DB::query()
            ->withRecursiveCte('valid_places', $roots, $children)
            ->select(
                'placelocation.pl_id',
                DB::raw('CASE pl_parent_id WHEN 0 THEN NULL ELSE pl_parent_id END'),
                'pl_place',
                DB::raw("CAST(REPLACE(REPLACE(pl_lati, 'S', '-'), 'N', '') AS FLOAT)"),
                DB::raw("CAST(REPLACE(REPLACE(pl_long, 'W', '-'), 'E', '') AS FLOAT)"),
            )
            ->from('placelocation')
            ->join('valid_places', static fn (JoinOn $j): JoinOn => $j->on('valid_places.pl_id', 'placelocation.pl_id'))
            ->leftJoin('place_location', static fn (JoinOn $j): JoinOn => $j->on('place_location.id', 'placelocation.pl_id'))
            ->whereNull('place_location.id')
            ->orderBy('placelocation.pl_id');
    }

    public function upgrade(): void
    {
        // It is simpler to create a new table than to update the existing one.

        if (!DB::schemaBuilder()->hasTable('place_location')) {
            DB::schemaBuilder()->create('place_location', static function (Blueprint $table): void {
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

            // SqlServer cannot cascade-delete/update on self-relations.
            // Users will need to delete all child locations before deleting the parent.
            if (DB::driverName() !== Driver::SqlServer->value) {
                DB::schemaBuilder()->table('place_location', static function (Blueprint $table): void {
                    $table->foreign(['parent_id'])
                        ->references(['id'])
                        ->on('place_location')
                        ->cascadeOnDelete()
                        ->cascadeOnUpdate();
                });
            }
        }

        // This table should only exist if we are upgrading an old installation, which would have been
        // created with MySQL. Therefore, we can safely use MySQL-specific SQL.
        if (DB::schemaBuilder()->hasTable('placelocation')) {
            if (DB::driverName() === Driver::MySQL->value) {
                DB::query(table: 'placelocation')
                    ->whereEqual('pl_lati', '')
                    ->orWhereEqual('pl_long', '')
                    ->update([
                        'pl_lati' => null,
                        'pl_long' => null,
                    ]);

                // Remove invalid values.
                DB::query(table: 'placelocation')
                    ->whereRaw('(pl_lati NOT REGEXP ? OR pl_long NOT REGEXP ?)', [
                        '^[NS][0-9]+[.]?[0-9]*$',
                        '^[EW][0-9]+[.]?[0-9]*$',
                    ])
                    ->update([
                        'pl_lati' => null,
                        'pl_long' => null,
                    ]);

                // The existing data may have placenames that only differ after the first 120 chars.
                // Need to remove the constraint before we truncate/merge them.
                try {
                    DB::schemaBuilder()->table('placelocation', static function (Blueprint $table): void {
                        $table->dropUnique(['pl_parent_id', 'pl_place']);
                    });
                } catch (PDOException) {
                    // Already deleted, or does not exist;
                }

                DB::query(table: 'placelocation')
                    ->update([
                        'pl_place' => DB::substring('pl_place', 1, 120),
                    ]);

                // The lack of unique key constraints means that there may be duplicates...
                while (true) {
                    // Two places with the same name and parent...
                    $row = DB::query()
                        ->select(DB::raw('MIN(pl_id) AS min'), DB::raw('MAX(pl_id) AS max'))
                        ->from('placelocation')
                        ->groupBy('pl_parent_id', 'pl_place')
                        ->havingRaw('COUNT(*) > ?', [1])
                        ->first();

                    if ($row === null) {
                        break;
                    }

                    // ...move children to the first
                    DB::query(table: 'placelocation')
                        ->whereEqual('pl_parent_id', $row->max)
                        ->update(['pl_parent_id' => $row->min]);

                    // ...delete the second
                    DB::query(table: 'placelocation')
                        ->whereEqual('pl_id', $row->max)
                        ->delete();
                }

                DB::query(table: 'place_location')
                    ->insertFrom(['id', 'parent_id', 'place', 'latitude', 'longitude'], $this->legacyPlacesToImport());
            }

            DB::schemaBuilder()->drop('placelocation');
        }

        // Earlier versions of webtrees used 0 and NULL interchangeably.
        // Assume 0 at the country-level and NULL at lower levels.
        DB::query(table: 'place_location')
            ->whereNotNull('parent_id')
            ->whereEqual('latitude', 0)
            ->whereEqual('longitude', 0)
            ->update([
                'latitude'  => null,
                'longitude' => null,
            ]);
    }
}
