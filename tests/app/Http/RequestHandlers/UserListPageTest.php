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
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserListPage::class)]
class UserListPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(UserListPage::class));
    }

    public function testHandleReturnsOkResponse(): void
    {
        $user_service = new UserService();
        $user         = $user_service->create('ulp', 'User List Page', 'ulp@example.com', 'secret');

        $handler  = new UserListPage();
        $request  = self::createRequest('GET', [], [], [], ['user' => $user]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleWithFilterReturnsOk(): void
    {
        $user_service = new UserService();
        $user         = $user_service->create('ulpf', 'ULP Filter', 'ulpf@example.com', 'secret');

        $handler  = new UserListPage();
        $request  = self::createRequest('GET', ['filter' => 'test'], [], [], ['user' => $user]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
