<?php
namespace WT;

use PHPUnit_Framework_TestCase;

/**
 * Unit tests for the global functions in the file includes/functions/functions_print_facts.php
 *
 * @package   webtrees
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */

class FunctionsPrintFactsTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
		require_once 'includes/functions/functions_print_facts.php';
	}

	/**
	 * Test that function print_fact() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintFactExists() {
		$this->assertEquals(function_exists('\\print_fact'), true);
	}

	/**
	 * Test that function print_repository_record() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintRepositoryRecordExists() {
		$this->assertEquals(function_exists('\\print_repository_record'), true);
	}

	/**
	 * Test that function print_fact_sources() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintFactSourcesExists() {
		$this->assertEquals(function_exists('\\print_fact_sources'), true);
	}

	/**
	 * Test that function print_media_links() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintMediaLinksExists() {
		$this->assertEquals(function_exists('\\print_media_links'), true);
	}

	/**
	 * Test that function print_main_sources() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintMainSourcesExists() {
		$this->assertEquals(function_exists('\\print_main_sources'), true);
	}

	/**
	 * Test that function printSourceStructure() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintSourceStructureExists() {
		$this->assertEquals(function_exists('\\printSourceStructure'), true);
	}

	/**
	 * Test that function getSourceStructure() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetSourceStructureExists() {
		$this->assertEquals(function_exists('\\getSourceStructure'), true);
	}

	/**
	 * Test that function print_main_notes() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintMainNotesExists() {
		$this->assertEquals(function_exists('\\print_main_notes'), true);
	}

	/**
	 * Test that function print_main_media() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintMainMediaExists() {
		$this->assertEquals(function_exists('\\print_main_media'), true);
	}
}
