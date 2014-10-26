<?php
namespace WT;

use PHPUnit_Framework_TestCase;

/**
 * Unit tests for the global functions in the file includes/authentication.php
 *
 * @package   webtrees
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */

class AuthenticationTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
		require_once 'includes/authentication.php';
	}

	/**
	 * Test that function getUserFullName() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetUserFullNameExists() {
		$this->assertEquals(function_exists('\\getUserFullName'), true);
	}

	/**
	 * Test that function addMessage() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionAddMessageExists() {
		$this->assertEquals(function_exists('\\addMessage'), true);
	}

	/**
	 * Test that function deleteMessage() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionDeleteMessageExists() {
		$this->assertEquals(function_exists('\\deleteMessage'), true);
	}

	/**
	 * Test that function getUserMessages() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetUserMessagesExists() {
		$this->assertEquals(function_exists('\\getUserMessages'), true);
	}
}
