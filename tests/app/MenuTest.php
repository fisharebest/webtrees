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

/**
 * Test harness for the class Menu
 */
class MenuTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Menu::__construct
     *
     * @return void
     */
    public function testConstructorDefaults(): void
    {
        $menu = new Menu('Test!');

        $this->assertSame('Test!', $menu->getLabel());
        $this->assertSame('#', $menu->getLink());
        $this->assertSame('', $menu->getClass());
        $this->assertSame([], $menu->getAttrs());
        $this->assertSame([], $menu->getSubmenus());
    }

    /**
     * @covers \Fisharebest\Webtrees\Menu::__construct
     *
     * @return void
     */
    public function testConstructorNonDefaults(): void
    {
        $submenus = [new Menu('Submenu')];
        $menu     = new Menu('Test!', 'link.html', 'link-class', ['foo' => 'bar'], $submenus);

        $this->assertSame('Test!', $menu->getLabel());
        $this->assertSame('link.html', $menu->getLink());
        $this->assertSame('link-class', $menu->getClass());
        $this->assertSame(['foo' => 'bar'], $menu->getAttrs());
        $this->assertSame($submenus, $menu->getSubmenus());
    }

    /**
     * @covers \Fisharebest\Webtrees\Menu::getLabel
     * @covers \Fisharebest\Webtrees\Menu::setLabel
     *
     * @return void
     */
    public function testGetterSetterLabel(): void
    {
        $menu = new Menu('Test!');

        $return = $menu->setLabel('Label');

        $this->assertSame($return, $menu);
        $this->assertSame('Label', $menu->getLabel());
    }

    /**
     * @covers \Fisharebest\Webtrees\Menu::getLink
     * @covers \Fisharebest\Webtrees\Menu::setLink
     *
     * @return void
     */
    public function testGetterSetterLink(): void
    {
        $menu = new Menu('Test!');

        $return = $menu->setLink('link.html');

        $this->assertSame($return, $menu);
        $this->assertSame('link.html', $menu->getLink());
    }

    /**
     * @covers \Fisharebest\Webtrees\Menu::getClass
     * @covers \Fisharebest\Webtrees\Menu::setClass
     *
     * @return void
     */
    public function testGetterSetterId(): void
    {
        $menu = new Menu('Test!');

        $return = $menu->setClass('link-class');

        $this->assertSame($return, $menu);
        $this->assertSame('link-class', $menu->getClass());
    }

    /**
     * @covers \Fisharebest\Webtrees\Menu::getAttrs
     * @covers \Fisharebest\Webtrees\Menu::setAttrs
     *
     * @return void
     */
    public function testGetterSetterAttrs(): void
    {
        $menu = new Menu('Test!');

        $return = $menu->setAttrs(['foo' => 'bar']);

        $this->assertSame($return, $menu);
        $this->assertSame(['foo' => 'bar'], $menu->getAttrs());
    }

    /**
     * @covers \Fisharebest\Webtrees\Menu::getSubmenus
     * @covers \Fisharebest\Webtrees\Menu::setSubmenus
     *
     * @return void
     */
    public function testGetterSetterSubmenus(): void
    {
        $menu     = new Menu('Test!');
        $submenus = [
            new Menu('Sub1'),
            new Menu('Sub2'),
        ];

        $return = $menu->setSubmenus($submenus);

        $this->assertSame($return, $menu);
        $this->assertSame($submenus, $menu->getSubmenus());
    }
}
