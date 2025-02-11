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

use Fisharebest\Webtrees\Contracts\UserInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GuestUser::class)]
class GuestUserTest extends TestCase
{
    public function testAnonymous(): void
    {
        $user = new GuestUser();

        self::assertSame(0, $user->id());
        self::assertSame('GUEST_USER', $user->email());
        self::assertSame('GUEST_USER', $user->realName());
        self::assertSame('', $user->userName());
    }

    public function testVisitor(): void
    {
        $user = new GuestUser('guest@example.com', 'guest user');

        self::assertSame(0, $user->id());
        self::assertSame('guest@example.com', $user->email());
        self::assertSame('guest user', $user->realName());
        self::assertSame('', $user->userName());
    }

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
