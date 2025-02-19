<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use Fisharebest\Webtrees\Contracts\CacheFactoryInterface;
use Fisharebest\Webtrees\Services\UserService;
use Symfony\Component\Cache\Adapter\NullAdapter;

/**
 * @covers \Fisharebest\Webtrees\User
 */
class UserTest extends TestCase
{
    protected static bool $uses_database = true;

    protected function setUp(): void
    {
        parent::setUp();

        $cache_factory = $this->createMock(CacheFactoryInterface::class);
        $cache_factory->method('array')->willReturn(new Cache(new NullAdapter()));
        Registry::cache($cache_factory);
    }

    public function testConstructor(): void
    {
        $user = new User(123, 'username', 'real name', 'email');

        self::assertSame(123, $user->id());
        self::assertSame('email', $user->email());
        self::assertSame('real name', $user->realName());
        self::assertSame('username', $user->userName());
    }

    public function testGettersAndSetters(): void
    {
        $user_service = new UserService();
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');

        self::assertSame(1, $user->id());

        self::assertSame('user', $user->userName());
        $user->setUserName('foo');
        self::assertSame('foo', $user->userName());

        self::assertSame('User', $user->realName());
        $user->setRealName('Foo');
        self::assertSame('Foo', $user->realName());

        self::assertSame('user@example.com', $user->email());
        $user->setEmail('foo@example.com');
        self::assertSame('foo@example.com', $user->email());

        self::assertTrue($user->checkPassword('secret'));
        $user->setPassword('letmein');
        self::assertTrue($user->checkPassword('letmein'));
    }

    public function testPreferences(): void
    {
        $user_service = new UserService();
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');

        self::assertSame('', $user->getPreference('foo'));
        $user->setPreference('foo', 'bar');
        self::assertSame('bar', $user->getPreference('foo'));
    }
}
