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

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Menu::class)]
class MenuTest extends TestCase
{
    public function testConstructorDefaults(): void
    {
        $menu = new Menu('Test!');

        self::assertSame('Test!', $menu->getLabel());
        self::assertSame('#', $menu->getLink());
        self::assertSame('', $menu->getClass());
        self::assertSame([], $menu->getAttrs());
        self::assertSame([], $menu->getSubmenus());
    }

    public function testConstructorNonDefaults(): void
    {
        $submenus = [new Menu('Submenu')];
        $menu     = new Menu('Test!', 'link.html', 'link-class', ['foo' => 'bar'], $submenus);

        self::assertSame('Test!', $menu->getLabel());
        self::assertSame('link.html', $menu->getLink());
        self::assertSame('link-class', $menu->getClass());
        self::assertSame(['foo' => 'bar'], $menu->getAttrs());
        self::assertSame($submenus, $menu->getSubmenus());
    }

    public function testGetterSetterLabel(): void
    {
        $menu = new Menu('Test!');

        $return = $menu->setLabel('Label');

        self::assertSame($return, $menu);
        self::assertSame('Label', $menu->getLabel());
    }

    public function testGetterSetterLink(): void
    {
        $menu = new Menu('Test!');

        $return = $menu->setLink('link.html');

        self::assertSame($return, $menu);
        self::assertSame('link.html', $menu->getLink());
    }

    public function testGetterSetterId(): void
    {
        $menu = new Menu('Test!');

        $return = $menu->setClass('link-class');

        self::assertSame($return, $menu);
        self::assertSame('link-class', $menu->getClass());
    }

    public function testGetterSetterAttrs(): void
    {
        $menu = new Menu('Test!');

        $return = $menu->setAttrs(['foo' => 'bar']);

        self::assertSame($return, $menu);
        self::assertSame(['foo' => 'bar'], $menu->getAttrs());
    }

    public function testGetterSetterSubmenus(): void
    {
        $menu     = new Menu('Test!');
        $submenus = [
            new Menu('Sub1'),
            new Menu('Sub2'),
        ];

        $return = $menu->setSubmenus($submenus);

        self::assertSame($return, $menu);
        self::assertSame($submenus, $menu->getSubmenus());
    }
}
