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
		\Patchwork\Utf8\Bootup::initAll();
		\Patchwork\Utf8\Bootup::filterRequestUri();
		\Patchwork\Utf8\Bootup::filterRequestInputs();
	}

	/**
	 * Test that the class exists
	 *
	 * @return void
	 */
	public function testClassExists() {
		$this->assertTrue(class_exists('\WT_I18N'));
	}	/**
	 * Test WT_I18N::strtoupper()
	 *
	 * @todo test all locales
	 *
	 * @return void
	 */
	public function testStrtoupper() {
		$this->assertSame(WT_I18N::strtoupper(''), '');
		$this->assertSame(WT_I18N::strtoupper('Abc'), 'ABC');
	}

	/**
	 * Test WT_I18N::strtolower()
	 *
	 * @todo test all locales
	 *
	 * @return void
	 */
	public function testStrtolower() {
		$this->assertSame(WT_I18N::strtolower(''), '');
		$this->assertSame(WT_I18N::strtolower('Abc'), 'abc');
	}

	/**
	 * Test WT_I18N::strcasecmp()
	 *
	 * @todo test all locales
	 *
	 * @return void
	 */
	public function testStrcasecmp() {
		$this->assertSame(WT_I18N::strcasecmp('', ''), 0);
		$this->assertSame(WT_I18N::strcasecmp('Abc', 'abc'), 0);
		$this->assertTrue(WT_I18N::strcasecmp('Abc', 'bcd') < 0);
		$this->assertTrue(WT_I18N::strcasecmp('bcd', 'ABC') > 0);
		$this->assertTrue(WT_I18N::strcasecmp('Abc', 'abcd') < 0);
		$this->assertTrue(WT_I18N::strcasecmp('Abcd', 'abc') > 0);
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

		$this->assertSame(WT_I18N::reverseText(''), '');
		$this->assertSame(WT_I18N::reverseText('abc123'), 'abc123');
		$this->assertSame(WT_I18N::reverseText('<b>abc</b>123'), 'abc123');
		$this->assertSame(WT_I18N::reverseText('&lt;abc&gt;'), '<abc>');
		$this->assertSame(WT_I18N::reverseText('abc[123]'), 'abc[123]');
		$this->assertSame(WT_I18N::reverseText($rtl_123), $rtl_123);
		$this->assertSame(WT_I18N::reverseText($rtl_abc), $rtl_cba);
		$this->assertSame(WT_I18N::reverseText($rtl_abc . '123'), '123' . $rtl_cba);
		$this->assertSame(WT_I18N::reverseText($rtl_abc . '[123]'), '[123]' . $rtl_cba);
		$this->assertSame(WT_I18N::reverseText('123' . $rtl_abc . '456'), '456' . $rtl_cba . '123');
		$this->assertSame(WT_I18N::reverseText($rtl_abc . '&lt;'), '>' . $rtl_cba);
	}

	/**
	 * Test WT_I18N::languageName()
	 *
	 * @return void
	 */
	public function testKnownLanguageName() {
		$this->assertSame('العربية', WT_I18N::languageName('ar'));
		$this->assertSame('Deutsch', WT_I18N::languageName('de'));
		$this->assertSame('Ελληνικά', WT_I18N::languageName('el'));
		$this->assertSame('British English', WT_I18N::languageName('en-GB'));
		$this->assertSame('français', WT_I18N::languageName('fr'));
	}

	/**
	 * Test WT_I18N::languageName()
	 *
	 * @return void
	 */
	public function testUnknownLanguageName() {
		if (class_exists('\Locale')) {
			$this->assertSame('English (India)', WT_I18N::languageName('en-IN'));
		} else {
			$this->assertSame('en-IN', WT_I18N::languageName('en-IN'));
		}
	}

	/**
	 * Test WT_I18N::languageScript()
	 *
	 * @return void
	 */
	public function testKnownLanguageScript() {
		$this->assertSame('Arab', WT_I18N::languageScript('ar'));
		$this->assertSame('Latn', WT_I18N::languageScript('de'));
		$this->assertSame('Grek', WT_I18N::languageScript('el'));
	}

	/**
	 * Test WT_I18N::languageScript()
	 *
	 * @return void
	 */
	public function testUnknownLanguageScript() {
		$this->assertSame('Latn', WT_I18N::languageScript('zz'));
	}
}
