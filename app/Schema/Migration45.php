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
use Fisharebest\Database\Exceptions\QueryException;
use Fisharebest\Webtrees\DB;
use Illuminate\Database\Schema\Blueprint;

/**
 * Upgrade the database schema from version 45 to version 46.
 */
final readonly class Migration45 implements MigrationInterface
{
    public function upgrade(): void
    {
        // We now write only upper-case values to these columns.
        DB::query(table: 'media_file')->update(values: [
            'multimedia_format' => DB::upper('multimedia_format'),
        ]);

        DB::query(table: 'media_file')->update(values: [
            'source_media_type' => DB::upper('source_media_type'),
        ]);

        if (!DB::schemaBuilder()->hasColumn(table: 'gedcom', column: 'media_folder')) {
            DB::schemaBuilder()->table(table: 'gedcom', callback: function (Blueprint $table): void {
                $table->string(column: 'media_folder', length: 255)->default(value: 'media/')->index();
                $table->string(column: 'title', length: 255)->default(value: 'tree')->index();
                $table->string(column: 'gedcom_filename', length: 255)->default(value: 'tree.ged')->index();
                $table->integer(column: 'imported')->default(value: 1)->index();
                $table->integer(column: 'private')->default(value: 0)->index();
                $table->integer(column: 'contact_user_id')->nullable()->index();
                $table->integer(column: 'support_user_id')->nullable()->index();

                if (DB::driverName() === Driver::SqlServer->value) {
                    // SQL-Server can't use CASCADE or SET NULL constraints here, as it can't handle multiple paths
                    $table->foreign(columns: ['contact_user_id'])->references(['user_id'])->on('user');
                    $table->foreign(columns: ['support_user_id'])->references(['user_id'])->on('user');
                } else {
                    $table->foreign(columns: ['contact_user_id'])->references(['user_id'])->on('user')->nullOnDelete()->cascadeOnUpdate();
                    $table->foreign(columns: ['support_user_id'])->references(['user_id'])->on('user')->nullOnDelete()->cascadeOnUpdate();
                }
            });
        }

        $new_columns = [
            'MEDIA_DIRECTORY'        => 'media_folder',
            'imported'               => 'imported',
            'title'                  => 'title',
            'gedcom_filename'        => 'gedcom_filename',
            'REQUIRE_AUTHENTICATION' => 'private',
            'CONTACT_USER_ID'        => 'contact_user_id',
            'WEBMASTER_USER_ID'      => 'support_user_id',
        ];

        $rows = DB::query()
            ->select('gedcom_id', 'setting_name', 'setting_value')
            ->from('gedcom_setting')
            ->whereIn('setting_name', array_keys($new_columns))
            ->get();

        foreach ($rows as $row) {
            $column = $new_columns[$row->setting_name];

            $value = match ($row->setting_name) {
                'imported', 'REQUIRE_AUTHENTICATION'   => (int) (bool) $row->setting_value,
                'CONTACT_USER_ID', 'WEBMASTER_USER_ID' => (int) $row->setting_value,
                default                                => mb_substr($row->setting_value, 0, 255),
            };

            try {
                DB::query(table: 'gedcom')
                    ->whereEqual('gedcom_id', $row->gedcom_id)
                    ->update(values: [$column => $value]);
            } catch (QueryException) {
                // If it violates a foreign key constraint, then just ignore it.
            }
        }

        DB::query(table: 'gedcom_setting')
            ->whereIn('setting_name', array_keys($new_columns))
            ->delete();

        // Old setting, no longer used.
        DB::query(table: 'gedcom_setting')
            ->whereIn('setting_name', ['LANGUAGE'])
            ->delete();
    }
}
