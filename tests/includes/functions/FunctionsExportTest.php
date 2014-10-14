<?php
namespace WT;

use PHPUnit_Framework_TestCase;

/**
 * Unit tests for the global functions in the file includes/functions/functions_export.php
 *
 * @package   webtrees
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */

class FunctionsExportTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
		require_once 'includes/functions/functions_export.php';
	}

	/**
	 * Test that function reformat_record_export() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionReformatRecordExportExists() {
		$this->assertEquals(function_exists('\\reformat_record_export'), true);
	}

	/**
	 * Test that function gedcom_header() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGedcomHeaderExists() {
		$this->assertEquals(function_exists('\\gedcom_header'), true);
	}

	/**
	 * Test that function convert_media_path() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionConvertMediaPathExists() {
		$this->assertEquals(function_exists('\\convert_media_path'), true);
	}

	/**
	 * Test that function export_gedcom() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionExportGedcomExists() {
		$this->assertEquals(function_exists('\\export_gedcom'), true);
	}
}
