<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
 * Upgrade the database schema from version 40 to version 41.
 */
class Migration40 implements MigrationInterface
{
    /**
     * Upgrade to the next version
     *
     * @return void
     */
    public function upgrade(): void
    {
        // This table was previously created by the news module in 1.7.9.
        // These migrations are now part of the core code.

        if (!DB::schema()->hasTable('news')) {
            DB::schema()->create('news', static function (Blueprint $table): void {
                $table->integer('news_id', true);
                $table->integer('user_id')->nullable();
                $table->integer('gedcom_id')->nullable();
                $table->string('subject', 255);
                $table->text('body');
                $table->timestamp('updated')->useCurrent();

                $table->index(['user_id', 'updated']);
                $table->index(['gedcom_id', 'updated']);

                $table->foreign('user_id')->references('user_id')->on('user')->onDelete('cascade');
                $table->foreign('gedcom_id')->references('gedcom_id')->on('gedcom')->onDelete('cascade');
            });
        }
    }
}
