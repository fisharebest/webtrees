<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Illuminate\Database\Schema\Blueprint;

/**
 * Upgrade the database schema from version 39 to version 40.
 */
class Migration39 implements MigrationInterface
{
    public function upgrade(): void
    {
        // This table was previously created by the favorites module in 1.7.9.
        // These migrations are now part of the core code.

        if (!DB::schema()->hasTable('favorite')) {
            DB::schema()->create('favorite', static function (Blueprint $table): void {
                $table->integer('favorite_id', true);
                $table->integer('user_id')->nullable();
                $table->integer('gedcom_id');
                $table->string('xref', 20)->nullable();
                $table->enum('favorite_type', ['INDI', 'FAM', 'SOUR', 'REPO', 'OBJE', 'NOTE', 'URL']);
                $table->string('url', 255)->nullable();
                $table->string('title', 255)->nullable();
                $table->string('note', 1000)->nullable();

                $table->index('user_id');
                $table->index(['gedcom_id', 'user_id']);

                $table->foreign('user_id')->references('user_id')->on('user')->onDelete('cascade');
                $table->foreign('gedcom_id')->references('gedcom_id')->on('gedcom')->onDelete('cascade');
            });
        }
    }
}
