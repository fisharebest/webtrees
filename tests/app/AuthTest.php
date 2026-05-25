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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Cli\Console;
use Fisharebest\Webtrees\Services\UserService;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Auth::class)]
class AuthTest extends TestCase
{
    private UserService $user_service;

    protected static bool $uses_database = true;

    protected function setup(): void
    {
        parent::setup();

        $this->user_service = self::createStub(UserService::class);
        Registry::container()->set(UserService::class, $this->user_service);
    }

    public function testClass(): void
    {
        self::assertTrue(class_exists(Auth::class));
    }

    public function testLoginLogout(): void
    {
        $user = new DefaultUser();
        $this->user_service->method('find')->willReturn(null, $user, null);

        self::assertInstanceOf('Fisharebest\Webtrees\GuestUser', Auth::user());

        Auth::login($user);
        self::assertInstanceOf('Fisharebest\Webtrees\DefaultUser', Auth::user());

        Auth::logout();
        $this->user_service->method('find');
        self::assertInstanceOf('Fisharebest\Webtrees\GuestUser', Auth::user());
    }

    public function testCli(): void
    {
        self::assertInstanceOf('Fisharebest\Webtrees\GuestUser', Auth::user());
        Session::put(Console::CLI_SESSION, '1');
        self::assertInstanceOf('Fisharebest\Webtrees\CLiUser', Auth::user());
    }

}
