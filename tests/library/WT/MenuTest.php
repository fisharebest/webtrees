<?php
namespace WT;

use PHPUnit_Framework_TestCase;
use WT_Menu;

/**
 * Test harness for the class WT_Menu
 *
 * @package   webtrees
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
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
		$menu = new WT_Menu('Test!');

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
		$submenus = array(new WT_Menu('Submenu'));
		$menu = new WT_Menu('Test!', 'link.html', 'link-id', 'test();', $submenus);

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
		$menu = new WT_Menu('Test!');

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
		$menu = new WT_Menu('Test!');

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
		$menu = new WT_Menu('Test!');

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
		$menu = new WT_Menu('Test!');

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
		$menu = new WT_Menu('Test!');
		$submenus = array(
			new WT_Menu('Sub1'),
			new WT_Menu('Sub2'),
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
		$menu = new WT_Menu('Test!');

		$this->assertSame((string)$menu, $menu->getMenuAsList());
	}

	/**
	 * Test the list rendering for a simple link.
	 *
	 * @return void
	 */
	public function testFormatAsList() {
		$menu = new WT_Menu('Test!', 'link.html');

		$this->assertSame('<li><a href="link.html">Test!</a></li>', $menu->getMenuAsList());
	}

	/**
	 * Test the list rendering for a simple link with a CSS ID.
	 *
	 * @return void
	 */
	public function testFormatAsListWithId() {
		$menu = new WT_Menu('Test!', 'link.html', 'link-id');

		$this->assertSame('<li id="link-id"><a href="link.html">Test!</a></li>', $menu->getMenuAsList());
	}

	/**
	 * Test the list rendering for an empty target.
	 *
	 * @return void
	 */
	public function testFormatAsListWithNoTarget() {
		$menu = new WT_Menu('Test!', '');

		$this->assertSame('<li><a>Test!</a></li>', $menu->getMenuAsList());
	}

	/**
	 * Test the list rendering for a default (hash) target.
	 *
	 * @return void
	 */
	public function testFormatAsListWithHashTarget() {
		$menu = new WT_Menu('Test!');

		$this->assertSame('<li><a href="#">Test!</a></li>', $menu->getMenuAsList());
	}

	/**
	 * Test the list rendering for an onclick link.
	 *
	 * @return void
	 */
	public function testFormatAsListWithOnclick() {
		$menu = new WT_Menu('Test!', '#', '', 'return test();');

		$this->assertSame('<li><a href="#" onclick="return test();">Test!</a></li>', $menu->getMenuAsList());
	}

	/**
	 * Test the list rendering for an onclick link.
	 *
	 * @return void
	 */
	public function testFormatAsListWithOnclickAndId() {
		$menu = new WT_Menu('Test!', '#', 'link-id', 'return test();');

		$this->assertSame('<li id="link-id"><a href="#" onclick="return test();">Test!</a></li>', $menu->getMenuAsList());
	}
}
