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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Controllers;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\Controllers\MergeTrees;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\AdminService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MergeTrees::class)]
class MergeTreesTest extends TestCase
{
    public function testCopiesRecordsAndSkipsHeaderAndTrailer(): void
    {
        self::createDatabase();

        $tree_service = new TreeService(new GedcomImportService());
        $source = $tree_service->create('source', 'Source');
        $target = $tree_service->create('target', 'Target');

        DB::query(table: 'individuals')->whereEqual('i_file', $target->id())->delete();
        DB::query(table: 'name')->whereEqual('n_file', $target->id())->delete();
        DB::query(table: 'dates')->whereEqual('d_file', $target->id())->delete();

        foreach ([$source, $target] as $tree) {
            DB::query(table: 'gedcom')->whereEqual('gedcom_id', $tree->id())
                ->update(['imported' => 1, 'private' => 0]);
        }

        DB::query(table: 'families')->insert([
            'f_file' => $source->id(), 'f_id' => 'F1', 'f_gedcom' => '0 @F1@ FAM', 'f_numchil' => 0,
        ]);
        DB::query(table: 'sources')->insert([
            's_file' => $source->id(), 's_id' => 'S1', 's_name' => 'Source record', 's_gedcom' => '0 @S1@ SOUR',
        ]);
        DB::query(table: 'media')->insert([
            'm_file' => $source->id(), 'm_id' => 'M1', 'm_gedcom' => '0 @M1@ OBJE',
        ]);
        DB::query(table: 'media_file')->insert([
            'm_file' => $source->id(), 'm_id' => 'M1', 'multimedia_file_refn' => 'example.jpg',
            'multimedia_format' => 'jpg', 'source_media_type' => 'photo', 'descriptive_title' => 'Example',
        ]);
        DB::query(table: 'other')->insert([
            'o_file' => $source->id(), 'o_id' => 'R1', 'o_type' => 'REPO', 'o_gedcom' => '0 @R1@ REPO',
        ]);
        DB::query(table: 'link')->insert([
            'l_file' => $source->id(), 'l_from' => 'X1', 'l_type' => 'FAMC', 'l_to' => 'F1',
        ]);
        DB::query(table: 'link')->insert([
            'l_file' => $source->id(), 'l_from' => 'HEAD', 'l_type' => 'SOUR', 'l_to' => 'S1',
        ]);

        Registry::cache()->array()->forget('all-trees');
        $request = self::createRequest(params: ['tree1_name' => 'source', 'tree2_name' => 'target']);
        (new MergeTrees(new AdminService(), $tree_service))->post($request);

        foreach (
            [
                'individuals' => 'i_file',
                'families'    => 'f_file',
                'sources'     => 's_file',
                'media'       => 'm_file',
                'media_file'  => 'm_file',
                'name'        => 'n_file',
                'dates'       => 'd_file',
            ] as $table => $column
        ) {
            $source_count = DB::query(table: $table)->whereEqual($column, $source->id())->count();
            self::assertGreaterThan(0, $source_count, $table);
            self::assertSame(
                $source_count,
                DB::query(table: $table)->whereEqual($column, $target->id())->count(),
                $table,
            );
        }

        self::assertSame(1, DB::query(table: 'other')
            ->whereEqual('o_file', $target->id())->whereEqual('o_type', 'REPO')->count());
        self::assertSame(0, DB::query(table: 'other')
            ->whereEqual('o_file', $target->id())->whereEqual('o_type', 'TRLR')->count());
        self::assertSame(1, DB::query(table: 'link')
            ->whereEqual('l_file', $target->id())->whereEqual('l_from', 'X1')->count());
        self::assertSame(0, DB::query(table: 'link')
            ->whereEqual('l_file', $target->id())->whereEqual('l_from', 'HEAD')->count());
    }
}
