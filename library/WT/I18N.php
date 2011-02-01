<?php
// Class to support internationalisation (i18n) functionality.
//
// Copyright (C) 2010 Greg Roach
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
//
// @author Greg Roach
// @version $Id$
//
// We use gettext to provide translation.  You should configure xgettext to
// search for:
// translate()
// plural()
//
// We wrap the Zend_Translate gettext library, to allow us to add extra
// functionality, such as mixed RTL and LTR text.

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_I18N {
	static private $locale='';
	static private $dir='';
	static private $list_separator;
	static private $list_separator_last;
	static public  $alphabet;
	static public  $collation;

	// Initialise the translation adapter with a locale setting.
	// If null is passed, work out which language is needed from the environment.
	static public function init($locale=null) {
		$installed_languages=self::installed_languages();
		if (is_null($locale) || !array_key_exists($locale, $installed_languages)) {
			// Automatic locale selection.
			if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $installed_languages)) {
				// Requested in the URL?
				$locale=$_GET['lang'];
				unset($_GET['lang']);
				if (WT_USER_ID) {
					set_user_setting(WT_USER_ID, 'language', $locale);
				}
			} elseif (isset($_SESSION['locale']) && array_key_exists($_SESSION['locale'], $installed_languages)) {
				// Rembered from a previous visit?
				$locale=$_SESSION['locale'];
			} else {
				// Browser preference takes priority over gedcom default
				if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
					$prefs=explode(',', str_replace(' ', '', $_SERVER['HTTP_ACCEPT_LANGUAGE']));
				} else {
					$prefs=array();
				}
				if (WT_GED_ID) {
					// Add the gedcom's default language as a low-priority
					$locale=get_gedcom_setting(WT_GED_ID, 'language');
					if (!array_key_exists($locale, $installed_languages)) {
						$prefs[]=$locale.';q=0.2';
					}
				}
				$prefs2=array();
				foreach ($prefs as $pref) {
					list($l, $q)=explode(';q=', $pref.';q=1.0');
					$l=preg_replace(
						array('/-/', '/_[a-z][a-z]$/e'),
						array ('_', 'strtoupper("$0")'),
						$l
					); // en-gb => en_GB
					$prefs2[$l]=(float)$q;
				}
				// Ensure there is a fallback.
				if (!array_key_exists('en_US', $prefs2)) {
					$prefs2['en_US']=0.01;
				}
				arsort($prefs2);
				foreach (array_keys($prefs2) as $pref) {
					if (array_key_exists($pref, $installed_languages)) {
						$locale=$pref;
						break;
					}
				}
			}
		}
		// We now have a valid locale.  Save it and load it.
		$_SESSION['locale']=$locale;
		$translate=new Zend_Translate('gettext', WT_ROOT.'language/'.$locale.'.mo', $locale);
		// TODO: This is where we would use $translate->addTranslation() to add module translations
		// Make the locale and translation adapter available to the rest of the Zend Framework
		Zend_Registry::set('Zend_Locale',    $locale);
		Zend_Registry::set('Zend_Translate', $translate);

		// Extract language settings from the translation file
		global $DATE_FORMAT; // I18N: This is the format string for full dates.  See http://php.net/date for codes
		$DATE_FORMAT=self::noop('%j %F %Y');
		global $TIME_FORMAT; // I18N: This a the format string for the time-of-day.  See http://php.net/date for codes
		$TIME_FORMAT=self::noop('%g:%i:%s%a');
		global $ALPHABET_upper; // Alphabetic sorting sequence (upper-case letters), used by webtrees to sort strings
		$ALPHABET_upper=self::noop('ALPHABET_upper=ABCDEFGHIJKLMNOPQRSTUVWXYZ');
		list(, $ALPHABET_upper)=explode('=', $ALPHABET_upper);
		global $ALPHABET_lower; // Alphabetic sorting sequence (lower-case letters), used by webtrees to sort strings
		$ALPHABET_lower=self::noop('ALPHABET_lower=abcdefghijklmnopqrstuvwxyz');
		list(, $ALPHABET_lower)=explode('=', $ALPHABET_lower);
		global $WEEK_START; // I18N: This is the first day of the week on calendars. 0=Sunday, 1=Monday...
		$WEEK_START=self::noop('WEEK_START=0');
		list(, $WEEK_START)=explode('=', $WEEK_START);
		global $DICTIONARY_SORT; // I18N: 1=>ignore diacrics when sorting, 0=>letters with diacritics are distinct
		$DICTIONARY_SORT=self::noop('DICTIONARY_SORT=1');
		list(, $DICTIONARY_SORT)=explode('=', $DICTIONARY_SORT);

		global $TEXT_DIRECTION;
		$localeData=Zend_Locale_Data::getList($locale, 'layout');
		$TEXT_DIRECTION=$localeData['characters']=='right-to-left' ? 'rtl' : 'ltr';

		self::$locale=$locale;
		self::$dir=$TEXT_DIRECTION;

		// I18N: This is a space separated list of initial letters for lists of names, etc.  Multi-letter characters are OK, e.g. "A B C CS D DZ DZS E F G GY H ..."  You may use upper/lowers case, such as "D Dz Dzs".
		self::$alphabet=WT_I18N::translate('A B C D E F G H I J K L M N O P Q R S T U V W X Y Z');

		// I18N: This is the name of the MySQL collation that applies to your language.  A list is available at http://dev.mysql.com/doc/refman/5.0/en/charset-unicode-sets.html
		self::$collation=WT_I18N::translate('utf8_unicode_ci');

		// I18N: This is the puncutation symbol used to separate the first items in a list.  e.g. the <comma><space> in "red, green, yellow and blue"
		self::$list_separator=WT_I18N::noop('LANGUAGE_LIST_SEPARATOR');
		// I18N: This is the puncutation symbol used to separate the final items in a list.  e.g. the <space>and<space> in "red, green, yellow and blue"
		self::$list_separator_last=WT_I18N::noop('LANGUAGE_LIST_SEPARATOR_LAST');

		return $locale;
	}

	// Check which languages are installed
	static public function installed_languages() {
		static $installed_languages;
		if (!is_array($installed_languages)) {
			$installed_languages=array();
			$d=opendir(WT_ROOT.'language');
			while (($f=readdir($d))!==false) {
				if (preg_match('/^(([a-z][a-z][a-z]?)(_[A-Z][A-Z])?)\.mo$/', $f, $match)) {
					// launchpad does not support language variants.
					// Until it does, we cannot support languages such as sr@latin
					// See http://zendframework.com/issues/browse/ZF-7485

					// Sort by the transation of the base language, then the variant.
					// e.g. English|British English, Portuguese|Brazilian Portuguese
					$installed_languages[$match[1]]=
						Zend_Locale::getTranslation($match[2], 'language', $match[2]).'|'.
						Zend_Locale::getTranslation($match[1], 'language', $match[1]);
				}
			}
			closedir($d);
			if (empty($installed_languages)) {
				die('There are no lanuages installed.  You must include at least one xx.mo file in /language/');
			}
			// Sort by the combined language/language name...
			uasort($installed_languages, 'utf8_strcasecmp');
			foreach ($installed_languages as &$value) {
				// The locale database doesn't have translations for certain
				// "default" languages, such as zn_CH.
				if (substr($value, -1)=='|') {
					list($value,)=explode('|', $value);
				} else {
					list(,$value)=explode('|', $value);
				}
			}
		}
		return $installed_languages;
	}

	// Generate i18n markup for the <html> tag, e.g lang="ar" dir="RTL"
	static public function html_markup() {
		$localeData=Zend_Locale_Data::getList(self::$locale, 'layout');
		$dir=$localeData['characters']=='right-to-left' ? 'rtl' : 'ltr';
		list($lang)=explode('_', self::$locale);
		return 'lang="'.$lang.'" xml:lang="'.$lang.'" dir="'.$dir.'"';
	}

	// echo WT_I18N::translate('Hello World!');
	// echo WT_I18N::translate('The %s sat on the mat', 'cat');
	static public function translate(/* var_args */) {
		$args=func_get_args();
		$args[0]=Zend_Registry::get('Zend_Translate')->_($args[0]);
		foreach ($args as &$arg) {
			if (is_array($arg)) {
				$arg=WT_I18N::make_list($arg);
			}
		}
		foreach ($args as $n=>&$arg) {
			if ($n) {
				if (is_numeric($arg)) {
					// TODO? Convert latin to, say, arabic digits.
				} else {
					// For each embedded string, if the text-direction is the opposite of the
					// page language, then wrap it in directional indicators.  This will stop
					// weakly-directional characters being displayed in the wrong sequence.
					// We need to use unicode control characters instead of <span dir="rtl">
					// because we must use it in contexts (such as titles, select/options) where
					// markup is not permitted.
					if (self::$dir=='ltr') {
						if (utf8_direction($arg)=='rtl') {
							$arg='&#x202B;'.$arg.'&#x202C;';
						}
					} else {
						if (utf8_direction($arg)=='ltr') {
							$arg='&#x202A;'.$arg.'&#x202C;';
						}
					}
				}
			}
		}
		return call_user_func_array('sprintf', $args);
	}

	// Context sensitive version of translate.
	// echo WT_I18N::translate_c('NOMINATIVE', 'January');
	// echo WT_I18N::translate_c('GENITIVE',   'January');
	static public function translate_c(/* var_args */) {
		$args=func_get_args();
		$msgid=$args[0]."\x04".$args[1];
		$msgtxt=Zend_Registry::get('Zend_Translate')->_($msgid);
		if ($msgtxt==$msgid) {
			$msgtxt=$args[1];
		}
		$args[0]=$msgtxt;
		unset ($args[1]);
		foreach ($args as &$arg) {
			if (is_array($arg)) {
				$arg=WT_I18N::make_list($arg);
			}
		}
		// TODO: for each embedded string, if the text-direction is the opposite of the
		// page language, then wrap it in &ltr; on LTR pages and &rtl; on RTL pages.
		// This will ensure that non/weakly direction characters in the main string
		// are displayed correctly by the browser's BIDI algorithm.
		return call_user_func_array('sprintf', $args);
	}

	// Similar to translate, but do perform "no operation" on it.
	// This is necessary to fetch a format string (containing % characters) without
	// performing sustitution of arguments.
	static public function noop($string) {
		return Zend_Registry::get('Zend_Translate')->_($string);
	}

	// echo WT_I18N::plural('There is an error', 'There are errors', $num_errors);
	// echo WT_I18N::plural('There is one error', 'There are %d errors', $num_errors);
	// echo WT_I18N::plural('There is %1$d %2$s cat', 'There are %1$d %2$s cats', $num, $num, $colour);
	static public function plural(/* var_args */) {
		$args=func_get_args();
		$string=Zend_Registry::get('Zend_Translate')->plural($args[0], $args[1], $args[2]);
		array_splice($args, 0, 3, array($string));
		return call_user_func_array('sprintf', $args);
	}

	// Convert an array to a list.  For example
	// array("red", "green", "yellow", "blue") => "red, green, yellow and blue"
	static public function make_list($array) {
		// TODO: for each array element, if the text-direction is the opposite of the
		// page language, then wrap it in &ltr; on LTR pages and &rtl; on RTL pages.
		// This will ensure that non/weakly direction characters in the main string
		// are displayed correctly by the browser's BIDI algorithm.
		$n=count($array);
		switch ($n) {
		case 0:
			return '';
		case 1:
			return $array[0];
		default:
			return implode(self::$list_separator, array_slice($array, 0, $n-1)).self::$list_separator_last.$array[$n-1];
		}
	}

	// Convert a GEDCOM age string into translated_text
	// NB: The import function will have normalised this, so we don't need
	// to worry about badly formatted strings
	static public function gedcom_age($string) {
		switch ($string) {
		case 'STILLBORN':
			// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (stillborn)
			return WT_I18N::translate('(stillborn)');
		case 'INFANT':
			// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (in infancy)
			return WT_I18N::translate('(in infancy)');
		case 'CHILD':
			// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (in childhood)
			return WT_I18N::translate('(in childhood)');
		}
		$age=array();
		if (preg_match('/(\d+)y/', $string, $match)) {
			// I18N: Part of an age string. e.g 5 years, 4 months and 3 days
			$years=$match[1];
			$age[]=WT_I18N::plural('%d year', '%d years', $years, $years);
		} else {
			$years=-1;
		}
		if (preg_match('/(\d+)m/', $string, $match)) {
			// I18N: Part of an age string. e.g 5 years, 4 months and 3 days
			$age[]=WT_I18N::plural('%d month', '%d months', $match[1], $match[1]);
		}
		if (preg_match('/(\d+)w/', $string, $match)) {
			// I18N: Part of an age string. e.g 7 weeks and 3 days
			$age[]=WT_I18N::plural('%d week', '%d weeks', $match[1], $match[1]);
		}
		if (preg_match('/(\d+)d/', $string, $match)) {
			// I18N: Part of an age string. e.g 5 years, 4 months and 3 days
			$age[]=WT_I18N::plural('%d day', '%d days', $match[1], $match[1]);
		}
		// If an age is just a number of years, only show the number
		if (count($age)==1 && $years>=0) {
			$age=$years;
		}
		if ($age) {
			if (!substr_compare($string, '<', 0, 1)) {
				// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (aged less than 21 years)
				return WT_I18N::translate('(aged less than %s)', $age);
			} elseif (!substr_compare($string, '>', 0, 1)) {
				// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (aged more than 21 years)
				return WT_I18N::translate('(aged more than %s)', $age);
			} else {
				// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (aged 43 years)
				return WT_I18N::translate('(aged %s)', $age);
			}
		} else {
			// Not a valid string?
			return WT_I18N::translate('(aged %s)', $string);
		}
	}

	// century name, English => 21st, Polish => XXI, etc.
	static function century_name($century) {
		if ($century<0) {
			return str_replace(-$century, self::century_name(-$century), self::translate('%d B.C.', -$century));
		}
		switch ($century) {
		case 21: return self::translate_c('CENTURY', '21st');
		case 20: return self::translate_c('CENTURY', '20th');
		case 19: return self::translate_c('CENTURY', '19th');
		case 18: return self::translate_c('CENTURY', '18th');
		case 17: return self::translate_c('CENTURY', '17th');
		case 16: return self::translate_c('CENTURY', '16th');
		case 15: return self::translate_c('CENTURY', '15th');
		case 14: return self::translate_c('CENTURY', '14th');
		case 13: return self::translate_c('CENTURY', '13th');
		case 12: return self::translate_c('CENTURY', '12th');
		case 11: return self::translate_c('CENTURY', '11th');
		case 10: return self::translate_c('CENTURY', '10th');
		case  9: return self::translate_c('CENTURY', '9th');
		case  8: return self::translate_c('CENTURY', '8th');
		case  7: return self::translate_c('CENTURY', '7th');
		case  6: return self::translate_c('CENTURY', '6th');
		case  5: return self::translate_c('CENTURY', '5th');
		case  4: return self::translate_c('CENTURY', '4th');
		case  3: return self::translate_c('CENTURY', '3rd');
		case  2: return self::translate_c('CENTURY', '2nd');
		case  1: return self::translate_c('CENTURY', '1st');
		default: return ($century-1).'01-'.$century.'00';
		}
	}

	// Convert a number of seconds into a relative time.  e.g. 630 => "10 hours, 30 minutes ago"
	static function time_ago($seconds) {
		$year=365*24*60*60;
		$month=30*24*60*60;
		$day=24*60*60;
		$hour=60*60;
		$minute=60;

		// TODO: Display two units (years+months), (months+days), etc.
		// This requires "contexts".  i.e. "%d months" has a different translation
		// in different contexts.
		// We must AVOID combining phrases to make sentences.
		if ($seconds>$year) {
			$years=floor($seconds/$year);
			return WT_I18N::plural('%d year ago', '%d years ago', $years, $years);
		} elseif ($seconds>$month) {
			$months=floor($seconds/$month);
			return WT_I18N::plural('%d month ago', '%d months ago', $months, $months);
		} elseif ($seconds>$day) {
			$days=floor($seconds/$day);
			return WT_I18N::plural('%d day ago', '%d days ago', $days, $days);
		} elseif ($seconds>$hour) {
			$hours=floor($seconds/$hour);
			return WT_I18N::plural('%d hour ago', '%d hours ago', $hours, $hours);
		} elseif ($seconds>$minute) {
			$minutes=floor($seconds/$minute);
			return WT_I18N::plural('%d minute ago', '%d minutes ago', $minutes, $minutes);
		} else {
			return WT_I18N::plural('%d second ago', '%d seconds ago', $seconds, $seconds);
		}
	}
}
