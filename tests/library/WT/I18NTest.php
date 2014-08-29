<?php
namespace WT;

use PHPUnit_Framework_TestCase;
use WT_I18N;

/**
 * Test harness for the class WT_I18N
 *
 * @package   webtrees
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */

class I18NTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
	}

	/**
	 * Test WT_I18N::reverseText()
	 *
	 * @return void
	 */
	public function testReverseText() {
		// Create these strings carefully, as text editors can display them in confusing ways.
		$rtl_abc = 'א' . 'ב' . 'ג';
		$rtl_cba = 'ג' . 'ב' . 'א';
		$rtl_123 = '١' . '٢' . '٣';

		$this->assertEquals(WT_I18N::reverseText(''), '');
		$this->assertEquals(WT_I18N::reverseText('abc123'), 'abc123');
		$this->assertEquals(WT_I18N::reverseText('<b>abc</b>123'), 'abc123');
		$this->assertEquals(WT_I18N::reverseText('&lt;abc&gt;'), '<abc>');
		$this->assertEquals(WT_I18N::reverseText('abc[123]'), 'abc[123]');
		$this->assertEquals(WT_I18N::reverseText($rtl_123), $rtl_123);
		$this->assertEquals(WT_I18N::reverseText($rtl_abc), $rtl_cba);
		$this->assertEquals(WT_I18N::reverseText($rtl_abc . '123'), '123' . $rtl_cba);
		$this->assertEquals(WT_I18N::reverseText($rtl_abc . '[123]'), '[123]' . $rtl_cba);
		$this->assertEquals(WT_I18N::reverseText('123' . $rtl_abc . '456'), '456' . $rtl_cba . '123');
		$this->assertEquals(WT_I18N::reverseText($rtl_abc . '&lt;'), '>' . $rtl_cba);
	}
}
