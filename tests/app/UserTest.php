<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Services\UserService;

/**
 * Test the user functions
 */
class UserTest extends TestCase
{
    protected static $uses_database = true;

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

        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertSame(123, $user->id());
        $this->assertSame('email', $user->email());
        $this->assertSame('real name', $user->realName());
        $this->assertSame('username', $user->userName());
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

        $this->assertSame(1, $user->id());

        $this->assertSame('user', $user->userName());
        $user->setUserName('foo');
        $this->assertSame('foo', $user->userName());

        $this->assertSame('User', $user->realName());
        $user->setRealName('Foo');
        $this->assertSame('Foo', $user->realName());

        $this->assertSame('user@example.com', $user->email());
        $user->setEmail('foo@example.com');
        $this->assertSame('foo@example.com', $user->email());

        $this->assertTrue($user->checkPassword('secret'));
        $user->setPassword('letmein');
        $this->assertTrue($user->checkPassword('letmein'));
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

        $this->assertSame('', $user->getPreference('foo'));
        $user->setPreference('foo', 'bar');
        $this->assertSame('bar', $user->getPreference('foo'));
    }
}
