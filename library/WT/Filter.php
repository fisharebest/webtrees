<?php
// Filter/escape/validate input and output
//
// webtrees: Web based Family History software
// Copyright (c) 2013 webtrees development team
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Filter {
	// REGEX to match a URL
	// Some versions of RFC3987 have an appendix B which gives the following regex
	// (([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?
	// This matches far too much while a “precise” regex is several pages long.
	// This is a compromise.
	const URL_REGEX='((https?|ftp]):)(//([^\s/?#<>]*))?([^\s?#<>]*)(\?([^\s#<>]*))?(#[^\s?#<>]+)?';


	//////////////////////////////////////////////////////////////////////////////
	// Escape a string for use in HTML
	//////////////////////////////////////////////////////////////////////////////
	public static function escapeHtml($string) {
		if (defined('ENT_SUBSTITUTE')) {
			// PHP5.4 allows us to substitute invalid UTF8 sequences
			return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		} else {
			return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
		}
	}

	//////////////////////////////////////////////////////////////////////////////
	// Escape a string for use in a URL
	//////////////////////////////////////////////////////////////////////////////
	public static function escapeUrl($string) {
		return rawurlencode($string);
	}

	//////////////////////////////////////////////////////////////////////////////
	// Escape a string for use in Javascript
	//////////////////////////////////////////////////////////////////////////////
	public static function escapeJs($string) {
		return preg_replace_callback('/[^A-Za-z0-9,. _]/Su', array('WT_Filter', 'escapeJsCallback'), $string);
	}

	private static function escapeJsCallback($x) {
		if (strlen($x[0]) == 1) {
			return sprintf('\\x%02X', ord($x[0]));
		} elseif (function_exists('iconv')) {
			return sprintf('\\u%04s', strtoupper(bin2hex(iconv('UTF-8', 'UTF-16BE', $x[0]))));
		} elseif (function_exists('mb_convert_encoding')) {
			return sprintf('\\u%04s', strtoupper(bin2hex(mb_convert_encoding($x[0], 'UTF-16BE', 'UTF-8'))));
		} else {
			return $x[0];
		}
	}

	//////////////////////////////////////////////////////////////////////////////
	// Unescape an HTML string, giving just the literal text
	//////////////////////////////////////////////////////////////////////////////
	public static function unescapeHtml($string) {
		return html_entity_decode(strip_tags($string), ENT_QUOTES, 'UTF-8');
	}

	//////////////////////////////////////////////////////////////////////////////
	// Escape a string for use in HTML, and additionally:
	// Convert URLs to links, inserting soft-hyphens at word boundaries so that
	// the browser can word-wrap.
	//////////////////////////////////////////////////////////////////////////////
	public static function expandUrls($text) {
		return preg_replace_callback(
			'/' . addcslashes('(?!>)' . self::URL_REGEX . '(?!</a>)', '/') . '/i',
			create_function( // Insert soft hyphens into the replaced string
				'$m',
				'return "<a href=\"" . $m[0] . "\" target=\"blank\">" . preg_replace("/\b/", "&shy;", $m[0]) . "</a>";'
			),
			WT_Filter::escapeHtml($text)
		);
	}

	//////////////////////////////////////////////////////////////////////////////
	// Validate INPUT requests
	//////////////////////////////////////////////////////////////////////////////
	private static function _input($source, $variable, $regexp=null, $default=null) {
		if ($regexp) {
			return filter_input(
				$source,
				$variable,
				FILTER_VALIDATE_REGEXP,
				array(
					'options' => array(
						'regexp'  => '/^(' . $regexp . ')$/u',
						'default' => $default,
					),
				)
			);
		} else {
			$tmp = filter_input(
				$source,
				$variable,
				FILTER_CALLBACK,
				array(
					'options' => array('WT_Filter', '_inputCallback'),
				)
			);
			return ($tmp===null || $tmp===false) ? $default : $tmp;
		}
	}

	private static function _inputCallback($x) {
		return mb_check_encoding($x, 'UTF-8') ? $x : false;
	}

	private static function _inputArray($source, $variable, $regexp=null, $default=null) {
		if ($regexp) {
			// PHP5.3 requires the $tmp variable
			$tmp = filter_input_array(
				$source,
				array(
					$variable => array(
						'flags'   => FILTER_REQUIRE_ARRAY,
						'filter'  => FILTER_VALIDATE_REGEXP,
						'options' => array(
							'regexp'  => '/^(' . $regexp . ')$/u',
							'default' => $default,
						),
					),
				)
			);
			return $tmp[$variable] ? $tmp[$variable] : array();
		} else {
			// PHP5.3 requires the $tmp variable
			$tmp = filter_input_array(
				$source,
				array(
					$variable => array(
						'flags'   => FILTER_REQUIRE_ARRAY,
						'filter'  => FILTER_CALLBACK,
						'options' => array('WT_Filter', '_inputArrayCallback')
					),
				)
			);
			return $tmp[$variable] ? $tmp[$variable] : array();
		}
	}

	private static function _inputArrayCallback($x) {
		return mb_check_encoding($x, 'UTF-8') ? $x : false;
	}

	//////////////////////////////////////////////////////////////////////////////
	// Validate GET requests
	//////////////////////////////////////////////////////////////////////////////
	public static function get($variable, $regexp=null, $default=null) {
		return self::_input(INPUT_GET, $variable, $regexp, $default);
	}

	public static function getArray($variable, $regexp=null, $default=null) {
		return self::_inputArray(INPUT_GET, $variable, $regexp, $default);
	}

	public static function getBool($variable) {
		return filter_input(INPUT_GET, $variable, FILTER_VALIDATE_BOOLEAN);
	}

	public static function getInteger($variable, $min=0, $max=PHP_INT_MAX, $default=0) {
		return filter_input(INPUT_GET, $variable, FILTER_VALIDATE_INT, array('options'=>array('min_range'=>$min, 'max_range'=>$max, 'default'=>$default)));
	}

	public static function getEmail($variable, $default=null) {
		return filter_input(INPUT_GET, $variable, FILTER_VALIDATE_EMAIL) ? filter_input(INPUT_GET, $variable, FILTER_VALIDATE_EMAIL) : $default;
	}

	public static function getUrl($variable, $default=null) {
		return filter_input(INPUT_GET, $variable, FILTER_VALIDATE_URL) ? filter_input(INPUT_GET, $variable, FILTER_VALIDATE_URL) : $default;
	}

	//////////////////////////////////////////////////////////////////////////////
	// Validate POST requests
	//////////////////////////////////////////////////////////////////////////////
	public static function post($variable, $regexp=null, $default=null) {
		return self::_input(INPUT_POST, $variable, $regexp, $default);
	}

	public static function postArray($variable, $regexp=null, $default=null) {
		return self::_inputArray(INPUT_POST, $variable, $regexp, $default);
	}

	public static function postBool($variable) {
		return filter_input(INPUT_POST, $variable, FILTER_VALIDATE_BOOLEAN);
	}

	public static function postInteger($variable, $min=0, $max=PHP_INT_MAX, $default=0) {
		return filter_input(INPUT_POST, $variable, FILTER_VALIDATE_INT, array('options'=>array('min_range'=>$min, 'max_range'=>$max, 'default'=>$default)));
	}

	public static function postEmail($variable, $default=null) {
		return filter_input(INPUT_POST, $variable, FILTER_VALIDATE_EMAIL) ? filter_input(INPUT_POST, $variable, FILTER_VALIDATE_EMAIL) : $default;
	}

	public static function postUrl($variable, $default=null) {
		return filter_input(INPUT_POST, $variable, FILTER_VALIDATE_URL) ? filter_input(INPUT_POST, $variable, FILTER_VALIDATE_URL) : $default;
	}

	//////////////////////////////////////////////////////////////////////////////
	// Cross-Site Request Forgery tokens - ensure that the user is submitting
	// a form that was generated by the current session.
	//////////////////////////////////////////////////////////////////////////////
	public static function getCsrfToken() {
		global $WT_SESSION;

		if ($WT_SESSION->CSRF_TOKEN === null) {
			$charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcedfghijklmnopqrstuvwxyz0123456789';
			for ($n=0; $n<32; ++$n) {
				$WT_SESSION->CSRF_TOKEN .= substr($charset, mt_rand(0, 61), 1);
			}
		}

		return $WT_SESSION->CSRF_TOKEN;
	}

	// Generate an <input> element - to protect the current form from CSRF attacks.
	public static function getCsrf() {
		return '<input type="hidden" name="csrf" value="' . WT_Filter::getCsrfToken() . '">';
	}

	// Check that the POST request contains the CSRF token generated above.
	public static function checkCsrf() {
		if (WT_Filter::post('csrf') !== WT_Filter::getCsrfToken()) {
			// Oops.  Something is not quite right
			AddToLog('CSRF mismatch - session expired or malicious attack', 'auth');
			WT_FlashMessages::addMessage(WT_I18N::translate('This form has expired.  Try again.'));
			return false;
		}
		return true;
	}
}
