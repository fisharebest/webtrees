<?php
namespace WT;

use PHPUnit_Framework_TestCase;

/**
 *FunctionsUtf8Test
 *
 * Test harness for the global functions in the file includes/functions/functions_utf-8.php
 *
 * @package   webtrees
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */

class FunctionsUtf8Test extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
		require_once 'includes/functions/functions_utf-8.php';
	}

	/**
	 * Test that function utf8_strtoupper() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionUtf8StrtoupperExists() {
		$this->assertEquals(function_exists('\\utf8_strtoupper'), true);
	}

	/**
	 * Test that function utf8_substr() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionUtf8SubstrExists() {
		$this->assertEquals(function_exists('\\utf8_substr'), true);
	}

	/**
	 * Test that function utf8_strlen() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionUtf8StrlenExists() {
		$this->assertEquals(function_exists('\\utf8_strlen'), true);
	}

	/**
	 * Test that function utf8_strcasecmp() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionUtf8StrcasecmpExists() {
		$this->assertEquals(function_exists('\\utf8_strcasecmp'), true);
	}

	/**
	 * Test that function reverseText() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionReverseTextExists() {
		$this->assertEquals(function_exists('\\reverseText'), true);
	}
}
