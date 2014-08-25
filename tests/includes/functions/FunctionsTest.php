<?php
namespace WT;

use PHPUnit_Framework_TestCase;

/**
 * Unit tests for the global functions in the file includes/functions/functions.php
 *
 * @package   webtrees
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */

class FunctionsTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
		require_once 'includes/functions/functions.php';
	}

	/**
	 * Test that function fetch_latest_version() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFetchLatestVersionExists() {
		$this->assertEquals(function_exists('\\fetch_latest_version'), true);
	}

	/**
	 * Test that function file_upload_error_text() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFileUploadErrorTextExists() {
		$this->assertEquals(function_exists('\\file_upload_error_text'), true);
	}

	/**
	 * Test that function load_gedcom_settings() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionLoadGedcomSettingsExists() {
		$this->assertEquals(function_exists('\\load_gedcom_settings'), true);
	}

	/**
	 * Test that function get_sub_record() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetSubRecordExists() {
		$this->assertEquals(function_exists('\\get_sub_record'), true);
	}

	/**
	 * Test that function get_cont() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetContExists() {
		$this->assertEquals(function_exists('\\get_cont'), true);
	}

	/**
	 * Test that function event_sort() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionEventSortExists() {
		$this->assertEquals(function_exists('\\event_sort'), true);
	}

	/**
	 * Test that function event_sort_name() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionEventSortNameExists() {
		$this->assertEquals(function_exists('\\event_sort_name'), true);
	}

	/**
	 * Test that function sort_facts() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionSortFactsExists() {
		$this->assertEquals(function_exists('\\sort_facts'), true);
	}

	/**
	 * Test that function get_close_relationship_name() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetCloseRelationshipNameExists() {
		$this->assertEquals(function_exists('\\get_close_relationship_name'), true);
	}

	/**
	 * Test that function get_associate_relationship_name() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetAssociateRelationshipNameExists() {
		$this->assertEquals(function_exists('\\get_associate_relationship_name'), true);
	}

	/**
	 * Test that function get_relationship() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetRelationshipExists() {
		$this->assertEquals(function_exists('\\get_relationship'), true);
	}

	/**
	 * Test that function get_relationship_name() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetRelationshipNameExists() {
		$this->assertEquals(function_exists('\\get_relationship_name'), true);
	}

	/**
	 * Test that function cousin_name() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionCousinNameExists() {
		$this->assertEquals(function_exists('\\cousin_name'), true);
	}

	/**
	 * Test that function cousin_name2() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionCousinName2Exists() {
		$this->assertEquals(function_exists('\\cousin_name2'), true);
	}

	/**
	 * Test that function get_relationship_name_from_path() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetRelationshipNameFromPathExists() {
		$this->assertEquals(function_exists('\\get_relationship_name_from_path'), true);
	}

	/**
	 * Test that function get_theme_names() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetThemeNamesExists() {
		$this->assertEquals(function_exists('\\get_theme_names'), true);
	}

	/**
	 * Test that function get_query_url() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetQueryUrlExists() {
		$this->assertEquals(function_exists('\\get_query_url'), true);
	}

	/**
	 * Test that function get_new_xref() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetNewXrefExists() {
		$this->assertEquals(function_exists('\\get_new_xref'), true);
	}

	/**
	 * Test that function isFileExternal() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionIsFileExternalExists() {
		$this->assertEquals(function_exists('\\isFileExternal'), true);
	}

	/**
	 * Tests for function edit_field_inline()
	 *
	 * @return void
	 */
	public function testFunctionIsFileExternal() {
		$this->assertEquals(isFileExternal('http://www.example.com/file.txt'), true);
		$this->assertEquals(isFileExternal('file.txt'), false);
		$this->assertEquals(isFileExternal('folder/file.txt'), false);
		$this->assertEquals(isFileExternal('folder\\file.txt'), false);
		$this->assertEquals(isFileExternal('/folder/file.txt'), false);
		$this->assertEquals(isFileExternal('\\folder\\file.txt'), false);
		$this->assertEquals(isFileExternal('C:\\folder\\file.txt'), false);
	}
}
