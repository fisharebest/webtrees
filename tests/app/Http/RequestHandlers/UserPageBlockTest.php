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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Services\HomePageService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserPageBlock::class)]
class UserPageBlockTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(UserPageBlock::class));
    }

    public function testHandleReturnsOkResponse(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('id')->willReturn(1);

        $user_service = new UserService();
        $user         = $user_service->create('upb', 'User Page Block', 'upb@example.com', 'secret');

        $block = self::createStub(ModuleBlockInterface::class);
        $block->method('getBlock')->willReturn('<p>User block content</p>');

        $home_page_service = $this->createMock(HomePageService::class);
        $home_page_service->expects(self::once())
            ->method('getBlockModule')
            ->willReturn($block);

        $handler  = new UserPageBlock($home_page_service);
        // block_id=0 will not match any DB row, so DB lookup returns 0
        $request  = self::createRequest('GET', ['block_id' => '0'], [], [], ['tree' => $tree, 'user' => $user]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
