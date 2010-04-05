<?php
/**
 * Class to support internationalisation (i18n) functionality.
 *
 * Copyright (C) 2010 Greg Roach
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
 * @author Greg Roach
 * @version $Id$
 *
 * We use gettext to provide translation.  You should configure xgettext to
 * search for:
 * translate()
 * plural()
 *
 * We wrap the Zend_Translate gettext library, to allow us to add extra
 * functionality, such as mixed RTL and LTR text.
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLASS_I18N_PHP', '');

class i18n {
	static private $locale='';
	static private $dir='';
	static private $list_separator;
	static private $list_separator_last;

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
					// TODO: this value isn't currently stored in the DB!
					$locale=get_gedcom_setting(WT_GED_ID, 'language');
					if (array_key_exists($locale, $installed_languages)) {
						$prefs[]=$locale.';q=0.2';
					}
				}
				$prefs2=array();
				foreach ($prefs as $pref) {
					list($l, $q)=explode(';q=', $pref.';q=1.0');
					$prefs2[$l]=(float)$q;
				}
				// Ensure there is a fallback.  en is always available
				if (!array_key_exists('en', $prefs2)) {
					$prefs2['en']=0.0;
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
		global $MULTI_LETTER_ALPHABET; // I18N: semicolon separated list of digraphs
		$MULTI_LETTER_ALPHABET=self::noop('MULTI_LETTER_ALPHABET=');
		list(, $MULTI_LETTER_ALPHABET)=explode('=', $MULTI_LETTER_ALPHABET);
		global $DICTIONARY_SORT; // I18N: 1=>ignore diacrics when sorting, 0=>letters with diacritics are distinct
		$DICTIONARY_SORT=self::noop('DICTIONARY_SORT=1');
		list(, $DICTIONARY_SORT)=explode('=', $DICTIONARY_SORT);

		global $TEXT_DIRECTION;
		$localeData=Zend_Locale_Data::getList($locale, 'layout');
		$TEXT_DIRECTION=$localeData['characters']=='right-to-left' ? 'rtl' : 'ltr';

		self::$locale=$locale;
		self::$dir=$TEXT_DIRECTION;


		// I18N: This is the puncutation symbol used to separate the first items in a list.  e.g. the <comma><space> in "red, green, yellow and blue"
		self::$list_separator=i18n::noop('LANGUAGE_LIST_SEPARATOR');
		// I18N: This is the puncutation symbol used to separate the final items in a list.  e.g. the <space>and<space> in "red, green, yellow and blue"
		self::$list_separator_last=i18n::noop('LANGUAGE_LIST_SEPARATOR_LAST');

		return $locale;
	}

	// Check which languages are installed
	static public function installed_languages() {
		if (isset($_SESSION['installed_languages'])) {
			return $_SESSION['installed_languages'];
		} else {
			$_SESSION['installed_languages']=array();
			$d=opendir(WT_ROOT.'language');
			while (($f=readdir($d))!==false) {
				if (preg_match('/^([a-z][a-z][a-z]?(@[a-z]+|_[A-Z][A-Z])?)\.mo$/', $f, $match)) {
					// TODO: gettext() and ZF use different standards for locale names :-(
					if ($match[1]=='sr@latin' || $match[1]=='zh_CN') {
						// TODO:
						continue;
					}
					$_SESSION['installed_languages'][$match[1]]=Zend_Locale::getTranslation($match[1], 'language', $match[1]);
				}
			}
			closedir($d);
			if (empty($_SESSION['installed_languages'])) {
				die('There are no lanuages installed.  You must include at least one xx.mo file in /language/');
			}
			uasort($_SESSION['installed_languages'], 'utf8_strcasecmp');
			return $_SESSION['installed_languages'];
		}
	}

	// Generate i18n markup for the <html> tag, e.g lang="ar" dir="RTL"
	static public function html_markup() {
		$localeData=Zend_Locale_Data::getList(self::$locale, 'layout');
		$dir=$localeData['characters']=='right-to-left' ? 'RTL' : 'LTR';
		list($lang)=explode('_', self::$locale);
		return 'lang="'.$lang.'" xml:lang="'.$lang.'" dir="'.$dir.'"';
	}

	// echo i18n::translate('Hello World!');
	// echo i18n::translate('The %s sat on the mat', 'cat');
	static public function translate(/* var_args */) {
		$args=func_get_args();
		$args[0]=Zend_Registry::get('Zend_Translate')->_($args[0]);
		foreach ($args as &$arg) {
			if (is_array($arg)) {
				$arg=i18n::make_list($arg);
			}
		}
		// TODO: for each embedded string, if the text-direction is the opposite of the
		// page language, then wrap it in &ltr; on LTR pages and &rtl; on RTL pages.
		// This will ensure that non/weakly direction characters in the main string
		// are displayed correctly by the browser's BIDI algorithm.
		return call_user_func_array('sprintf', $args);
	}

	// Context sensitive version of translate.
	// echo i18n::translate_c('NOMINATIVE', 'January');
	// echo i18n::translate_c('GENITIVE',   'January');
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
				$arg=i18n::make_list($arg);
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

	// echo i18n::plural('There is an error', 'There are errors', $num_errors);
	// echo i18n::plural('There is one error', 'There are %d errors', $num_errors);
	// echo i18n::plural('There is %$1d %$2s cat', 'There are %$1d %$2s cats', $num, $num, $colour);
	static public function plural(/* var_args */) {
		$args=func_get_args();
		$string=Zend_Registry::get('Zend_Translate')->plural($args[0], $args[1], $args[2]);
		array_splice($args, 0, 3, array($string));
		return call_user_func_array('sprintf', $args);
	}

	// These two functions are deprecated.
	static public function is_translated($string) {
		return i18n::translate($string)!=$string;
	}
	static public function is_not_translated($string) {
		return i18n::translate($string)==$string;
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
			return $array(0);
		default:
			return implode(self::$list_separator, array_slice($array, 0, $n-1)).self::$list_separator_last.$array[$n-1];
		}
	}

	// Provide a (one letter) abbreviation of a fact name for charts, etc.
	static public function fact_abbreviation($fact) {
		$abbrev='ABBREV_'.$fact;
		if (i18n::is_translated($abbrev)) {
			return i18n::translate($abbrev);
		} else {
			// Just use the first letter of the full fact
			return utf8_substr(i18n::translate($fact), 0, 1);
		}
	}

	// Convert a GEDCOM age string into translated_text
	// NB: The import function will have normalised this, so we don't need
	// to worry about badly formatted strings
	static public function gedcom_age($string) {
		switch ($string) {
		case 'STILLBORN':
			// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (stillborn)
			return i18n::translate('(stillborn)');
		case 'INFANT':
			// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (in infancy)
			return i18n::translate('(in infancy)');
		case 'CHILD':
			// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (in childhood)
			return i18n::translate('(in childhood)');
		}
		$age=array();
		if (preg_match('/(\d+)y/', $string, $match)) {
			// I18N: Part of an age string. e.g 5 years, 4 months and 3 days
			$years=$match[1];
			$age[]=i18n::plural('%d year', '%d years', $years, $years);
		} else {
			$years=-1;
		}
		if (preg_match('/(\d+)m/', $string, $match)) {
			// I18N: Part of an age string. e.g 5 years, 4 months and 3 days
			$age[]=i18n::plural('%d month', '%d months', $match[1], $match[1]);
		}
		if (preg_match('/(\d+)w/', $string, $match)) {
			// I18N: Part of an age string. e.g 7 weeks and 3 days
			$age[]=i18n::plural('%d week', '%d weeks', $match[1], $match[1]);
		}
		if (preg_match('/(\d+)d/', $string, $match)) {
			// I18N: Part of an age string. e.g 5 years, 4 months and 3 days
			$age[]=i18n::plural('%d day', '%d days', $match[1], $match[1]);
		}
		// If an age is just a number of years, only show the number
		if (count($age)==1 && $years>=0) {
			$age=$years;
		}
		if ($age) {
			if (!substr_compare($string, '<', 0, 1)) {
				// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (aged less than 21 years)
				return i18n::translate('(aged less than %s)', $age);
			} elseif (!substr_compare($string, '>', 0, 1)) {
				// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (aged more than 21 years)
				return i18n::translate('(aged more than %s)', $age);
			} else {
				// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (aged 43 years)			
				return i18n::translate('(aged %s)', $age);
			}
		} else {
			// Not a valid string?
			return i18n::translate('(aged %s)', $string);
		}
	}

	// 5=>fifth, 9=>ninth, etc.  Used for Nth cousins, etc.
	static function ordinal_word($n) {
		switch ($n) {
		case 1:  return i18n::translate('first');
		case 2:  return i18n::translate('second');
		case 3:  return i18n::translate('third');
		case 4:  return i18n::translate('fourth');
		case 5:  return i18n::translate('fifth');
		case 6:  return i18n::translate('sixth');
		case 7:  return i18n::translate('seventh');
		case 8:  return i18n::translate('eighth');
		case 9:  return i18n::translate('ninth');
		case 10: return i18n::translate('tenth');
		case 11: return i18n::translate('eleventh');
		case 12: return i18n::translate('twelfth');
		case 13: return i18n::translate('thirteenth');
		case 14: return i18n::translate('fourteenth');
		case 15: return i18n::translate('fifteenth');
		default: return /* I18N: Generalisation of first, second, third, ... */i18n::translate('%d x', $n);
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
			return i18n::plural('%d year ago', '%d years ago', $years, $years);
		} elseif ($seconds>$month) {
			$months=floor($seconds/$month);
			return i18n::plural('%d month ago', '%d months ago', $months, $months);
		} elseif ($seconds>$day) {
			$days=floor($seconds/$day);
			return i18n::plural('%d day ago', '%d days ago', $days, $days);
		} elseif ($seconds>$hour) {
			$hours=floor($seconds/$hour);
			return i18n::plural('%d hour ago', '%d hours ago', $hours, $hours);
		} elseif ($seconds>$minute) {
			$minutes=floor($seconds/$minute);
			return i18n::plural('%d minute ago', '%d minutes ago', $minutes, $minutes);
		} else {
			return i18n::plural('%d second ago', '%d seconds ago', $seconds, $seconds);
		}
	}
}
