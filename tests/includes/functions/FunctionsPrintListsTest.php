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
 * Unit tests for the global functions in the file includes/functions/functions_print_lists.php
 */
class FunctionsPrintListsTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 */
	public function setUp() {
	}

	/**
	 * Test that function format_indi_table() exists in the correct namespace.
	 */
	public function testFunctionFormatIndiTableExists() {
		$this->assertEquals(function_exists('format_indi_table'), true);
	}

	/**
	 * Test that function format_fam_table() exists in the correct namespace.
	 */
	public function testFunctionFormatFamTableExists() {
		$this->assertEquals(function_exists('format_fam_table'), true);
	}

	/**
	 * Test that function format_sour_table() exists in the correct namespace.
	 */
	public function testFunctionFormatSourTableExists() {
		$this->assertEquals(function_exists('format_sour_table'), true);
	}

	/**
	 * Test that function format_note_table() exists in the correct namespace.
	 */
	public function testFunctionFormatNoteTableExists() {
		$this->assertEquals(function_exists('format_note_table'), true);
	}

	/**
	 * Test that function format_repo_table() exists in the correct namespace.
	 */
	public function testFunctionFormatRepoTableExists() {
		$this->assertEquals(function_exists('format_repo_table'), true);
	}

	/**
	 * Test that function format_media_table() exists in the correct namespace.
	 */
	public function testFunctionFormatMediaTableExists() {
		$this->assertEquals(function_exists('format_media_table'), true);
	}

	/**
	 * Test that function format_surname_table() exists in the correct namespace.
	 */
	public function testFunctionFormatSurnameTableExists() {
		$this->assertEquals(function_exists('format_surname_table'), true);
	}

	/**
	 * Test that function format_surname_tagcloud() exists in the correct namespace.
	 */
	public function testFunctionFormatSurnameTagcloudExists() {
		$this->assertEquals(function_exists('format_surname_tagcloud'), true);
	}

	/**
	 * Test that function format_surname_list() exists in the correct namespace.
	 */
	public function testFunctionFormatSurnameListExists() {
		$this->assertEquals(function_exists('format_surname_list'), true);
	}

	/**
	 * Test that function print_changes_list() exists in the correct namespace.
	 */
	public function testFunctionPrintChangesListExists() {
		$this->assertEquals(function_exists('print_changes_list'), true);
	}

	/**
	 * Test that function print_changes_table() exists in the correct namespace.
	 */
	public function testFunctionPrintChangesTableExists() {
		$this->assertEquals(function_exists('print_changes_table'), true);
	}

	/**
	 * Test that function print_events_table() exists in the correct namespace.
	 */
	public function testFunctionPrintEventsTableExists() {
		$this->assertEquals(function_exists('print_events_table'), true);
	}

	/**
	 * Test that function print_events_list() exists in the correct namespace.
	 */
	public function testFunctionPrintEventsListExists() {
		$this->assertEquals(function_exists('print_events_list'), true);
	}

	/**
	 * Test that function print_chart_by_age() exists in the correct namespace.
	 */
	public function testFunctionPrintChartByAgeExists() {
		$this->assertEquals(function_exists('print_chart_by_age'), true);
	}

	/**
	 * Test that function print_chart_by_decade() exists in the correct namespace.
	 */
	public function testFunctionPrintChartByDecadeExists() {
		$this->assertEquals(function_exists('print_chart_by_decade'), true);
	}
}
