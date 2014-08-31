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
	 * Test that function return_bytes() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionReturnBytesExists() {
		$this->assertEquals(function_exists('\\return_bytes'), true);
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
