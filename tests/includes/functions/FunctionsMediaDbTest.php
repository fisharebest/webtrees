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
 * Unit tests for the global functions in the file includes/functions/functions_mediadb.php
 */
class FunctionsMediaDbTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
	}

	/**
	 * Test the function return_bytes().
	 *
	 * @return void
	 */
	public function testFunctionReturnBytes() {
		$this->assertSame(-1, return_bytes(''));
		$this->assertSame(-1, return_bytes('-1'));
		$this->assertSame(42, return_bytes('42'));
		$this->assertSame(42, return_bytes('42b'));
		$this->assertSame(42, return_bytes('42B'));
		$this->assertSame(43008, return_bytes('42k'));
		$this->assertSame(43008, return_bytes('42K'));
		$this->assertSame(44040192, return_bytes('42m'));
		$this->assertSame(44040192, return_bytes('42M'));
		$this->assertSame(45097156608, return_bytes('42g'));
		$this->assertSame(45097156608, return_bytes('42G'));
	}

	/**
	 * Test that function hasMemoryForImage() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionHasMemoryForImageExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\hasMemoryForImage'), true);
	}
}
