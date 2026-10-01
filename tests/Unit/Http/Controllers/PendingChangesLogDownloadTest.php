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
use Fisharebest\Webtrees\Http\Controllers\PendingChangesLogDownload;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PendingChangesLogDownload::class)]
class PendingChangesLogDownloadTest extends TestCase
{
    public function testDownloadsFilteredChanges(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('test', 'Test');
        $user = (new UserService(new SystemClock()))->create('tester', 'Tester', 'tester@example.com', 'secret');

        foreach (['accepted', 'pending'] as $status) {
            DB::query(table: 'change')->insert([
                'gedcom_id'  => $tree->id(),
                'xref'       => 'I1',
                'status'     => $status,
                'old_gedcom' => '',
                'new_gedcom' => '0 @I1@ INDI',
                'user_id'    => $user->id(),
            ]);
        }

        $controller = new PendingChangesLogDownload(new PendingChangesService(new GedcomImportService()));
        $response   = $controller->get(self::createRequest(query: ['type' => 'accepted']), $tree);

        self::assertStringContainsString('"accepted"', (string) $response->getBody());
        self::assertStringNotContainsString('"pending"', (string) $response->getBody());
    }
}
