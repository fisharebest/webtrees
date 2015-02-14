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
 * Unit tests for the global functions in the file includes/functions/functions_charts.php
 */
class FunctionsChartsTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
	}

	/**
	 * Test that function print_sosa_number() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintSosaNumberExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\print_sosa_number'), true);
	}

	/**
	 * Test that function print_family_children() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintFamilyParentsExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\print_family_children'), true);
	}

	/**
	 * Test that function print_family_children() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintFamilyChildrenExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\print_family_children'), true);
	}

	/**
	 * Test that function print_sosa_family() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintSosaFamilyExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\print_sosa_family'), true);
	}

	/**
	 * Test that function print_url_arrow() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintUrlArrowExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\print_url_arrow'), true);
	}

	/**
	 * Test that function get_sosa_name() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetSosaNameExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_sosa_name'), true);
	}

	/**
	 * Test that function print_cousins() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintCousinsExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\print_cousins'), true);
	}
}
