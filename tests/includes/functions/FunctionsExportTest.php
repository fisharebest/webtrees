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
 * Unit tests for the global functions in the file includes/functions/functions_export.php
 */
class FunctionsExportTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
	}

	/**
	 * Test that function reformat_record_export() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionReformatRecordExportExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\reformat_record_export'), true);
	}

	/**
	 * Test that function gedcom_header() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGedcomHeaderExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\gedcom_header'), true);
	}

	/**
	 * Test that function convert_media_path() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionConvertMediaPathExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\convert_media_path'), true);
	}

	/**
	 * Test that function export_gedcom() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionExportGedcomExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\export_gedcom'), true);
	}
}
