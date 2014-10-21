<?php
namespace WT;

use PHPUnit_Framework_TestCase;

/**
 * Unit tests for the global functions in the file includes/functions/functions_date.php
 *
 * @package   webtrees
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */

class FunctionsDateTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
		require_once 'includes/functions/functions_date.php';
	}

	/**
	 * Test that function get_age_at_event() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetAgeAtEventExists() {
		$this->assertEquals(function_exists('\\get_age_at_event'), true);
	}

	/**
	 * Test that function format_timestamp() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFormatTimestampExists() {
		$this->assertEquals(function_exists('\\format_timestamp'), true);
	}

	/**
	 * Test that function timestamp_to_gedcom_date() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionTimestampToGedcomDateExists() {
		$this->assertEquals(function_exists('\\timestamp_to_gedcom_date'), true);
	}
}
