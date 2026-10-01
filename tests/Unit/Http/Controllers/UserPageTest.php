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
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Http\Controllers\UserPage;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\HomePageService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserPage::class)]
class UserPageTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(UserPage::class));
    }

    public function testInitializesOnlyUsersWithoutBlocks(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('name', 'title');
        $user = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        DB::query(table: 'module')->insert(['module_name' => 'test-block']);
        DB::query(table: 'block')->insert([
            'user_id'     => -1,
            'location'    => 'main',
            'block_order' => 1,
            'module_name' => 'test-block',
        ]);
        self::createRequest(attributes: ['tree' => $tree]);
        Registry::container()->set(ModuleThemeInterface::class, self::createStub(ModuleThemeInterface::class));

        $home_page_service = self::createMock(HomePageService::class);
        $home_page_service->expects(self::once())->method('checkDefaultUserBlocksExist');
        $home_page_service->method('userBlocks')->willReturn(new Collection());
        $controller = new UserPage($user, $home_page_service);

        self::assertSame(HttpStatusCode::OK->value, $controller->get($tree)->getStatusCode());
        self::assertSame(1, DB::query(table: 'block')->whereEqual('user_id', $user->id())->count());

        self::assertSame(HttpStatusCode::OK->value, $controller->get($tree)->getStatusCode());
        self::assertSame(1, DB::query(table: 'block')->whereEqual('user_id', $user->id())->count());
    }
}
