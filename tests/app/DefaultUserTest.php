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
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

/**
 * Test the DefaultUser class
 */
class DefaultUserTest extends TestCase
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

        self::assertInstanceOf(UserInterface::class, $user);
        self::assertSame(-1, $user->id());
        self::assertSame('DEFAULT_USER', $user->email());
        self::assertSame('DEFAULT_USER', $user->realName());
        self::assertSame('', $user->userName());
    }

    /**
     * @covers \Fisharebest\Webtrees\DefaultUser::getPreference
     * @covers \Fisharebest\Webtrees\DefaultUser::setPreference
     * @return void
     */
    public function testPreferences(): void
    {
        $user = new DefaultUser();

        self::assertSame('', $user->getPreference('foo'));
        self::assertSame('', $user->getPreference('foo'));
        self::assertSame('bar', $user->getPreference('foo', 'bar'));

        // Default users store preferences in the database
        $user->setPreference('foo', 'bar');

        self::assertSame('bar', $user->getPreference('foo'));
    }
}
