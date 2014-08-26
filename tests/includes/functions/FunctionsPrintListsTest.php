<?php
namespace WT;

use PHPUnit_Framework_TestCase;

/**
 * Unit tests for the global functions in the file includes/functions/functions_print_lists.php
 *
 * @package   webtrees
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */

class FunctionsPrintListsTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
		require_once 'includes/functions/functions_print_lists.php';
	}

	/**
	 * Test that function format_indi_table() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFormatIndiTableExists() {
		$this->assertEquals(function_exists('\\format_indi_table'), true);
	}

	/**
	 * Test that function format_fam_table() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFormatFamTableExists() {
		$this->assertEquals(function_exists('\\format_fam_table'), true);
	}

	/**
	 * Test that function format_sour_table() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFormatSourTableExists() {
		$this->assertEquals(function_exists('\\format_sour_table'), true);
	}

	/**
	 * Test that function format_note_table() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFormatNoteTableExists() {
		$this->assertEquals(function_exists('\\format_note_table'), true);
	}

	/**
	 * Test that function format_repo_table() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFormatRepoTableExists() {
		$this->assertEquals(function_exists('\\format_repo_table'), true);
	}

	/**
	 * Test that function format_media_table() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFormatMediaTableExists() {
		$this->assertEquals(function_exists('\\format_media_table'), true);
	}

	/**
	 * Test that function format_surname_table() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFormatSurnameTableExists() {
		$this->assertEquals(function_exists('\\format_surname_table'), true);
	}

	/**
	 * Test that function format_surname_tagcloud() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFormatSurnameTagcloudExists() {
		$this->assertEquals(function_exists('\\format_surname_tagcloud'), true);
	}

	/**
	 * Test that function format_surname_list() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFormatSurnameListExists() {
		$this->assertEquals(function_exists('\\format_surname_list'), true);
	}

	/**
	 * Test that function print_changes_list() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintChangesListExists() {
		$this->assertEquals(function_exists('\\print_changes_list'), true);
	}

	/**
	 * Test that function print_changes_table() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintChangesTableExists() {
		$this->assertEquals(function_exists('\\print_changes_table'), true);
	}

	/**
	 * Test that function print_events_table() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintEventsTableExists() {
		$this->assertEquals(function_exists('\\print_events_table'), true);
	}

	/**
	 * Test that function print_events_list() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintEventsListExists() {
		$this->assertEquals(function_exists('\\print_events_list'), true);
	}

	/**
	 * Test that function print_chart_by_age() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintChartByAgeExists() {
		$this->assertEquals(function_exists('\\print_chart_by_age'), true);
	}

	/**
	 * Test that function print_chart_by_decade() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintChartByDecadeExists() {
		$this->assertEquals(function_exists('\\print_chart_by_decade'), true);
	}
}
