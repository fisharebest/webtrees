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
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AccountDelete::class)]
class AccountDeleteTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(AccountDelete::class));
    }

    public function testHandleDeletesNonAdminUser(): void
    {
        $user_service = new UserService();
        $user         = $user_service->create('delme', 'Delete Me', 'delme@example.com', 'password1');

        $handler = new AccountDelete($user_service);
        $request = self::createRequest()
            ->withAttribute('user', $user);

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());

        // User should be deleted
        self::assertNull($user_service->findByUserName('delme'));
    }

    public function testHandleDoesNotDeleteAdministrator(): void
    {
        $user_service = new UserService();
        $admin        = $user_service->create('nodeladmin', 'No Del Admin', 'nodeladmin@example.com', 'password1');
        $admin->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');

        $handler = new AccountDelete($user_service);
        $request = self::createRequest()
            ->withAttribute('user', $admin);

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());

        // Administrator should NOT be deleted
        self::assertNotNull($user_service->findByUserName('nodeladmin'));
    }

    public function testHandleWithGuestUserRedirects(): void
    {
        $user_service = self::createStub(UserService::class);

        // Default request has GuestUser which is not instanceof User
        $handler  = new AccountDelete($user_service);
        $request  = self::createRequest();
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
