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

use Fisharebest\Webtrees\Menu;

/**
 * Test harness for the class Menu
 */
class MenuTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Prepare the environment for these tests.
     */
    public function setUp()
    {
    }

    /**
     * Test the constructor with default parameters.
     */
    public function testConstructorDefaults()
    {
        $menu = new Menu('Test!');

        $this->assertSame('Test!', $menu->getLabel());
        $this->assertSame('#', $menu->getLink());
        $this->assertSame('', $menu->getClass());
        $this->assertSame(array(), $menu->getAttrs());
        $this->assertSame(array(), $menu->getSubmenus());
    }

    /**
     * Test the constructor with non-default parameters.
     */
    public function testConstructorNonDefaults()
    {
        $submenus = array(new Menu('Submenu'));
        $menu     = new Menu('Test!', 'link.html', 'link-class', array('foo' => 'bar'), $submenus);

        $this->assertSame('Test!', $menu->getLabel());
        $this->assertSame('link.html', $menu->getLink());
        $this->assertSame('link-class', $menu->getClass());
        $this->assertSame(array('foo' => 'bar'), $menu->getAttrs());
        $this->assertSame($submenus, $menu->getSubmenus());
    }

    /**
     * Test the getter/setter for the label.
     */
    public function testGetterSetterLabel()
    {
        $menu = new Menu('Test!');

        $return = $menu->setLabel('Label');

        $this->assertSame($return, $menu);
        $this->assertSame('Label', $menu->getLabel());
    }

    /**
     * Test the getter/setter for the link.
     */
    public function testGetterSetterLink()
    {
        $menu = new Menu('Test!');

        $return = $menu->setLink('link.html');

        $this->assertSame($return, $menu);
        $this->assertSame('link.html', $menu->getLink());
    }

    /**
     * Test the getter/setter for the ID.
     */
    public function testGetterSetterId()
    {
        $menu = new Menu('Test!');

        $return = $menu->setClass('link-class');

        $this->assertSame($return, $menu);
        $this->assertSame('link-class', $menu->getClass());
    }

    /**
     * Test the getter/setter for the Attrs event.
     */
    public function testGetterSetterAttrs()
    {
        $menu = new Menu('Test!');

        $return = $menu->setAttrs(array('foo' => 'bar'));

        $this->assertSame($return, $menu);
        $this->assertSame(array('foo' => 'bar'), $menu->getAttrs());
    }

    /**
     * Test the getter/setter for the submenus.
     */
    public function testGetterSetterSubmenus()
    {
        $menu     = new Menu('Test!');
        $submenus = array(
            new Menu('Sub1'),
            new Menu('Sub2'),
        );

        $return = $menu->setSubmenus($submenus);

        $this->assertSame($return, $menu);
        $this->assertSame($submenus, $menu->getSubmenus());
    }

    /**
     * Test the string cast.
     */
    public function testStringCast()
    {
        $menu = new Menu('Test!');

        $this->assertSame((string) $menu, $menu->getMenuAsList());
    }

    /**
     * Test the list rendering for a simple link.
     */
    public function testFormatAsList()
    {
        $menu = new Menu('Test!', 'link.html');

        $this->assertSame('<li class=""><a href="link.html">Test!</a></li>', $menu->getMenuAsList());
    }

    /**
     * Test the list rendering for a simple link with a CSS ID.
     */
    public function testFormatAsListWithClass()
    {
        $menu = new Menu('Test!', 'link.html', 'link-class');

        $this->assertSame('<li class="link-class"><a href="link.html">Test!</a></li>', $menu->getMenuAsList());
    }

    /**
     * Test the list rendering for an empty target.
     */
    public function testFormatAsListWithNoTarget()
    {
        $menu = new Menu('Test!', '');

        $this->assertSame('<li class=""><a>Test!</a></li>', $menu->getMenuAsList());
    }

    /**
     * Test the list rendering for a default (hash) target.
     */
    public function testFormatAsListWithHashTarget()
    {
        $menu = new Menu('Test!');

        $this->assertSame('<li class=""><a href="#">Test!</a></li>', $menu->getMenuAsList());
    }

    /**
     * Test the list rendering for an onclick link.
     */
    public function testFormatAsListWithAttrs()
    {
        $menu = new Menu('Test!', '#', '', array('foo' => 'bar'));

        $this->assertSame('<li class=""><a href="#" foo="bar">Test!</a></li>', $menu->getMenuAsList());
    }

    /**
     * Test the list rendering for an onclick link.
     */
    public function testFormatAsListWithAttrsAndId()
    {
        $menu = new Menu('Test!', '#', 'link-class', array('foo' => 'bar'));

        $this->assertSame('<li class="link-class"><a href="#" foo="bar">Test!</a></li>', $menu->getMenuAsList());
    }
}
