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
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\HomePageService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserPageBlockEdit::class)]
class UserPageBlockEditTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(UserPageBlockEdit::class));
    }

    public function testHandleReturnsOkResponse(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('upbe', 'User Block Edit');

        $user_service = new UserService();
        $user         = $user_service->create('upbe', 'User Block Edit', 'upbe@example.com', 'secret');

        $block = self::createStub(ModuleBlockInterface::class);
        $block->method('title')->willReturn('Test User Block');
        $block->method('editBlockConfiguration')->willReturn('');

        $home_page_service = $this->createMock(HomePageService::class);
        $home_page_service->expects(self::once())
            ->method('userBlock')
            ->willReturn($block);

        $handler  = new UserPageBlockEdit($home_page_service);
        $request  = self::createRequest('GET', [], [], [], ['tree' => $tree, 'user' => $user, 'block_id' => 1]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
