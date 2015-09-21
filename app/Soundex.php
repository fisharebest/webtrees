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
namespace Fisharebest\Webtrees;

/**
 * Phonetic matching of strings.
 */
class Soundex {
	/**
	 * Which algorithms are supported.
	 *
	 * @return string[]
	 */
	public static function getAlgorithms() {
		return array(
			'std' => /* I18N: http://en.wikipedia.org/wiki/Soundex */ I18N::translate('Russell'),
			'dm'  => /* I18N: http://en.wikipedia.org/wiki/Daitch–Mokotoff_Soundex */ I18N::translate('Daitch-Mokotoff'),
		);
	}

	/**
	 * Is there a match between two soundex codes?
	 *
	 * @param string $soundex1
	 * @param string $soundex2
	 *
	 * @return bool
	 */
	public static function compare($soundex1, $soundex2) {
		if ($soundex1 && $soundex2) {
			foreach (explode(':', $soundex1) as $code) {
				if (strpos($soundex2, $code) !== false) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Generate Russell soundex codes for a given text.
	 *
	 * @param $text
	 *
	 * @return null|string
	 */
	public static function russell($text) {
		$words         = preg_split('/\s/', $text, -1, PREG_SPLIT_NO_EMPTY);
		$soundex_array = array();
		foreach ($words as $word) {
			$soundex = soundex($word);
			// Only return codes from recognisable sounds
			if ($soundex !== '0000') {
				$soundex_array[] = $soundex;
			}
		}
		// Combine words, e.g. “New York” as “Newyork”
		if (count($words) > 1) {
			$soundex_array[] = soundex(strtr($text, ' ', ''));
		}
		// A varchar(255) column can only hold 51 4-character codes (plus 50 delimiters)
		$soundex_array = array_slice(array_unique($soundex_array), 0, 51);

		if ($soundex_array) {
			return implode(':', $soundex_array);
		} else {
			return null;
		}
	}

	/**
	 * Generate Daitch–Mokotoff soundex codes for a given text.
	 *
	 * @param $text
	 *
	 * @return null|string
	 */
	public static function daitchMokotoff($text) {
		$words         = preg_split('/\s/', $text, -1, PREG_SPLIT_NO_EMPTY);
		$soundex_array = array();
		foreach ($words as $word) {
			$soundex_array = array_merge($soundex_array, self::daitchMokotoffWord($word));
		}
		// Combine words, e.g. “New York” as “Newyork”
		if (count($words) > 1) {
			$soundex_array = array_merge($soundex_array, self::daitchMokotoffWord(strtr($text, ' ', '')));
		}
		// A varchar(255) column can only hold 36 6-character codes (plus 35 delimiters)
		$soundex_array = array_slice(array_unique($soundex_array), 0, 36);

		if ($soundex_array) {
			return implode(':', $soundex_array);
		} else {
			return null;
		}
	}

	// Determine the Daitch–Mokotoff Soundex code for a word
	// Original implementation by Gerry Kroll, and analysis by Meliza Amity

	// Max. table key length (in ASCII bytes -- NOT in UTF-8 characters!)
	const MAXCHAR = 7;

	/**
	 * Name transformation arrays.
	 * Used to transform the Name string to simplify the "sounds like" table.
	 * This is especially useful in Hebrew.
	 *
	 * Each array entry defines the "from" and "to" arguments of an preg($from, $to, $text)
	 * function call to achieve the desired transformations.
	 *
	 * Note about the use of "\x01":
	 * This code, which can’t legitimately occur in the kind of text we're dealing with,
	 * is used as a place-holder so that conditional string replacements can be done.
	 *
	 * @var string[][]
	 */
	private static $transformNameTable = array(
		// Force Yiddish ligatures to be treated as separate letters
		array('װ', 'וו'),
		array('ײ', 'יי'),
		array('ױ', 'וי'),
		array('בו', 'בע'),
		array('פו', 'פע'),
		array('ומ', 'עמ'),
		array('ום', 'עם'),
		array('ונ', 'ענ'),
		array('ון', 'ען'),
		array('וו', 'ב'),
		array("\x01", ''),
		array('ייה$', "\x01ה"),
		array('ייע$', "\x01ע"),
		array('יי', 'ע'),
		array("\x01", 'יי'),
	);

	/**
	 * The DM sound coding table is organized this way:
	 * key: a variable-length string that corresponds to the UTF-8 character sequence
	 * represented by the table entry.  Currently, that string can be up to 7
	 * bytes long.  This maximum length is defined by the value of global variable
	 * $maxchar.
	 *
	 * value: an array as follows:
	 * [0]:  zero if not a vowel
	 * [1]:  sound value when this string is at the beginning of the word
	 * [2]:  sound value when this string is followed by a vowel
	 * [3]:  sound value for other cases
	 * [1],[2],[3] can be repeated several times to create branches in the code
	 * an empty sound value means "ignore in this state"
	 *
	 * @var string[][]
	 */
	private static $dmsounds = array(
		'A'       => array('1', '0', '', ''),
		'À'       => array('1', '0', '', ''),
		'Á'       => array('1', '0', '', ''),
		'Â'       => array('1', '0', '', ''),
		'Ã'       => array('1', '0', '', ''),
		'Ä'       => array('1', '0', '1', '', '0', '', ''),
		'Å'       => array('1', '0', '', ''),
		'Ă'       => array('1', '0', '', ''),
		'Ą'       => array('1', '', '', '', '', '', '6'),
		'Ạ'       => array('1', '0', '', ''),
		'Ả'       => array('1', '0', '', ''),
		'Ấ'       => array('1', '0', '', ''),
		'Ầ'       => array('1', '0', '', ''),
		'Ẩ'       => array('1', '0', '', ''),
		'Ẫ'       => array('1', '0', '', ''),
		'Ậ'       => array('1', '0', '', ''),
		'Ắ'       => array('1', '0', '', ''),
		'Ằ'       => array('1', '0', '', ''),
		'Ẳ'       => array('1', '0', '', ''),
		'Ẵ'       => array('1', '0', '', ''),
		'Ặ'       => array('1', '0', '', ''),
		'AE'      => array('1', '0', '1', ''),
		'Æ'       => array('1', '0', '1', ''),
		'AI'      => array('1', '0', '1', ''),
		'AJ'      => array('1', '0', '1', ''),
		'AU'      => array('1', '0', '7', ''),
		'AV'      => array('1', '0', '7', '', '7', '7', '7'),
		'ÄU'      => array('1', '0', '1', ''),
		'AY'      => array('1', '0', '1', ''),
		'B'       => array('0', '7', '7', '7'),
		'C'       => array('0', '5', '5', '5', '34', '4', '4'),
		'Ć'       => array('0', '4', '4', '4'),
		'Č'       => array('0', '4', '4', '4'),
		'Ç'       => array('0', '4', '4', '4'),
		'CH'      => array('0', '5', '5', '5', '34', '4', '4'),
		'CHS'     => array('0', '5', '54', '54'),
		'CK'      => array('0', '5', '5', '5', '45', '45', '45'),
		'CCS'     => array('0', '4', '4', '4'),
		'CS'      => array('0', '4', '4', '4'),
		'CSZ'     => array('0', '4', '4', '4'),
		'CZ'      => array('0', '4', '4', '4'),
		'CZS'     => array('0', '4', '4', '4'),
		'D'       => array('0', '3', '3', '3'),
		'Ď'       => array('0', '3', '3', '3'),
		'Đ'       => array('0', '3', '3', '3'),
		'DRS'     => array('0', '4', '4', '4'),
		'DRZ'     => array('0', '4', '4', '4'),
		'DS'      => array('0', '4', '4', '4'),
		'DSH'     => array('0', '4', '4', '4'),
		'DSZ'     => array('0', '4', '4', '4'),
		'DT'      => array('0', '3', '3', '3'),
		'DDZ'     => array('0', '4', '4', '4'),
		'DDZS'    => array('0', '4', '4', '4'),
		'DZ'      => array('0', '4', '4', '4'),
		'DŹ'      => array('0', '4', '4', '4'),
		'DŻ'      => array('0', '4', '4', '4'),
		'DZH'     => array('0', '4', '4', '4'),
		'DZS'     => array('0', '4', '4', '4'),
		'E'       => array('1', '0', '', ''),
		'È'       => array('1', '0', '', ''),
		'É'       => array('1', '0', '', ''),
		'Ê'       => array('1', '0', '', ''),
		'Ë'       => array('1', '0', '', ''),
		'Ĕ'       => array('1', '0', '', ''),
		'Ė'       => array('1', '0', '', ''),
		'Ę'       => array('1', '', '', '6', '', '', ''),
		'Ẹ'       => array('1', '0', '', ''),
		'Ẻ'       => array('1', '0', '', ''),
		'Ẽ'       => array('1', '0', '', ''),
		'Ế'       => array('1', '0', '', ''),
		'Ề'       => array('1', '0', '', ''),
		'Ể'       => array('1', '0', '', ''),
		'Ễ'       => array('1', '0', '', ''),
		'Ệ'       => array('1', '0', '', ''),
		'EAU'     => array('1', '0', '', ''),
		'EI'      => array('1', '0', '1', ''),
		'EJ'      => array('1', '0', '1', ''),
		'EU'      => array('1', '1', '1', ''),
		'EY'      => array('1', '0', '1', ''),
		'F'       => array('0', '7', '7', '7'),
		'FB'      => array('0', '7', '7', '7'),
		'G'       => array('0', '5', '5', '5', '34', '4', '4'),
		'Ğ'       => array('0', '', '', ''),
		'GGY'     => array('0', '5', '5', '5'),
		'GY'      => array('0', '5', '5', '5'),
		'H'       => array('0', '5', '5', '', '5', '5', '5'),
		'I'       => array('1', '0', '', ''),
		'Ì'       => array('1', '0', '', ''),
		'Í'       => array('1', '0', '', ''),
		'Î'       => array('1', '0', '', ''),
		'Ï'       => array('1', '0', '', ''),
		'Ĩ'       => array('1', '0', '', ''),
		'Į'       => array('1', '0', '', ''),
		'İ'       => array('1', '0', '', ''),
		'Ỉ'       => array('1', '0', '', ''),
		'Ị'       => array('1', '0', '', ''),
		'IA'      => array('1', '1', '', ''),
		'IE'      => array('1', '1', '', ''),
		'IO'      => array('1', '1', '', ''),
		'IU'      => array('1', '1', '', ''),
		'J'       => array('0', '1', '', '', '4', '4', '4', '5', '5', ''),
		'K'       => array('0', '5', '5', '5'),
		'KH'      => array('0', '5', '5', '5'),
		'KS'      => array('0', '5', '54', '54'),
		'L'       => array('0', '8', '8', '8'),
		'Ľ'       => array('0', '8', '8', '8'),
		'Ĺ'       => array('0', '8', '8', '8'),
		'Ł'       => array('0', '7', '7', '7', '8', '8', '8'),
		'LL'      => array('0', '8', '8', '8', '58', '8', '8', '1', '8', '8'),
		'LLY'     => array('0', '8', '8', '8', '1', '8', '8'),
		'LY'      => array('0', '8', '8', '8', '1', '8', '8'),
		'M'       => array('0', '6', '6', '6'),
		'MĔ'      => array('0', '66', '66', '66'),
		'MN'      => array('0', '66', '66', '66'),
		'N'       => array('0', '6', '6', '6'),
		'Ń'       => array('0', '6', '6', '6'),
		'Ň'       => array('0', '6', '6', '6'),
		'Ñ'       => array('0', '6', '6', '6'),
		'NM'      => array('0', '66', '66', '66'),
		'O'       => array('1', '0', '', ''),
		'Ò'       => array('1', '0', '', ''),
		'Ó'       => array('1', '0', '', ''),
		'Ô'       => array('1', '0', '', ''),
		'Õ'       => array('1', '0', '', ''),
		'Ö'       => array('1', '0', '', ''),
		'Ø'       => array('1', '0', '', ''),
		'Ő'       => array('1', '0', '', ''),
		'Œ'       => array('1', '0', '', ''),
		'Ơ'       => array('1', '0', '', ''),
		'Ọ'       => array('1', '0', '', ''),
		'Ỏ'       => array('1', '0', '', ''),
		'Ố'       => array('1', '0', '', ''),
		'Ồ'       => array('1', '0', '', ''),
		'Ổ'       => array('1', '0', '', ''),
		'Ỗ'       => array('1', '0', '', ''),
		'Ộ'       => array('1', '0', '', ''),
		'Ớ'       => array('1', '0', '', ''),
		'Ờ'       => array('1', '0', '', ''),
		'Ở'       => array('1', '0', '', ''),
		'Ỡ'       => array('1', '0', '', ''),
		'Ợ'       => array('1', '0', '', ''),
		'OE'      => array('1', '0', '', ''),
		'OI'      => array('1', '0', '1', ''),
		'OJ'      => array('1', '0', '1', ''),
		'OU'      => array('1', '0', '', ''),
		'OY'      => array('1', '0', '1', ''),
		'P'       => array('0', '7', '7', '7'),
		'PF'      => array('0', '7', '7', '7'),
		'PH'      => array('0', '7', '7', '7'),
		'Q'       => array('0', '5', '5', '5'),
		'R'       => array('0', '9', '9', '9'),
		'Ř'       => array('0', '4', '4', '4'),
		'RS'      => array('0', '4', '4', '4', '94', '94', '94'),
		'RZ'      => array('0', '4', '4', '4', '94', '94', '94'),
		'S'       => array('0', '4', '4', '4'),
		'Ś'       => array('0', '4', '4', '4'),
		'Š'       => array('0', '4', '4', '4'),
		'Ş'       => array('0', '4', '4', '4'),
		'SC'      => array('0', '2', '4', '4'),
		'ŠČ'      => array('0', '2', '4', '4'),
		'SCH'     => array('0', '4', '4', '4'),
		'SCHD'    => array('0', '2', '43', '43'),
		'SCHT'    => array('0', '2', '43', '43'),
		'SCHTCH'  => array('0', '2', '4', '4'),
		'SCHTSCH' => array('0', '2', '4', '4'),
		'SCHTSH'  => array('0', '2', '4', '4'),
		'SD'      => array('0', '2', '43', '43'),
		'SH'      => array('0', '4', '4', '4'),
		'SHCH'    => array('0', '2', '4', '4'),
		'SHD'     => array('0', '2', '43', '43'),
		'SHT'     => array('0', '2', '43', '43'),
		'SHTCH'   => array('0', '2', '4', '4'),
		'SHTSH'   => array('0', '2', '4', '4'),
		'ß'       => array('0', '', '4', '4'),
		'ST'      => array('0', '2', '43', '43'),
		'STCH'    => array('0', '2', '4', '4'),
		'STRS'    => array('0', '2', '4', '4'),
		'STRZ'    => array('0', '2', '4', '4'),
		'STSCH'   => array('0', '2', '4', '4'),
		'STSH'    => array('0', '2', '4', '4'),
		'SSZ'     => array('0', '4', '4', '4'),
		'SZ'      => array('0', '4', '4', '4'),
		'SZCS'    => array('0', '2', '4', '4'),
		'SZCZ'    => array('0', '2', '4', '4'),
		'SZD'     => array('0', '2', '43', '43'),
		'SZT'     => array('0', '2', '43', '43'),
		'T'       => array('0', '3', '3', '3'),
		'Ť'       => array('0', '3', '3', '3'),
		'Ţ'       => array('0', '3', '3', '3', '4', '4', '4'),
		'TC'      => array('0', '4', '4', '4'),
		'TCH'     => array('0', '4', '4', '4'),
		'TH'      => array('0', '3', '3', '3'),
		'TRS'     => array('0', '4', '4', '4'),
		'TRZ'     => array('0', '4', '4', '4'),
		'TS'      => array('0', '4', '4', '4'),
		'TSCH'    => array('0', '4', '4', '4'),
		'TSH'     => array('0', '4', '4', '4'),
		'TSZ'     => array('0', '4', '4', '4'),
		'TTCH'    => array('0', '4', '4', '4'),
		'TTS'     => array('0', '4', '4', '4'),
		'TTSCH'   => array('0', '4', '4', '4'),
		'TTSZ'    => array('0', '4', '4', '4'),
		'TTZ'     => array('0', '4', '4', '4'),
		'TZ'      => array('0', '4', '4', '4'),
		'TZS'     => array('0', '4', '4', '4'),
		'U'       => array('1', '0', '', ''),
		'Ù'       => array('1', '0', '', ''),
		'Ú'       => array('1', '0', '', ''),
		'Û'       => array('1', '0', '', ''),
		'Ü'       => array('1', '0', '', ''),
		'Ũ'       => array('1', '0', '', ''),
		'Ū'       => array('1', '0', '', ''),
		'Ů'       => array('1', '0', '', ''),
		'Ű'       => array('1', '0', '', ''),
		'Ų'       => array('1', '0', '', ''),
		'Ư'       => array('1', '0', '', ''),
		'Ụ'       => array('1', '0', '', ''),
		'Ủ'       => array('1', '0', '', ''),
		'Ứ'       => array('1', '0', '', ''),
		'Ừ'       => array('1', '0', '', ''),
		'Ử'       => array('1', '0', '', ''),
		'Ữ'       => array('1', '0', '', ''),
		'Ự'       => array('1', '0', '', ''),
		'UE'      => array('1', '0', '', ''),
		'UI'      => array('1', '0', '1', ''),
		'UJ'      => array('1', '0', '1', ''),
		'UY'      => array('1', '0', '1', ''),
		'UW'      => array('1', '0', '1', '', '0', '7', '7'),
		'V'       => array('0', '7', '7', '7'),
		'W'       => array('0', '7', '7', '7'),
		'X'       => array('0', '5', '54', '54'),
		'Y'       => array('1', '1', '', ''),
		'Ý'       => array('1', '1', '', ''),
		'Ỳ'       => array('1', '1', '', ''),
		'Ỵ'       => array('1', '1', '', ''),
		'Ỷ'       => array('1', '1', '', ''),
		'Ỹ'       => array('1', '1', '', ''),
		'Z'       => array('0', '4', '4', '4'),
		'Ź'       => array('0', '4', '4', '4'),
		'Ż'       => array('0', '4', '4', '4'),
		'Ž'       => array('0', '4', '4', '4'),
		'ZD'      => array('0', '2', '43', '43'),
		'ZDZ'     => array('0', '2', '4', '4'),
		'ZDZH'    => array('0', '2', '4', '4'),
		'ZH'      => array('0', '4', '4', '4'),
		'ZHD'     => array('0', '2', '43', '43'),
		'ZHDZH'   => array('0', '2', '4', '4'),
		'ZS'      => array('0', '4', '4', '4'),
		'ZSCH'    => array('0', '4', '4', '4'),
		'ZSH'     => array('0', '4', '4', '4'),
		'ZZS'     => array('0', '4', '4', '4'),
		// Cyrillic alphabet
		'А'   => array('1', '0', '', ''),
		'Б'   => array('0', '7', '7', '7'),
		'В'   => array('0', '7', '7', '7'),
		'Г'   => array('0', '5', '5', '5'),
		'Д'   => array('0', '3', '3', '3'),
		'ДЗ'  => array('0', '4', '4', '4'),
		'Е'   => array('1', '0', '', ''),
		'Ё'   => array('1', '0', '', ''),
		'Ж'   => array('0', '4', '4', '4'),
		'З'   => array('0', '4', '4', '4'),
		'И'   => array('1', '0', '', ''),
		'Й'   => array('1', '1', '', '', '4', '4', '4'),
		'К'   => array('0', '5', '5', '5'),
		'Л'   => array('0', '8', '8', '8'),
		'М'   => array('0', '6', '6', '6'),
		'Н'   => array('0', '6', '6', '6'),
		'О'   => array('1', '0', '', ''),
		'П'   => array('0', '7', '7', '7'),
		'Р'   => array('0', '9', '9', '9'),
		'РЖ'  => array('0', '4', '4', '4'),
		'С'   => array('0', '4', '4', '4'),
		'Т'   => array('0', '3', '3', '3'),
		'У'   => array('1', '0', '', ''),
		'Ф'   => array('0', '7', '7', '7'),
		'Х'   => array('0', '5', '5', '5'),
		'Ц'   => array('0', '4', '4', '4'),
		'Ч'   => array('0', '4', '4', '4'),
		'Ш'   => array('0', '4', '4', '4'),
		'Щ'   => array('0', '2', '4', '4'),
		'Ъ'   => array('0', '', '', ''),
		'Ы'   => array('0', '1', '', ''),
		'Ь'   => array('0', '', '', ''),
		'Э'   => array('1', '0', '', ''),
		'Ю'   => array('0', '1', '', ''),
		'Я'   => array('0', '1', '', ''),
		// Greek alphabet
		'Α'   => array('1', '0', '', ''),
		'Ά'   => array('1', '0', '', ''),
		'ΑΙ'  => array('1', '0', '1', ''),
		'ΑΥ'  => array('1', '0', '1', ''),
		'Β'   => array('0', '7', '7', '7'),
		'Γ'   => array('0', '5', '5', '5'),
		'Δ'   => array('0', '3', '3', '3'),
		'Ε'   => array('1', '0', '', ''),
		'Έ'   => array('1', '0', '', ''),
		'ΕΙ'  => array('1', '0', '1', ''),
		'ΕΥ'  => array('1', '1', '1', ''),
		'Ζ'   => array('0', '4', '4', '4'),
		'Η'   => array('1', '0', '', ''),
		'Ή'   => array('1', '0', '', ''),
		'Θ'   => array('0', '3', '3', '3'),
		'Ι'   => array('1', '0', '', ''),
		'Ί'   => array('1', '0', '', ''),
		'Ϊ'   => array('1', '0', '', ''),
		'ΐ'   => array('1', '0', '', ''),
		'Κ'   => array('0', '5', '5', '5'),
		'Λ'   => array('0', '8', '8', '8'),
		'Μ'   => array('0', '6', '6', '6'),
		'ΜΠ'  => array('0', '7', '7', '7'),
		'Ν'   => array('0', '6', '6', '6'),
		'ΝΤ'  => array('0', '3', '3', '3'),
		'Ξ'   => array('0', '5', '54', '54'),
		'Ο'   => array('1', '0', '', ''),
		'Ό'   => array('1', '0', '', ''),
		'ΟΙ'  => array('1', '0', '1', ''),
		'ΟΥ'  => array('1', '0', '1', ''),
		'Π'   => array('0', '7', '7', '7'),
		'Ρ'   => array('0', '9', '9', '9'),
		'Σ'   => array('0', '4', '4', '4'),
		'ς'   => array('0', '', '', '4'),
		'Τ'   => array('0', '3', '3', '3'),
		'ΤΖ'  => array('0', '4', '4', '4'),
		'ΤΣ'  => array('0', '4', '4', '4'),
		'Υ'   => array('1', '1', '', ''),
		'Ύ'   => array('1', '1', '', ''),
		'Ϋ'   => array('1', '1', '', ''),
		'ΰ'   => array('1', '1', '', ''),
		'ΥΚ'  => array('1', '5', '5', '5'),
		'ΥΥ'  => array('1', '65', '65', '65'),
		'Φ'   => array('0', '7', '7', '7'),
		'Χ'   => array('0', '5', '5', '5'),
		'Ψ'   => array('0', '7', '7', '7'),
		'Ω'   => array('1', '0', '', ''),
		'Ώ'   => array('1', '0', '', ''),
		// Hebrew alphabet
		'א'     => array('1', '0', '', ''),
		'או'    => array('1', '0', '7', ''),
		'אג'    => array('1', '4', '4', '4', '5', '5', '5', '34', '34', '34'),
		'בב'    => array('0', '7', '7', '7', '77', '77', '77'),
		'ב'     => array('0', '7', '7', '7'),
		'גג'    => array('0', '4', '4', '4', '5', '5', '5', '45', '45', '45', '55', '55', '55', '54', '54', '54'),
		'גד'    => array('0', '43', '43', '43', '53', '53', '53'),
		'גה'    => array('0', '45', '45', '45', '55', '55', '55'),
		'גז'    => array('0', '44', '44', '44', '45', '45', '45'),
		'גח'    => array('0', '45', '45', '45', '55', '55', '55'),
		'גכ'    => array('0', '45', '45', '45', '55', '55', '55'),
		'גך'    => array('0', '45', '45', '45', '55', '55', '55'),
		'גצ'    => array('0', '44', '44', '44', '45', '45', '45'),
		'גץ'    => array('0', '44', '44', '44', '45', '45', '45'),
		'גק'    => array('0', '45', '45', '45', '54', '54', '54'),
		'גש'    => array('0', '44', '44', '44', '54', '54', '54'),
		'גת'    => array('0', '43', '43', '43', '53', '53', '53'),
		'ג'     => array('0', '4', '4', '4', '5', '5', '5'),
		'דז'    => array('0', '4', '4', '4'),
		'דד'    => array('0', '3', '3', '3', '33', '33', '33'),
		'דט'    => array('0', '33', '33', '33'),
		'דש'    => array('0', '4', '4', '4'),
		'דצ'    => array('0', '4', '4', '4'),
		'דץ'    => array('0', '4', '4', '4'),
		'ד'     => array('0', '3', '3', '3'),
		'הג'    => array('0', '54', '54', '54', '55', '55', '55'),
		'הכ'    => array('0', '55', '55', '55'),
		'הח'    => array('0', '55', '55', '55'),
		'הק'    => array('0', '55', '55', '55', '5', '5', '5'),
		'הה'    => array('0', '5', '5', '', '55', '55', ''),
		'ה'     => array('0', '5', '5', ''),
		'וי'    => array('1', '', '', '', '7', '7', '7'),
		'ו'     => array('1', '7', '7', '7', '7', '', ''),
		'וו'    => array('1', '7', '7', '7', '7', '', ''),
		'וופ'   => array('1', '7', '7', '7', '77', '77', '77'),
		'זש'    => array('0', '4', '4', '4', '44', '44', '44'),
		'זדז'   => array('0', '2', '4', '4'),
		'ז'     => array('0', '4', '4', '4'),
		'זג'    => array('0', '44', '44', '44', '45', '45', '45'),
		'זז'    => array('0', '4', '4', '4', '44', '44', '44'),
		'זס'    => array('0', '44', '44', '44'),
		'זצ'    => array('0', '44', '44', '44'),
		'זץ'    => array('0', '44', '44', '44'),
		'חג'    => array('0', '54', '54', '54', '53', '53', '53'),
		'חח'    => array('0', '5', '5', '5', '55', '55', '55'),
		'חק'    => array('0', '55', '55', '55', '5', '5', '5'),
		'חכ'    => array('0', '45', '45', '45', '55', '55', '55'),
		'חס'    => array('0', '5', '54', '54'),
		'חש'    => array('0', '5', '54', '54'),
		'ח'     => array('0', '5', '5', '5'),
		'טש'    => array('0', '4', '4', '4'),
		'טד'    => array('0', '33', '33', '33'),
		'טי'    => array('0', '3', '3', '3', '4', '4', '4', '3', '3', '34'),
		'טת'    => array('0', '33', '33', '33'),
		'טט'    => array('0', '3', '3', '3', '33', '33', '33'),
		'ט'     => array('0', '3', '3', '3'),
		'י'     => array('1', '1', '', ''),
		'יא'    => array('1', '1', '', '', '1', '1', '1'),
		'כג'    => array('0', '55', '55', '55', '54', '54', '54'),
		'כש'    => array('0', '5', '54', '54'),
		'כס'    => array('0', '5', '54', '54'),
		'ככ'    => array('0', '5', '5', '5', '55', '55', '55'),
		'כך'    => array('0', '5', '5', '5', '55', '55', '55'),
		'כ'     => array('0', '5', '5', '5'),
		'כח'    => array('0', '55', '55', '55', '5', '5', '5'),
		'ך'     => array('0', '', '5', '5'),
		'ל'     => array('0', '8', '8', '8'),
		'לל'    => array('0', '88', '88', '88', '8', '8', '8'),
		'מנ'    => array('0', '66', '66', '66'),
		'מן'    => array('0', '66', '66', '66'),
		'ממ'    => array('0', '6', '6', '6', '66', '66', '66'),
		'מם'    => array('0', '6', '6', '6', '66', '66', '66'),
		'מ'     => array('0', '6', '6', '6'),
		'ם'     => array('0', '', '6', '6'),
		'נמ'    => array('0', '66', '66', '66'),
		'נם'    => array('0', '66', '66', '66'),
		'ננ'    => array('0', '6', '6', '6', '66', '66', '66'),
		'נן'    => array('0', '6', '6', '6', '66', '66', '66'),
		'נ'     => array('0', '6', '6', '6'),
		'ן'     => array('0', '', '6', '6'),
		'סתש'   => array('0', '2', '4', '4'),
		'סתז'   => array('0', '2', '4', '4'),
		'סטז'   => array('0', '2', '4', '4'),
		'סטש'   => array('0', '2', '4', '4'),
		'סצד'   => array('0', '2', '4', '4'),
		'סט'    => array('0', '2', '4', '4', '43', '43', '43'),
		'סת'    => array('0', '2', '4', '4', '43', '43', '43'),
		'סג'    => array('0', '44', '44', '44', '4', '4', '4'),
		'סס'    => array('0', '4', '4', '4', '44', '44', '44'),
		'סצ'    => array('0', '44', '44', '44'),
		'סץ'    => array('0', '44', '44', '44'),
		'סז'    => array('0', '44', '44', '44'),
		'סש'    => array('0', '44', '44', '44'),
		'ס'     => array('0', '4', '4', '4'),
		'ע'     => array('1', '0', '', ''),
		'פב'    => array('0', '7', '7', '7', '77', '77', '77'),
		'פוו'   => array('0', '7', '7', '7', '77', '77', '77'),
		'פפ'    => array('0', '7', '7', '7', '77', '77', '77'),
		'פף'    => array('0', '7', '7', '7', '77', '77', '77'),
		'פ'     => array('0', '7', '7', '7'),
		'ף'     => array('0', '', '7', '7'),
		'צג'    => array('0', '44', '44', '44', '45', '45', '45'),
		'צז'    => array('0', '44', '44', '44'),
		'צס'    => array('0', '44', '44', '44'),
		'צצ'    => array('0', '4', '4', '4', '5', '5', '5', '44', '44', '44', '54', '54', '54', '45', '45', '45'),
		'צץ'    => array('0', '4', '4', '4', '5', '5', '5', '44', '44', '44', '54', '54', '54'),
		'צש'    => array('0', '44', '44', '44', '4', '4', '4', '5', '5', '5'),
		'צ'     => array('0', '4', '4', '4', '5', '5', '5'),
		'ץ'     => array('0', '', '4', '4'),
		'קה'    => array('0', '55', '55', '5'),
		'קס'    => array('0', '5', '54', '54'),
		'קש'    => array('0', '5', '54', '54'),
		'קק'    => array('0', '5', '5', '5', '55', '55', '55'),
		'קח'    => array('0', '55', '55', '55'),
		'קכ'    => array('0', '55', '55', '55'),
		'קך'    => array('0', '55', '55', '55'),
		'קג'    => array('0', '55', '55', '55', '54', '54', '54'),
		'ק'     => array('0', '5', '5', '5'),
		'רר'    => array('0', '99', '99', '99', '9', '9', '9'),
		'ר'     => array('0', '9', '9', '9'),
		'שטז'   => array('0', '2', '4', '4'),
		'שתש'   => array('0', '2', '4', '4'),
		'שתז'   => array('0', '2', '4', '4'),
		'שטש'   => array('0', '2', '4', '4'),
		'שד'    => array('0', '2', '43', '43'),
		'שז'    => array('0', '44', '44', '44'),
		'שס'    => array('0', '44', '44', '44'),
		'שת'    => array('0', '2', '43', '43'),
		'שג'    => array('0', '4', '4', '4', '44', '44', '44', '4', '43', '43'),
		'שט'    => array('0', '2', '43', '43', '44', '44', '44'),
		'שצ'    => array('0', '44', '44', '44', '45', '45', '45'),
		'שץ'    => array('0', '44', '', '44', '45', '', '45'),
		'שש'    => array('0', '4', '4', '4', '44', '44', '44'),
		'ש'     => array('0', '4', '4', '4'),
		'תג'    => array('0', '34', '34', '34'),
		'תז'    => array('0', '34', '34', '34'),
		'תש'    => array('0', '4', '4', '4'),
		'תת'    => array('0', '3', '3', '3', '4', '4', '4', '33', '33', '33', '44', '44', '44', '34', '34', '34', '43', '43', '43'),
		'ת'     => array('0', '3', '3', '3', '4', '4', '4'),
		// Arabic alphabet
		'ا'   => array('1', '0', '', ''),
		'ب'   => array('0', '7', '7', '7'),
		'ت'   => array('0', '3', '3', '3'),
		'ث'   => array('0', '3', '3', '3'),
		'ج'   => array('0', '4', '4', '4'),
		'ح'   => array('0', '5', '5', '5'),
		'خ'   => array('0', '5', '5', '5'),
		'د'   => array('0', '3', '3', '3'),
		'ذ'   => array('0', '3', '3', '3'),
		'ر'   => array('0', '9', '9', '9'),
		'ز'   => array('0', '4', '4', '4'),
		'س'   => array('0', '4', '4', '4'),
		'ش'   => array('0', '4', '4', '4'),
		'ص'   => array('0', '4', '4', '4'),
		'ض'   => array('0', '3', '3', '3'),
		'ط'   => array('0', '3', '3', '3'),
		'ظ'   => array('0', '4', '4', '4'),
		'ع'   => array('1', '0', '', ''),
		'غ'   => array('0', '0', '', ''),
		'ف'   => array('0', '7', '7', '7'),
		'ق'   => array('0', '5', '5', '5'),
		'ك'   => array('0', '5', '5', '5'),
		'ل'   => array('0', '8', '8', '8'),
		'لا'  => array('0', '8', '8', '8'),
		'م'   => array('0', '6', '6', '6'),
		'ن'   => array('0', '6', '6', '6'),
		'هن'  => array('0', '66', '66', '66'),
		'ه'   => array('0', '5', '5', ''),
		'و'   => array('1', '', '', '', '7', '', ''),
		'ي'   => array('0', '1', '', ''),
		'آ'   => array('0', '1', '', ''),
		'ة'   => array('0', '', '', '3'),
		'ی'   => array('0', '1', '', ''),
		'ى'   => array('1', '1', '', ''),
	);

	/**
	 * Calculate the Daitch-Mokotoff soundex for a word.
	 *
	 * @param string $name
	 *
	 * @return string[] List of possible DM codes for the word.
	 */
	private static function daitchMokotoffWord($name) {
		// Apply special transformation rules to the input string
		$name = I18N::strtoupper($name);
		foreach (self::$transformNameTable as $transformRule) {
			$name = str_replace($transformRule[0], $transformRule[1], $name);
		}

		// Initialize
		$name_script = I18N::textScript($name);
		$noVowels    = ($name_script == 'Hebr' || $name_script == 'Arab');

		$lastPos         = strlen($name) - 1;
		$currPos         = 0;
		$state           = 1; // 1: start of input string, 2: before vowel, 3: other
		$result          = array(); // accumulate complete 6-digit D-M codes here
		$partialResult   = array(); // accumulate incomplete D-M codes here
		$partialResult[] = array('!'); // initialize 1st partial result  ('!' stops "duplicate sound" check)

		// Loop through the input string.
		// Stop when the string is exhausted or when no more partial results remain
		while (count($partialResult) !== 0 && $currPos <= $lastPos) {
			// Find the DM coding table entry for the chunk at the current position
			$thisEntry = substr($name, $currPos, self::MAXCHAR); // Get maximum length chunk
			while ($thisEntry != '') {
				if (isset(self::$dmsounds[$thisEntry])) {
					break;
				}
				$thisEntry = substr($thisEntry, 0, -1); // Not in table: try a shorter chunk
			}
			if ($thisEntry === '') {
				$currPos++; // Not in table: advance pointer to next byte
				continue; // and try again
			}

			$soundTableEntry = self::$dmsounds[$thisEntry];
			$workingResult   = $partialResult;
			$partialResult   = array();
			$currPos += strlen($thisEntry);

			// Not at beginning of input string
			if ($state != 1) {
				if ($currPos <= $lastPos) {
					// Determine whether the next chunk is a vowel
					$nextEntry = substr($name, $currPos, self::MAXCHAR); // Get maximum length chunk
					while ($nextEntry != '') {
						if (isset(self::$dmsounds[$nextEntry])) {
							break;
						}
						$nextEntry = substr($nextEntry, 0, -1); // Not in table: try a shorter chunk
					}
				} else {
					$nextEntry = '';
				}
				if ($nextEntry != '' && self::$dmsounds[$nextEntry][0] != '0') {
					$state = 2;
				} else {
					// Next chunk is a vowel
					$state = 3;
				}
			}

			while ($state < count($soundTableEntry)) {
				// empty means 'ignore this sound in this state'
				if ($soundTableEntry[$state] == '') {
					foreach ($workingResult as $workingEntry) {
						$tempEntry = $workingEntry;
						$tempEntry[count($tempEntry) - 1] .= '!'; // Prevent false 'doubles'
						$partialResult[] = $tempEntry;
					}
				} else {
					foreach ($workingResult as $workingEntry) {
						if ($soundTableEntry[$state] !== $workingEntry[count($workingEntry) - 1]) {
							// Incoming sound isn't a duplicate of the previous sound
							$workingEntry[] = $soundTableEntry[$state];
						} else {
							// Incoming sound is a duplicate of the previous sound
							// For Hebrew and Arabic, we need to create a pair of D-M sound codes,
							// one of the pair with only a single occurrence of the duplicate sound,
							// the other with both occurrences
							if ($noVowels) {
								$workingEntry[] = $soundTableEntry[$state];
							}
						}
						if (count($workingEntry) < 7) {
							$partialResult[] = $workingEntry;
						} else {
							// This is the 6th code in the sequence
							// We're looking for 7 entries because the first is '!' and doesn't count
							$tempResult = str_replace('!', '', implode('', $workingEntry));
							// Only return codes from recognisable sounds
							if ($tempResult) {
								$result[] = substr($tempResult . '000000', 0, 6);
							}
						}
					}
				}
				$state = $state + 3; // Advance to next triplet while keeping the same basic state
			}
		}

		// Zero-fill and copy all remaining partial results
		foreach ($partialResult as $workingEntry) {
			$tempResult = str_replace('!', '', implode('', $workingEntry));
			// Only return codes from recognisable sounds
			if ($tempResult) {
				$result[] = substr($tempResult . '000000', 0, 6);
			}
		}

		return $result;
	}
}
