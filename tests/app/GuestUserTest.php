<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

/**
 * Test the GuestUser class
 */
class GuestUserTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\GuestUser::__construct
     * @covers \Fisharebest\Webtrees\GuestUser::id
     * @covers \Fisharebest\Webtrees\GuestUser::email
     * @covers \Fisharebest\Webtrees\GuestUser::realName
     * @covers \Fisharebest\Webtrees\GuestUser::userName
     * @return void
     */
    public function testAnonymous(): void
    {
        $user = new GuestUser();

        self::assertInstanceOf(UserInterface::class, $user);
        self::assertSame(0, $user->id());
        self::assertSame('GUEST_USER', $user->email());
        self::assertSame('GUEST_USER', $user->realName());
        self::assertSame('', $user->userName());
    }

    /**
     * @covers \Fisharebest\Webtrees\GuestUser::__construct
     * @covers \Fisharebest\Webtrees\GuestUser::id
     * @covers \Fisharebest\Webtrees\GuestUser::email
     * @covers \Fisharebest\Webtrees\GuestUser::realName
     * @covers \Fisharebest\Webtrees\GuestUser::userName
     * @return void
     */
    public function testVisitor(): void
    {
        $user = new GuestUser('guest@example.com', 'guest user');

        self::assertInstanceOf(UserInterface::class, $user);
        self::assertSame(0, $user->id());
        self::assertSame('guest@example.com', $user->email());
        self::assertSame('guest user', $user->realName());
        self::assertSame('', $user->userName());
    }

    /**
     * @covers \Fisharebest\Webtrees\GuestUser::getPreference
     * @covers \Fisharebest\Webtrees\GuestUser::setPreference
     * @return void
     */
    public function testPreferences(): void
    {
        $user = new GuestUser();

        self::assertSame('', $user->getPreference('foo'));
        self::assertSame('', $user->getPreference('foo'));
        self::assertSame('bar', $user->getPreference('foo', 'bar'));

        // Guests users store preferences in the session
        $user->setPreference('foo', 'bar');

        self::assertSame('bar', $user->getPreference('foo'));
    }
}
