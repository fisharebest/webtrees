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

namespace Fisharebest\Webtrees\Tests\Unit\Schema;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Schema\Migration37;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Migration37::class)]
class Migration37Test extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(Migration37::class));
    }

    public function testCopiesLegacyMediaFiles(): void
    {
        self::createDatabase();

        DB::schemaBuilder()->drop('media_file');
        DB::schemaBuilder()->table('media', static function (Blueprint $table): void {
            $table->string('m_filename', 248)->nullable();
            $table->string('m_ext', 6)->nullable();
            $table->string('m_type', 20)->nullable();
            $table->string('m_titl', 248)->nullable();
        });
        DB::query(table: 'media')->insert([
            'm_id'       => 'M1',
            'm_file'     => 1,
            'm_filename' => 'example.jpg',
            'm_ext'      => 'jpg',
            'm_type'     => 'photo',
            'm_titl'     => 'Example',
        ]);

        (new Migration37())->upgrade();

        self::assertSame(
            ['example.jpg', 'jpg', 'photo', 'Example'],
            array_values((array) DB::query()
                ->select('multimedia_file_refn', 'multimedia_format', 'source_media_type', 'descriptive_title')
                ->from('media_file')
                ->whereEqual('m_id', 'M1')
                ->first()),
        );
    }
}
