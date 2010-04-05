<?PHP
/**
 * Special Character tables, for use by JavaScript to input characters
 * that aren't on your keyboard
 *
 * THIS FILE MUST BE SAVED IN UTF-8 ENCODING (or some special characters will be lost)
 *
 * When updating, be sure to add the language into the array $specialchar_languages,
 * add a case for that language into the switch, and add any new special characters
 * into the default case of the switch near the bottom.
 *
 * Languages alpha by name in original language.
 * Special characters by language from European Commision, Research in Official Statistics:
 * http://europa.eu.int/comm/eurostat/research/index.htm?http://europa.eu.int/en/comm/eurostat/research/isi/special/&1
 * Other sources:
 * Czech: http://webdesign.about.com/library/blhtmlcodes-cz.htm
 * Irish: offline sources
 * Hawaiian: http://www.olelo.hawaii.edu/eng/resources/unicode.html
 * Lithuanian: http://www.eki.ee/letter/chardata.cgi?lang=lt+Lithuanian&script=latin
 *
 * Other special characters are all listed at the bottom.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2007  PGV Development Team
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
 * @subpackage Languages
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_SPECIALCHARS_PHP', '');

$specialchar_languages = array(
	"af" => Zend_Locale::getTranslation('af', 'language', WT_LOCALE),
	"cs" => Zend_Locale::getTranslation('cs', 'language', WT_LOCALE),
	"sk" => Zend_Locale::getTranslation('sk', 'language', WT_LOCALE),
	"da" => Zend_Locale::getTranslation('da', 'language', WT_LOCALE),
	"de" => Zend_Locale::getTranslation('de', 'language', WT_LOCALE),
	"en" => Zend_Locale::getTranslation('en', 'language', WT_LOCALE),
	"es" => Zend_Locale::getTranslation('es', 'language', WT_LOCALE),
	"eu" => Zend_Locale::getTranslation('eu', 'language', WT_LOCALE),
	"fr" => Zend_Locale::getTranslation('fr', 'language', WT_LOCALE),
	"gd" => Zend_Locale::getTranslation('gd', 'language', WT_LOCALE),
	"el" => Zend_Locale::getTranslation('el', 'language', WT_LOCALE),
	"is" => Zend_Locale::getTranslation('is', 'language', WT_LOCALE),
	"it" => Zend_Locale::getTranslation('it', 'language', WT_LOCALE),
	"hu" => Zend_Locale::getTranslation('hu', 'language', WT_LOCALE),
	"lt" => Zend_Locale::getTranslation('lt', 'language', WT_LOCALE),
	"nl" => Zend_Locale::getTranslation('nl', 'language', WT_LOCALE),
	"nn" => Zend_Locale::getTranslation('nn', 'language', WT_LOCALE),
	"haw" => Zend_Locale::getTranslation('haw', 'language', WT_LOCALE),
	"pl" => Zend_Locale::getTranslation('pl', 'language', WT_LOCALE),
	"pt" => Zend_Locale::getTranslation('pt', 'language', WT_LOCALE),
	"sl" => Zend_Locale::getTranslation('sl', 'language', WT_LOCALE),
	"fi" => Zend_Locale::getTranslation('fi', 'language', WT_LOCALE),
	"sv" => Zend_Locale::getTranslation('sv', 'language', WT_LOCALE),
	"tr" => Zend_Locale::getTranslation('tr', 'language', WT_LOCALE),
	"he" => Zend_Locale::getTranslation('he', 'language', WT_LOCALE),
	"ar" => Zend_Locale::getTranslation('ar', 'language', WT_LOCALE),
	"ru" => Zend_Locale::getTranslation('ru', 'language', WT_LOCALE),
	"vi" => Zend_Locale::getTranslation('vi', 'language', WT_LOCALE),
);

switch ($language_filter) {
case "af": // Afrikaans
   $ucspecialchars = array(
		"&#200;" => "È",
		"&#201;" => "É",
		"&#202;" => "Ê",
		"&#203;" => "Ë",
		"&#206;" => "Î",
		"&#207;" => "Ï",
		"&#212;" => "Ô",
		"&#219;" => "Û",
	);
	$lcspecialchars = array(
		"&#232;" => "è",
		"&#233;" => "é",
		"&#234;" => "ê",
		"&#235;" => "ë",
		"&#238;" => "î",
		"&#239;" => "ï",
		"&#244;" => "ô",
		"&#251;" => "û",
		"&#329;" => "ŉ", // n preceded by apostrophe
	);
	break;
case "cs": // Ceština
   $ucspecialchars = array(
		"&#193;" => "Á",
		"&#260;" => "Ą", // A cedille
		"&#196;" => "Ä",
		"&#201;" => "É",
		"&#280;" => "Ę", // E cedille
		"&#282;" => "Ě", // E hachek
		"&#205;" => "Í",
		"&#211;" => "Ó",
		"&#212;" => "Ô",
		"&#218;" => "Ú",
		"&#366;" => "Ů", // U ring
		"&#221;" => "Ý",
		"&#268;" => "Č", // C hachek
		"&#313;" => "Ĺ", // L acute
		"&#327;" => "Ň", // N hachek
		"&#340;" => "Ŕ", // R acute
		"&#344;" => "Ř", // R hachek
		"&#352;" => "Š", // S hachek
		"&#381;" => "Ž", // Z hachek
	);
	$lcspecialchars = array(
		"&#225;" => "á",
		"&#261;" => "ą", // a cedille
		"&#228;" => "ä",
		"&#233;" => "é",
		"&#281;" => "ę", // e cedille
		"&#283;" => "ě", // e hachek
		"&#237;" => "í",
		"&#243;" => "ó",
		"&#244;" => "ô",
		"&#250;" => "ú",
		"&#367;" => "ů", // u ring
		"&#253;" => "ý",
		"&#269;" => "č", // c hachek
		"&#271;" => "ď", // &#271; d apostrophe - shows incorrectly as d hacheck - d
		"&#357;" => "ť", // t apostrophe
		"&#314;" => "ĺ", // l acute
		"&#328;" => "ň", // n hachek
		"&#341;" => "ŕ", // r acute
		"&#345;" => "ř", // r hachek
		"&#353;" => "š", // s hachek
		"&#382;" => "ž", // z hachek
	);
	break;
case "sk": // Slovenčina
   $ucspecialchars = array(
		"&#193;" => "Á",
		"&#196;" => "Ä",
		"&#268;" => "Č", // C hachek
		"&#270;" => "Ď", // D hachek
		"&#201;" => "É",
		"&#282;" => "Ě", // E hachek
		"&#205;" => "Í",
		"&#313;" => "Ĺ", // L acute
		"&#317;" => "Ľ", // L hachek
		"&#327;" => "Ň", // N hachek
		"&#211;" => "Ó",
		"&#212;" => "Ô",
		"&#340;" => "Ŕ", // R acute
		"&#344;" => "Ř", // R hachek
		"&#352;" => "Š", // S hachek
		"&#356;" => "Ť", // T hachek
		"&#218;" => "Ú",
		"&#366;" => "Ů", // U ring
		"&#221;" => "Ý",
		"&#381;" => "Ž", // Z hachek
	);
	$lcspecialchars = array(
		"&#225;" => "á",
		"&#228;" => "ä",
		"&#269;" => "č", // c hachek
		"&#271;" => "ď", // d hacheck
		"&#233;" => "é",
		"&#283;" => "ě", // e hachek
		"&#237;" => "í",
		"&#314;" => "ĺ", // l acute
		"&#318;" => "ľ", // l hachek
		"&#328;" => "ň", // n hachek
		"&#243;" => "ó",
		"&#244;" => "ô",
		"&#341;" => "ŕ", // r acute
		"&#345;" => "ř", // r hachek
		"&#353;" => "š", // s hachek
		"&#357;" => "ť", // t hachek
		"&#250;" => "ú",
		"&#367;" => "ů", // u ring
		"&#253;" => "ý",
		"&#382;" => "ž", // z hachek
	);
	break;
case "da": // Dansk
   $ucspecialchars = array(
		"&#197;" => "Å",
		"&#198;" => "Æ",
		"&#201;" => "É",
		"&#216;" => "Ø",
		"&#193;" => "Á",
		"&#205;" => "Í",
		"&#211;" => "Ó",
		"&#218;" => "Ú",
		"&#221;" => "Ý",
	);
	$lcspecialchars = array(
		"&#229;" => "å",
		"&#230;" => "æ",
		"&#233;" => "é",
		"&#248;" => "ø",
		"&#225;" => "á",
		"&#237;" => "í",
		"&#243;" => "ó",
		"&#250;" => "ú",
		"&#253;" => "ý",
	);
	break;
case "de": // Deutsch
   $ucspecialchars = array(
		"&#196;" => "Ä",
		"&#214;" => "Ö",
		"&#220;" => "Ü",
		"&#192;" => "À",
		"&#201;" => "É",
	);
	$lcspecialchars = array(
		"&#228;" => "ä",
		"&#246;" => "ö",
		"&#252;" => "ü",
		"&#224;" => "à",
		"&#233;" => "é",
		"&#223;" => "ß",
	);
	break;
case "en": // English  -- limited copy of the default case (Western European set)
   $ucspecialchars = array(
   		"&#192;" => "À",
		"&#193;" => "Á",
		"&#194;" => "Â",
		"&#195;" => "Ã",
		"&#196;" => "Ä",
		"&#197;" => "Å",
		"&#198;" => "Æ",
		"&#199;" => "Ç",
		"&#208;" => "Ð",  // capital Eth
		"&#200;" => "È",
		"&#201;" => "É",
		"&#202;" => "Ê",
		"&#203;" => "Ë",
		"&#204;" => "Ì",
		"&#205;" => "Í",
		"&#206;" => "Î",
		"&#207;" => "Ï",
		"&#306;" => "Ĳ",  // ligature IJ
		"&#209;" => "Ñ",
		"&#210;" => "Ò",
		"&#211;" => "Ó",
		"&#212;" => "Ô",
		"&#213;" => "Õ",
		"&#214;" => "Ö",
		"&#338;" => "Œ",
		"&#216;" => "Ø",
		"&#222;" => "Þ",  // capital Thorn
		"&#217;" => "Ù",
		"&#218;" => "Ú",
		"&#219;" => "Û",
		"&#220;" => "Ü",
		"&#221;" => "Ý",
		"&#159;" => "Ÿ",
	);
	$lcspecialchars = array(
		"&#224;" => "à",
		"&#225;" => "á",
		"&#226;" => "â",
		"&#227;" => "ã",
		"&#228;" => "ä",
		"&#229;" => "å",
		"&#230;" => "æ",
		"&#231;" => "ç",
		"&#240;" => "ð",  // lower-case Thorn
		"&#232;" => "è",
		"&#233;" => "é",
		"&#234;" => "ê",
		"&#235;" => "ë",
		"&#236;" => "ì",
		"&#237;" => "í",
		"&#238;" => "î",
		"&#239;" => "ï",
		"&#307;" => "ĳ",  // ligature ij
		"&#241;" => "ñ",
		"&#242;" => "ò",
		"&#243;" => "ó",
		"&#244;" => "ô",
		"&#245;" => "õ",
		"&#246;" => "ö",
		"&#339;" => "œ",
		"&#248;" => "ø",
		"&#254;" => "þ",  // lower-case Eth
		"&#249;" => "ù",
		"&#250;" => "ú",
		"&#251;" => "û",
		"&#252;" => "ü",
		"&#253;" => "ý",
		"&#255;" => "ÿ",
		"&#223;" => "ß",
	);
	break;
case "es": // Español
   $ucspecialchars = array(
		"&#193;" => "Á",
		"&#201;" => "É",
		"&#205;" => "Í",
		"&#209;" => "Ñ",
		"&#211;" => "Ó",
		"&#218;" => "Ú",
		"&#220;" => "Ü",
		"&#199;" => "Ç",
	);
	$lcspecialchars = array(
		"&#225;" => "á",
		"&#233;" => "é",
		"&#237;" => "í",
		"&#241;" => "ñ",
		"&#243;" => "ó",
		"&#250;" => "ú",
		"&#252;" => "ü",
		"&#231;" => "ç",
	);
	break;
case "eu": // Euskara
   $ucspecialchars = array(
		"&#199;" => "Ç",
	);
	$lcspecialchars = array(
		"&#231;" => "ç",
	);
	break;
case "fr": // Français
   $ucspecialchars = array(
		"&#192;" => "À",
		"&#194;" => "Â",
		"&#198;" => "Æ",
		"&#199;" => "Ç",
		"&#200;" => "È",
		"&#201;" => "É",
		"&#202;" => "Ê",
		"&#203;" => "Ë",
		"&#206;" => "Î",
		"&#207;" => "Ï",
		"&#212;" => "Ô",
		"&#338;" => "Œ",
		"&#217;" => "Ù",
		"&#219;" => "Û",
		"&#220;" => "Ü",
		"&#159;" => "Ÿ",
	);
	$lcspecialchars = array(
		"&#224;" => "à",
		"&#226;" => "â",
		"&#230;" => "æ",
		"&#231;" => "ç",
		"&#232;" => "è",
		"&#233;" => "é",
		"&#234;" => "ê",
		"&#235;" => "ë",
		"&#238;" => "î",
		"&#239;" => "ï",
		"&#244;" => "ô",
		"&#339;" => "œ",
		"&#249;" => "ù",
		"&#251;" => "û",
		"&#252;" => "ü",
		"&#255;" => "ÿ",
	);
	break;
case "gd-ie": // Gaeilge
   $ucspecialchars = array(
		"&#193;" => "Á",
		"&#201;" => "É",
		"&#205;" => "Í",
		"&#211;" => "Ó",
		"&#218;" => "Ú",
	);
	$lcspecialchars = array(
		"&#225;" => "á",
		"&#233;" => "é",
		"&#237;" => "í",
		"&#243;" => "ó",
		"&#250;" => "ú",
	);
	break;
case "is": // Íslenska
   $ucspecialchars = array(
		"&#193;" => "Á",
		"&#198;" => "Æ",
		"&#208;" => "Ð",
		"&#201;" => "É",
		"&#205;" => "Í",
		"&#211;" => "Ó",
		"&#214;" => "Ö",
		"&#222;" => "Þ",
		"&#218;" => "Ú",
		"&#221;" => "Ý",
	);
	$lcspecialchars = array(
		"&#225;" => "á",
		"&#230;" => "æ",
		"&#240;" => "ð",
		"&#233;" => "é",
		"&#237;" => "í",
		"&#243;" => "ó",
		"&#246;" => "ö",
		"&#254;" => "þ",
		"&#250;" => "ú",
		"&#253;" => "ý",
	);
	break;
case "it": // Italiano
   $ucspecialchars = array(
		"&#192;" => "À",
		"&#200;" => "È",
		"&#201;" => "É",
		"&#204;" => "Ì",
		"&#205;" => "Í",
		"&#210;" => "Ò",
		"&#211;" => "Ó",
		"&#217;" => "Ù",
		"&#218;" => "Ú",
		"&#207;" => "Ï",
	);
	$lcspecialchars = array(
		"&#224;" => "à",
		"&#232;" => "è",
		"&#233;" => "é",
		"&#236;" => "ì",
		"&#237;" => "í",
		"&#242;" => "ò",
		"&#243;" => "ó",
		"&#249;" => "ù",
		"&#250;" => "ú",
		"&#239;" => "ï",
	);
	break;
case "hu": // Magyar
   $ucspecialchars = array(
		"&#193;" => "Á",
		"&#201;" => "É",
		"&#205;" => "Í",
		"&#211;" => "Ó",
		"&#214;" => "Ö",
		"&#336;" => "Ő", // O with double acute
		"&#218;" => "Ú",
		"&#220;" => "Ü",
		"&#368;" => "Ű", // U with double acute
	);
	$lcspecialchars = array(
		"&#225;" => "á",
		"&#233;" => "é",
		"&#237;" => "í",
		"&#243;" => "ó",
		"&#246;" => "ö",
		"&#337;" => "ő", // o with double acute
		"&#250;" => "ú",
		"&#252;" => "ü",
		"&#369;" => "ű", // u with double acute
	);
	break;
case "lt": // Lithuanian
   $ucspecialchars = array(
		"&#260;" => "Ą", // A cedille
		"&#268;" => "Č", // C with hachek/caron
		"&#280;" => "Ę", // E cedille
		"&#278;" => "Ė", // E with dot above
		"&#302;" => "Į", // I  with ogonek
		"&#352;" => "Š", // S hachek
		"&#370;" => "Ų", // U  with ogonek
		"&#362;" => "Ū", // U with macron
		"&#381;" => "Ž", // Z hachek
	);
	$lcspecialchars = array(
		"&#261;" => "ą", // a cedille
		"&#269;" => "č", // c hachek/caron
		"&#281;" => "ę", // e cedille
		"&#279;" => "ė", // e with dot above
		"&#303;" => "į", // i with ogonek
		"&#353;" => "š", // s hachek
		"&#371;" => "ų", // u with ogonek
		"&#363;" => "ū", // u with macron
		"&#382;" => "ž", // z hachek
         );
	break;
case "nl": // Nederlands
   $ucspecialchars = array(
		"&#193;" => "Á",
		"&#194;" => "Â",
		"&#200;" => "È",
		"&#201;" => "É",
		"&#202;" => "Ê",
		"&#203;" => "Ë",
		"&#205;" => "Í",
		"&#207;" => "Ï",
		"&#306;" => "Ĳ", // ligature IJ
		"&#211;" => "Ó",
		"&#212;" => "Ô",
		"&#214;" => "Ö",
		"&#218;" => "Ú",
		"&#217;" => "Ù",
		"&#196;" => "Ä",
		"&#219;" => "Û",
		"&#220;" => "Ü",
	);
	$lcspecialchars = array(
		"&#225;" => "á",
		"&#226;" => "â",
		"&#232;" => "è",
		"&#233;" => "é",
		"&#234;" => "ê",
		"&#235;" => "ë",
		"&#237;" => "í",
		"&#239;" => "ï",
		"&#307;" => "ĳ", // ligature ij
		"&#243;" => "ó",
		"&#244;" => "ô",
		"&#246;" => "ö",
		"&#250;" => "ú",
		"&#249;" => "ù",
		"&#228;" => "ä",
		"&#251;" => "û",
		"&#252;" => "ü",
	);
	break;
case "no": // Norsk
   $ucspecialchars = array(
		"&#198;" => "Æ",
		"&#216;" => "Ø",
		"&#197;" => "Å",
		"&#192;" => "À",
		"&#201;" => "É",
		"&#202;" => "Ê",
		"&#211;" => "Ó",
		"&#210;" => "Ò",
		"&#212;" => "Ô",
	);
	$lcspecialchars = array(
		"&#230;" => "æ",
		"&#248;" => "ø",
		"&#229;" => "å",
		"&#224;" => "à",
		"&#233;" => "é",
		"&#234;" => "ê",
		"&#243;" => "ó",
		"&#242;" => "ò",
		"&#244;" => "ô",
	);
	break;
case "hawaiian": // 'Olelo Hawai'i
   $ucspecialchars = array(
		"&#256;" => "Ā", // A with macron
		"&#274;" => "Ē", // E with macron
		"&#298;" => "Ī", // I with macron
		"&#332;" => "Ō", // O with macron
		"&#362;" => "Ū", // U with macron
		"&#x2018;" => "‘", // ‘okina ('okina) - new unicode &#699;
	);
	$lcspecialchars = array(
		"&#257;" => "ā", // a with macron
		"&#275;" => "ē", // e with macron
		"&#299;" => "ī", // i with macron
		"&#333;" => "ō", // o with macron
		"&#363;" => "ū", // u with macron
		"&#x2018;" => "‘", // ‘okina ('okina) - new unicode &#699;
	);
	break;
case "pl": // Polski
   $ucspecialchars = array(
		"&#260;" => "Ą", // A with ogonek
		"&#262;" => "Ć", // C with acute
		"&#280;" => "Ę", // E with ogonek
		"&#321;" => "Ł", // L with stroke
		"&#323;" => "Ń", // N with acute
		"&#211;" => "Ó",
		"&#346;" => "Ś", // S with acute
		"&#377;" => "Ź", // Z with acute
		"&#379;" => "Ż", // Z with dot above
	);
	$lcspecialchars = array(
		"&#261;" => "ą", // a with ogonek
		"&#263;" => "ć", // c with acute
		"&#281;" => "ę", // e with ogonek
		"&#322;" => "ł", // l with stroke
		"&#324;" => "ń", // n with acute
		"&#243;" => "ó",
		"&#347;" => "ś", // s with acute
		"&#378;" => "ź", // z with acute
		"&#380;" => "ż", // z with dot above
	);
	break;
case "pt": // Portuguese
   $ucspecialchars = array(
		"&#192;" => "À",
		"&#193;" => "Á",
		"&#194;" => "Â",
		"&#195;" => "Ã",
		"&#199;" => "Ç",
		"&#201;" => "É",
		"&#202;" => "Ê",
		"&#205;" => "Í",
		"&#211;" => "Ó",
		"&#212;" => "Ô",
		"&#213;" => "Õ",
		"&#218;" => "Ú",
		"&#220;" => "Ü",
		"&#200;" => "È",
		"&#210;" => "Ò",
	);
	$lcspecialchars = array(
		"&#224;" => "à",
		"&#225;" => "á",
		"&#226;" => "â",
		"&#227;" => "ã",
		"&#231;" => "ç",
		"&#233;" => "é",
		"&#234;" => "ê",
		"&#237;" => "í",
		"&#243;" => "ó",
		"&#244;" => "ô",
		"&#245;" => "õ",
		"&#250;" => "ú",
		"&#252;" => "ü",
		"&#232;" => "è",
		"&#242;" => "ò",
	);
	break;
case "sl": // Slovenšcina
   $ucspecialchars = array(
		"&#268;" => "Č", // C with caron
		"&#352;" => "Š", // S with caron
		"&#381;" => "Ž", // Z with caron
		"&#262;" => "Ć", // C with acute
		"&#272;" => "Ð", // D with stroke
		"&#196;" => "Ä",
		"&#214;" => "Ö",
		"&#220;" => "Ü",
	);
	$lcspecialchars = array(
		"&#269;" => "č", // c with caron
		"&#353;" => "š", // s with caron
		"&#382;" => "ž", // z with caron
		"&#263;" => "ć", // c with acute
		"&#273;" => "đ", // d with stroke
		"&#228;" => "ä",
		"&#246;" => "ö",
		"&#252;" => "ü",
	);
	break;
case "fi": // Suomi
   $ucspecialchars = array(
		"&#196;" => "Ä",
		"&#214;" => "Ö",
		"&#197;" => "Å",
		"&#352;" => "Š",
		"&#381;" => "Ž",
	);
	$lcspecialchars = array(
		"&#228;" => "ä",
		"&#246;" => "ö",
		"&#229;" => "å",
		"&#353;" => "š",
		"&#382;" => "ž",
	);
	break;
case "sv": // Svenska
   $ucspecialchars = array(
		"&#196;" => "Ä",
		"&#197;" => "Å",
		"&#201;" => "É",
		"&#214;" => "Ö",
		"&#193;" => "Á",
		"&#203;" => "Ë",
		"&#220;" => "Ü",
	);
	$lcspecialchars = array(
		"&#228;" => "ä",
		"&#229;" => "å",
		"&#233;" => "é",
		"&#246;" => "ö",
		"&#225;" => "á",
		"&#235;" => "ë",
		"&#252;" => "ü",
	);
	break;
case "tr": // Türkçe
   $ucspecialchars = array(
		"&#194;" => "Â",
		"&#199;" => "Ç",
		"&#286;" => "Ğ", // G with breve
		"&#206;" => "Î",
		"&#304;" => "İ", // I with dot above
		"&#214;" => "Ö",
		"&#350;" => "Ş", // S with cedilla
		"&#219;" => "Û",
		"&#220;" => "Ü",
	);
	$lcspecialchars = array(
		"&#226;" => "â",
		"&#231;" => "ç",
		"&#287;" => "ğ", // g with breve
		"&#238;" => "î",
		"&#305;" => "ı", // i without dot above
		"&#246;" => "ö",
		"&#351;" => "ş", // s with cedilla
		"&#251;" => "û",
		"&#252;" => "ü",
	);
	break;
case "el": // greek
$ucspecialchars = array(
		"Ά" => "Ά",
		"Α" => "Α",
		"Β" => "Β", // G with breve
		"Γ" => "Γ",
		"Δ" => "Δ", // I with dot above
		"Έ" => "Έ",
		"Ε" => "Ε", // S with cedilla
		"Ζ" => "Ζ",
		"Η" => "Η",
		"Θ" => "Θ",
		"Ί" => "Ί",
		"Ϊ" => "Ϊ",
		"Ϊ" => "Ϊ",
		"Ι" => "Ι",
		"Κ" => "Κ",
		"Λ" => "Λ",
		"Μ" => "Μ",
		"Ν" => "Ν",
		"Ξ" => "Ξ",
		"Ό" => "Ό",
		"Ο" => "Ο",
		"Π" => "Π",
		"Ρ" => "Ρ",
		"Σ" => "Σ",
		"Σ" => "Σ",
		"Τ" => "Τ",
		"Ύ" => "Ύ",
		"Ϋ" => "Ϋ",
		"Ϋ" => "Ϋ",
		"Υ" => "Υ",
		"Φ" => "Φ",
		"Χ" => "Χ",
		"Ψ" => "Ψ",
		"Ώ" => "Ώ",
		"Ω" => "Ω"
	);
	$lcspecialchars = array(
		"ά" => "ά",
		"α" => "α",
		"β" => "β", // g with breve
		"γ" => "γ",
		"δ" => "δ", // i without dot above
		"έ" => "έ",
		"ε" => "ε", // s with cedilla
		"ζ" => "ζ",
		"η" => "η",
		"θ" => "θ",
		"ί" => "ί",
		"ϊ" => "ϊ",
		"ΐ" => "ΐ",
		"ι" => "ι",
		"κ" => "κ",
		"λ" => "λ",
		"μ" => "μ",
		"ν" => "ν",
		"ξ" => "ξ",
		"ό" => "ό",
		"ο" => "ο",
		"π" => "π",
		"ρ" => "ρ",
		"σ" => "σ",
		"ς" => "ς",
		"τ" => "τ",
		"ύ" => "ύ",
		"ϋ" => "ϋ",
		"ΰ" => "ΰ",
		"υ" => "υ",
		"φ" => "φ",
		"χ" => "χ",
		"ψ" => "ψ",
		"ώ" => "ώ",
		"ω" => "ω",
	);
	break;
	case "he": // hebrew
   $ucspecialchars = array(
		"א" => "א",
		"ב" => "ב",
		"ג" => "ג",
		"ד" => "ד",
		"ה" => "ה",
		"ו" => "ו",
		"ז" => "ז",
		"ח" => "ח",
		"ט" => "ט",
		"י" => "י",
		"כ" => "כ",
		"ך" => "ך",
		"ל" => "ל",
		"מ" => "מ",
		"ם" => "ם",
		"נ" => "נ",
		"ן" => "ן",
		"ס" => "ס",
		"ע" => "ע",
		"פ" => "פ",
		"ף" => "ף",
		"צ" => "צ",
		"ץ" => "ץ",
		"ק" => "ק",
		"ר" => "ר",
		"ש" => "ש",
		"ת" => "ת"
	);
	$lcspecialchars = array(
		"א" => "א",
		"ב" => "ב",
		"ג" => "ג",
		"ד" => "ד",
		"ה" => "ה",
		"ו" => "ו",
		"ז" => "ז",
		"ח" => "ח",
		"ט" => "ט",
		"י" => "י",
		"כ" => "כ",
		"ך" => "ך",
		"ל" => "ל",
		"מ" => "מ",
		"ם" => "ם",
		"נ" => "נ",
		"ן" => "ן",
		"ס" => "ס",
		"ע" => "ע",
		"פ" => "פ",
		"ף" => "ף",
		"צ" => "צ",
		"ץ" => "ץ",
		"ק" => "ק",
		"ר" => "ר",
		"ש" => "ש",
		"ת" => "ת"
	);
	break;
	case "ar": // arabic
   $ucspecialchars = array(
		"ا" => "ا",
		"ب" => "ب",
		"ت" => "ت",
		"ث" => "ث",
		"ج" => "ج",
		"ح" => "ح",
		"خ" => "خ",
		"د" => "د",
		"ذ" => "ذ",
		"ر" => "ر",
		"ز" => "ز",
		"س" => "س",
		"ش" => "ش",
		"ص" => "ص",
		"ض" => "ض",
		"ط" => "ط",
		"ظ" => "ظ",
		"ع" => "ع",
		"غ" => "غ",
		"ف" => "ف",
		"ق" => "ق",
		"ك" => "ك",
		"ل" => "ل",
		"م" => "م",
		"ن" => "ن",
		"ه" => "ه",
		"و" => "و",
		"ي" => "ي",
		"آ" => "آ",
		"ة" => "ة",
		"ى" => "ى",
		"ی" => "ی"
	);
	$lcspecialchars = array(
		"ا" => "ا",
		"ب" => "ب",
		"ت" => "ت",
		"ث" => "ث",
		"ج" => "ج",
		"ح" => "ح",
		"خ" => "خ",
		"د" => "د",
		"ذ" => "ذ",
		"ر" => "ر",
		"ز" => "ز",
		"س" => "س",
		"ش" => "ش",
		"ص" => "ص",
		"ض" => "ض",
		"ط" => "ط",
		"ظ" => "ظ",
		"ع" => "ع",
		"غ" => "غ",
		"ف" => "ف",
		"ق" => "ق",
		"ك" => "ك",
		"ل" => "ل",
		"م" => "م",
		"ن" => "ن",
		"ه" => "ه",
		"و" => "و",
		"ي" => "ي",
		"آ" => "آ",
		"ة" => "ة",
		"ى" => "ى",
		"ی" => "ی"
	);
	break;
	case "ru": // russian
   $ucspecialchars = array(
		"А" => "А",
		"Б" => "Б",
		"В" => "В",
		"Г" => "Г",
		"Д" => "Д",
		"Е" => "Е",
		"Ё" => "Ё",
		"Ж" => "Ж",
		"З" => "З",
		"И" => "И",
		"Й" => "Й",
		"К" => "К",
		"Л" => "Л",
		"М" => "М",
		"Н" => "Н",
		"О" => "О",
		"П" => "П",
		"Р" => "Р",
		"С" => "С",
		"Т" => "Т",
		"У" => "У",
		"Ф" => "Ф",
		"Х" => "Х",
		"Ц" => "Ц",
		"Ч" => "Ч",
		"Ш" => "Ш",
		"Щ" => "Щ",
		"Ъ" => "Ъ",
		"Ы" => "Ы",
		"Ь" => "Ь",
		"Э" => "Э",
		"Ю" => "Ю",
		"Я" => "Я"
	);
	$lcspecialchars = array(
		"а" => "а",
		"б" => "б",
		"в" => "в",
		"г" => "г",
		"д" => "д",
		"е" => "е",
		"ё" => "ё",
		"ж" => "ж",
		"з" => "з",
		"и" => "и",
		"й" => "й",
		"к" => "к",
		"л" => "л",
		"м" => "м",
		"н" => "н",
		"о" => "о",
		"п" => "п",
		"р" => "р",
		"с" => "с",
		"т" => "т",
		"у" => "у",
		"ф" => "ф",
		"х" => "х",
		"ц" => "ц",
		"ч" => "ч",
		"ш" => "ш",
		"щ" => "щ",
		"ъ" => "ъ",
		"ы" => "ы",
		"ь" => "ь",
		"э" => "э",
		"ю" => "ю",
		"я" => "я"
	);
	break;
	case "vi": // vietnamese
    $ucspecialchars = array(
        "À" => "À", // A with grave
        "Á" => "Á", // A with acute
        "Â" => "Â", // A with circumflex
        "Ã" => "Ã", // A with tilde
        "Ạ" => "Ạ", // A with dot below
        "Ả" => "Ả", // A with hook above
        "Ă" => "Ă", // A with breve
        "Ấ" => "Ấ", // A with circumflex and acute
        "Ầ" => "Ầ", // A with circumflex and grave
        "Ẫ" => "Ẫ", // A with circumflex and tilde
        "Ậ" => "Ậ", // A with circumflex and dot below
        "Ắ" => "Ắ", // A with breve and acute
        "Ằ" => "Ằ", // A with breve and grave
        "Ẳ" => "Ẳ", // A with breve and hook above
        "Ẵ" => "Ẵ", // A with breve and tilde
        "Ặ" => "Ặ", // A with breve and dot below
        "Đ" => "Đ", // D with stroke
        "È" => "È", // E with grave
        "É" => "É", // E with acute
        "Ê" => "Ê", // E with circumflex
        "Ẹ" => "Ẹ", // E with dot below
        "Ẻ" => "Ẻ", // E with hook above
        "Ẽ" => "Ẽ", // E with tilde
        "Ế" => "Ế", // E with circumflex and acute
        "Ề" => "Ề", // E with circumflex and grave
        "Ể" => "Ể", // E with circumflex and hook above
        "Ễ" => "Ễ", // E with circumflex and tilde
        "Ệ" => "Ệ", // E with circumflex and dot below
        "Ì" => "Ì", // I with grave
        "Í" => "Í", // I with acute
        "Ĩ" => "Ĩ", // I with tilde
        "Ỉ" => "Ỉ", // I with hook above
        "Ị" => "Ị", // I with dot below
        "Ò" => "Ò", // O with grave
        "Ó" => "Ó", // O with acute
        "Ô" => "Ô", // O with circumflex
        "Õ" => "Õ", // O with tilde
        "Ơ" => "Ơ", // O with horn
        "Ọ" => "Ọ", // O with dot below
        "Ỏ" => "Ỏ", // O with hook above
        "Ố" => "Ố", // O with circumflex and acute
        "Ồ" => "Ồ", // O with circumflex and grave
        "Ổ" => "Ổ", // O with circumflex and hook above
        "Ỗ" => "Ỗ", // O with circumflex and tilde
        "Ộ" => "Ộ", // O with circumflex and dot below
        "Ớ" => "Ớ", // O with horn and acute
        "Ờ" => "Ờ", // O with horn and grave
        "Ở" => "Ở", // O with horn and hook above
        "Ỡ" => "Ỡ", // O with horn and tilde
        "Ợ" => "Ợ", // O with horn and dot below
        "Ù" => "Ù", // U with grave
        "Ú" => "Ú", // U with acute
        "Ũ" => "Ũ", // U with tilde
        "Ư" => "Ư", // U with horn
        "Ụ" => "Ụ", // U with dot below
        "Ủ" => "Ủ", // U with hook above
        "Ứ" => "Ứ", // U with horn and acute
        "Ừ" => "Ừ", // U with horn and grave
        "Ử" => "Ử", // U with horn and hook above
        "Ữ" => "Ữ", // U with horn and tilde
        "Ự" => "Ự", // U with horn and dot below
        "Ý" => "Ý", // Y with acute
        "Ỳ" => "Ỳ", // Y with grave
        "Ỵ" => "Ỵ", // Y with dot below
        "Ỷ" => "Ỷ", // Y with hook above
        "Ỹ" => "Ỹ", // Y with tilde
	);
	$lcspecialchars = array(
        "à" => "à", // a with grave
        "á" => "á", // a with acute
        "â" => "â", // a with circumflex
        "ã" => "ã", // a with tilde
        "ạ" => "ạ", // a with dot below
        "ả" => "ả", // a with hook above
        "ă" => "ă", // a with breve
        "ấ" => "ấ", // a with circumflex and acute
        "ầ" => "ầ", // a with circumflex and grave
        "ẫ" => "ẫ", // a with circumflex and tilde
        "ậ" => "ậ", // a with circumflex and dot below
        "ắ" => "ắ", // a with breve and acute
        "ằ" => "ằ", // a with breve and grave
        "ẳ" => "ẳ", // a with breve and hook above
        "ẵ" => "ẵ", // a with breve and tilde
        "ặ" => "ặ", // a with breve and dot below
        "đ" => "đ", // d with stroke
        "è" => "è", // e with grave
        "é" => "é", // e with acute
        "ê" => "ê", // e with circumflex
        "ẹ" => "ẹ", // e with dot below
        "ẻ" => "ẻ", // e with hook above
        "ẽ" => "ẽ", // e with tilde
        "ế" => "ế", // e with circumflex and acute
        "ề" => "ề", // e with circumflex and grave
        "ể" => "ể", // e with circumflex and hook above
        "ễ" => "ễ", // e with circumflex and tilde
        "ệ" => "ệ", // e with circumflex and dot below
        "ì" => "ì", // i with grave
        "í" => "í", // i with acute
        "ĩ" => "ĩ", // i with tilde
        "ỉ" => "ỉ", // i with hook above
        "ị" => "ị", // i with dot below
        "ò" => "ò", // o with grave
        "ó" => "ó", // o with acute
        "ô" => "ô", // o with circumflex
        "õ" => "õ", // o with tilde
        "ơ" => "ơ", // o with horn
        "ọ" => "ọ", // o with dot below
        "ỏ" => "ỏ", // o with hook above
        "ố" => "ố", // o with circumflex and acute
        "ồ" => "ồ", // o with circumflex and grave
        "ổ" => "ổ", // o with circumflex and hook above
        "ỗ" => "ỗ", // o with circumflex and tilde
        "ộ" => "ộ", // o with circumflex and dot below
        "ớ" => "ớ", // o with horn and acute
        "ờ" => "ờ", // o with horn and grave
        "ở" => "ở", // o with horn and hook above
        "ỡ" => "ỡ", // o with horn and tilde
        "ợ" => "ợ", // o with horn and dot below
        "ù" => "ù", // u with grave
        "ú" => "ú", // u with acute
        "ũ" => "ũ", // u with tilde
        "ư" => "ư", // u with horn
        "ụ" => "ụ", // u with dot below
        "ủ" => "ủ", // u with hook above
        "ứ" => "ứ", // u with horn and acute
        "ừ" => "ừ", // u with horn and grave
        "ử" => "ử", // u with horn and hook above
        "ữ" => "ữ", // u with horn and tilde
        "ự" => "ự", // u with horn and dot below
        "ý" => "ý", // y with acute
        "ỳ" => "ỳ", // y with grave
        "ỵ" => "ỵ", // y with dot below
        "ỷ" => "ỷ", // y with hook above
        "ỹ" => "ỹ", // y with tilde
	);
break;


default: // list all
   $ucspecialchars = array(
		"&#192;" => "À",
		"&#193;" => "Á",
		"&#194;" => "Â",
		"&#195;" => "Ã",
		"&#196;" => "Ä",
		"&#197;" => "Å",
		"&#260;" => "Ą", // A cedille
		"&#256;" => "Ā", // A with macron
		"&#198;" => "Æ",
		"&#199;" => "Ç",
		"&#268;" => "Č", // C with hachek/caron
		"&#262;" => "Ć", // C with acute
		"&#208;" => "Ð", // eth
		"&#272;" => "Ð", // D with stroke
		"&#270;" => "Ď", // D hachek
		"&#200;" => "È",
		"&#201;" => "É",
		"&#202;" => "Ê",
		"&#203;" => "Ë",
		"&#280;" => "Ę", // E cedille
		"&#282;" => "Ě", // E hachek
		"&#274;" => "Ē", // E with macron
		"&#286;" => "Ğ", // G with breve
		"&#204;" => "Ì",
		"&#205;" => "Í",
		"&#206;" => "Î",
		"&#207;" => "Ï",
		"&#304;" => "İ", // I with dot above
		"&#298;" => "Ī", // I with macron
		"&#306;" => "Ĳ", // ligature IJ
		"&#313;" => "Ĺ", // L acute
		"&#317;" => "Ľ", // L hachek
		"&#321;" => "Ł", // L with stroke
		"&#209;" => "Ñ",
		"&#327;" => "Ň", // N hachek
		"&#323;" => "Ń", // N with acute
		"&#210;" => "Ò",
		"&#211;" => "Ó",
		"&#212;" => "Ô",
		"&#213;" => "Õ",
		"&#214;" => "Ö",
		"&#336;" => "Ő", // O with double acute
		"&#332;" => "Ō", // O with macron
		"&#338;" => "Œ",
		"&#216;" => "Ø",
		"&#340;" => "Ŕ", // R acute
		"&#344;" => "Ř", // R hachek
		"&#352;" => "Š", // S hachek
		"&#346;" => "Ś", // S with acute
		"&#350;" => "Ş", // S with cedilla
		"&#356;" => "Ť", // T hachek
		"&#217;" => "Ù",
		"&#218;" => "Ú",
		"&#219;" => "Û",
		"&#220;" => "Ü",
		"&#366;" => "Ů", // U ring
		"&#368;" => "Ű", // U with double acute
		"&#362;" => "Ū", // U with macron
		"&#221;" => "Ý",
		"&#222;" => "Þ",
		"&#159;" => "Ÿ",
		"&#381;" => "Ž", // Z hachek
		"&#377;" => "Ź", // Z with acute
		"&#379;" => "Ż", // Z with dot above
		"&#x2018;" => "‘", // ‘okina ('okina) - new unicode &#699;
	);
	$lcspecialchars = array(
		"&#224;" => "à",
		"&#225;" => "á",
		"&#226;" => "â",
		"&#227;" => "ã",
		"&#228;" => "ä",
		"&#229;" => "å",
		"&#261;" => "ą", // a cedille
		"&#257;" => "ā", // a with macron
		"&#230;" => "æ",
		"&#231;" => "ç",
		"&#269;" => "č", // c hachek/caron
		"&#263;" => "ć", // c with acute
		"&#271;" => "ď", // d hacheck
		"&#273;" => "đ", // d with stroke
		"&#240;" => "ð",
		"&#232;" => "è",
		"&#233;" => "é",
		"&#234;" => "ê",
		"&#235;" => "ë",
		"&#281;" => "ę", // e cedille
		"&#283;" => "ě", // e hachek
		"&#275;" => "ē", // e with macron
		"&#287;" => "ğ", // g with breve
		"&#236;" => "ì",
		"&#237;" => "í",
		"&#238;" => "î",
		"&#239;" => "ï",
		"&#305;" => "ı", // i without dot above
		"&#299;" => "ī", // i with macron
		"&#307;" => "ĳ", // ligature ij
		"&#314;" => "ĺ", // l acute
		"&#318;" => "ľ", // l hachek
		"&#322;" => "ł", // l with stroke
		"&#241;" => "ñ",
		"&#329;" => "ŉ", // n preceded by apostrophe
		"&#328;" => "ň", // n hachek
		"&#324;" => "ń", // n with acute
		"&#242;" => "ò",
		"&#243;" => "ó",
		"&#244;" => "ô",
		"&#245;" => "õ",
		"&#246;" => "ö",
		"&#337;" => "ő", // o with double acute
		"&#333;" => "ō", // o with macron
		"&#339;" => "œ",
		"&#248;" => "ø",
		"&#341;" => "ŕ", // r acute
		"&#345;" => "ř", // r hachek
		"&#353;" => "š", // s hachek
		"&#347;" => "ś", // s with acute
		"&#351;" => "ş", // s with cedilla
		"&#223;" => "ß",
		"&#357;" => "ť", // t apostrophe
		"&#249;" => "ù",
		"&#250;" => "ú",
		"&#251;" => "û",
		"&#252;" => "ü",
		"&#367;" => "ů", // u ring
		"&#369;" => "ű", // u with double acute
		"&#363;" => "ū", // u with macron
		"&#253;" => "ý",
		"&#254;" => "þ",
		"&#255;" => "ÿ",
		"&#382;" => "ž", // z hachek
		"&#378;" => "ź", // z with acute
		"&#380;" => "ż", // z with dot above
		"&#x2018;" => "‘", // ‘okina ('okina) - new unicode &#699;
	);
}
$otherspecialchars = array(
	"&#161;" => "¡",
	"&#191;" => "¿",
	"&#171;" => "«",
	"&#187;" => "»",
	"&#8224;" => "†",
	"&#8225;" => "‡",
	"&#8734;" => "∞",  // infinity
	"&#247;" => "÷",
	"&#215;" => "×",
	"&#170;" => "ª",  // feminine ordinal (nª)
	"&#186;" => "º",  // masculine ordinal (nº)
	"&#8364;" => "€",
	"&#162;" => "¢",
	"&#163;" => "£",
	"&#165;" => "¥",
	"&#167;" => "§",
	"&#169;" => "©",
	"&#176;" => "°",  // degree symbol
	"&#182;" => "¶",
);
?>
