<?php
// Debug functions.
//
// Copyright (C) 2014 Greg Roach
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Debug {
	private static $CHAR_DECORATION=array(
		// Add diacritics to letters to test UTF8 encoding issues
		'a'=>'ă', 'b'=>'ḅ', 'c'=>'ç', 'd'=>'đ', 'e'=>'ě', 'f'=>'ḟ', 'g'=>'ğ',
		'h'=>'ħ', 'i'=>'ĩ', 'j'=>'ĵ', 'k'=>'ḱ', 'l'=>'ł', 'm'=>'ḿ', 'n'=>'ñ',
		'o'=>'ø', 'p'=>'ṗ', 'q'=>'ǫ', 'r'=>'ṙ', 's'=>'š', 't'=>'ť', 'u'=>'û',
		'v'=>'ṽ', 'w'=>'ẃ', 'x'=>'ẍ', 'y'=>'ÿ', 'z'=>'ẕ',

		'A'=>'Ă', 'B'=>'Ḅ', 'C'=>'Ç', 'D'=>'Đ', 'E'=>'Ě', 'F'=>'Ḟ', 'G'=>'Ğ',
		'H'=>'Ħ', 'I'=>'Ĩ', 'J'=>'ĵ', 'K'=>'Ḱ', 'L'=>'Ł', 'M'=>'Ḿ', 'N'=>'Ñ',
		'O'=>'Ø', 'P'=>'ṗ', 'Q'=>'ǫ', 'R'=>'ṙ', 'S'=>'š', 'T'=>'ť', 'U'=>'Û',
		'V'=>'Ṽ', 'W'=>'Ẃ', 'X'=>'Ẍ', 'Y'=>'Ý', 'Z'=>'Ẕ',
		// Modify puncutation, so we can easily identify untranslated punctuation
		'['=>'⟨', ']'=>'⟩', '('=>'⟨', ')'=>'⟩', '{'=>'⟨', '}'=>'⟩',
		'.'=>'·', ':'=>'˸'
	);

	// Pseudo-tranlsate a string.  Give it the attributes of a foreign language,
	// while leaving it understandable by an English speaking developer.
	public static function pseudoTranslate($text) {
		global $TEXT_DIRECTION;

		// There are certain texts that we should never translate!
		switch ($text) {
		case 'utf8_unicode_ci':
			return $text;
		default:
			break;
		}

		// Process the each UTF8 character separately.
		// Take care not to transform HTML tags
		$tmp='';
		$in_html=false;
		$in_format=false;
		while ($text) {
			// Read the next multi-byte character
			$next_char='';
			while (ord(substr($text, 0, 1)) & 0xF0 == 0xC0) {
				$next_char.=substr($text, 0, 1);
			}
			$next_char.=substr($text, 0, 1);
			$text=substr($text, 1);
			switch ($next_char) {
			case '<':
				$in_html=true;
				$tmp.=$next_char;
				break;
			case '>':
				$in_html=false;
				$tmp.=$next_char;
				break;
			case '%':
				$in_format=true;
				$tmp.=$next_char;
				break;
			default:
				if ($in_html || $in_format || !array_key_exists($next_char, self::$CHAR_DECORATION)) {
					$tmp.=$next_char;
					if ($in_format && $next_char >='a' && $next_char<='z') {
						$in_format=false;
					}
				} else {
					if ($TEXT_DIRECTION=='ltr') {
						// Many languages have longer words than English - add some padding
						$tmp.='&thinsp;'.self::$CHAR_DECORATION[$next_char].'&thinsp;';
					} else {
						// RTL words tend to be short, so no need to add padding
						$tmp.=WT_UTF8_RLO.$next_char.WT_UTF8_PDF;
					}
				}
				break;
			}
		}
		// Highlight the boundaries between translated strings
		return '|'.$tmp.'|';
	}
}
