<?php

/**
 * PHP Polyfill
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   MIT or GPLv3+
 */

use Fisharebest\PhpPolyfill\Php54;

if (PHP_VERSION_ID < 50600) {
	// Add features that were introduced in PHP 5.5

	if (PHP_VERSION_ID === 50509 && PHP_INT_SIZE === 4) {
		// Missing functions in PHP 5.5.9 - affects 32 bit builds of Ubuntu 14.04LTS
		// See https://bugs.launchpad.net/ubuntu/+source/php5/+bug/1315888
		if (!function_exists('gzopen') && function_exists('gzopen64')) {
			function gzopen($filename, $mode, $use_include_path = 0) { return gzopen64($filename, $mode, $use_include_path); }
		}
		if (!function_exists('gzseek') && function_exists('gzseek64')) {
			function gzseek($zp, $offset, $whence = SEEK_SET) { return gzseek64($zp, $offset, $whence); }
		}
		if (!function_exists('gztell') && function_exists('gztell64')) {
			function gztell($zp) { return gztell64($zp); }
		}
	}

	if (PHP_VERSION_ID < 50400) {
		// Magic quotes were removed in PHP 5.4
		if (get_magic_quotes_gpc()) {
			Php54::removeMagicQuotes();
		}
		// The global session variable bug/feature was removed in PHP 5.4
		if (ini_get('session.bug_compat_42')) {
			ini_set('session.bug_compat_42', '0');
		}

		// Add features that were introduced in PHP 5.4
		if (!function_exists('http_response_code')) {
			function http_response_code($reponse_code = null) { return Php54::httpResponseCode($reponse_code); }
		}
	}
}
