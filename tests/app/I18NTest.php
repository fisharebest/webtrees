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
 * Test harness for the class I18N
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
	 * Test I18N::strtoupper()
	 *
	 * @todo test all locales
	 *
	 * @return void
	 */
	public function testStrtoupper() {
		$this->assertSame(I18N::strtoupper(''), '');
		$this->assertSame(I18N::strtoupper('Abc'), 'ABC');
	}

	/**
	 * Test I18N::strtolower()
	 *
	 * @todo test all locales
	 *
	 * @return void
	 */
	public function testStrtolower() {
		$this->assertSame(I18N::strtolower(''), '');
		$this->assertSame(I18N::strtolower('Abc'), 'abc');
	}

	/**
	 * Test I18N::strcasecmp()
	 *
	 * @todo test all locales
	 *
	 * @return void
	 */
	public function testStrcasecmp() {
		$this->assertSame(I18N::strcasecmp('', ''), 0);
		$this->assertSame(I18N::strcasecmp('Abc', 'abc'), 0);
		$this->assertTrue(I18N::strcasecmp('Abc', 'bcd') < 0);
		$this->assertTrue(I18N::strcasecmp('bcd', 'ABC') > 0);
		$this->assertTrue(I18N::strcasecmp('Abc', 'abcd') < 0);
		$this->assertTrue(I18N::strcasecmp('Abcd', 'abc') > 0);
	}

	/**
	 * Test I18N::reverseText()
	 *
	 * @return void
	 */
	public function testReverseText() {
		// Create these strings carefully, as text editors can display them in confusing ways.
		$rtl_abc = 'א' . 'ב' . 'ג';
		$rtl_cba = 'ג' . 'ב' . 'א';
		$rtl_123 = '١' . '٢' . '٣';

		$this->assertSame(I18N::reverseText(''), '');
		$this->assertSame(I18N::reverseText('abc123'), 'abc123');
		$this->assertSame(I18N::reverseText('<b>abc</b>123'), 'abc123');
		$this->assertSame(I18N::reverseText('&lt;abc&gt;'), '<abc>');
		$this->assertSame(I18N::reverseText('abc[123]'), 'abc[123]');
		$this->assertSame(I18N::reverseText($rtl_123), $rtl_123);
		$this->assertSame(I18N::reverseText($rtl_abc), $rtl_cba);
		$this->assertSame(I18N::reverseText($rtl_abc . '123'), '123' . $rtl_cba);
		$this->assertSame(I18N::reverseText($rtl_abc . '[123]'), '[123]' . $rtl_cba);
		$this->assertSame(I18N::reverseText('123' . $rtl_abc . '456'), '456' . $rtl_cba . '123');
		$this->assertSame(I18N::reverseText($rtl_abc . '&lt;'), '>' . $rtl_cba);
	}

	/**
	 * Test I18N::languageName()
	 *
	 * @return void
	 */
	public function testKnownLanguageName() {
		$this->assertSame('العربية', I18N::languageName('ar'));
		$this->assertSame('Deutsch', I18N::languageName('de'));
		$this->assertSame('Ελληνικά', I18N::languageName('el'));
		$this->assertSame('British English', I18N::languageName('en-GB'));
		$this->assertSame('français', I18N::languageName('fr'));
	}

	/**
	 * Test I18N::languageName()
	 *
	 * @return void
	 */
	public function testUnknownLanguageName() {
		if (class_exists('\Locale')) {
			$this->assertSame('English (India)', I18N::languageName('en-IN'));
		} else {
			$this->assertSame('en-IN', I18N::languageName('en-IN'));
		}
	}

	/**
	 * Test I18N::languageScript()
	 *
	 * @return void
	 */
	public function testKnownLanguageScript() {
		$this->assertSame('Arab', I18N::languageScript('ar'));
		$this->assertSame('Latn', I18N::languageScript('de'));
		$this->assertSame('Grek', I18N::languageScript('el'));
	}

	/**
	 * Test I18N::languageScript()
	 *
	 * @return void
	 */
	public function testUnknownLanguageScript() {
		$this->assertSame('Latn', I18N::languageScript('zz'));
	}
}
