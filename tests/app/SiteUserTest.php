<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
 * Test the SiteUser class
 */
class SiteUserTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\SiteUser::id
     * @covers \Fisharebest\Webtrees\SiteUser::email
     * @covers \Fisharebest\Webtrees\SiteUser::realName
     * @covers \Fisharebest\Webtrees\SiteUser::userName
     * @return void
     */
    public function testConstructor(): void
    {
        $user = new SiteUser();
        Site::setPreference('SMTP_FROM_NAME', 'email@example.com');
        Site::setPreference('SMTP_DISP_NAME', 'My site');

        self::assertInstanceOf(UserInterface::class, $user);
        self::assertSame(0, $user->id());
        self::assertSame('email@example.com', $user->email());
        self::assertSame('My site', $user->realName());
        self::assertSame('', $user->userName());
    }

    /**
     * @covers \Fisharebest\Webtrees\SiteUser::getPreference
     * @covers \Fisharebest\Webtrees\SiteUser::setPreference
     * @return void
     */
    public function testPreferences(): void
    {
        $user = new SiteUser();

        self::assertSame('', $user->getPreference('foo'));
        self::assertSame('', $user->getPreference('foo'));
        self::assertSame('bar', $user->getPreference('foo', 'bar'));

        // Site users do not have preferences
        $user->setPreference('foo', 'bar');

        self::assertSame('', $user->getPreference('foo'));
    }
}
