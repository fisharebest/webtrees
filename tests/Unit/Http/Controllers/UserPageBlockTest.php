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
use Fisharebest\Webtrees\Http\Controllers\UserPageBlock;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\HomePageService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserPageBlock::class)]
class UserPageBlockTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(UserPageBlock::class));
    }

    public function testLoadsOnlyBlocksBelongingToTheUser(): void
    {
        self::createDatabase();

        $tree         = (new TreeService(new GedcomImportService()))->create('name', 'title');
        $user_service = new UserService(new SystemClock());
        $user         = $user_service->create('first', 'First', 'first@example.com', 'secret');
        $other_user   = $user_service->create('second', 'Second', 'second@example.com', 'secret');
        DB::query(table: 'module')->insert(['module_name' => 'test-block']);
        $block_id = DB::query(table: 'block')->insertGetId([
            'user_id'     => $user->id(),
            'location'    => 'main',
            'block_order' => 1,
            'module_name' => 'test-block',
        ], 'block_id');

        $module = self::createStub(ModuleBlockInterface::class);
        $module->method('getBlock')->willReturn('Block content');
        $block_ids         = [];
        $home_page_service = self::createMock(HomePageService::class);
        $home_page_service->expects(self::exactly(2))->method('getBlockModule')
            ->willReturnCallback(static function (Tree $requested_tree, int $requested_block_id) use (&$block_ids, $module, $tree): ModuleBlockInterface {
                self::assertSame($tree, $requested_tree);
                $block_ids[] = $requested_block_id;

                return $module;
            });

        $request = self::createRequest(query: ['block_id' => (string) $block_id], attributes: ['tree' => $tree]);

        self::assertStringContainsString('Block content', (string) (new UserPageBlock($user, $home_page_service))->get($request, $tree)->getBody());
        (new UserPageBlock($other_user, $home_page_service))->get($request, $tree);

        self::assertSame([$block_id, 0], $block_ids);
    }
}
