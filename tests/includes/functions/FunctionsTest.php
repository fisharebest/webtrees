<?php

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

/**
 * Unit tests for the global functions in the file includes/functions/functions.php
 */
class FunctionsTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 */
	public function setUp() {
	}

	/**
	 * Test that function fetch_latest_version() exists in the correct namespace.
	 */
	public function testFunctionFetchLatestVersionExists() {
		$this->assertEquals(function_exists('fetch_latest_version'), true);
	}

	/**
	 * Test that function file_upload_error_text() exists in the correct namespace.
	 */
	public function testFunctionFileUploadErrorTextExists() {
		$this->assertEquals(function_exists('file_upload_error_text'), true);
	}

	/**
	 * Test that function get_sub_record() exists in the correct namespace.
	 */
	public function testFunctionGetSubRecordExists() {
		$this->assertEquals(function_exists('get_sub_record'), true);
	}

	/**
	 * Test that function get_cont() exists in the correct namespace.
	 */
	public function testFunctionGetContExists() {
		$this->assertEquals(function_exists('get_cont'), true);
	}

	/**
	 * Test that function event_sort() exists in the correct namespace.
	 */
	public function testFunctionEventSortExists() {
		$this->assertEquals(function_exists('event_sort'), true);
	}

	/**
	 * Test that function event_sort_name() exists in the correct namespace.
	 */
	public function testFunctionEventSortNameExists() {
		$this->assertEquals(function_exists('event_sort_name'), true);
	}

	/**
	 * Test that function sort_facts() exists in the correct namespace.
	 */
	public function testFunctionSortFactsExists() {
		$this->assertEquals(function_exists('sort_facts'), true);
	}

	/**
	 * Test that function get_close_relationship_name() exists in the correct namespace.
	 */
	public function testFunctionGetCloseRelationshipNameExists() {
		$this->assertEquals(function_exists('get_close_relationship_name'), true);
	}

	/**
	 * Test that function get_associate_relationship_name() exists in the correct namespace.
	 */
	public function testFunctionGetAssociateRelationshipNameExists() {
		$this->assertEquals(function_exists('get_associate_relationship_name'), true);
	}

	/**
	 * Test that function get_relationship() exists in the correct namespace.
	 */
	public function testFunctionGetRelationshipExists() {
		$this->assertEquals(function_exists('get_relationship'), true);
	}

	/**
	 * Test that function get_relationship_name() exists in the correct namespace.
	 */
	public function testFunctionGetRelationshipNameExists() {
		$this->assertEquals(function_exists('get_relationship_name'), true);
	}

	/**
	 * Test that function cousin_name() exists in the correct namespace.
	 */
	public function testFunctionCousinNameExists() {
		$this->assertEquals(function_exists('cousin_name'), true);
	}

	/**
	 * Test that function cousin_name2() exists in the correct namespace.
	 */
	public function testFunctionCousinName2Exists() {
		$this->assertEquals(function_exists('cousin_name2'), true);
	}

	/**
	 * Test that function get_relationship_name_from_path() exists in the correct namespace.
	 */
	public function testFunctionGetRelationshipNameFromPathExists() {
		$this->assertEquals(function_exists('get_relationship_name_from_path'), true);
	}

	/**
	 * Test that function get_query_url() exists in the correct namespace.
	 */
	public function testFunctionGetQueryUrlExists() {
		$this->assertEquals(function_exists('get_query_url'), true);
	}

	/**
	 * Test that function isFileExternal() exists in the correct namespace.
	 */
	public function testFunctionIsFileExternalExists() {
		$this->assertEquals(function_exists('isFileExternal'), true);
	}

	/**
	 * Tests for function isFileExternal()
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
