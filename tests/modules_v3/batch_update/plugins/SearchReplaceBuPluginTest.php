<?php
namespace WT;

use PHPUnit_Framework_TestCase;

/**
 * Unit tests for the batch update plugin: search_replace
 *
 * @package   webtrees
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */

class SearchReplaceBuPluginTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
		require_once 'modules_v3/batch_update/admin_batch_update.php'; // for class base_plugin
		require_once 'modules_v3/batch_update/plugins/search_replace.php';
	}

	/**
	 * Test that the class exists.
	 *
	 * @return void
	 */
	public function testFunctionGetUserFullNameExists() {
		$this->assertEquals(true, class_exists('search_replace_bu_plugin'));
	}
}
