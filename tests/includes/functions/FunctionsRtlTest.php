<?php
namespace WT;

use PHPUnit_Framework_TestCase;

/**
 * Unit tests for the global functions in the file includes/functions/functions_rtl.php
 *
 * @package   webtrees
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */

class FunctionsRtlTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
		require_once 'includes/functions/functions_rtl.php';
	}

	/**
	 * Test that function stripLRMRLM() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionStripLRMRLMExists() {
		$this->assertEquals(function_exists('\\stripLRMRLM'), true);
	}

	/**
	 * Test that function spanLTRRTL() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionSpanLTRRTLExists() {
		$this->assertEquals(function_exists('\\spanLTRRTL'), true);
	}

	/**
	 * Test that function starredName() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionStarredNameExists() {
		$this->assertEquals(function_exists('\\starredName'), true);
	}

	/**
	 * Test that function getChar() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetCharExists() {
		$this->assertEquals(function_exists('\\getChar'), true);
	}

	/**
	 * Test that function breakCurrentSpan() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionBreakCurrentSpanExists() {
		$this->assertEquals(function_exists('\\breakCurrentSpan'), true);
	}

	/**
	 * Test that function beginCurrentSpan() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionBeginCurrentSpanExists() {
		$this->assertEquals(function_exists('\\beginCurrentSpan'), true);
	}

	/**
	 * Test that function finishCurrentSpan() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFinishCurrentSpanExists() {
		$this->assertEquals(function_exists('\\finishCurrentSpan'), true);
	}

	/**
	 * Test that function utf8_wordwrap() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionUtf8WordwrapExists() {
		$this->assertEquals(function_exists('\\utf8_wordwrap'), true);
	}
}
