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
 * Unit tests for the global functions in the file includes/functions/functions.php
 */
class FunctionsTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
	}

	/**
	 * Test that function fetch_latest_version() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionFetchLatestVersionExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\fetch_latest_version'), true);
	}

	/**
	 * Test that function file_upload_error_text() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionFileUploadErrorTextExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\file_upload_error_text'), true);
	}

	/**
	 * Test that function get_sub_record() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetSubRecordExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_sub_record'), true);
	}

	/**
	 * Test that function get_cont() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetContExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_cont'), true);
	}

	/**
	 * Test that function event_sort() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionEventSortExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\event_sort'), true);
	}

	/**
	 * Test that function event_sort_name() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionEventSortNameExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\event_sort_name'), true);
	}

	/**
	 * Test that function sort_facts() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionSortFactsExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\sort_facts'), true);
	}

	/**
	 * Test that function get_close_relationship_name() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetCloseRelationshipNameExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_close_relationship_name'), true);
	}

	/**
	 * Test that function get_associate_relationship_name() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetAssociateRelationshipNameExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_associate_relationship_name'), true);
	}

	/**
	 * Test that function get_relationship() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetRelationshipExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_relationship'), true);
	}

	/**
	 * Test that function get_relationship_name() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetRelationshipNameExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_relationship_name'), true);
	}

	/**
	 * Test that function cousin_name() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionCousinNameExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\cousin_name'), true);
	}

	/**
	 * Test that function cousin_name2() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionCousinName2Exists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\cousin_name2'), true);
	}

	/**
	 * Test that function get_relationship_name_from_path() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetRelationshipNameFromPathExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_relationship_name_from_path'), true);
	}

	/**
	 * Test that function get_query_url() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetQueryUrlExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_query_url'), true);
	}

	/**
	 * Test that function isFileExternal() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionIsFileExternalExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\isFileExternal'), true);
	}

	/**
	 * Tests for function isFileExternal()
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
