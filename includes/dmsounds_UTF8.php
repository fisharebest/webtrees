<?php
/**
 * Sound table for Daitch-Mokotoff "Sounds like" algorithm
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_DMSOUNDS_UTF8_PHP', '');

// Hebrew alphabet
define('ALEF', 'א');
define('BET', 'ב');
define('GIMEL', 'ג');
define('DALET', 'ד');
define('HE', 'ה');
define('VAV', 'ו');
define('ZAYIN', 'ז');
define('HET', 'ח');
define('TET', 'ט');
define('YOD', 'י');
define('FINAL_KAF', 'ך');
define('KAF', 'כ');
define('LAMED', 'ל');
define('FINAL_MEM', 'ם');
define('MEM', 'מ');
define('FINAL_NUN', 'ן');
define('NUN', 'נ');
define('SAMEKH', 'ס');
define('AYIN', 'ע');
define('FINAL_PE', 'ף');
define('PE', 'פ');
define('FINAL_TSADI', 'ץ');
define('TSADI', 'צ');
define('QOF', 'ק');
define('RESH', 'ר');
define('SHIN', 'ש');
define('TAV', 'ת');
define('DOUBLE_VAV', 'װ');
define('DOUBLE_YOD', 'ײ');
define('VAV_YOD', 'ױ');

/**
 * Name transformation arrays.
 *
 * Used to transform the Name string to simplify the "sounds like" table.
 * This is especially useful in Hebrew.
 *
 * Each array entry defines the "from" and "to" arguments of an preg($from, $to, $text)
 * function call to achieve the desired transformations.
 *
 * Note about the use of "\x01":
 *		This code, which can't legitimately occur in the kind of text we're dealing with,
 *		is used as a place-holder so that conditional string replacements can be done.
 */
$transformNameTable = array(
	// Force Yiddish ligatures to be treated as separate letters
	array(DOUBLE_VAV,		VAV.VAV),
	array(DOUBLE_YOD,		YOD.YOD),
	array(VAV_YOD,			VAV.YOD),
	// Feature request 1511090, bullet (a)
	array(BET.VAV,			BET.AYIN),
	array(PE.VAV,			PE.AYIN),
	array(VAV.MEM,			AYIN.MEM),
	array(VAV.FINAL_MEM,	AYIN.FINAL_MEM),
	array(VAV.NUN,			AYIN.NUN),
	array(VAV.FINAL_NUN,	AYIN.FINAL_NUN),
	// Feature request 1511090, bullet (b)
	array(VAV.VAV,			BET),
	// Feature request 1511090, bullet (c)
	array("\x01",			''),
	array(YOD.YOD.HE.'$',	"\x01".HE),
	array(YOD.YOD.AYIN.'$',	"\x01".AYIN),
	array(YOD.YOD,			AYIN),
	array("\x01",			YOD.YOD)
	);

$maxchar = 7;		// Max. table key length (in ASCII bytes -- NOT in UTF-8 characters!)

/**
 * The DM sound coding table is organized this way:
 *		key:	a variable-length string that corresponds to the UTF-8 character sequence
 *				represented by the table entry.  Currently, that string can be up to 7
 *				bytes long.  This maximum length is defined by the value of global variable
 *				$maxchar.
 *		value:	an array as follows:
 *				[0]:  zero if not a vowel
 *				[1]:  sound value when this string is at the beginning of the word
 *				[2]:  sound value when this string is followed by a vowel
 *				[3]:  sound value for other cases
 *				[1],[2],[3] can be repeated several times to create branches in the code
 *				an empty sound value means "ignore in this state"
 */
$dmsounds = array();

// Latin alphabet
$dmsounds["A"] = array('1',   '0','','');
$dmsounds["À"] = array('1',   '0','','');
$dmsounds["Á"] = array('1',   '0','','');
$dmsounds["Â"] = array('1',   '0','','');
$dmsounds["Ã"] = array('1',   '0','','');
$dmsounds["Ä"] = array('1',   '0','1','',   '0','','');
$dmsounds["Å"] = array('1',   '0','','');
$dmsounds["Ă"] = array('1',   '0','','');
$dmsounds["Ą"] = array('1',   '','','',   '','','6');
$dmsounds["Ạ"] = array('1',   '0','','');
$dmsounds["Ả"] = array('1',   '0','','');
$dmsounds["Ấ"] = array('1',   '0','','');
$dmsounds["Ầ"] = array('1',   '0','','');
$dmsounds["Ẩ"] = array('1',   '0','','');
$dmsounds["Ẫ"] = array('1',   '0','','');
$dmsounds["Ậ"] = array('1',   '0','','');
$dmsounds["Ắ"] = array('1',   '0','','');
$dmsounds["Ằ"] = array('1',   '0','','');
$dmsounds["Ẳ"] = array('1',   '0','','');
$dmsounds["Ẵ"] = array('1',   '0','','');
$dmsounds["Ặ"] = array('1',   '0','','');
$dmsounds["AE"] = array('1',   '0','1','');
$dmsounds["Æ"] = array('1',   '0','1','');
$dmsounds["AI"] = array('1',   '0','1','');
$dmsounds["AJ"] = array('1',   '0','1','');
$dmsounds["AU"] = array('1',   '0','7','');
$dmsounds["AV"] = array('1',   '0','7','',   '7','7','7');
$dmsounds["ÄU"] = array('1',   '0','1','');
$dmsounds["AY"] = array('1',   '0','1','');
$dmsounds["B"] = array('0',   '7','7','7');
//$dmsounds["C"] = array('0',   '5','5','5',   '4','4','4');
$dmsounds["C"] = array('0',   '5','5','5',   '34','4','4');
$dmsounds["Ć"] = array('0',   '4','4','4');
$dmsounds["Č"] = array('0',   '4','4','4');
$dmsounds["Ç"] = array('0',   '4','4','4');
//$dmsounds["CH"] = array('0',   '5','5','5',   '4','4','4');
$dmsounds["CH"] = array('0',   '5','5','5',   '34','4','4');
$dmsounds["CHS"] = array('0',   '5','54','54');
$dmsounds["CK"] = array('0',   '5','5','5',   '45','45','45');
$dmsounds["CCS"] = array('0',   '4','4','4');
$dmsounds["CS"] = array('0',   '4','4','4');
$dmsounds["CSZ"] = array('0',   '4','4','4');
$dmsounds["CZ"] = array('0',   '4','4','4');
$dmsounds["CZS"] = array('0',   '4','4','4');
$dmsounds["D"] = array('0',   '3','3','3');
$dmsounds["Ď"] = array('0',   '3','3','3');
$dmsounds["Đ"] = array('0',   '3','3','3');
$dmsounds["DRS"] = array('0',   '4','4','4');
$dmsounds["DRZ"] = array('0',   '4','4','4');
$dmsounds["DS"] = array('0',   '4','4','4');
$dmsounds["DSH"] = array('0',   '4','4','4');
$dmsounds["DSZ"] = array('0',   '4','4','4');
$dmsounds["DT"] = array('0',   '3','3','3');
$dmsounds["DDZ"] = array('0',   '4','4','4');
$dmsounds["DDZS"] = array('0',   '4','4','4');
$dmsounds["DZ"] = array('0',   '4','4','4');
$dmsounds["DŹ"] = array('0',   '4','4','4');
$dmsounds["DŻ"] = array('0',   '4','4','4');
$dmsounds["DZH"] = array('0',   '4','4','4');
$dmsounds["DZS"] = array('0',   '4','4','4');
$dmsounds["E"] = array('1',   '0','','');
$dmsounds["È"] = array('1',   '0','','');
$dmsounds["É"] = array('1',   '0','','');
$dmsounds["Ê"] = array('1',   '0','','');
$dmsounds["Ë"] = array('1',   '0','','');
$dmsounds["Ĕ"] = array('1',   '0','','');
$dmsounds["Ė"] = array('1',   '0','','');
$dmsounds["Ę"] = array('1',   '','','6',   '','','');
$dmsounds["Ẹ"] = array('1',   '0','','');
$dmsounds["Ẻ"] = array('1',   '0','','');
$dmsounds["Ẽ"] = array('1',   '0','','');
$dmsounds["Ế"] = array('1',   '0','','');
$dmsounds["Ề"] = array('1',   '0','','');
$dmsounds["Ể"] = array('1',   '0','','');
$dmsounds["Ễ"] = array('1',   '0','','');
$dmsounds["Ệ"] = array('1',   '0','','');
$dmsounds["EAU"] = array('1',   '0','','');
$dmsounds["EI"] = array('1',   '0','1','');
$dmsounds["EJ"] = array('1',   '0','1','');
$dmsounds["EU"] = array('1',   '1','1','');
$dmsounds["EY"] = array('1',   '0','1','');
$dmsounds["F"] = array('0',   '7','7','7');
$dmsounds["FB"] = array('0',   '7','7','7');
//$dmsounds["G"] = array('0',   '5','5','5',   '4','4','4');
$dmsounds["G"] = array('0',   '5','5','5',   '34','4','4');
$dmsounds["Ğ"] = array('0',   '','','');
$dmsounds["GGY"] = array('0',   '5','5','5');
$dmsounds["GY"] = array('0',   '5','5','5');
$dmsounds["H"] = array('0',   '5','5','',   '5','5','5');
$dmsounds["I"] = array('1',   '0','','');
$dmsounds["Ì"] = array('1',   '0','','');
$dmsounds["Í"] = array('1',   '0','','');
$dmsounds["Î"] = array('1',   '0','','');
$dmsounds["Ï"] = array('1',   '0','','');
$dmsounds["Ĩ"] = array('1',   '0','','');
$dmsounds["Į"] = array('1',   '0','','');
$dmsounds["İ"] = array('1',   '0','','');
$dmsounds["Ỉ"] = array('1',   '0','','');
$dmsounds["Ị"] = array('1',   '0','','');
$dmsounds["IA"] = array('1',   '1','','');
$dmsounds["IE"] = array('1',   '1','','');
$dmsounds["IO"] = array('1',   '1','','');
$dmsounds["IU"] = array('1',   '1','','');
$dmsounds["J"] = array('0',   '1','','',   '4','4','4',   '5','5','');
$dmsounds["K"] = array('0',   '5','5','5');
$dmsounds["KH"] = array('0',   '5','5','5');
$dmsounds["KS"] = array('0',   '5','54','54');
$dmsounds["L"] = array('0',   '8','8','8');
$dmsounds["Ľ"] = array('0',   '8','8','8');
$dmsounds["Ĺ"] = array('0',   '8','8','8');
$dmsounds["Ł"] = array('0',   '7','7','7',   '8','8','8');
//$dmsounds["LL"] = array('0',   '8','8','8',   '58','8','8',   '1','','');
$dmsounds["LL"] = array('0',   '8','8','8',   '58','8','8',   '1','8','8');
//$dmsounds["LLY"] = array('0',   '8','8','8',   '1','','');
$dmsounds["LLY"] = array('0',   '8','8','8',   '1','8','8');
//$dmsounds["LY"] = array('0',   '8','8','8',   '1','','');
$dmsounds["LY"] = array('0',   '8','8','8',   '1','8','8');
$dmsounds["M"] = array('0',   '6','6','6');
$dmsounds["MĔ"] = array('0',   '66','66','66');
$dmsounds["MN"] = array('0',   '66','66','66');
$dmsounds["N"] = array('0',   '6','6','6');
$dmsounds["Ń"] = array('0',   '6','6','6');
$dmsounds["Ň"] = array('0',   '6','6','6');
$dmsounds["Ñ"] = array('0',   '6','6','6');
$dmsounds["NM"] = array('0',   '66','66','66');
$dmsounds["O"] = array('1',   '0','','');
$dmsounds["Ò"] = array('1',   '0','','');
$dmsounds["Ó"] = array('1',   '0','','');
$dmsounds["Ô"] = array('1',   '0','','');
$dmsounds["Õ"] = array('1',   '0','','');
$dmsounds["Ö"] = array('1',   '0','','');
$dmsounds["Ø"] = array('1',   '0','','');
$dmsounds["Ő"] = array('1',   '0','','');
$dmsounds["Œ"] = array('1',   '0','','');
$dmsounds["Ơ"] = array('1',   '0','','');
$dmsounds["Ọ"] = array('1',   '0','','');
$dmsounds["Ỏ"] = array('1',   '0','','');
$dmsounds["Ố"] = array('1',   '0','','');
$dmsounds["Ồ"] = array('1',   '0','','');
$dmsounds["Ổ"] = array('1',   '0','','');
$dmsounds["Ỗ"] = array('1',   '0','','');
$dmsounds["Ộ"] = array('1',   '0','','');
$dmsounds["Ớ"] = array('1',   '0','','');
$dmsounds["Ờ"] = array('1',   '0','','');
$dmsounds["Ở"] = array('1',   '0','','');
$dmsounds["Ỡ"] = array('1',   '0','','');
$dmsounds["Ợ"] = array('1',   '0','','');
$dmsounds["OE"] = array('1',   '0','','');
$dmsounds["OI"] = array('1',   '0','1','');
$dmsounds["OJ"] = array('1',   '0','1','');
$dmsounds["OU"] = array('1',   '0','','');
$dmsounds["OY"] = array('1',   '0','1','');
$dmsounds["P"] = array('0',   '7','7','7');
$dmsounds["PF"] = array('0',   '7','7','7');
$dmsounds["PH"] = array('0',   '7','7','7');
$dmsounds["Q"] = array('0',   '5','5','5');
$dmsounds["R"] = array('0',   '9','9','9');
$dmsounds["Ř"] = array('0',   '4','4','4');
$dmsounds["RS"] = array('0',   '4','4','4',   '94','94','94');
$dmsounds["RZ"] = array('0',   '4','4','4',   '94','94','94');
$dmsounds["S"] = array('0',   '4','4','4');
$dmsounds["Ś"] = array('0',   '4','4','4');
$dmsounds["Š"] = array('0',   '4','4','4');
$dmsounds["Ş"] = array('0',   '4','4','4');
$dmsounds["SC"] = array('0',   '2','4','4');
$dmsounds["ŠČ"] = array('0',   '2','4','4');
$dmsounds["SCH"] = array('0',   '4','4','4');
$dmsounds["SCHD"] = array('0',   '2','43','43');
$dmsounds["SCHT"] = array('0',   '2','43','43');
$dmsounds["SCHTCH"] = array('0',   '2','4','4');
$dmsounds["SCHTSCH"] = array('0',   '2','4','4');
$dmsounds["SCHTSH"] = array('0',   '2','4','4');
$dmsounds["SD"] = array('0',   '2','43','43');
$dmsounds["SH"] = array('0',   '4','4','4');
$dmsounds["SHCH"] = array('0',   '2','4','4');
$dmsounds["SHD"] = array('0',   '2','43','43');
$dmsounds["SHT"] = array('0',   '2','43','43');
$dmsounds["SHTCH"] = array('0',   '2','4','4');
$dmsounds["SHTSH"] = array('0',   '2','4','4');
$dmsounds["ß"] = array('0',   '','4','4');
$dmsounds["ST"] = array('0',   '2','43','43');
$dmsounds["STCH"] = array('0',   '2','4','4');
$dmsounds["STRS"] = array('0',   '2','4','4');
$dmsounds["STRZ"] = array('0',   '2','4','4');
$dmsounds["STSCH"] = array('0',   '2','4','4');
$dmsounds["STSH"] = array('0',   '2','4','4');
$dmsounds["SSZ"] = array('0',   '4','4','4');
$dmsounds["SZ"] = array('0',   '4','4','4');
$dmsounds["SZCS"] = array('0',   '2','4','4');
$dmsounds["SZCZ"] = array('0',   '2','4','4');
$dmsounds["SZD"] = array('0',   '2','43','43');
$dmsounds["SZT"] = array('0',   '2','43','43');
$dmsounds["T"] = array('0',   '3','3','3');
$dmsounds["Ť"] = array('0',   '3','3','3');
$dmsounds["Ţ"] = array('0',   '3','3','3',   '4','4','4');
$dmsounds["TC"] = array('0',   '4','4','4');
$dmsounds["TCH"] = array('0',   '4','4','4');
$dmsounds["TH"] = array('0',   '3','3','3');
$dmsounds["TRS"] = array('0',   '4','4','4');
$dmsounds["TRZ"] = array('0',   '4','4','4');
$dmsounds["TS"] = array('0',   '4','4','4');
$dmsounds["TSCH"] = array('0',   '4','4','4');
$dmsounds["TSH"] = array('0',   '4','4','4');
$dmsounds["TSZ"] = array('0',   '4','4','4');
$dmsounds["TTCH"] = array('0',   '4','4','4');
$dmsounds["TTS"] = array('0',   '4','4','4');
$dmsounds["TTSCH"] = array('0',   '4','4','4');
$dmsounds["TTSZ"] = array('0',   '4','4','4');
$dmsounds["TTZ"] = array('0',   '4','4','4');
$dmsounds["TZ"] = array('0',   '4','4','4');
$dmsounds["TZS"] = array('0',   '4','4','4');
$dmsounds["U"] = array('1',   '0','','');
$dmsounds["Ù"] = array('1',   '0','','');
$dmsounds["Ú"] = array('1',   '0','','');
$dmsounds["Û"] = array('1',   '0','','');
$dmsounds["Ü"] = array('1',   '0','','');
$dmsounds["Ũ"] = array('1',   '0','','');
$dmsounds["Ū"] = array('1',   '0','','');
$dmsounds["Ů"] = array('1',   '0','','');
$dmsounds["Ű"] = array('1',   '0','','');
$dmsounds["Ų"] = array('1',   '0','','');
$dmsounds["Ư"] = array('1',   '0','','');
$dmsounds["Ụ"] = array('1',   '0','','');
$dmsounds["Ủ"] = array('1',   '0','','');
$dmsounds["Ứ"] = array('1',   '0','','');
$dmsounds["Ừ"] = array('1',   '0','','');
$dmsounds["Ử"] = array('1',   '0','','');
$dmsounds["Ữ"] = array('1',   '0','','');
$dmsounds["Ự"] = array('1',   '0','','');
$dmsounds["UE"] = array('1',   '0','','');
$dmsounds["UI"] = array('1',   '0','1','');
$dmsounds["UJ"] = array('1',   '0','1','');
$dmsounds["UY"] = array('1',   '0','1','');
$dmsounds["UW"] = array('1',   '0','1','',   '0','7','7');
$dmsounds["V"] = array('0',   '7','7','7');
//$dmsounds["W"] = array('0',   '7','7','7',   '7','','');
$dmsounds["W"] = array('0',   '7','7','7');
$dmsounds["X"] = array('0',   '5','54','54');
$dmsounds["Y"] = array('1',   '1','','');
$dmsounds["Ý"] = array('1',   '1','','');
$dmsounds["Ỳ"] = array('1',   '1','','');
$dmsounds["Ỵ"] = array('1',   '1','','');
$dmsounds["Ỷ"] = array('1',   '1','','');
$dmsounds["Ỹ"] = array('1',   '1','','');
$dmsounds["Z"] = array('0',   '4','4','4');
$dmsounds["Ź"] = array('0',   '4','4','4');
$dmsounds["Ż"] = array('0',   '4','4','4');
$dmsounds["Ž"] = array('0',   '4','4','4');
$dmsounds["ZD"] = array('0',   '2','43','43');
$dmsounds["ZDZ"] = array('0',   '2','4','4');
$dmsounds["ZDZH"] = array('0',   '2','4','4');
$dmsounds["ZH"] = array('0',   '4','4','4');
$dmsounds["ZHD"] = array('0',   '2','43','43');
$dmsounds["ZHDZH"] = array('0',   '2','4','4');
$dmsounds["ZS"] = array('0',   '4','4','4');
$dmsounds["ZSCH"] = array('0',   '4','4','4');
$dmsounds["ZSH"] = array('0',   '4','4','4');
$dmsounds["ZZS"] = array('0',   '4','4','4');

// Cyrillic alphabet
$dmsounds["А"] = array('1',   '0','','');
$dmsounds["Б"] = array('0',   '7','7','7');
$dmsounds["В"] = array('0',   '7','7','7');
$dmsounds["Г"] = array('0',   '5','5','5');
$dmsounds["Д"] = array('0',   '3','3','3');
$dmsounds["ДЗ"] = array('0',   '4','4','4');
$dmsounds["Е"] = array('1',   '0','','');
$dmsounds["Ё"] = array('1',   '0','','');
$dmsounds["Ж"] = array('0',   '4','4','4');
$dmsounds["З"] = array('0',   '4','4','4');
$dmsounds["И"] = array('1',   '0','','');
$dmsounds["Й"] = array('1',   '1','','',   '4','4','4');
$dmsounds["К"] = array('0',   '5','5','5');
$dmsounds["Л"] = array('0',   '8','8','8');
$dmsounds["М"] = array('0',   '6','6','6');
$dmsounds["Н"] = array('0',   '6','6','6');
$dmsounds["О"] = array('1',   '0','','');
$dmsounds["П"] = array('0',   '7','7','7');
$dmsounds["Р"] = array('0',   '9','9','9');
$dmsounds["РЖ"] = array('0',   '4','4','4');
$dmsounds["С"] = array('0',   '4','4','4');
$dmsounds["Т"] = array('0',   '3','3','3');
$dmsounds["У"] = array('1',   '0','','');
$dmsounds["Ф"] = array('0',   '7','7','7');
$dmsounds["Х"] = array('0',   '5','5','5');
$dmsounds["Ц"] = array('0',   '4','4','4');
$dmsounds["Ч"] = array('0',   '4','4','4');
$dmsounds["Ш"] = array('0',   '4','4','4');
$dmsounds["Щ"] = array('0',   '2','4','4');
$dmsounds["Ъ"] = array('0',   '','','');
$dmsounds["Ы"] = array('0',   '1','','');
$dmsounds["Ь"] = array('0',   '','','');
$dmsounds["Э"] = array('1',   '0','','');
$dmsounds["Ю"] = array('0',   '1','','');
$dmsounds["Я"] = array('0',   '1','','');

// Greek alphabet
$dmsounds["Α"] = array('1',   '0','','');
$dmsounds["Ά"] = array('1',   '0','','');
$dmsounds["ΑΙ"] = array('1',   '0','1','');
$dmsounds["ΑΥ"] = array('1',   '0','1','');
$dmsounds["Β"] = array('0',   '7','7','7');
$dmsounds["Γ"] = array('0',   '5','5','5');
$dmsounds["Δ"] = array('0',   '3','3','3');
$dmsounds["Ε"] = array('1',   '0','','');
$dmsounds["Έ"] = array('1',   '0','','');
$dmsounds["ΕΙ"] = array('1',   '0','1','');
$dmsounds["ΕΥ"] = array('1',   '1','1','');
$dmsounds["Ζ"] = array('0',   '4','4','4');
$dmsounds["Η"] = array('1',   '0','','');
$dmsounds["Ή"] = array('1',   '0','','');
$dmsounds["Θ"] = array('0',   '3','3','3');
$dmsounds["Ι"] = array('1',   '0','','');
$dmsounds["Ί"] = array('1',   '0','','');
$dmsounds["Ϊ"] = array('1',   '0','','');
$dmsounds["ΐ"] = array('1',   '0','','');
$dmsounds["Κ"] = array('0',   '5','5','5');
$dmsounds["Λ"] = array('0',   '8','8','8');
$dmsounds["Μ"] = array('0',   '6','6','6');
$dmsounds["ΜΠ"] = array('0',   '7','7','7');
$dmsounds["Ν"] = array('0',   '6','6','6');
$dmsounds["ΝΤ"] = array('0',   '3','3','3');
$dmsounds["Ξ"] = array('0',   '5','54','54');
$dmsounds["Ο"] = array('1',   '0','','');
$dmsounds["Ό"] = array('1',   '0','','');
$dmsounds["ΟΙ"] = array('1',   '0','1','');
$dmsounds["ΟΥ"] = array('1',   '0','1','');
$dmsounds["Π"] = array('0',   '7','7','7');
$dmsounds["Ρ"] = array('0',   '9','9','9');
$dmsounds["Σ"] = array('0',   '4','4','4');
$dmsounds["ς"] = array('0',   '','','4');
$dmsounds["Τ"] = array('0',   '3','3','3');
$dmsounds["ΤΖ"] = array('0',   '4','4','4');
$dmsounds["ΤΣ"] = array('0',   '4','4','4');
$dmsounds["Υ"] = array('1',   '1','','');
$dmsounds["Ύ"] = array('1',   '1','','');
$dmsounds["Ϋ"] = array('1',   '1','','');
$dmsounds["ΰ"] = array('1',   '1','','');
$dmsounds["ΥΚ"] = array('1',   '5','5','5');
$dmsounds["ΥΥ"] = array('1',   '65','65','65');
$dmsounds["Φ"] = array('0',   '7','7','7');
$dmsounds["Χ"] = array('0',   '5','5','5');
$dmsounds["Ψ"] = array('0',   '7','7','7');
$dmsounds["Ω"] = array('1',   '0','','');
$dmsounds["Ώ"] = array('1',   '0','','');

// Hebrew alphabet
$dmsounds[ALEF] = array('1',   '0','','');
$dmsounds[ALEF.VAV] = array('1',   '0','7','');
$dmsounds[ALEF.GIMEL] = array('1',   '4','4','4',   '5','5','5',   '34','34','34');
//$dmsounds[BET.BET] = array('0',   '77','77','77');
$dmsounds[BET.BET] = array('0',   '7','7','7',   '77','77','77');
$dmsounds[BET] = array('0',   '7','7','7');
//$dmsounds[GIMEL.GIMEL] = array('0',   '45','45','45',   '55','55','55');
$dmsounds[GIMEL.GIMEL] = array('0',   '4','4','4',   '5','5','5',   '45','45','45',   '55','55','55',   '54','54','54');
$dmsounds[GIMEL.DALET] = array('0',   '43','43','43',   '53','53','53');
$dmsounds[GIMEL.HE] = array('0',   '45','45','45',   '55','55','55');
$dmsounds[GIMEL.ZAYIN] = array('0',   '44','44','44',   '45','45','45');
$dmsounds[GIMEL.HET] = array('0',   '45','45','45',   '55','55','55');
$dmsounds[GIMEL.KAF] = array('0',   '45','45','45',   '55','55','55');
$dmsounds[GIMEL.FINAL_KAF] = array('0',   '45','45','45',   '55','55','55');
$dmsounds[GIMEL.TSADI] = array('0',   '44','44','44',   '45','45','45');
$dmsounds[GIMEL.FINAL_TSADI] = array('0',   '44','44','44',   '45','45','45');
$dmsounds[GIMEL.QOF] = array('0',   '45','45','45',   '54','54','54');
$dmsounds[GIMEL.SHIN] = array('0',   '44','44','44',   '54','54','54');
$dmsounds[GIMEL.TAV] = array('0',   '43','43','43',   '53','53','53');
$dmsounds[GIMEL] = array('0',   '4','4','4',   '5','5','5');
$dmsounds[DALET.ZAYIN] = array('0',   '4','4','4');
//$dmsounds[DALET.DALET] = array('0',   '33','33','33');
$dmsounds[DALET.DALET] = array('0',   '3','3','3',   '33','33','33');
$dmsounds[DALET.TET] = array('0',   '33','33','33');
$dmsounds[DALET.SHIN] = array('0',   '4','4','4');
$dmsounds[DALET.TSADI] = array('0',   '4','4','4');
$dmsounds[DALET.FINAL_TSADI] = array('0',   '4','4','4');
$dmsounds[DALET] = array('0',   '3','3','3');
$dmsounds[HE.GIMEL] = array('0',   '54','54','54',   '55','55','55');
$dmsounds[HE.KAF] = array('0',   '55','55','55');
$dmsounds[HE.HET] = array('0',   '55','55','55');
$dmsounds[HE.QOF] = array('0',   '55','55','55',   '5','5','5');
$dmsounds[HE.HE] = array('0',   '5','5','',   '55','55','');	// -- added by GK
$dmsounds[HE] = array('0',   '5','5','');
$dmsounds[VAV.YOD] = array('1',   '','','',   '7','7','7');
$dmsounds[VAV] = array('1',   '7','7','7',   '7','','');
$dmsounds[VAV.VAV] = array('1',   '7','7','7',   '7','','');
$dmsounds[VAV.VAV.PE] = array('1',   '7','7','7',   '77','77','77');
$dmsounds[ZAYIN.SHIN] = array('0',   '4','4','4',   '44','44','44');
$dmsounds[ZAYIN.DALET.ZAYIN] = array('0',   '2','4','4');
$dmsounds[ZAYIN] = array('0',   '4','4','4');
$dmsounds[ZAYIN.GIMEL] = array('0',   '44','44','44',   '45','45','45');
//$dmsounds[ZAYIN.ZAYIN] = array('0',   '44','44','44');
$dmsounds[ZAYIN.ZAYIN] = array('0',   '4','4','4',   '44','44','44');
$dmsounds[ZAYIN.SAMEKH] = array('0',   '44','44','44');
$dmsounds[ZAYIN.TSADI] = array('0',   '44','44','44');
$dmsounds[ZAYIN.FINAL_TSADI] = array('0',   '44','44','44');
$dmsounds[HET.GIMEL] = array('0',   '54','54','54',   '53','53','53');
//$dmsounds[HET.HET] = array('0',   '55','55','55');
$dmsounds[HET.HET] = array('0',   '5','5','5',   '55','55','55');
$dmsounds[HET.QOF] = array('0',   '55','55','55',   '5','5','5');
$dmsounds[HET.KAF] = array('0',   '45','45','45',   '55','55','55');
$dmsounds[HET.SAMEKH] = array('0',   '5','54','54');
$dmsounds[HET.SHIN] = array('0',   '5','54','54');
$dmsounds[HET] = array('0',   '5','5','5');
$dmsounds[TET.SHIN] = array('0',   '4','4','4');
$dmsounds[TET.DALET] = array('0',   '33','33','33');
$dmsounds[TET.YOD] = array('0',   '3','3','3',   '4','4','4',   '3','3','34');
$dmsounds[TET.TAV] = array('0',   '33','33','33');
$dmsounds[TET.TET] = array('0',   '3','3','3',   '33','33','33');	// -- added by GK
$dmsounds[TET] = array('0',   '3','3','3');
$dmsounds[YOD] = array('1',   '1','','');
$dmsounds[YOD.ALEF] = array('1',   '1','','',   '1','1','1');
$dmsounds[KAF.GIMEL] = array('0',   '55','55','55',   '54','54','54');
$dmsounds[KAF.SHIN] = array('0',   '5','54','54');
$dmsounds[KAF.SAMEKH] = array('0',   '5','54','54');
$dmsounds[KAF.KAF] = array('0',   '5','5','5',   '55','55','55');	// == added by GK
$dmsounds[KAF.FINAL_KAF] = array('0',   '5','5','5',   '55','55','55');	// == added by GK
$dmsounds[KAF] = array('0',   '5','5','5');
$dmsounds[KAF.HET] = array('0',   '55','55','55',   '5','5','5');
$dmsounds[FINAL_KAF] = array('0',   '','5','5');
$dmsounds[LAMED] = array('0',   '8','8','8');
$dmsounds[LAMED.LAMED] = array('0',   '88','88','88',   '8','8','8');
$dmsounds[MEM.NUN] = array('0',   '66','66','66');
$dmsounds[MEM.FINAL_NUN] = array('0',   '66','66','66');
//$dmsounds[MEM.MEM] = array('0',   '66','66','66');
$dmsounds[MEM.MEM] = array('0',   '6','6','6',   '66','66','66');
$dmsounds[MEM.FINAL_MEM] = array('0',   '6','6','6',   '66','66','66');	// -- added by GK
$dmsounds[MEM] = array('0',   '6','6','6');
$dmsounds[FINAL_MEM] = array('0',   '','6','6');
$dmsounds[NUN.MEM] = array('0',   '66','66','66');
$dmsounds[NUN.FINAL_MEM] = array('0',   '66','66','66');
//$dmsounds[NUN.NUN] = array('0',   '66','66','66');
$dmsounds[NUN.NUN] = array('0',   '6','6','6',   '66','66','66');
$dmsounds[NUN.FINAL_NUN] = array('0',   '6','6','6',   '66','66','66');	// -- added by GK
$dmsounds[NUN] = array('0',   '6','6','6');
$dmsounds[FINAL_NUN] = array('0',   '','6','6');
$dmsounds[SAMEKH.TAV.SHIN] = array('0',   '2','4','4');
$dmsounds[SAMEKH.TAV.ZAYIN] = array('0',   '2','4','4');
$dmsounds[SAMEKH.TET.ZAYIN] = array('0',   '2','4','4');
$dmsounds[SAMEKH.TET.SHIN] = array('0',   '2','4','4');
$dmsounds[SAMEKH.TSADI.DALET] = array('0',   '2','4','4');
$dmsounds[SAMEKH.TET] = array('0',   '2','4','4',   '43','43','43');
$dmsounds[SAMEKH.TAV] = array('0',   '2','4','4',   '43','43','43');
$dmsounds[SAMEKH.GIMEL] = array('0',   '44','44','44',   '4','4','4');
//$dmsounds[SAMEKH.SAMEKH] = array('0',   '44','44','44');
$dmsounds[SAMEKH.SAMEKH] = array('0',   '4','4','4',   '44','44','44');
$dmsounds[SAMEKH.TSADI] = array('0',   '44','44','44');
$dmsounds[SAMEKH.FINAL_TSADI] = array('0',   '44','44','44');
$dmsounds[SAMEKH.ZAYIN] = array('0',   '44','44','44');
$dmsounds[SAMEKH.SHIN] = array('0',   '44','44','44');
$dmsounds[SAMEKH] = array('0',   '4','4','4');
$dmsounds[AYIN] = array('1',   '0','','');
$dmsounds[PE.BET] = array('0',   '7','7','7',   '77','77','77');
$dmsounds[PE.VAV.VAV] = array('0',   '7','7','7',   '77','77','77');
$dmsounds[PE.PE] = array('0',   '7','7','7',   '77','77','77');
$dmsounds[PE.FINAL_PE] = array('0',   '7','7','7',   '77','77','77');	// -- added by GK
$dmsounds[PE] = array('0',   '7','7','7');
$dmsounds[FINAL_PE] = array('0',   '','7','7');
$dmsounds[TSADI.GIMEL] = array('0',   '44','44','44',   '45','45','45');
$dmsounds[TSADI.ZAYIN] = array('0',   '44','44','44');
$dmsounds[TSADI.SAMEKH] = array('0',   '44','44','44');
//$dmsounds[TSADI.TSADI] = array('0',   '44','44','44');
//$dmsounds[TSADI.FINAL_TSADI] = array('0',   '44','44','44');
$dmsounds[TSADI.TSADI] = array('0',   '4','4','4',   '5','5','5',   '44','44','44',   '54','54','54',   '45','45','45');
$dmsounds[TSADI.FINAL_TSADI] = array('0',   '4','4','4',   '5','5','5',   '44','44','44',   '54','54','54');
$dmsounds[TSADI.SHIN] = array('0',   '44','44','44',   '4','4','4',   '5','5','5');
$dmsounds[TSADI] = array('0',   '4','4','4',   '5','5','5');
$dmsounds[FINAL_TSADI] = array('0',   '','4','4');
$dmsounds[QOF.HE] = array('0',   '55','55','5');
$dmsounds[QOF.SAMEKH] = array('0',   '5','54','54');
$dmsounds[QOF.SHIN] = array('0',   '5','54','54');
//$dmsounds[QOF.QOF] = array('0',   '55','55','55');
$dmsounds[QOF.QOF] = array('0',   '5','5','5',   '55','55','55');
$dmsounds[QOF.HET] = array('0',   '55','55','55');
$dmsounds[QOF.KAF] = array('0',   '55','55','55');
$dmsounds[QOF.FINAL_KAF] = array('0',   '55','55','55');
$dmsounds[QOF.GIMEL] = array('0',   '55','55','55',   '54','54','54');
$dmsounds[QOF] = array('0',   '5','5','5');
$dmsounds[RESH.RESH] = array('0',   '99','99','99',   '9','9','9');
$dmsounds[RESH] = array('0',   '9','9','9');
$dmsounds[SHIN.TET.ZAYIN] = array('0',   '2','4','4');
$dmsounds[SHIN.TAV.SHIN] = array('0',   '2','4','4');
$dmsounds[SHIN.TAV.ZAYIN] = array('0',   '2','4','4');
$dmsounds[SHIN.TET.SHIN] = array('0',   '2','4','4');
$dmsounds[SHIN.DALET] = array('0',   '2','43','43');
$dmsounds[SHIN.ZAYIN] = array('0',   '44','44','44');
$dmsounds[SHIN.SAMEKH] = array('0',   '44','44','44');
$dmsounds[SHIN.TAV] = array('0',   '2','43','43');
$dmsounds[SHIN.GIMEL] = array('0',   '4','4','4',   '44','44','44',   '4','43','43');
$dmsounds[SHIN.TET] = array('0',   '2','43','43',   '44','44','44');
$dmsounds[SHIN.TSADI] = array('0',   '44','44','44',   '45','45','45');
$dmsounds[SHIN.FINAL_TSADI] = array('0',   '44','','44',   '45','','45');
$dmsounds[SHIN.SHIN] = array('0',   '4','4','4',   '44','44','44');
$dmsounds[SHIN] = array('0',   '4','4','4');
$dmsounds[TAV.GIMEL] = array('0',   '34','34','34');
$dmsounds[TAV.ZAYIN] = array('0',   '34','34','34');
$dmsounds[TAV.SHIN] = array('0',   '4','4','4');
//$dmsounds[TAV.TAV] = array('0',   '33','33','33',   '4','4','4');
$dmsounds[TAV.TAV] = array('0',   '3','3','3',   '4','4','4',   '33','33','33',   '44','44','44',   '34','34','34',   '43','43','43');
$dmsounds[TAV] = array('0',   '3','3','3',   '4','4','4');

// Arabic alphabet
$dmsounds["ا"] = array('1',   '0','','');
$dmsounds["ب"] = array('0',   '7','7','7');
$dmsounds["ت"] = array('0',   '3','3','3');
$dmsounds["ث"] = array('0',   '3','3','3');
$dmsounds["ج"] = array('0',   '4','4','4');
$dmsounds["ح"] = array('0',   '5','5','5');
$dmsounds["خ"] = array('0',   '5','5','5');
$dmsounds["د"] = array('0',   '3','3','3');
$dmsounds["ذ"] = array('0',   '3','3','3');
$dmsounds["ر"] = array('0',   '9','9','9');
$dmsounds["ز"] = array('0',   '4','4','4');
$dmsounds["س"] = array('0',   '4','4','4');
$dmsounds["ش"] = array('0',   '4','4','4');
$dmsounds["ص"] = array('0',   '4','4','4');
$dmsounds["ض"] = array('0',   '3','3','3');
$dmsounds["ط"] = array('0',   '3','3','3');
$dmsounds["ظ"] = array('0',   '4','4','4');
$dmsounds["ع"] = array('1',   '0','','');
$dmsounds["غ"] = array('0',   '0','','');
$dmsounds["ف"] = array('0',   '7','7','7');
$dmsounds["ق"] = array('0',   '5','5','5');
$dmsounds["ك"] = array('0',   '5','5','5');
$dmsounds["ل"] = array('0',   '8','8','8');
$dmsounds["لا"] = array('0',   '8','8','8');
$dmsounds["م"] = array('0',   '6','6','6');
$dmsounds["ن"] = array('0',   '6','6','6');
$dmsounds["هن"] = array('0',   '66','66','66');
$dmsounds["ه"] = array('0',   '5','5','');
$dmsounds["و"] = array('1',   '','','',   '7','','');
$dmsounds["ي"] = array('0',   '1','','');
$dmsounds["آ"] = array('0',   '1','','');
$dmsounds["ة"] = array('0',   '','','3');
$dmsounds["ی"] = array('0',   '1','','');
$dmsounds["ى"] = array('1',   '1','','');

?>
