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
 * Unit tests for the global functions in the file includes/functions/functions_import.php
 */
class FunctionsImportTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 */
	public function setUp() {
	}

	/**
	 * Test that function reformat_record_import() exists in the correct namespace.
	 */
	public function testFunctionReformatRecordImportExists() {
		$this->assertEquals(function_exists('reformat_record_import'), true);
	}

	/**
	 * Test that function update_dates() exists in the correct namespace.
	 */
	public function testFunctionUpdateDatesExists() {
		$this->assertEquals(function_exists('update_dates'), true);
	}

	/**
	 * Test that function update_links() exists in the correct namespace.
	 */
	public function testFunctionUpdateLinksExists() {
		$this->assertEquals(function_exists('update_links'), true);
	}

	/**
	 * Test that function update_names() exists in the correct namespace.
	 */
	public function testFunctionUpdateNamesExists() {
		$this->assertEquals(function_exists('update_names'), true);
	}

	/**
	 * Test that function convert_inline_media() exists in the correct namespace.
	 */
	public function testFunctionConvertInlineMediaExists() {
		$this->assertEquals(function_exists('convert_inline_media'), true);
	}

	/**
	 * Test that function create_media_object() exists in the correct namespace.
	 */
	public function testFunctionCreateMediaObjectExists() {
		$this->assertEquals(function_exists('create_media_object'), true);
	}

	/**
	 * Test that function accept_all_changes() exists in the correct namespace.
	 */
	public function testFunctionAcceptAllChangesExists() {
		$this->assertEquals(function_exists('accept_all_changes'), true);
	}

	/**
	 * Test that function reject_all_changes() exists in the correct namespace.
	 */
	public function testFunctionRejectAllChangesExists() {
		$this->assertEquals(function_exists('reject_all_changes'), true);
	}

	/**
	 * Test that function update_record() exists in the correct namespace.
	 */
	public function testFunctionUpdateRecordExists() {
		$this->assertEquals(function_exists('update_record'), true);
	}
}
