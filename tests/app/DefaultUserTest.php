<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Cache\Adapter\NullAdapter;

#[CoversClass(DefaultUser::class)]
class DefaultUserTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * Things to run before every test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $cache_factory = $this->createMock(CacheFactoryInterface::class);
        $cache_factory->method('array')->willReturn(new Cache(new NullAdapter()));
        Registry::cache($cache_factory);
    }

    public function testDefaultUser(): void
    {
        $user = new DefaultUser();

        self::assertInstanceOf(UserInterface::class, $user);
        self::assertSame(-1, $user->id());
        self::assertSame('DEFAULT_USER', $user->email());
        self::assertSame('DEFAULT_USER', $user->realName());
        self::assertSame('', $user->userName());
    }

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
