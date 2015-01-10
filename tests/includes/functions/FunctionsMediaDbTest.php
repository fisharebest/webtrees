<?php
namespace WT;

use PHPUnit_Framework_TestCase;

/**
 * Unit tests for the global functions in the file includes/functions/functions_mediadb.php
 *
 * @package   webtrees
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */

class FunctionsMediaDbTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
		require_once 'includes/functions/functions_mediadb.php';
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
	 * Test that function hasMemoryForImage() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionHasMemoryForImageExists() {
		$this->assertEquals(function_exists('\\hasMemoryForImage'), true);
	}
}
