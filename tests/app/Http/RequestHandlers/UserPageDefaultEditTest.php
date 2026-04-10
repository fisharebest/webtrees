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
use Fisharebest\Webtrees\Services\HomePageService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserPageDefaultEdit::class)]
class UserPageDefaultEditTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(UserPageDefaultEdit::class));
    }

    public function testHandleReturnsOkResponse(): void
    {
        // Mock HomePageService to avoid view-rendering of real block modules.
        $home_page_service = $this->createMock(HomePageService::class);
        $home_page_service->method('checkDefaultUserBlocksExist');
        $home_page_service->method('userBlocks')->willReturn(new Collection());
        $home_page_service->method('availableUserBlocks')->willReturn(new Collection());

        $handler  = new UserPageDefaultEdit($home_page_service);
        $request  = self::createRequest();
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
