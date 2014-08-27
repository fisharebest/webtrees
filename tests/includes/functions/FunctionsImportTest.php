<?php
namespace WT;

use PHPUnit_Framework_TestCase;

/**
 * Unit tests for the global functions in the file includes/functions/functions_import.php
 *
 * @package   webtrees
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */

class FunctionsImportTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
		require_once 'includes/functions/functions_import.php';
	}

	/**
	 * Test that function reformat_record_import() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionReformatRecordImportExists() {
		$this->assertEquals(function_exists('\\reformat_record_import'), true);
	}

	/**
	 * Test that function update_dates() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionUpdateDatesExists() {
		$this->assertEquals(function_exists('\\update_dates'), true);
	}

	/**
	 * Test that function update_links() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionUpdateLinksExists() {
		$this->assertEquals(function_exists('\\update_links'), true);
	}

	/**
	 * Test that function update_names() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionUpdateNamesExists() {
		$this->assertEquals(function_exists('\\update_names'), true);
	}

	/**
	 * Test that function convert_inline_media() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionConvertInlineMediaExists() {
		$this->assertEquals(function_exists('\\convert_inline_media'), true);
	}

	/**
	 * Test that function create_media_object() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionCreateMediaObjectExists() {
		$this->assertEquals(function_exists('\\create_media_object'), true);
	}

	/**
	 * Test that function empty_database() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionEmptyDatabaseExists() {
		$this->assertEquals(function_exists('\\empty_database'), true);
	}

	/**
	 * Test that function accept_all_changes() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionAcceptAllChangesExists() {
		$this->assertEquals(function_exists('\\accept_all_changes'), true);
	}

	/**
	 * Test that function reject_all_changes() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionRejectAllChangesExists() {
		$this->assertEquals(function_exists('\\reject_all_changes'), true);
	}

	/**
	 * Test that function update_record() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionUpdateRecordExists() {
		$this->assertEquals(function_exists('\\update_record'), true);
	}
}
