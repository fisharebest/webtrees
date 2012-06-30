<?php
// Class to support internationalisation (i18n) functionality.
//
// We use gettext to provide translation.  You should configure xgettext to
// search for:
// translate()
// plural()
//
// We wrap the Zend_Translate gettext library, to allow us to add extra
// functionality, such as mixed RTL and LTR text.
//
// Copyright (C) 2012 Greg Roach
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
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_I18N {
	static public  $locale='';
	static private $dir='';
	static public  $collation;
	static public  $list_separator;
	static private $cache=null;

	// Initialise the translation adapter with a locale setting.
	// If null is passed, work out which language is needed from the environment.
	static public function init($locale=null) {
		global $WT_SESSION;

		// The translation libraries only work with a cache.
		$cache_options=array('automatic_serialization'=>true);

		if (ini_get('apc.enabled')) {
			self::$cache=Zend_Cache::factory('Core', 'Apc', $cache_options, array());
		} else {
			if (!is_dir(WT_DATA_DIR.DIRECTORY_SEPARATOR.'cache')) {
				// We may not have permission - especially during setup, before we instruct
				// the user to "chmod 777 /data"
				@mkdir(WT_DATA_DIR.DIRECTORY_SEPARATOR.'cache');
			}
			if (is_dir(WT_DATA_DIR.DIRECTORY_SEPARATOR.'cache')) {
				self::$cache=Zend_Cache::factory('Core', 'File', $cache_options, array('cache_dir'=>WT_DATA_DIR.DIRECTORY_SEPARATOR.'cache'));
			} else {
				// No cache available :-(
				self::$cache=Zend_Cache::factory('Core', 'Zend_Cache_Backend_BlackHole', $cache_options, array(), false, true);
			}
		}

		// If we created a cache, use it.
		if (self::$cache) {
			Zend_Locale::setCache(self::$cache);
			Zend_Translate::setCache(self::$cache);
		}

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
			} elseif (array_key_exists($WT_SESSION->locale, $installed_languages)) {
				// Rembered from a previous visit?
				$locale=$WT_SESSION->locale;
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
		// Load the translation file
		$translate=new Zend_Translate('gettext', WT_ROOT.'language/'.$locale.'.mo', $locale);

		// Make the locale and translation adapter available to the rest of the Zend Framework
		Zend_Registry::set('Zend_Locale',    $locale);
		Zend_Registry::set('Zend_Translate', $translate);

		// Load any local user translations
		if (is_dir(WT_DATA_DIR.'language')) {
			if (file_exists(WT_DATA_DIR.'language/'.$locale.'.mo')) {
				$translate->addTranslation(
					new Zend_Translate('gettext', WT_DATA_DIR.'language/'.$locale.'.mo', $locale)
				);
			}
			if (file_exists(WT_DATA_DIR.'language/'.$locale.'.php')) {
				$translate->addTranslation(
					new Zend_Translate('array', WT_DATA_DIR.'language/'.$locale.'.php', $locale)
				);
			}
			if (file_exists(WT_DATA_DIR.'language/'.$locale.'.csv')) {
				$translate->addTranslation(
					new Zend_Translate('csv', WT_DATA_DIR.'language/'.$locale.'.csv', $locale)
				);
			}
		}

		// Extract language settings from the translation file
		global $DATE_FORMAT; // I18N: This is the format string for full dates.  See http://php.net/date for codes
		$DATE_FORMAT=self::noop('%j %F %Y');
		global $TIME_FORMAT; // I18N: This a the format string for the time-of-day.  See http://php.net/date for codes
		$TIME_FORMAT=self::noop('%g:%i:%s %a');
		global $ALPHABET_upper; // Alphabetic sorting sequence (upper-case letters), used by webtrees to sort strings
		$ALPHABET_upper=self::noop('ALPHABET_upper=ABCDEFGHIJKLMNOPQRSTUVWXYZ');
		list(, $ALPHABET_upper)=explode('=', $ALPHABET_upper);
		global $ALPHABET_lower; // Alphabetic sorting sequence (lower-case letters), used by webtrees to sort strings
		$ALPHABET_lower=self::noop('ALPHABET_lower=abcdefghijklmnopqrstuvwxyz');
		list(, $ALPHABET_lower)=explode('=', $ALPHABET_lower);
		global $WEEK_START; // I18N: This is the first day of the week on calendars. 0=Sunday, 1=Monday...
		$WEEK_START=self::noop('WEEK_START=0');
		list(, $WEEK_START)=explode('=', $WEEK_START);

		global $TEXT_DIRECTION;
		$localeData=Zend_Locale_Data::getList($locale, 'layout');
		$TEXT_DIRECTION=$localeData['characters']=='right-to-left' ? 'rtl' : 'ltr';

		self::$locale=$locale;
		self::$dir=$TEXT_DIRECTION;

		// I18N: This punctuation is used to separate lists of items.
		self::$list_separator=self::translate(', ');

		// I18N: This is the name of the MySQL collation that applies to your language.  A list is available at http://dev.mysql.com/doc/refman/5.0/en/charset-unicode-sets.html
		self::$collation=self::translate('utf8_unicode_ci');

		return $locale;
	}

	// Check which languages are installed
	static public function installed_languages() {
		$mo_files=glob(WT_ROOT.'language'.DIRECTORY_SEPARATOR.'*.mo');
		$cache_key=md5(serialize($mo_files));

		if (!($installed_languages=self::$cache->load($cache_key))) {
			$installed_languages=array();
			foreach ($mo_files as $mo_file) {
				if (preg_match('/^(([a-z][a-z][a-z]?)(_[A-Z][A-Z])?)\.mo$/', basename($mo_file), $match)) {
					// launchpad does not support language variants.
					// Until it does, we cannot support languages such as sr@latin
					// See http://zendframework.com/issues/browse/ZF-7485

					// Sort by the transation of the base language, then the variant.
					// e.g. English|British English, Portuguese|Brazilian Portuguese
					$tmp1=Zend_Locale::getTranslation($match[1], 'language', $match[1]);
					if ($match[1]==$match[2]) {
						$tmp2=$tmp1;
					} else {
						$tmp2=Zend_Locale::getTranslation($match[2], 'language', $match[2]);
					}
					$installed_languages[$match[1]]=$tmp2.'|'.$tmp1;
				}
			}
			if (empty($installed_languages)) {
				// We cannot translate this
				die('There are no languages installed.  You must include at least one xx.mo file in /language/');
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
			self::$cache->save($installed_languages, $cache_key);
		}
		return $installed_languages;
	}

	// Generate i18n markup for the <html> tag, e.g lang="ar" dir="RTL"
	static public function html_markup() {
		$localeData=Zend_Locale_Data::getList(self::$locale, 'layout');
		$dir=$localeData['characters']=='right-to-left' ? 'rtl' : 'ltr';
		list($lang)=explode('_', self::$locale);
		return 'lang="'.$lang.'" dir="'.$dir.'"';
	}

	// Translate a number into the local representation.  e.g. 12345.67 becomes
	// en: 12,345.67
	// fr: 12 345,67
	// de: 12.345,67
	static public function number($n, $precision=0) {
		// Add "punctuation" and convert digits
		$n=Zend_Locale_Format::toNumber($n, array('locale'=>WT_LOCALE, 'precision'=>$precision));
		$n=self::digits($n);
		return $n;
	}
	// Convert the digits 0-9 into the local script
	// Used for years, etc., where we do not want thousands-separators, decimals, etc.
	static public function digits($n) {
		if (WT_NUMBERING_SYSTEM!='latn') {
			return Zend_Locale_Format::convertNumerals($n, 'latn', WT_NUMBERING_SYSTEM);
		} else {
			return $n;
		}
	}

	// Translate a fraction into a percentage.  e.g. 0.123 becomes
	// en: 12.3%
	// fr: 12,3 %
	// de: 12,3%
	static public function percentage($n, $precision=0) {
		return
			/* I18N: This is a percentage, such as "32.5%". "%s" is the number, "%%" is the percent symbol.  Some languages require a (non-breaking) space between the two, or a different symbol. */
			self::translate('%s%%', self::number($n*100.0, $precision));
	}

	// echo self::translate('Hello World!');
	// echo self::translate('The %s sat on the mat', 'cat');
	static public function translate(/* var_args */) {
		$args=func_get_args();
		if (WT_DEBUG_LANG) {
			$args[0]=WT_Debug::pseudoTranslate($args[0]);
		} else {
			$args[0]=Zend_Registry::get('Zend_Translate')->_($args[0]);
		}
		return call_user_func_array('sprintf', $args);
	}

	// Context sensitive version of translate.
	// echo self::translate_c('NOMINATIVE', 'January');
	// echo self::translate_c('GENITIVE',   'January');
	static public function translate_c(/* var_args */) {
		$args=func_get_args();
		if (WT_DEBUG_LANG) {
			$msgtxt=WT_Debug::pseudoTranslate($args[1]);
		} else {
			$msgid=$args[0]."\x04".$args[1];
			$msgtxt=Zend_Registry::get('Zend_Translate')->_($msgid);
			if ($msgtxt==$msgid) {
				$msgtxt=$args[1];
			}
		}
		$args[0]=$msgtxt;
		unset ($args[1]);
		return call_user_func_array('sprintf', $args);
	}

	// Similar to translate, but do perform "no operation" on it.
	// This is necessary to fetch a format string (containing % characters) without
	// performing sustitution of arguments.
	static public function noop($string) {
		return Zend_Registry::get('Zend_Translate')->_($string);
	}

	// echo self::plural('There is an error', 'There are errors', $num_errors);
	// echo self::plural('There is one error', 'There are %s errors', $num_errors);
	// echo self::plural('There is %1$d %2$s cat', 'There are %1$d %2$s cats', $num, $num, $colour);
	static public function plural(/* var_args */) {
		$args=func_get_args();
		if (WT_DEBUG_LANG) {
			if ($args[2]==1) {
				$string=WT_Debug::pseudoTranslate($args[0]);
			} else {
				$string=WT_Debug::pseudoTranslate($args[1]);
			}
		} else {
			$string=Zend_Registry::get('Zend_Translate')->plural($args[0], $args[1], $args[2]);
		}
		array_splice($args, 0, 3, array($string));
		return call_user_func_array('sprintf', $args);
	}

	// Convert a GEDCOM age string into translated_text
	// NB: The import function will have normalised this, so we don't need
	// to worry about badly formatted strings
	// NOTE: this function is not yet complete - eventually it will replace get_age_at_event()
	static public function gedcom_age($string) {
		switch ($string) {
		case 'STILLBORN':
			// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (stillborn)
			return self::translate('(stillborn)');
		case 'INFANT':
			// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (in infancy)
			return self::translate('(in infancy)');
		case 'CHILD':
			// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (in childhood)
			return self::translate('(in childhood)');
		}
		$age=array();
		if (preg_match('/(\d+)y/', $string, $match)) {
			// I18N: Part of an age string. e.g 5 years, 4 months and 3 days
			$years=$match[1];
			$age[]=self::plural('%s year', '%s years', $years, self::number($years));
		} else {
			$years=-1;
		}
		if (preg_match('/(\d+)m/', $string, $match)) {
			// I18N: Part of an age string. e.g 5 years, 4 months and 3 days
			$age[]=self::plural('%s month', '%s months', $match[1], self::number($match[1]));
		}
		if (preg_match('/(\d+)w/', $string, $match)) {
			// I18N: Part of an age string. e.g 7 weeks and 3 days
			$age[]=self::plural('%s week', '%s weeks', $match[1], self::number($match[1]));
		}
		if (preg_match('/(\d+)d/', $string, $match)) {
			// I18N: Part of an age string. e.g 5 years, 4 months and 3 days
			$age[]=self::plural('%s day', '%s days', $match[1], self::number($match[1]));
		}
		// If an age is just a number of years, only show the number
		if (count($age)==1 && $years>=0) {
			$age=$years;
		}
		if ($age) {
			if (!substr_compare($string, '<', 0, 1)) {
				// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (aged less than 21 years)
				return self::translate('(aged less than %s)', $age);
			} elseif (!substr_compare($string, '>', 0, 1)) {
				// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (aged more than 21 years)
				return self::translate('(aged more than %s)', $age);
			} else {
				// I18N: Description of someone's age at an event.  e.g Died 14 Jan 1900 (aged 43 years)
				return self::translate('(aged %s)', $age);
			}
		} else {
			// Not a valid string?
			return self::translate('(aged %s)', $string);
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
		// This requires "contexts".  i.e. "%s months" has a different translation
		// in different contexts.
		// We must AVOID combining phrases to make sentences.
		if ($seconds>$year) {
			$years=(int)($seconds/$year);
			return self::plural('%s year ago', '%s years ago', $years, self::number($years));
		} elseif ($seconds>$month) {
			$months=(int)($seconds/$month);
			return self::plural('%s month ago', '%s months ago', $months, self::number($months));
		} elseif ($seconds>$day) {
			$days=(int)($seconds/$day);
			return self::plural('%s day ago', '%s days ago', $days, self::number($days));
		} elseif ($seconds>$hour) {
			$hours=(int)($seconds/$hour);
			return self::plural('%s hour ago', '%s hours ago', $hours, self::number($hours));
		} elseif ($seconds>$minute) {
			$minutes=(int)($seconds/$minute);
			return self::plural('%s minute ago', '%s minutes ago', $minutes, self::number($minutes));
		} else {
			return self::plural('%s second ago', '%s seconds ago', $seconds, self::number($seconds));
		}
	}

	// Generate consistent I18N for datatables.js
	static function datatablesI18N(array $lengths=null) {
		if ($lengths===null) {
			$lengths=array(10, 20, 30, 50, 100, -1);
		}

		$length_menu='';
		foreach ($lengths as $length) {
			$length_menu.=
				'<option value="'.$length.'">'.
				($length==-1 ? /* I18N: listbox option, e.g. "10,25,50,100,all" */ self::translate('All') : self::number($length)).
				'</option>';
		}
		$length_menu='<select>'.$length_menu.'</select>';
		$length_menu=/* I18N: Display %s [records per page], %s is a placeholder for listbox containing numeric options */ self::translate('Display %s', $length_menu);

		// Which symbol is used for separating numbers into groups
		$symbols = Zend_Locale_Data::getList(WT_LOCALE, 'symbols');
		// Which digits are used for numbers
		$numbering_system=Zend_Locale_Data::getContent(WT_LOCALE, 'defaultnumberingsystem');
		$digits=Zend_Locale_Data::getContent(WT_LOCALE, 'numberingsystem', $numbering_system);

		if ($digits=='0123456789') {
			$callback='';
		} else {
			$callback=',
				"fnInfoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {
					return sPre
						.replace(/0/g, "'.utf8_substr($digits, 0, 1).'")
						.replace(/1/g, "'.utf8_substr($digits, 1, 1).'")
						.replace(/2/g, "'.utf8_substr($digits, 2, 1).'")
						.replace(/3/g, "'.utf8_substr($digits, 3, 1).'")
						.replace(/4/g, "'.utf8_substr($digits, 4, 1).'")
						.replace(/5/g, "'.utf8_substr($digits, 5, 1).'")
						.replace(/6/g, "'.utf8_substr($digits, 6, 1).'")
						.replace(/7/g, "'.utf8_substr($digits, 7, 1).'")
						.replace(/8/g, "'.utf8_substr($digits, 8, 1).'")
						.replace(/9/g, "'.utf8_substr($digits, 9, 1).'");
    			},
				"fnFormatNumber": function(iIn) {
					return String(iIn)
						.replace(/0/g, "'.utf8_substr($digits, 0, 1).'")
						.replace(/1/g, "'.utf8_substr($digits, 1, 1).'")
						.replace(/2/g, "'.utf8_substr($digits, 2, 1).'")
						.replace(/3/g, "'.utf8_substr($digits, 3, 1).'")
						.replace(/4/g, "'.utf8_substr($digits, 4, 1).'")
						.replace(/5/g, "'.utf8_substr($digits, 5, 1).'")
						.replace(/6/g, "'.utf8_substr($digits, 6, 1).'")
						.replace(/7/g, "'.utf8_substr($digits, 7, 1).'")
						.replace(/8/g, "'.utf8_substr($digits, 8, 1).'")
						.replace(/9/g, "'.utf8_substr($digits, 9, 1).'");
    			}
			';
		}

		return
			'"oLanguage": {'.
			' "oPaginate": {'.
			'  "sFirst":    "'./* I18N: button label, first page    */ self::translate('first').'",'.
			'  "sLast":     "'./* I18N: button label, last page     */ self::translate('last').'",'.
			'  "sNext":     "'./* I18N: button label, next page     */ self::translate('next').'",'.
			'  "sPrevious": "'./* I18N: button label, previous page */ self::translate('previous').'"'.
			' },'.
			' "sEmptyTable":     "'.self::translate('No records to display').'",'.
			' "sInfo":           "'./* I18N: %s are placeholders for numbers */ self::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_').'",'.
			' "sInfoEmpty":      "'.self::translate('Showing %1$s to %2$s of %3$s', 0, 0, 0).'",'.
			' "sInfoFiltered":   "'./* I18N: %s is a placeholder for a number */ self::translate('(filtered from %s total entries)', '_MAX_').'",'.
			' "sInfoPostfix":    "",'.
			' "sInfoThousands":  "'.$symbols['group'].'",'.
			' "sLengthMenu":     "'.addslashes($length_menu).'",'.
			' "sLoadingRecords": "'.self::translate('Loading...').'",'.
			' "sProcessing":     "'.self::translate('Loading...').'",'.
			' "sSearch":         "'.self::translate('Filter').'",'.
			' "sUrl":            "",'.
			' "sZeroRecords":    "'.self::translate('No records to display').'"'.
			'}'.
			$callback;
	}
}
