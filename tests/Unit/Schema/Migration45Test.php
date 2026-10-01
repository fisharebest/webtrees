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
use Fisharebest\Webtrees\Schema\Migration45;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Migration45::class)]
class Migration45Test extends TestCase
{
    public function testCopiesSettingsAndUppercasesMediaFormats(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('test', 'Test');
        DB::query(table: 'media_file')->insert(values: [
            'm_id' => 'M1',
            'm_file' => $tree->id(),
            'multimedia_file_refn' => 'image.jpg',
            'multimedia_format' => 'jpg',
            'source_media_type' => 'photo',
            'descriptive_title' => '',
        ]);
        DB::query(table: 'gedcom_setting')->insert(values: [
            'gedcom_id' => $tree->id(),
            'setting_name' => 'title',
            'setting_value' => 'Updated title',
        ]);

        (new Migration45())->upgrade();

        self::assertSame('Updated title', DB::query()->from('gedcom')->whereEqual('gedcom_id', $tree->id())->value('title'));
        self::assertSame(0, DB::query()->from('gedcom_setting')->whereEqual('setting_name', 'title')->count());
        self::assertSame('JPG', DB::query()->from('media_file')->whereEqual('m_id', 'M1')->value('multimedia_format'));
        self::assertSame('PHOTO', DB::query()->from('media_file')->whereEqual('m_id', 'M1')->value('source_media_type'));
    }
}
