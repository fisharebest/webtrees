<?php

/**
 * PHP Polyfill
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2016 Greg Roach
 * @license       MIT or GPLv3+
 */

namespace Fisharebest\PhpPolyfill\Test;

use Fisharebest\PhpPolyfill\Php54;
use PHPUnit_Framework_Error_Warning;
use PHPUnit_Framework_TestCase;

/**
 * Class Php54Test - tests for class Php54
 */
class Php54Test extends PHPUnit_Framework_TestCase {
	/**
	 * @covers Fisharebest\PhpPolyfill\Php54::httpResponseCode
	 * @runInSeparateProcess
	 */
	public function testHttpResponseCodeDefaultValueIs200() {
		$code = Php54::httpResponseCode(null);

		$this->assertSame(200, $code);
	}

	/**
	 * @covers Fisharebest\PhpPolyfill\Php54::httpResponseCode
	 * @runInSeparateProcess
	 */
	public function testHttpResponseCodePreviousValueReturned() {
		$code1 = Php54::httpResponseCode(403);
		$code2 = Php54::httpResponseCode(null);

		$this->assertSame(200, $code1);
		$this->assertSame(403, $code2);
	}

	/**
	 * @covers Fisharebest\PhpPolyfill\Php54::httpResponseCode
	 * @runInSeparateProcess
	 */
	public function testHttpResponseCodeNumericStringsAreConvertedToIntegers() {
		$code1 = Php54::httpResponseCode('403');
		$code2 = Php54::httpResponseCode(null);

		$this->assertSame(200, $code1);
		$this->assertSame(403, $code2);
	}

	/**
	 * @covers Fisharebest\PhpPolyfill\Php54::httpResponseCode
	 * @runInSeparateProcess
	 * @expectedException        PHPUnit_Framework_Error_Warning
	 * @expectedExceptionCode    E_USER_WARNING
	 * @expectedExceptionMessage http_response_code() expects parameter 1 to be long, string given
	 */
	public function testHttpResponseCodeNonNumericStringsAreInvalid() {
		Php54::httpResponseCode('foo-bar');
	}

	/**
	 * @covers Fisharebest\PhpPolyfill\Php54::httpResponseCode
	 * @runInSeparateProcess
	 */
	public function testHttpResponseIgnoreInvalid() {
		ini_set('error_reporting', 0);
		$code = Php54::httpResponseCode('foo-bar');

		$this->assertSame(200, $code);
	}

	/**
	 * @covers Fisharebest\PhpPolyfill\Php54::removeMagicQuotesFromArray
	 * @runInSeparateProcess
	 */
	public function testHttpRemoveMagicQuotesFromArray() {
		$input = array(
			addslashes('f\\oo') => addslashes('b\'ar'),
			addslashes('b"az') => array(
				addslashes('q\\ux') => addslashes('e\'ek'),
			),
		);
		$expected = array(
			'f\\oo' => 'b\'ar',
			'b"az'  => array(
				'q\\ux' => 'e\'ek'
			),
		);


		$output = Php54::removeMagicQuotesFromArray($input);

		$this->assertSame($expected, $output);
	}

	/**
	 * @covers Fisharebest\PhpPolyfill\Php54::removeMagicQuotes
	 * @runInSeparateProcess
	 */
	public function testHttpRemoveMagicQuotes() {
		$_GET     = array(addslashes('g\\et') => addslashes('G\'ET'));
		$_POST    = array(addslashes('p\\ost') => addslashes('P\'OST'));
		$_COOKIE  = array(addslashes('c\\ookie') => addslashes('C\'OOKIE'));
		$_REQUEST = array(addslashes('r\\equest') => addslashes('R\'EQUEST'));

		Php54::removeMagicQuotes();

		$this->assertSame(array('g\\et' => 'G\'ET'), $_GET);
		$this->assertSame(array('p\\ost' => 'P\'OST'), $_POST);
		$this->assertSame(array('c\\ookie' => 'C\'OOKIE'), $_COOKIE);
		$this->assertSame(array('r\\equest' => 'R\'EQUEST'), $_REQUEST);
	}
}
