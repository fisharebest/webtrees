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
use Fisharebest\Webtrees\Services\TreeService;

/**
 * Test the TreeUser class
 */
class TreeUserTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\TreeUser::__construct
     * @covers \Fisharebest\Webtrees\TreeUser::id
     * @covers \Fisharebest\Webtrees\TreeUser::email
     * @covers \Fisharebest\Webtrees\TreeUser::realName
     * @covers \Fisharebest\Webtrees\TreeUser::userName
     * @return void
     */
    public function testConstructor(): void
    {
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');
        $user         = new TreeUser($tree);

        self::assertSame(0, $user->id());
        self::assertSame('', $user->email());
        self::assertSame('title', $user->realName());
        self::assertSame('', $user->userName());
    }

    /**
     * @covers \Fisharebest\Webtrees\TreeUser::getPreference
     * @covers \Fisharebest\Webtrees\TreeUser::setPreference
     * @return void
     */
    public function testPreferences(): void
    {
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');
        $user         = new TreeUser($tree);

        self::assertSame('', $user->getPreference('foo'));
        self::assertSame('', $user->getPreference('foo'));
        self::assertSame('bar', $user->getPreference('foo', 'bar'));

        // Tree users do not have preferences
        $user->setPreference('foo', 'bar');

        self::assertSame('', $user->getPreference('foo'));
    }
}
