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

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;

/**
 * Upgrade the database schema from version 37 to version 38.
 */
class Migration37 implements MigrationInterface
{
    /**
     * Upgrade to the next version
     *
     * @return void
     */
    public function upgrade(): void
    {
        // These tables were created by webtrees 1.x, and may not exist if we first installed webtrees 2.x
        DB::schema()->dropIfExists('site_access_rule');
        DB::schema()->dropIfExists('next_id');

        // Split the media table into media/media_file so that we can store multiple media
        // files in each media object.
        if (!DB::schema()->hasTable('media_file')) {
            DB::schema()->create('media_file', static function (Blueprint $table): void {
                $table->integer('id', true);
                $table->string('m_id', 20);
                $table->integer('m_file');
                $table->string('multimedia_file_refn', 248); // GEDCOM only allows 30 characters
                $table->string('multimedia_format', 4);
                $table->string('source_media_type', 15);
                $table->string('descriptive_title', 248);

                $table->index(['m_id', 'm_file']);
                $table->index(['m_file', 'm_id']);
                $table->index(['m_file', 'multimedia_file_refn']);
                $table->index(['m_file', 'multimedia_format']);
                $table->index(['m_file', 'source_media_type']);
                $table->index(['m_file', 'descriptive_title']);
            });
        }

        if (DB::table('media_file')->count() === 0 && DB::schema()->hasColumn('media', 'm_filename')) {
            (new Builder(DB::connection()))->from('media_file')->insertUsing([
                'm_id',
                'm_file',
                'multimedia_file_refn',
                'multimedia_format',
                'source_media_type',
                'descriptive_title',
            ], function (Builder $query): void {
                // SQLite also supports SUBSTRING() from 3.34.0 (2020-12-01)
                $substring_function = DB::connection()->getDriverName() === 'sqlite' ? 'SUBSTR' : 'SUBSTRING';

                $query->select([
                    'm_id',
                    'm_file',
                    new Expression($substring_function . '(m_filename, 1, 248)'),
                    new Expression($substring_function . '(m_ext, 1, 4)'),
                    new Expression($substring_function . '(m_type, 1, 15)'),
                    new Expression($substring_function . '(m_titl, 1, 248)'),
                ])->from('media');
            });

            // The Laravel database library for SQLite can only drop one column at a time.
            DB::schema()->table('media', static function (Blueprint $table): void {
                $table->dropColumn('m_filename');
            });
            DB::schema()->table('media', static function (Blueprint $table): void {
                $table->dropColumn('m_ext');
            });
            DB::schema()->table('media', static function (Blueprint $table): void {
                $table->dropColumn('m_type');
            });
            DB::schema()->table('media', static function (Blueprint $table): void {
                $table->dropColumn('m_titl');
            });
        }
    }
}
