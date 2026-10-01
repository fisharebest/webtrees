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

namespace Fisharebest\Webtrees\Tests\Unit\Services;

use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\HomePageService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(HomePageService::class)]
class HomePageServiceTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(HomePageService::class));
    }

    public function testLoadsOnlyBlocksInTheRequestedScope(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('first', 'First');
        $user = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        $block = self::createStub(ModuleBlockInterface::class);
        $block->method('name')->willReturn('test-block');
        $module_service = self::createStub(ModuleService::class);
        $module_service->method('findByName')->willReturn($block);
        $module_service->method('findByComponent')->willReturn(new Collection([$block]));
        $service = new HomePageService($module_service);

        DB::query(table: 'module')->insert(values: ['module_name' => 'test-block']);
        DB::query(table: 'block')->insert(values: [
            'block_id' => 101, 'gedcom_id' => $tree->id(), 'block_order' => 1, 'module_name' => 'test-block',
        ]);
        DB::query(table: 'block')->insert(values: [
            'block_id' => 102, 'user_id' => $user->id(), 'block_order' => 1, 'module_name' => 'test-block',
        ]);

        self::assertSame($block, $service->treeBlock(self::createRequest(attributes: ['tree' => $tree, 'block_id' => '101'])));
        self::assertSame($block, $service->userBlock(self::createRequest(attributes: ['block_id' => '102']), $user));
        self::assertSame($block, $service->getBlockModule($tree, 101));

        $this->expectException(HttpNotFoundException::class);
        $service->treeBlock(self::createRequest(attributes: ['tree' => $tree, 'block_id' => '102']));
    }

    public function testListsAndUpdatesOnlyMatchingBlocks(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('first', 'First');
        $user = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        $block = self::createStub(ModuleBlockInterface::class);
        $block->method('name')->willReturn('test-block');
        $block->method('isTreeBlock')->willReturn(true);
        $block->method('isUserBlock')->willReturn(true);
        $module_service = self::createStub(ModuleService::class);
        $module_service->method('findByComponent')->willReturn(new Collection([$block]));
        $service = new HomePageService($module_service);

        DB::query(table: 'module')->insert(values: ['module_name' => 'test-block']);
        $blocks = [
            ['block_id' => 101, 'gedcom_id' => $tree->id(), 'location' => 'main', 'block_order' => 2],
            ['block_id' => 102, 'gedcom_id' => $tree->id(), 'location' => 'main', 'block_order' => 1],
            ['block_id' => 103, 'gedcom_id' => $tree->id(), 'location' => 'side', 'block_order' => 1],
            ['block_id' => 201, 'user_id' => $user->id(), 'location' => 'main', 'block_order' => 2],
            ['block_id' => 202, 'user_id' => $user->id(), 'location' => 'main', 'block_order' => 1],
            ['block_id' => 203, 'user_id' => $user->id(), 'location' => 'side', 'block_order' => 1],
        ];

        foreach ($blocks as $values) {
            DB::query(table: 'block')->insert(values: $values + ['module_name' => 'test-block']);
        }

        self::assertSame([102, 101], $service->treeBlocks($tree, $user, 'main')->keys()->all());
        self::assertSame([202, 201], $service->userBlocks($tree, $user, 'main')->keys()->all());

        $service->updateTreeBlocks($tree->id(), new Collection(['102']), new Collection(['103']));
        $service->updateUserBlocks($user->id(), new Collection(['202']), new Collection(['203']));

        self::assertSame([102, 103], DB::query()
            ->from('block')
            ->whereEqual('gedcom_id', $tree->id())
            ->orderBy('block_id')
            ->pluck('block_id'));
        self::assertSame([202, 203], DB::query()
            ->from('block')
            ->whereEqual('user_id', $user->id())
            ->orderBy('block_id')
            ->pluck('block_id'));
    }

    public function testExistingDefaultBlocksAreNotDuplicated(): void
    {
        self::createDatabase();

        DB::query(table: 'module')->insert(values: ['module_name' => 'test-block']);
        DB::query(table: 'block')->insert(values: ['gedcom_id' => -1, 'block_order' => 1, 'module_name' => 'test-block']);
        DB::query(table: 'block')->insert(values: ['user_id' => -1, 'block_order' => 1, 'module_name' => 'test-block']);

        $service = new HomePageService(self::createStub(ModuleService::class));
        $service->checkDefaultTreeBlocksExist();
        $service->checkDefaultUserBlocksExist();

        self::assertSame(2, DB::query()
            ->from('block')
            ->count());
    }
}
