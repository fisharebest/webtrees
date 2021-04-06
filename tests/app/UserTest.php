<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Services\UserService;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

/**
 * Test the user functions
 */
class UserTest extends TestCase
{
    protected static $uses_database = true;

    public function setUp(): void
    {
        parent::setUp();

        $cache_factory = self::createMock(CacheFactoryInterface::class);
        $cache_factory->method('array')->willReturn(new Cache(new TagAwareAdapter(new NullAdapter())));
        Registry::cache($cache_factory);
    }

    /**
     * @covers \Fisharebest\Webtrees\User::__construct
     * @covers \Fisharebest\Webtrees\User::id
     * @covers \Fisharebest\Webtrees\User::email
     * @covers \Fisharebest\Webtrees\User::realName
     * @covers \Fisharebest\Webtrees\User::userName
     * @return void
     */
    public function testConstructor(): void
    {
        $user = new User(123, 'username', 'real name', 'email');

        self::assertInstanceOf(UserInterface::class, $user);
        self::assertSame(123, $user->id());
        self::assertSame('email', $user->email());
        self::assertSame('real name', $user->realName());
        self::assertSame('username', $user->userName());
    }

    /**
     * @covers \Fisharebest\Webtrees\User::setUserName
     * @covers \Fisharebest\Webtrees\User::userName
     * @covers \Fisharebest\Webtrees\User::setRealName
     * @covers \Fisharebest\Webtrees\User::realName
     * @covers \Fisharebest\Webtrees\User::setEmail
     * @covers \Fisharebest\Webtrees\User::email
     * @covers \Fisharebest\Webtrees\User::setPassword
     * @covers \Fisharebest\Webtrees\User::checkPassword
     * @return void
     */
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

    /**
     * @covers \Fisharebest\Webtrees\User::setPreference
     * @covers \Fisharebest\Webtrees\User::getPreference
     * @return void
     */
    public function testPreferences(): void
    {
        $user_service = new UserService();
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');

        self::assertSame('', $user->getPreference('foo'));
        $user->setPreference('foo', 'bar');
        self::assertSame('bar', $user->getPreference('foo'));
    }
}
