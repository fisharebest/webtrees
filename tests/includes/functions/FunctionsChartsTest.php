<?php
namespace WT;

use PHPUnit_Framework_TestCase;

/**
 * Unit tests for the global functions in the file includes/functions/functions_charts.php
 *
 * @package   webtrees
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */

class FunctionsChartsTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
		require_once 'includes/functions/functions_charts.php';
	}

	/**
	 * Test that function print_sosa_number() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintSosaNumberExists() {
		$this->assertEquals(function_exists('\\print_sosa_number'), true);
	}

	/**
	 * Test that function print_family_children() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintFamilyParentsExists() {
		$this->assertEquals(function_exists('\\print_family_children'), true);
	}

	/**
	 * Test that function print_family_children() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintFamilyChildrenExists() {
		$this->assertEquals(function_exists('\\print_family_children'), true);
	}

	/**
	 * Test that function print_sosa_family() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintSosaFamilyExists() {
		$this->assertEquals(function_exists('\\print_sosa_family'), true);
	}

	/**
	 * Test that function ancestry_array() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionAncestryArrayExists() {
		$this->assertEquals(function_exists('\\ancestry_array'), true);
	}

	/**
	 * Test that function print_url_arrow() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintUrlArrowExists() {
		$this->assertEquals(function_exists('\\print_url_arrow'), true);
	}

	/**
	 * Test that function get_sosa_name() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetSosaNameExists() {
		$this->assertEquals(function_exists('\\get_sosa_name'), true);
	}

	/**
	 * Test that function print_cousins() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintCousinsExists() {
		$this->assertEquals(function_exists('\\print_cousins'), true);
	}
}
