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
 * Unit tests for the global functions in the file includes/functions/functions_print_facts.php
 */
class FunctionsPrintFactsTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 */
	public function setUp() {
	}

	/**
	 * Test that function print_fact() exists in the correct namespace.
	 */
	public function testFunctionPrintFactExists() {
		$this->assertEquals(function_exists('print_fact'), true);
	}

	/**
	 * Test that function print_repository_record() exists in the correct namespace.
	 */
	public function testFunctionPrintRepositoryRecordExists() {
		$this->assertEquals(function_exists('print_repository_record'), true);
	}

	/**
	 * Test that function print_fact_sources() exists in the correct namespace.
	 */
	public function testFunctionPrintFactSourcesExists() {
		$this->assertEquals(function_exists('print_fact_sources'), true);
	}

	/**
	 * Test that function print_media_links() exists in the correct namespace.
	 */
	public function testFunctionPrintMediaLinksExists() {
		$this->assertEquals(function_exists('print_media_links'), true);
	}

	/**
	 * Test that function print_main_sources() exists in the correct namespace.
	 */
	public function testFunctionPrintMainSourcesExists() {
		$this->assertEquals(function_exists('print_main_sources'), true);
	}

	/**
	 * Test that function printSourceStructure() exists in the correct namespace.
	 */
	public function testFunctionPrintSourceStructureExists() {
		$this->assertEquals(function_exists('printSourceStructure'), true);
	}

	/**
	 * Test that function getSourceStructure() exists in the correct namespace.
	 */
	public function testFunctionGetSourceStructureExists() {
		$this->assertEquals(function_exists('getSourceStructure'), true);
	}

	/**
	 * Test that function print_main_notes() exists in the correct namespace.
	 */
	public function testFunctionPrintMainNotesExists() {
		$this->assertEquals(function_exists('print_main_notes'), true);
	}

	/**
	 * Test that function print_main_media() exists in the correct namespace.
	 */
	public function testFunctionPrintMainMediaExists() {
		$this->assertEquals(function_exists('print_main_media'), true);
	}
}
