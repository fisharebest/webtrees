<?php
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

use Fisharebest\Webtrees\Filter;

// If set to 1, these cause a warning to be displayed whenever
// there exists both $_SESSION['foo'] and global $foo.
// The warning prevents session cookies from being sent.
ini_set('session.bug_compat_warn', 0);
ini_set('session.bug_compat_42', 0);

/**
 * This function was introduced in PHP 5.4
 */
function session_register_shutdown() {
	register_shutdown_function('session_write_close');
}

// http://php.net/manual/en/security.magicquotes.disabling.php
if (get_magic_quotes_gpc()) {
	$_process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
	while (list($key, $val) = each($_process)) {
		foreach ($val as $k => $v) {
			unset($_process[$key][$k]);
			if (is_array($v)) {
				$_process[$key][stripslashes($k)] = $v;
				$_process[]                       = &$_process[$key][stripslashes($k)];
			} else {
				$_process[$key][stripslashes($k)] = stripslashes($v);
			}
		}
	}
	unset($_process);
}

////////////////////////////////////////////////////////////////////////////////
// The ircmaxell/password-compat library does not support unpatched versions of
// PHP older than PHP5.3.6.  These versions of PHP have no secure crypt library.
////////////////////////////////////////////////////////////////////////////////
$hash = '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
if (!defined('PASSWORD_BCRYPT') && crypt("password", $hash) !== $hash) {
	define('PASSWORD_BCRYPT', 1);
	define('PASSWORD_DEFAULT', 1);
	/**
	 * Shim for PHP5.5
	 *
	 * @param string $password
	 * @param int    $algo
	 *
	 * @return string
	 */
	function password_hash($password, $algo) {
		return crypt($password);
	}

	/**
	 * Shim for PHP5.5
	 *
	 * @param string $hash
	 * @param int    $algo
	 *
	 * @return bool
	 */
	function password_needs_rehash($hash, $algo) {
		return false;
	}

	/**
	 * Shim for PHP5.5
	 *
	 * @param string $password
	 * @param string $hash
	 *
	 * @return bool
	 */
	function password_verify($password, $hash) {
		return crypt($password, $hash) === $hash;
	}
}

/**
 * https://php.net/http_response_code
 *
 * @param string|null $code
 *
 * @link https://php.net/http_response_code
 *
 * @return int|null
 */
function http_response_code($code = null) {
	static $http_response_code = 200;

	if ($code !== null) {
		switch ($code) {
		case 100:
			$text = 'Continue';
			break;
		case 101:
			$text = 'Switching Protocols';
			break;
		case 200:
			$text = 'OK';
			break;
		case 201:
			$text = 'Created';
			break;
		case 202:
			$text = 'Accepted';
			break;
		case 203:
			$text = 'Non-Authoritative Information';
			break;
		case 204:
			$text = 'No Content';
			break;
		case 205:
			$text = 'Reset Content';
			break;
		case 206:
			$text = 'Partial Content';
			break;
		case 300:
			$text = 'Multiple Choices';
			break;
		case 301:
			$text = 'Moved Permanently';
			break;
		case 302:
			$text = 'Moved Temporarily';
			break;
		case 303:
			$text = 'See Other';
			break;
		case 304:
			$text = 'Not Modified';
			break;
		case 305:
			$text = 'Use Proxy';
			break;
		case 400:
			$text = 'Bad Request';
			break;
		case 401:
			$text = 'Unauthorized';
			break;
		case 402:
			$text = 'Payment Required';
			break;
		case 403:
			$text = 'Forbidden';
			break;
		case 404:
			$text = 'Not Found';
			break;
		case 405:
			$text = 'Method Not Allowed';
			break;
		case 406:
			$text = 'Not Acceptable';
			break;
		case 407:
			$text = 'Proxy Authentication Required';
			break;
		case 408:
			$text = 'Request Time-out';
			break;
		case 409:
			$text = 'Conflict';
			break;
		case 410:
			$text = 'Gone';
			break;
		case 411:
			$text = 'Length Required';
			break;
		case 412:
			$text = 'Precondition Failed';
			break;
		case 413:
			$text = 'Request Entity Too Large';
			break;
		case 414:
			$text = 'Request-URI Too Large';
			break;
		case 415:
			$text = 'Unsupported Media Type';
			break;
		case 500:
			$text = 'Internal Server Error';
			break;
		case 501:
			$text = 'Not Implemented';
			break;
		case 502:
			$text = 'Bad Gateway';
			break;
		case 503:
			$text = 'Service Unavailable';
			break;
		case 504:
			$text = 'Gateway Time-out';
			break;
		case 505:
			$text = 'HTTP Version not supported';
			break;
		default:
			throw new \DomainException;
		}
		$http_response_code = $code;
		$protocol           = Filter::server('SERVER_PROTOCOL', null, 'HTTP/1.0');
		header($protocol . ' ' . $code . ' ' . $text);
	}

	return $http_response_code;
}
