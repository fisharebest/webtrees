<?php

/**
 * PHP Polyfill
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2016 Greg Roach
 * @license   MIT or GPLv3+
 */

namespace Fisharebest\PhpPolyfill;

use Symfony\Polyfill\Php54\Php54 as SymfonyPhp54;

/**
 * Class Php - polyfills for poor implementations of PHP
 */
class Php {
	const BIG_ENDIAN_INF_BYTES    = '7ff0000000000000';
	const LITTLE_ENDIAN_INF_BYTES = '000000000000f07f';

	/**
	 * Some builds of PHP (e.g. strato.de on SunOS) omit the
	 * definition of INF.
	 *
	 * Note: we can't use hex2bin() on PHP 5.3.  Use pack('H*') instead.
	 *
	 * @link http://php.net/manual/en/math.constants.php
	 *
	 * @return double
	 */
	public static function inf() {
		$inf_bytes = self::isLittleEndian() ? self::LITTLE_ENDIAN_INF_BYTES : self::BIG_ENDIAN_INF_BYTES;
		$inf       = unpack('d', pack('H*', $inf_bytes));

		return $inf[1];
	}


	/**
	 * Is this CPU big-endian or little-endian?
	 *
	 * @return bool
	 */
	private static function isLittleEndian() {
		$tmp = unpack('S',"\x01\x00");

		return $tmp[1] === 1;
	}
}
