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
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\GuestUser;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Logout::class)]
class LogoutTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(Logout::class));
    }

    public function testHandleLogoutAuthenticatedUser(): void
    {
        $user_service = new UserService();
        $user = $user_service->create('logoutuser', 'Logout User', 'logout@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_EMAIL_VERIFIED, '1');
        $user->setPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED, '1');

        Auth::login($user);
        self::assertSame($user->id(), Auth::id());

        $handler  = new Logout();
        $request  = self::createRequest()
            ->withAttribute('user', $user);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertNull(Auth::id());
    }

    public function testHandleLogoutGuestUser(): void
    {
        $handler  = new Logout();
        $request  = self::createRequest()
            ->withAttribute('user', new GuestUser());
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleAjaxLogout(): void
    {
        $user_service = new UserService();
        $user = $user_service->create('ajaxlogout', 'Ajax Logout', 'ajax@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_EMAIL_VERIFIED, '1');
        $user->setPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED, '1');

        Auth::login($user);

        $handler  = new Logout();
        $request  = self::createRequest()
            ->withAttribute('user', $user)
            ->withHeader('x-requested-with', 'XMLHttpRequest');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
        self::assertNull(Auth::id());
    }
}
