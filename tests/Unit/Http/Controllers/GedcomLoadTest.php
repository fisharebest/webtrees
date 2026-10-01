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

use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\Controllers\GedcomLoad;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GedcomLoad::class)]
class GedcomLoadTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(GedcomLoad::class));
    }

    public function testImportsNextChunk(): void
    {
        self::createDatabase();

        $gedcom_import_service = new GedcomImportService();
        $tree                  = (new TreeService($gedcom_import_service))->create('name', 'title');

        DB::query(table: 'gedcom_chunk')->insert([
            'gedcom_id'  => $tree->id(),
            'chunk_data' => "0 HEAD\n1 SOUR webtrees\n0 TRLR",
            'imported'   => 0,
        ]);

        $controller = new GedcomLoad(
            $gedcom_import_service,
            new TimeoutService(new PhpService(), new SystemClock()),
        );

        $controller->post($tree);

        self::assertSame(1, DB::query(table: 'gedcom_chunk')->whereEqual('gedcom_id', $tree->id())->count());
        self::assertSame(1, DB::query(table: 'gedcom_chunk')->whereEqual('gedcom_id', $tree->id())->value('imported'));
    }

    public function testDeletesAtMostTenThousandRowsBeforeCheckingTimeout(): void
    {
        self::createDatabase();

        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('first', 'First');
        $other_tree            = $tree_service->create('second', 'Second');

        DB::query(table: 'gedcom_chunk')->insert([
            'gedcom_id'  => $tree->id(),
            'chunk_data' => "0 HEAD\n0 TRLR",
            'imported'   => 0,
        ]);

        for ($offset = 0; $offset < 10000; $offset += 100) {
            $rows = [];
            for ($index = $offset; $index < $offset + 100; $index++) {
                $rows[] = [
                    'i_id'     => 'I' . $index,
                    'i_file'   => $tree->id(),
                    'i_rin'    => '',
                    'i_sex'    => 'U',
                    'i_gedcom' => '0 @I' . $index . '@ INDI',
                ];
            }

            DB::query(table: 'individuals')->insertBatch($rows);
        }

        $timeout_service = self::createStub(TimeoutService::class);
        $timeout_service->method('isTimeLimitUp')->willReturn(true);

        $response = (new GedcomLoad($gedcom_import_service, $timeout_service))->post($tree);

        self::assertStringNotContainsString('data-wt-import-fail', (string) $response->getBody());
        self::assertStringContainsString('Deleting', (string) $response->getBody());
        self::assertSame(1, DB::query()->from('individuals')->whereEqual('i_file', $tree->id())->count());
        self::assertSame(1, DB::query()->from('individuals')->whereEqual('i_file', $other_tree->id())->count());
    }

    public function testKeepsMediaLinksDuringLimitedDeletion(): void
    {
        self::createDatabase();

        $gedcom_import_service = new GedcomImportService();
        $tree                  = (new TreeService($gedcom_import_service))->create('first', 'First');
        $tree->setPreference('keep_media', '1');

        DB::query(table: 'gedcom_chunk')->insert([
            'gedcom_id'  => $tree->id(),
            'chunk_data' => "0 HEAD\n0 TRLR",
            'imported'   => 0,
        ]);
        DB::query(table: 'link')->insert([
            'l_file' => $tree->id(), 'l_from' => 'I1', 'l_to' => 'M1', 'l_type' => 'OBJE',
        ]);
        DB::query(table: 'link')->insert([
            'l_file' => $tree->id(), 'l_from' => 'I1', 'l_to' => 'N1', 'l_type' => 'NOTE',
        ]);

        $timeout_service = self::createStub(TimeoutService::class);
        $timeout_service->method('isTimeLimitUp')->willReturnCallback(static fn (): bool => DB::query()
            ->from('link')
            ->whereEqual('l_file', $tree->id())
            ->whereEqual('l_type', 'NOTE')
            ->count() === 0);

        (new GedcomLoad($gedcom_import_service, $timeout_service))->post($tree);

        self::assertSame(['OBJE'], DB::query()->from('link')->whereEqual('l_file', $tree->id())->pluck('l_type'));
    }
}
