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

/**
 * Test the DefaultUser class
 */
class DefaultUserTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\DefaultUser::__construct
     * @covers \Fisharebest\Webtrees\DefaultUser::id
     * @covers \Fisharebest\Webtrees\DefaultUser::email
     * @covers \Fisharebest\Webtrees\DefaultUser::realName
     * @covers \Fisharebest\Webtrees\DefaultUser::userName
     * @return void
     */
    public function testDefaultUser(): void
    {
        $user = new DefaultUser();

        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertSame(-1, $user->id());
        $this->assertSame('DEFAULT_USER', $user->email());
        $this->assertSame('DEFAULT_USER', $user->realName());
        $this->assertSame('', $user->userName());
    }

    /**
     * @covers \Fisharebest\Webtrees\DefaultUser::getPreference
     * @covers \Fisharebest\Webtrees\DefaultUser::setPreference
     * @return void
     */
    public function testPreferences(): void
    {
        $user = new DefaultUser();

        $this->assertSame('', $user->getPreference('foo'));
        $this->assertSame('', $user->getPreference('foo', ''));
        $this->assertSame('bar', $user->getPreference('foo', 'bar'));

        // Default users store preferences in the database
        $user->setPreference('foo', 'bar');

        $this->assertSame('bar', $user->getPreference('foo'));
    }
}
