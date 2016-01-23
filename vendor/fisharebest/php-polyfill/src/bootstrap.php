<?php

/**
 * PHP Polyfill
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2016 Greg Roach
 * @license   MIT or GPLv3+
 */

use Fisharebest\PhpPolyfill\Php;
use Fisharebest\PhpPolyfill\Php54;

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

if (!defined('INF')) {
	define('INF', PHP::inf());
}
