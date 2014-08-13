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

class WT_I18N {
	// Lookup table to convert unicode code-points into scripts.
	// See https://en.wikipedia.org/wiki/Unicode_block
	// Note: we only need details for scripts of languages into which webtrees is translated.
	private static $scripts = array(
		array('Latn', 0x0041, 0x005A), // a-z
		array('Latn', 0x0061, 0x007A), // A-Z
		array('Latn', 0x0100, 0x02AF),
		array('Grek', 0x0370, 0x03FF),
		array('Cyrl', 0x0400, 0x052F),
		array('Hebr', 0x0590, 0x05FF),
		array('Arab', 0x0600, 0x06FF),
		array('Arab', 0x0750, 0x077F),
		array('Arab', 0x08A0, 0x08FF),
		array('Deva', 0x0900, 0x097F),
		array('Taml', 0x0B80, 0x0BFF),
		array('Sinh', 0x0D80, 0x0DFF),
		array('Thai', 0x0E00, 0x0E7F),
		array('Geor', 0x10A0, 0x10FF),
		array('Grek', 0x1F00, 0x1FFF),
		array('Deva', 0xA8E0, 0xA8FF),
		array('Hans', 0x3000, 0x303F), // Mixed CJK, not just Hans
		array('Hans', 0x3400, 0xFAFF), // Mixed CJK, not just Hans
		array('Hans', 0x20000, 0x2FA1F), // Mixed CJK, not just Hans
	);

	public  static $locale;
	public  static $collation;
	public  static $list_separator;
	private static $dir;
	private static $cache;
	private static $numbering_system;
	private static $translation_adapter;

	// Initialise the translation adapter with a locale setting.
	// If null is passed, work out which language is needed from the environment.
	public static function init($locale=null) {
		global $WT_SESSION;

		// The translation libraries only work with a cache.
		$cache_options = array(
			'automatic_serialization' => true,
			'cache_id_prefix'         => md5(WT_SERVER_NAME . WT_SCRIPT_PATH),
		);

		if (ini_get('apc.enabled')) {
			self::$cache = Zend_Cache::factory('Core', 'Apc', $cache_options, array());
		} elseif (WT_File::mkdir(WT_DATA_DIR . 'cache')) {
			self::$cache = Zend_Cache::factory('Core', 'File', $cache_options, array('cache_dir'=>WT_DATA_DIR . 'cache'));
		} else {
			self::$cache = Zend_Cache::factory('Core', 'Zend_Cache_Backend_BlackHole', $cache_options, array(), false, true);
		}

		Zend_Locale::setCache(self::$cache);
		Zend_Translate::setCache(self::$cache);

		$installed_languages=self::installed_languages();
		if (is_null($locale) || !array_key_exists($locale, $installed_languages)) {
			// Automatic locale selection.
			$locale = WT_Filter::get('lang');
			if ($locale && array_key_exists($locale, $installed_languages)) {
				// Requested in the URL?
				if (\WT\Auth::id()) {
					\WT\Auth::user()->setSetting('language', $locale);
				}
			} elseif (array_key_exists($WT_SESSION->locale, $installed_languages)) {
				// Rembered from a previous visit?
				$locale = $WT_SESSION->locale;
			} else {
				// Browser preference takes priority over gedcom default
				if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
					$prefs = explode(',', str_replace(' ', '', $_SERVER['HTTP_ACCEPT_LANGUAGE']));
				} else {
					$prefs = array();
				}
				if (WT_GED_ID) {
					// Add the tree’s default language as a low-priority
					$locale = get_gedcom_setting(WT_GED_ID, 'LANGUAGE');
					$prefs[] = $locale.';q=0.2';
				}
				$prefs2=array();
				foreach ($prefs as $pref) {
					list($l, $q)=explode(';q=', $pref.';q=1.0');
					$l=preg_replace_callback(
						'/_[a-z][a-z]$/',
						function($x) { return strtoupper($x[0]); },
						str_replace('-', '_', $l)
					); // en-gb => en_GB
					if (array_key_exists($l, $prefs2)) {
						$prefs2[$l]=max((float)$q, $prefs2[$l]);
					} else {
						$prefs2[$l]=(float)$q;
					}
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
		self::$translation_adapter = new Zend_Translate('gettext', WT_ROOT.'language/'.$locale.'.mo', $locale);

		// Deprecated - some custom modules use this to add translations
		Zend_Registry::set('Zend_Translate', WT_I18N::$translation_adapter);

		// Load any local user translations
		if (is_dir(WT_DATA_DIR.'language')) {
			if (file_exists(WT_DATA_DIR.'language/'.$locale.'.mo')) {
				WT_I18N::addTranslation(
					new Zend_Translate('gettext', WT_DATA_DIR.'language/'.$locale.'.mo', $locale)
				);
			}
			if (file_exists(WT_DATA_DIR.'language/'.$locale.'.php')) {
				WT_I18N::addTranslation(
					new Zend_Translate('array', WT_DATA_DIR.'language/'.$locale.'.php', $locale)
				);
			}
			if (file_exists(WT_DATA_DIR.'language/'.$locale.'.csv')) {
				WT_I18N::addTranslation(
					new Zend_Translate('csv', WT_DATA_DIR.'language/'.$locale.'.csv', $locale)
				);
			}
		}

		// Extract language settings from the translation file
		global $DATE_FORMAT; // I18N: This is the format string for full dates.  See http://php.net/date for codes
		$DATE_FORMAT=self::noop('%j %F %Y');
		global $TIME_FORMAT; // I18N: This is the format string for the time-of-day.  See http://php.net/date for codes
		$TIME_FORMAT=self::noop('%H:%i:%s');
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
		$TEXT_DIRECTION = WT_I18N::scriptDirection(WT_I18N::languageScript($locale));

		self::$locale=$locale;
		self::$dir=$TEXT_DIRECTION;

		// I18N: This punctuation is used to separate lists of items.
		self::$list_separator=WT_I18N::translate(', ');

		// I18N: This is the name of the MySQL collation that applies to your language.  A list is available at http://dev.mysql.com/doc/refman/5.0/en/charset-unicode-sets.html
		self::$collation=WT_I18N::translate('utf8_unicode_ci');

		// Non-latin numbers may require non-latin digits
		try {
			self::$numbering_system = Zend_Locale_Data::getContent($locale, 'defaultnumberingsystem');
		} catch (Zend_Locale_Exception $ex) {
			// The latest CLDR database omits some languges such as Tatar (tt)
			self::$numbering_system = 'latin';
		}

		return $locale;
	}

	// Add a translation file
	public static function addTranslation(Zend_Translate $translation) {
		WT_I18N::$translation_adapter->addTranslation($translation);
	}

	// Check which languages are installed
	public static function installed_languages() {
		$mo_files=glob(WT_ROOT.'language'.DIRECTORY_SEPARATOR.'*.mo');
		$cache_key=md5(serialize($mo_files));

		if (!($installed_languages=self::$cache->load($cache_key))) {
			$installed_languages=array();
			foreach ($mo_files as $mo_file) {
				if (preg_match('/^(([a-z][a-z][a-z]?)([-_][A-Z][A-Z])?([-_][A-Za-z]+)*)\.mo$/', basename($mo_file), $match)) {
					// Sort by the transation of the base language, then the variant.
					// e.g. English|British English, Portuguese|Brazilian Portuguese
					$tmp1 = WT_I18N::languageName($match[1]);
					if ($match[1]==$match[2]) {
						$tmp2=$tmp1;
					} else {
						$tmp2 = WT_I18N::languageName($match[2]);
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

	// Generate i18n markup for the <html> tag, e.g. lang="ar" dir="rtl"
	public static function html_markup() {
		$localeData=Zend_Locale_Data::getList(self::$locale, 'layout');
		$dir=$localeData['characterOrder']=='right-to-left' ? 'rtl' : 'ltr';
		list($lang) = preg_split('/[-_@]/', self::$locale);
		return 'lang="'.$lang.'" dir="'.$dir.'"';
	}

	// Translate a number into the local representation.  e.g. 12345.67 becomes
	// en: 12,345.67
	// fr: 12 345,67
	// de: 12.345,67
	public static function number($n, $precision=0) {
		// Add "punctuation" and convert digits
		$n=Zend_Locale_Format::toNumber($n, array('locale'=>WT_LOCALE, 'precision'=>$precision));
		$n=self::digits($n);
		return $n;
	}

	// Convert the digits 0-9 into the local script
	// Used for years, etc., where we do not want thousands-separators, decimals, etc.
	public static function digits($n) {
		if (self::$numbering_system != 'latn') {
			return Zend_Locale_Format::convertNumerals($n, 'latn', self::$numbering_system);
		} else {
			return $n;
		}
	}

	// Translate a fraction into a percentage.  e.g. 0.123 becomes
	// en: 12.3%
	// fr: 12,3 %
	// de: 12,3%
	public static function percentage($n, $precision=0) {
		return
			/* I18N: This is a percentage, such as “32.5%”. “%s” is the number, “%%” is the percent symbol.  Some languages require a (non-breaking) space between the two, or a different symbol. */
			WT_I18N::translate('%s%%', self::number($n*100.0, $precision));
	}

	// echo WT_I18N::translate('Hello World!');
	// echo WT_I18N::translate('The %s sat on the mat', 'cat');
	public static function translate(/* var_args */) {
		$args = func_get_args();
		$args[0] = self::$translation_adapter->_($args[0]);

		return call_user_func_array('sprintf', $args);
	}

	// Context sensitive version of translate.
	// echo WT_I18N::translate_c('NOMINATIVE', 'January');
	// echo WT_I18N::translate_c('GENITIVE',   'January');
	public static function translate_c(/* var_args */) {
		$args = func_get_args();
		$msgid = $args[0] . "\x04" . $args[1];
		$msgtxt = self::$translation_adapter->_($msgid);
		if ($msgtxt == $msgid) {
			$msgtxt = $args[1];
		}
		$args[0] = $msgtxt;
		unset ($args[1]);

		return call_user_func_array('sprintf', $args);
	}

	// Similar to translate, but do perform "no operation" on it.
	// This is necessary to fetch a format string (containing % characters) without
	// performing sustitution of arguments.
	public static function noop($string) {
		return self::$translation_adapter->_($string);
	}

	// echo self::plural('There is an error', 'There are errors', $num_errors);
	// echo self::plural('There is one error', 'There are %s errors', $num_errors);
	// echo self::plural('There is %1$d %2$s cat', 'There are %1$d %2$s cats', $num, $num, $colour);
	public static function plural(/* var_args */) {
		$args = func_get_args();
		$string = self::$translation_adapter->plural($args[0], $args[1], $args[2]);
		array_splice($args, 0, 3, array($string));

		return call_user_func_array('sprintf', $args);
	}

	// Convert a GEDCOM age string into translated_text
	// NB: The import function will have normalised this, so we don't need
	// to worry about badly formatted strings
	// NOTE: this function is not yet complete - eventually it will replace get_age_at_event()
	public static function gedcom_age($string) {
		switch ($string) {
		case 'STILLBORN':
			// I18N: Description of an individual’s age at an event.  e.g. Died 14 Jan 1900 (stillborn)
			return WT_I18N::translate('(stillborn)');
		case 'INFANT':
			// I18N: Description of an individual’s age at an event.  e.g. Died 14 Jan 1900 (in infancy)
			return WT_I18N::translate('(in infancy)');
		case 'CHILD':
			// I18N: Description of an individual’s age at an event.  e.g. Died 14 Jan 1900 (in childhood)
			return WT_I18N::translate('(in childhood)');
		}
		$age=array();
		if (preg_match('/(\d+)y/', $string, $match)) {
			// I18N: Part of an age string. e.g. 5 years, 4 months and 3 days
			$years=$match[1];
			$age[]=self::plural('%s year', '%s years', $years, self::number($years));
		} else {
			$years=-1;
		}
		if (preg_match('/(\d+)m/', $string, $match)) {
			// I18N: Part of an age string. e.g. 5 years, 4 months and 3 days
			$age[]=self::plural('%s month', '%s months', $match[1], self::number($match[1]));
		}
		if (preg_match('/(\d+)w/', $string, $match)) {
			// I18N: Part of an age string. e.g. 7 weeks and 3 days
			$age[]=self::plural('%s week', '%s weeks', $match[1], self::number($match[1]));
		}
		if (preg_match('/(\d+)d/', $string, $match)) {
			// I18N: Part of an age string. e.g. 5 years, 4 months and 3 days
			$age[]=self::plural('%s day', '%s days', $match[1], self::number($match[1]));
		}
		// If an age is just a number of years, only show the number
		if (count($age)==1 && $years>=0) {
			$age=$years;
		}
		if ($age) {
			if (!substr_compare($string, '<', 0, 1)) {
				// I18N: Description of an individual’s age at an event.  e.g. Died 14 Jan 1900 (aged less than 21 years)
				return WT_I18N::translate('(aged less than %s)', $age);
			} elseif (!substr_compare($string, '>', 0, 1)) {
				// I18N: Description of an individual’s age at an event.  e.g. Died 14 Jan 1900 (aged more than 21 years)
				return WT_I18N::translate('(aged more than %s)', $age);
			} else {
				// I18N: Description of an individual’s age at an event.  e.g. Died 14 Jan 1900 (aged 43 years)
				return WT_I18N::translate('(aged %s)', $age);
			}
		} else {
			// Not a valid string?
			return WT_I18N::translate('(aged %s)', $string);
		}
	}

	// Convert a number of seconds into a relative time.  e.g. 630 => "10 hours, 30 minutes ago"
	public static function time_ago($seconds) {
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

	// Return the endonym for a given language - as per http://cldr.unicode.org/
	public static function languageName($language) {
		switch (str_replace(array('_', '@'), '-', $language)) {
		case 'af':      return 'Afrikaans';
		case 'ar':      return 'العربية';
		case 'bg':      return 'български';
		case 'bs':      return 'bosanski';
		case 'ca':      return 'català';
		case 'cs':      return 'čeština';
		case 'da':      return 'dansk';
		case 'de':      return 'Deutsch';
		case 'dv':      return 'ދިވެހިބަސް';
		case 'el':      return 'Ελληνικά';
		case 'en':      return 'English';
		case 'en-AU':   return 'Australian English';
		case 'en-GB':   return 'British English';
		case 'en-US':   return 'U.S. English';
		case 'es':      return 'español';
		case 'et':      return 'eesti';
		case 'fa':      return 'فارسی';
		case 'fi':      return 'suomi';
		case 'fo':      return 'føroyskt';
		case 'fr':      return 'français';
		case 'fr-CA':   return 'français canadien';
		case 'gl':      return 'galego';
		case 'haw':     return 'ʻŌlelo Hawaiʻi';
		case 'he':      return 'עברית';
		case 'hr':      return 'hrvatski';
		case 'hu':      return 'magyar';
		case 'id':      return 'Bahasa Indonesia';
		case 'is':      return 'íslenska';
		case 'it':      return 'italiano';
		case 'ja':      return '日本語';
		case 'ka':      return 'ქართული';
		case 'ko':      return '한국어';
		case 'lt':      return 'lietuvių';
		case 'lv':      return 'latviešu';
		case 'mi':      return 'Māori';
		case 'mr':      return 'मराठी';
		case 'ms':      return 'Bahasa Melayu';
		case 'nb':      return 'norsk bokmål';
		case 'ne':      return 'नेपाली';
		case 'nl':      return 'Nederlands';
		case 'nn':      return 'nynorsk';
		case 'oc':      return 'occitan';
		case 'pl':      return 'polski';
		case 'pt':      return 'português';
		case 'pt-BR':   return 'português do Brasil';
		case 'ro':      return 'română';
		case 'ru':      return 'русский';
		case 'sk':      return 'slovenčina';
		case 'sl':      return 'slovenščina';
		case 'sr':      return 'Српски';
		case 'sr-Latn': return 'srpski';
		case 'sv':      return 'svenska';
		case 'ta':      return 'தமிழ்';
		case 'tr':      return 'Türkçe';
		case 'tt':      return 'Татар';
		case 'uk':      return 'українська';
		case 'vi':      return 'Tiếng Việt';
		case 'yi':      return 'ייִדיש';
		case 'zh':      return '中文';
		case 'zh-CN':   return '简体中文'; // Simplified Chinese
		case 'zh-TW':   return '繁體中文'; // Traditional Chinese
		default:
			// Use the PHP/intl library, if it exists
			if (class_exists('\\Locale')) {
				return Locale::getDisplayName($language, $language);
			}
			return $language;
		}
	}

	// Return the script used by a given language
	// The PHP/intl library does not provde this information.
	public static function languageScript($language) {
		switch (str_replace(array('_', '@'), '-', $language)) {
		case 'af':      return 'Latn';
		case 'ar':      return 'Arab';
		case 'bg':      return 'Cyrl';
		case 'bs':      return 'Latn';
		case 'ca':      return 'Latn';
		case 'cs':      return 'Latn';
		case 'da':      return 'Latn';
		case 'de':      return 'Latn';
		case 'dv':      return 'Thaa';
		case 'el':      return 'Grek';
		case 'en':      return 'Latn';
		case 'en-AU':   return 'Latn';
		case 'en-GB':   return 'Latn';
		case 'en-US':   return 'Latn';
		case 'es':      return 'Latn';
		case 'et':      return 'Latn';
		case 'fa':      return 'Arab';
		case 'fi':      return 'Latn';
		case 'fo':      return 'Latn';
		case 'fr':      return 'Latn';
		case 'fr-CA':   return 'Latn';
		case 'gl':      return 'Latn';
		case 'haw':     return 'Latn';
		case 'he':      return 'Hebr';
		case 'hr':      return 'Latn';
		case 'hu':      return 'Latn';
		case 'id':      return 'Latn';
		case 'is':      return 'Latn';
		case 'it':      return 'Latn';
		case 'ja':      return 'Kana';
		case 'ka':      return 'Geor';
		case 'ko':      return 'Kore';
		case 'lt':      return 'Latn';
		case 'lv':      return 'Latn';
		case 'mi':      return 'Latn';
		case 'mr':      return 'Mymr';
		case 'ms':      return 'Latn';
		case 'nb':      return 'Latn';
		case 'ne':      return 'Deva';
		case 'nl':      return 'Latn';
		case 'nn':      return 'Latn';
		case 'oc':      return 'Latn';
		case 'pl':      return 'Latn';
		case 'pt':      return 'Latn';
		case 'pt-BR':   return 'Latn';
		case 'ro':      return 'Latn';
		case 'ru':      return 'Cyrl';
		case 'sk':      return 'Latn';
		case 'sl':      return 'Latn';
		case 'sr':      return 'Cyrl';
		case 'sr-Latn': return 'Latn';
		case 'sv':      return 'Latn';
		case 'ta':      return 'Taml';
		case 'tr':      return 'Latn';
		case 'tt':      return 'Cyrl';
		case 'uk':      return 'Cyrl';
		case 'vi':      return 'Latn';
		case 'yi':      return 'Hebr';
		case 'zh':      return 'Hans';
		case 'zh-CN':   return 'Hans';
		case 'zh-TW':   return 'Hant';
		default:
			return 'Latn';
		}
	}

	// Identify the script used for a piece of text
	public static function textScript($string) {
		$string = strip_tags($string);                               // otherwise HTML tags show up as latin
		$string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');  // otherwise HTML entities show up as latin
		$string = str_replace(array('@N.N.', '@P.N.'), '', $string); // otherwise unknown names show up as latin
		$pos = 0;
		$strlen = strlen($string);
		while ($pos < $strlen) {
			// get the Unicode Code Point for the character at position $pos
			$byte1 = ord($string[$pos]);
			if ($byte1 < 0x80) {
				$code_point = $byte1;
				$chrlen = 1;
			} elseif ($byte1 < 0xC0) {
				// Invalid continuation character
				return 'Latn';
			} elseif ($byte1 < 0xE0) {
				$code_point = (($byte1 & 0x1F) << 6) + (ord($string[$pos + 1]) & 0x3F);
				$chrlen = 2;
			} elseif ($byte1 < 0xF0) {
				$code_point = (($byte1 & 0x0F) << 12) + ((ord($string[$pos + 1]) & 0x3F) << 6) + (ord($string[$pos + 2]) & 0x3F);
				$chrlen = 3;
			} elseif ($byte1 < 0xF8) {
				$code_point = (($byte1 & 0x07) << 24) + ((ord($string[$pos + 1]) & 0x3F) << 12) + ((ord($string[$pos + 2]) & 0x3F) << 6) + (ord($string[$pos + 3]) & 0x3F);
				$chrlen = 3;
 			} else {
				// Invalid UTF
				return 'Latn';
			}

			foreach (self::$scripts as $range) {
				if ($code_point >= $range[1] && $code_point <= $range[2]) {
					return $range[0];
				}
			}
			// Not a recognised script.  Maybe punctuation, spacing, etc.  Keep looking.
			$pos += $chrlen;
		}

		return 'Latn';
	}

	// Return the direction (ltr or rtl) for a given script
	// The PHP/intl library does not provde this information.
	public static function scriptDirection($script) {
		switch ($script) {
		case 'Arab':
		case 'Hebr':
		case 'Mong':
		case 'Thaa':
			return 'rtl';
		default:
			return 'ltr';
		}
	}

	// Generate consistent I18N for datatables.js
	public static function datatablesI18N(array $lengths=null) {
		if ($lengths===null) {
			$lengths=array(10, 20, 30, 50, 100, -1);
		}

		$length_menu='';
		foreach ($lengths as $length) {
			$length_menu.=
				'<option value="'.$length.'">'.
				($length==-1 ? /* I18N: listbox option, e.g. “10,25,50,100,all” */ WT_I18N::translate('All') : self::number($length)).
				'</option>';
		}
		$length_menu='<select>'.$length_menu.'</select>';
		$length_menu=/* I18N: Display %s [records per page], %s is a placeholder for listbox containing numeric options */ WT_I18N::translate('Display %s', $length_menu);

		// Which symbol is used for separating numbers into groups
		$symbols = Zend_Locale_Data::getList(self::$locale, 'symbols');
		// Which digits are used for numbers
		$digits = Zend_Locale_Data::getContent(self::$locale, 'numberingsystem', self::$numbering_system);

		if ($digits=='0123456789') {
			$callback='';
		} else {
			$callback=',
				"infoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {
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
				"formatNumber": function(iIn) {
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
			'"language": {'.
			' "paginate": {'.
			'  "first":    "'./* I18N: button label, first page    */ WT_I18N::translate('first').'",'.
			'  "last":     "'./* I18N: button label, last page     */ WT_I18N::translate('last').'",'.
			'  "next":     "'./* I18N: button label, next page     */ WT_I18N::translate('next').'",'.
			'  "previous": "'./* I18N: button label, previous page */ WT_I18N::translate('previous').'"'.
			' },'.
			' "emptyTable":     "'.WT_I18N::translate('No records to display').'",'.
			' "info":           "'./* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_').'",'.
			' "infoEmpty":      "'.WT_I18N::translate('Showing %1$s to %2$s of %3$s', 0, 0, 0).'",'.
			' "infoFiltered":   "'./* I18N: %s is a placeholder for a number */ WT_I18N::translate('(filtered from %s total entries)', '_MAX_').'",'.
			' "infoPostfix":    "",'.
			' "infoThousands":  "'.$symbols['group'].'",'.
			' "lengthMenu":     "'.WT_Filter::escapeJs($length_menu).'",'.
			' "loadingRecords": "'.WT_I18N::translate('Loading…').'",'.
			' "processing":     "'.WT_I18N::translate('Loading…').'",'.
			' "search":         "'.WT_I18N::translate('Filter').'",'.
			' "url":            "",'.
			' "zeroRecords":    "'.WT_I18N::translate('No records to display').'"'.
			'}'.
			$callback;
	}
}
