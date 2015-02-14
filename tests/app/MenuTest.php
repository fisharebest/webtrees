<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

use PHPUnit_Framework_TestCase;

/**
 * Test harness for the class Menu
 */
class MenuTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests.
	 *
	 * @return void
	 */
	public function setUp() {
	}

	/**
	 * Test the constructor with default parameters.
	 *
	 * @return void
	 */
	public function testConstructorDefaults() {
		$menu = new Menu('Test!');

		$this->assertSame('Test!', $menu->getLabel());
		$this->assertSame('#', $menu->getLink());
		$this->assertSame('', $menu->getId());
		$this->assertSame('', $menu->getOnclick());
		$this->assertSame(array(), $menu->getSubmenus());
	}

	/**
	 * Test the constructor with non-default parameters.
	 *
	 * @return void
	 */
	public function testConstructorNonDefaults() {
		$submenus = array(new Menu('Submenu'));
		$menu = new Menu('Test!', 'link.html', 'link-id', 'test();', $submenus);

		$this->assertSame('Test!', $menu->getLabel());
		$this->assertSame('link.html', $menu->getLink());
		$this->assertSame('link-id', $menu->getId());
		$this->assertSame('test();', $menu->getOnclick());
		$this->assertSame($submenus, $menu->getSubmenus());
	}

	/**
	 * Test the getter/setter for the label.
	 *
	 * @return void
	 */
	public function testGetterSetterLabel() {
		$menu = new Menu('Test!');

		$return = $menu->setLabel('Label');

		$this->assertSame($return, $menu);
		$this->assertSame('Label', $menu->getLabel());
	}

	/**
	 * Test the getter/setter for the link.
	 *
	 * @return void
	 */
	public function testGetterSetterLink() {
		$menu = new Menu('Test!');

		$return = $menu->setLink('link.html');

		$this->assertSame($return, $menu);
		$this->assertSame('link.html', $menu->getLink());
	}

	/**
	 * Test the getter/setter for the ID.
	 *
	 * @return void
	 */
	public function testGetterSetterId() {
		$menu = new Menu('Test!');

		$return = $menu->setId('link-id');

		$this->assertSame($return, $menu);
		$this->assertSame('link-id', $menu->getId());
	}

	/**
	 * Test the getter/setter for the Onclick event.
	 *
	 * @return void
	 */
	public function testGetterSetterOnclick() {
		$menu = new Menu('Test!');

		$return = $menu->setOnclick('test();');

		$this->assertSame($return, $menu);
		$this->assertSame('test();', $menu->getOnclick());
	}

	/**
	 * Test the getter/setter for the submenus.
	 *
	 * @return void
	 */
	public function testGetterSetterSubmenus() {
		$menu = new Menu('Test!');
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
	 *
	 * @return void
	 */
	public function testStringCast() {
		$menu = new Menu('Test!');

		$this->assertSame((string)$menu, $menu->getMenuAsList());
	}

	/**
	 * Test the list rendering for a simple link.
	 *
	 * @return void
	 */
	public function testFormatAsList() {
		$menu = new Menu('Test!', 'link.html');

		$this->assertSame('<li><a href="link.html">Test!</a></li>', $menu->getMenuAsList());
	}

	/**
	 * Test the list rendering for a simple link with a CSS ID.
	 *
	 * @return void
	 */
	public function testFormatAsListWithId() {
		$menu = new Menu('Test!', 'link.html', 'link-id');

		$this->assertSame('<li id="link-id"><a href="link.html">Test!</a></li>', $menu->getMenuAsList());
	}

	/**
	 * Test the list rendering for an empty target.
	 *
	 * @return void
	 */
	public function testFormatAsListWithNoTarget() {
		$menu = new Menu('Test!', '');

		$this->assertSame('<li><a>Test!</a></li>', $menu->getMenuAsList());
	}

	/**
	 * Test the list rendering for a default (hash) target.
	 *
	 * @return void
	 */
	public function testFormatAsListWithHashTarget() {
		$menu = new Menu('Test!');

		$this->assertSame('<li><a href="#">Test!</a></li>', $menu->getMenuAsList());
	}

	/**
	 * Test the list rendering for an onclick link.
	 *
	 * @return void
	 */
	public function testFormatAsListWithOnclick() {
		$menu = new Menu('Test!', '#', '', 'return test();');

		$this->assertSame('<li><a href="#" onclick="return test();">Test!</a></li>', $menu->getMenuAsList());
	}

	/**
	 * Test the list rendering for an onclick link.
	 *
	 * @return void
	 */
	public function testFormatAsListWithOnclickAndId() {
		$menu = new Menu('Test!', '#', 'link-id', 'return test();');

		$this->assertSame('<li id="link-id"><a href="#" onclick="return test();">Test!</a></li>', $menu->getMenuAsList());
	}
}
