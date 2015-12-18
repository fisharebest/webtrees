<?php

/**
 * PHP Polyfill
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   MIT or GPLv3+
 */

namespace Fisharebest\PhpPolyfill;

/**
 * Class Php54 - polyfills for functions introduced in PHP5.4
 */
class Php54 {
	/**
	 * @link https://php.net/http_response_code
	 *
	 * @param string|null $response_code
	 *
	 * @return int
	 */
	public static function httpResponseCode($response_code) {
		static $current_code = 200;

		$messages = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Moved Temporarily',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
			308 => 'Permanent Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Time-out',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Large',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Time-out',
			505 => 'HTTP Version not supported',
		);

		$previous_code = $current_code;

		if (is_numeric($response_code)) {
			$current_code = (int)$response_code;
			$protocol    = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
			$message     = isset($messages[$response_code]) ? $messages[$response_code] : 'Unknown Status Code';
			header($protocol . ' ' . $response_code . ' ' . $message);
		} elseif (null !== $response_code) {
			$type = gettype($response_code);
			trigger_error('http_response_code() expects parameter 1 to be long, ' . $type . ' given', E_USER_WARNING);
		}

		return $previous_code;
	}

	/**
	 * @link https://php.net/manual/en/security.magicquotes.disabling.php
	 *
	 * @param mixed[] $old
	 *
	 * @return mixed[]
	 */
	public static function removeMagicQuotesFromArray(array $old) {
		$new = array();
		foreach ($old as $key => $value) {
			if (is_array($value)) {
				$new[stripslashes($key)] = self::removeMagicQuotesFromArray($value);
			} else {
				$new[stripslashes($key)] = stripslashes($value);
			}
		}

		return $new;
	}

	/**
	 * @link https://php.net/manual/en/security.magicquotes.disabling.php
	 */
	public static function removeMagicQuotes() {
		$_GET     = self::removeMagicQuotesFromArray($_GET);
		$_POST    = self::removeMagicQuotesFromArray($_POST);
		$_COOKIE  = self::removeMagicQuotesFromArray($_COOKIE);
		$_REQUEST = self::removeMagicQuotesFromArray($_REQUEST);
	}
}
