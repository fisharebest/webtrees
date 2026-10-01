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
use Fisharebest\Webtrees\Enums\ChangeStatus;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Http\Controllers\CheckTree;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\MemoryService;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CheckTree::class)]
class CheckTreeTest extends TestCase
{
    public function testLatestPendingChangeOverridesStoredRecord(): void
    {
        self::createDatabase();

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');
        $user         = (new UserService(new SystemClock()))->create('tester', 'Tester', 'tester@example.com', 'secret');

        foreach (
            [
                [$tree->id(), ChangeStatus::Pending->value, 'OLDER_CHANGE'],
                [$tree->id(), ChangeStatus::Pending->value, 'LATEST_CHANGE'],
                [$tree->id(), ChangeStatus::Accepted->value, 'ACCEPTED_CHANGE'],
                [$other_tree->id(), ChangeStatus::Pending->value, 'OTHER_TREE_CHANGE'],
            ] as [$tree_id, $status, $value]
        ) {
            DB::query(table: 'change')->insert(values: [
                'gedcom_id'  => $tree_id,
                'xref'       => 'X1',
                'status'     => $status,
                'old_gedcom' => '',
                'new_gedcom' => "0 @X1@ INDI\n1 BADTAG " . $value,
                'user_id'    => $user->id(),
            ]);
        }

        $php_service = new PhpService();
        $controller  = new CheckTree(new Gedcom(), new TimeoutService($php_service, new SystemClock()), new MemoryService($php_service));
        $request     = self::createRequest(attributes: ['tree' => $tree]);
        $html        = (string) $controller->get($request, $tree)->getBody();

        self::assertStringContainsString('LATEST_CHANGE', $html);
        self::assertStringNotContainsString('OLDER_CHANGE', $html);
        self::assertStringNotContainsString('ACCEPTED_CHANGE', $html);
        self::assertStringNotContainsString('OTHER_TREE_CHANGE', $html);
    }

    public function testClass(): void
    {
        self::assertTrue(class_exists(CheckTree::class));
    }
}
