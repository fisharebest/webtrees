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

#[CoversClass(NoReplyUser::class)]
class NoReplyUserTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testConstructor(): void
    {
        $user = new NoReplyUser();

        self::assertInstanceOf(UserInterface::class, $user);
        self::assertSame(0, $user->id());
        self::assertSame('no-reply@localhost', $user->email());
        self::assertSame(Webtrees::NAME, $user->realName());
        self::assertSame('', $user->userName());
    }

    public function testPreferences(): void
    {
        $user = new NoReplyUser();

        self::assertSame('', $user->getPreference('foo'));
        self::assertSame('', $user->getPreference('foo'));
        self::assertSame('bar', $user->getPreference('foo', 'bar'));

        // No reply users do not have preferences
        $user->setPreference('foo', 'bar');

        self::assertSame('', $user->getPreference('foo'));
    }
}
