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

use Fisharebest\Webtrees\Contracts\UserInterface;

/**
 * Test the NoReplyUser class
 */
class NoReplyUserTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\NoReplyUser::id
     * @covers \Fisharebest\Webtrees\NoReplyUser::email
     * @covers \Fisharebest\Webtrees\NoReplyUser::realName
     * @covers \Fisharebest\Webtrees\NoReplyUser::userName
     * @return void
     */
    public function testConstructor(): void
    {
        $user = new NoReplyUser();

        self::assertInstanceOf(UserInterface::class, $user);
        self::assertSame(0, $user->id());
        self::assertSame('no-reply@localhost', $user->email());
        self::assertSame(Webtrees::NAME, $user->realName());
        self::assertSame('', $user->userName());
    }

    /**
     * @covers \Fisharebest\Webtrees\NoReplyUser::getPreference
     * @covers \Fisharebest\Webtrees\NoReplyUser::setPreference
     * @return void
     */
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
