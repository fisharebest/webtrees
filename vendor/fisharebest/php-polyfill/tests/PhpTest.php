<?php

/**
 * PHP Polyfill
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2016 Greg Roach
 * @license       MIT or GPLv3+
 */

namespace Fisharebest\PhpPolyfill\Test;

use Fisharebest\PhpPolyfill\Php;
use PHPUnit_Framework_Error_Warning;
use PHPUnit_Framework_TestCase;

/**
 * Class PhpTest - tests for class Php
 */
class PhpTest extends PHPUnit_Framework_TestCase {
	/**
	 * @covers Fisharebest\PhpPolyfill\Php::inf
	 * @covers Fisharebest\PhpPolyfill\Php::isLittleEndian
	 * @runInSeparateProcess
	 */
	public function testInf() {
		$this->assertTrue(is_float(Php::inf()));
		$this->assertTrue(is_double(Php::inf()));
		$this->assertTrue(is_infinite(Php::inf()));
		$this->assertFalse(is_finite(Php::inf()));
	}
}
