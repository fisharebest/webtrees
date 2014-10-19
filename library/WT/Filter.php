<?php
// Filter/escape/validate input and output
//
// webtrees: Web based Family History software
// Copyright (c) 2014 webtrees development team
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use Michelf\MarkdownExtra;
use WT\Log;

/**
 * Class WT_Filter - utility functions for validating input and filtering output
 */
class WT_Filter {
	// REGEX to match a URL
	// Some versions of RFC3987 have an appendix B which gives the following regex
	// (([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?
	// This matches far too much while a “precise” regex is several pages long.
	// This is a compromise.
	const URL_REGEX='((https?|ftp]):)(//([^\s/?#<>]*))?([^\s?#<>]*)(\?([^\s#<>]*))?(#[^\s?#<>]+)?';

	/**
	 * Escape a string for use in HTML
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function escapeHtml($string) {
		if (defined('ENT_SUBSTITUTE')) {
			// PHP5.4 allows us to substitute invalid UTF8 sequences
			return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		} else {
			return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
		}
	}

	/**
	 * Escape a string for use in a URL
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function escapeUrl($string) {
		return rawurlencode($string);
	}

	/**
	 * Escape a string for use in Javascript
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function escapeJs($string) {
		return preg_replace_callback('/[^A-Za-z0-9,. _]/Su', function($x) {
			if (strlen($x[0]) == 1) {
				return sprintf('\\x%02X', ord($x[0]));
			} elseif (function_exists('iconv')) {
				return sprintf('\\u%04s', strtoupper(bin2hex(iconv('UTF-8', 'UTF-16BE', $x[0]))));
			} elseif (function_exists('mb_convert_encoding')) {
				return sprintf('\\u%04s', strtoupper(bin2hex(mb_convert_encoding($x[0], 'UTF-16BE', 'UTF-8'))));
			} else {
				return $x[0];
			}
		}, $string);
	}

	/**
	 * Escape a string for use in a SQL "LIKE" clause
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function escapeLike($string) {
		return strtr(
			$string,
			array(
				'\\' => '\\\\',
				'%'  => '\%',
				'_'  => '\_',
			)
		);
	}

	/**
	 * Unescape an HTML string, giving just the literal text
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function unescapeHtml($string) {
		return html_entity_decode(strip_tags($string), ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Format block-level text such as notes or transcripts, etc.
	 *
	 * @param string  $text
	 * @param WT_Tree $WT_TREE
	 *
	 * @return string
	 */
	public static function formatText($text, WT_Tree $WT_TREE) {
		switch ($WT_TREE->getPreference('FORMAT_TEXT')) {
		case 'markdown':
			return '<div class="markdown" dir="auto">' . WT_Filter::markdown($text) . '</div>';
		default:
			return '<div style="white-space: pre-wrap;" dir="auto">' . WT_Filter::expandUrls($text) . '</div>';
		}
	}

	/**
	 * Escape a string for use in HTML, and additionally convert URLs to links.
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public static function expandUrls($text) {
		return preg_replace_callback(
			'/' . addcslashes('(?!>)' . WT_Filter::URL_REGEX . '(?!</a>)', '/') . '/i',
			function ($m) {
				return '<a href="' . $m[0] . '" target="_blank">' . $m[0] . '</a>';
			},
			WT_Filter::escapeHtml($text)
		);
	}

	/**
	 * Format a block of text, using "Markdown".
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public static function markdown($text) {
		$parser                       = new MarkdownExtra;
		$parser->empty_element_suffix = '>';
		$parser->no_markup            = true;
		$text                         = $parser->transform($text);

		// HTMLPurifier needs somewhere to write temporary files
		$HTML_PURIFIER_CACHE_DIR = WT_DATA_DIR . 'html_purifier_cache';

		if (!is_dir($HTML_PURIFIER_CACHE_DIR)) {
			mkdir($HTML_PURIFIER_CACHE_DIR);
		}

		$config = HTMLPurifier_Config::createDefault();
		$config->set('Cache.SerializerPath', $HTML_PURIFIER_CACHE_DIR);
		$purifier = new HTMLPurifier($config);
		$text = $purifier->purify($text);

		return $text;
	}

	/**
	 * Validate INPUT requests
	 *
	 * @param string      $source
	 * @param string      $variable
	 * @param string|null $regexp
	 * @param string|null $default
	 *
	 * @return string|null
	 */
	private static function input($source, $variable, $regexp=null, $default=null) {
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
					'options' => function($x) {
						return !function_exists('mb_convert_encoding') || mb_check_encoding($x, 'UTF-8') ? $x : false;
					},
				)
			);
			return ($tmp===null || $tmp===false) ? $default : $tmp;
		}
	}

	/**
	 * Validate array INPUT requests
	 *
	 * @param string      $source
	 * @param string      $variable
	 * @param string|null $regexp
	 * @param string|null $default
	 *
	 * @return string[]
	 */
	private static function inputArray($source, $variable, $regexp=null, $default=null) {
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
			return $tmp[$variable] ?: array();
		} else {
			// PHP5.3 requires the $tmp variable
			$tmp = filter_input_array(
				$source,
				array(
					$variable => array(
						'flags'   => FILTER_REQUIRE_ARRAY,
						'filter'  => FILTER_CALLBACK,
						'options' => function($x) {
							return !function_exists('mb_convert_encoding') || mb_check_encoding($x, 'UTF-8') ? $x : false;
						}
					),
				)
			);
			return $tmp[$variable] ?: array();
		}
	}

	/**
	 * Validate GET requests
	 *
	 * @param string      $variable
	 * @param string|null $regexp
	 * @param string|null $default
	 *
	 * @return null|string
	 */
	public static function get($variable, $regexp=null, $default=null) {
		return self::input(INPUT_GET, $variable, $regexp, $default);
	}

	/**
	 * Validate array GET requests
	 *
	 * @param string      $variable
	 * @param string|null $regexp
	 * @param string|null $default
	 *
	 * @return string[]
	 */
	public static function getArray($variable, $regexp=null, $default=null) {
		return self::inputArray(INPUT_GET, $variable, $regexp, $default);
	}

	/**
	 * Validate boolean GET requests
	 *
	 * @param string $variable
	 *
	 * @return boolean
	 */
	public static function getBool($variable) {
		return (bool)filter_input(INPUT_GET, $variable, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * Validate integer GET requests
	 *
	 * @param string  $variable
	 * @param integer $min
	 * @param integer $max
	 * @param integer $default
	 *
	 * @return integer
	 */
	public static function getInteger($variable, $min=0, $max=PHP_INT_MAX, $default=0) {
		return filter_input(INPUT_GET, $variable, FILTER_VALIDATE_INT, array('options'=>array('min_range'=>$min, 'max_range'=>$max, 'default'=>$default)));
	}

	/**
	 * Validate email GET requests
	 *
	 * @param string      $variable
	 * @param string|null $default
	 *
	 * @return null|string
	 */
	public static function getEmail($variable, $default=null) {
		return filter_input(INPUT_GET, $variable, FILTER_VALIDATE_EMAIL) ?: $default;
	}

	/**
	 * Validate URL GET requests
	 *
	 * @param string      $variable
	 * @param string|null $default
	 *
	 * @return null|string
	 */
	public static function getUrl($variable, $default=null) {
		return filter_input(INPUT_GET, $variable, FILTER_VALIDATE_URL) ?: $default;
	}

	/**
	 * Validate POST requests
	 *
	 * @param string      $variable
	 * @param string|null $regexp
	 * @param string|null $default
	 *
	 * @return null|string
	 */
	public static function post($variable, $regexp=null, $default=null) {
		return self::input(INPUT_POST, $variable, $regexp, $default);
	}

	/**
	 * Validate array POST requests
	 *
	 * @param string      $variable
	 * @param string|null $regexp
	 * @param string|null $default
	 *
	 * @return string[]
	 */
	public static function postArray($variable, $regexp=null, $default=null) {
		return self::inputArray(INPUT_POST, $variable, $regexp, $default);
	}

	/**
	 * Validate boolean POST requests
	 *
	 * @param string $variable
	 *
	 * @return boolean
	 */
	public static function postBool($variable) {
		return (bool)filter_input(INPUT_POST, $variable, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * Validate integer POST requests
	 *
	 * @param string  $variable
	 * @param integer $min
	 * @param integer $max
	 * @param integer $default
	 *
	 * @return integer
	 */
	public static function postInteger($variable, $min=0, $max=PHP_INT_MAX, $default=0) {
		return filter_input(INPUT_POST, $variable, FILTER_VALIDATE_INT, array('options'=>array('min_range'=>$min, 'max_range'=>$max, 'default'=>$default)));
	}

	/**
	 * Validate email POST requests
	 *
	 * @param string      $variable
	 * @param string|null $default
	 *
	 * @return null|string
	 */
	public static function postEmail($variable, $default=null) {
		return filter_input(INPUT_POST, $variable, FILTER_VALIDATE_EMAIL) ?: $default;
	}

	/**
	 * Validate URL GET requests
	 *
	 * @param string      $variable
	 * @param string|null $default
	 *
	 * @return null|string
	 */
	public static function postUrl($variable, $default=null) {
		return filter_input(INPUT_POST, $variable, FILTER_VALIDATE_URL) ?: $default;
	}

	/**
	 * Validate COOKIE requests
	 *
	 * @param string      $variable
	 * @param string|null $regexp
	 * @param string|null $default
	 *
	 * @return null|string
	 */
	public static function cookie($variable, $regexp=null, $default=null) {
		return self::input(INPUT_COOKIE, $variable, $regexp, $default);
	}

	/**
	 * Cross-Site Request Forgery tokens - ensure that the user is submitting
	 * a form that was generated by the current session.
	 *
	 * @return string
	 */
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

	/**
	 * Generate an <input> element - to protect the current form from CSRF attacks.
	 *
	 * @return string
	 */
	public static function getCsrf() {
		return '<input type="hidden" name="csrf" value="' . WT_Filter::getCsrfToken() . '">';
	}

	/**
	 * Check that the POST request contains the CSRF token generated above.
	 *
	 * @return boolean
	 */
	public static function checkCsrf() {
		if (WT_Filter::post('csrf') !== WT_Filter::getCsrfToken()) {
			// Oops.  Something is not quite right
			Log::addAuthenticationLog('CSRF mismatch - session expired or malicious attack');
			WT_FlashMessages::addMessage(WT_I18N::translate('This form has expired.  Try again.'));
			return false;
		}
		return true;
	}
}
