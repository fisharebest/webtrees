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
	const ENCODING = 'utf-8';

	//////////////////////////////////////////////////////////////////////////////
	// Escape a string for use in HTML
	//////////////////////////////////////////////////////////////////////////////
	public static function escapeHtml($string) {
		if (defined('ENT_SUBSTITUTE')) {
			// PHP5.4 allows us to substitute invalid UTF8 sequences
			return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, self::ENCODING);
		} else {
			return htmlspecialchars($string, ENT_QUOTES, self::ENCODING);
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
		return preg_replace_callback('/[^A-Za-z0-9,. _]/Su', function($x) {
			if (strlen($x[0]) == 1) {
				return sprintf('\\x%02X', ord($x[0]));
			} elseif (function_exists('iconv')) {
				return sprintf('\\u%04s', strtoupper(bin2hex(iconv(self::ENCODING, 'utf16be', $x[0]))));
			} elseif (function_exists('mb_convert_encoding')) {
				return sprintf('\\u%04s', strtoupper(bin2hex(mb_convert_encoding($x[0], 'utf16be', self::ENCODING))));
			} else {
				return $x[0];
			}
		}, $string);
	}

	//////////////////////////////////////////////////////////////////////////////
	// Convert a string from utf8 to utf16be
	//////////////////////////////////////////////////////////////////////////////
	private static function utf16be($string) {
		if (function_exists('iconv')) {
			return iconv(self::ENCODING, 'utf16be', $string);
		} elseif (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($string, 'utf16be', self::ENCODING);
		} else {
			throw new Exception('Need either iconv or mbstring libraries');
		}
	}
}
