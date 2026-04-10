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

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LoginAction::class)]
class LoginActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(LoginAction::class));
    }

    public function testHandleSuccessfulLogin(): void
    {
        $_COOKIE['webtrees'] = 'session_id';

        $user_service = new UserService();
        $user = $user_service->create('testuser', 'Test User', 'test@example.com', 'secret123');
        $user->setPreference(UserInterface::PREF_IS_EMAIL_VERIFIED, '1');
        $user->setPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED, '1');

        $upgrade_service = $this->createMock(UpgradeService::class);
        $upgrade_service->method('isUpgradeAvailable')->willReturn(false);

        $handler  = new LoginAction($upgrade_service, $user_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'username' => 'testuser',
            'password' => 'secret123',
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertSame($user->id(), Session::get('wt_user'));

        unset($_COOKIE['webtrees']);
    }

    public function testHandleWithInvalidPassword(): void
    {
        $_COOKIE['webtrees'] = 'session_id';

        $user_service = new UserService();
        $user = $user_service->create('wrongpass', 'Wrong Pass', 'wrong@example.com', 'correct');
        $user->setPreference(UserInterface::PREF_IS_EMAIL_VERIFIED, '1');
        $user->setPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED, '1');

        $upgrade_service = self::createStub(UpgradeService::class);

        $handler  = new LoginAction($upgrade_service, $user_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'username' => 'wrongpass',
            'password' => 'incorrect',
        ]);
        $response = $handler->handle($request);

        // Failed login redirects back to login page
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertNull(Session::get('wt_user'));

        unset($_COOKIE['webtrees']);
    }

    public function testHandleWithUnknownUser(): void
    {
        $_COOKIE['webtrees'] = 'session_id';

        $user_service = new UserService();

        $upgrade_service = self::createStub(UpgradeService::class);

        $handler  = new LoginAction($upgrade_service, $user_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'username' => 'nonexistent',
            'password' => 'anything',
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertNull(Session::get('wt_user'));

        unset($_COOKIE['webtrees']);
    }

    public function testHandleWithUnverifiedEmail(): void
    {
        $_COOKIE['webtrees'] = 'session_id';

        $user_service = new UserService();
        $user = $user_service->create('unverified', 'Unverified', 'unverified@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_EMAIL_VERIFIED, '0');
        $user->setPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED, '1');

        $upgrade_service = self::createStub(UpgradeService::class);

        $handler  = new LoginAction($upgrade_service, $user_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'username' => 'unverified',
            'password' => 'secret',
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertNull(Session::get('wt_user'));

        unset($_COOKIE['webtrees']);
    }

    public function testHandleWithUnapprovedAccount(): void
    {
        $_COOKIE['webtrees'] = 'session_id';

        $user_service = new UserService();
        $user = $user_service->create('unapproved', 'Unapproved', 'unapproved@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_EMAIL_VERIFIED, '1');
        $user->setPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED, '0');

        $upgrade_service = self::createStub(UpgradeService::class);

        $handler  = new LoginAction($upgrade_service, $user_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'username' => 'unapproved',
            'password' => 'secret',
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertNull(Session::get('wt_user'));

        unset($_COOKIE['webtrees']);
    }

    public function testHandleWithNoCookies(): void
    {
        $_COOKIE = [];

        $user_service = new UserService();
        $upgrade_service = self::createStub(UpgradeService::class);

        $handler  = new LoginAction($upgrade_service, $user_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'username' => 'anyone',
            'password' => 'anything',
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertNull(Session::get('wt_user'));
    }
}
