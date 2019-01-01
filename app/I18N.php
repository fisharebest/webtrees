<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Collator;
use Exception;
use Fisharebest\Localization\Locale;
use Fisharebest\Localization\Locale\LocaleEnUs;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Localization\Translation;
use Fisharebest\Localization\Translator;
use Fisharebest\Webtrees\Functions\FunctionsEdit;

/**
 * Internationalization (i18n) and localization (l10n).
 */
class I18N
{
    /** @var LocaleInterface The current locale (e.g. LocaleEnGb) */
    private static $locale;

    /** @var Translator An object that performs translation */
    private static $translator;

    /** @var  Collator|null From the php-intl library */
    private static $collator;

    // Digits are always rendered LTR, even in RTL text.
    const DIGITS = '0123456789٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹';

    // These locales need special handling for the dotless letter I.
    const DOTLESS_I_LOCALES = [
        'az',
        'tr',
    ];
    const DOTLESS_I_TOLOWER = [
        'I' => 'ı',
        'İ' => 'i',
    ];
    const DOTLESS_I_TOUPPER = [
        'ı' => 'I',
        'i' => 'İ',
    ];

    // The ranges of characters used by each script.
    const SCRIPT_CHARACTER_RANGES = [
        [
            'Latn',
            0x0041,
            0x005A,
        ],
        [
            'Latn',
            0x0061,
            0x007A,
        ],
        [
            'Latn',
            0x0100,
            0x02AF,
        ],
        [
            'Grek',
            0x0370,
            0x03FF,
        ],
        [
            'Cyrl',
            0x0400,
            0x052F,
        ],
        [
            'Hebr',
            0x0590,
            0x05FF,
        ],
        [
            'Arab',
            0x0600,
            0x06FF,
        ],
        [
            'Arab',
            0x0750,
            0x077F,
        ],
        [
            'Arab',
            0x08A0,
            0x08FF,
        ],
        [
            'Deva',
            0x0900,
            0x097F,
        ],
        [
            'Taml',
            0x0B80,
            0x0BFF,
        ],
        [
            'Sinh',
            0x0D80,
            0x0DFF,
        ],
        [
            'Thai',
            0x0E00,
            0x0E7F,
        ],
        [
            'Geor',
            0x10A0,
            0x10FF,
        ],
        [
            'Grek',
            0x1F00,
            0x1FFF,
        ],
        [
            'Deva',
            0xA8E0,
            0xA8FF,
        ],
        [
            'Hans',
            0x3000,
            0x303F,
        ],
        // Mixed CJK, not just Hans
        [
            'Hans',
            0x3400,
            0xFAFF,
        ],
        // Mixed CJK, not just Hans
        [
            'Hans',
            0x20000,
            0x2FA1F,
        ],
        // Mixed CJK, not just Hans
    ];

    // Characters that are displayed in mirror form in RTL text.
    const MIRROR_CHARACTERS = [
        '('  => ')',
        ')'  => '(',
        '['  => ']',
        ']'  => '[',
        '{'  => '}',
        '}'  => '{',
        '<'  => '>',
        '>'  => '<',
        '‹ ' => '›',
        '› ' => '‹',
        '«'  => '»',
        '»'  => '«',
        '﴾ ' => '﴿',
        '﴿ ' => '﴾',
        '“ ' => '”',
        '” ' => '“',
        '‘ ' => '’',
        '’ ' => '‘',
    ];

    // Default list of locales to show in the menu.
    const DEFAULT_LOCALES = [
        'ar',
        'bg',
        'bs',
        'ca',
        'cs',
        'da',
        'de',
        'el',
        'en-GB',
        'en-US',
        'es',
        'et',
        'fi',
        'fr',
        'he',
        'hr',
        'hu',
        'is',
        'it',
        'ka',
        'kk',
        'lt',
        'mr',
        'nb',
        'nl',
        'nn',
        'pl',
        'pt',
        'ru',
        'sk',
        'sv',
        'tr',
        'uk',
        'vi',
        'zh-Hans',
    ];

    /** @var string Punctuation used to separate list items, typically a comma */
    public static $list_separator;

    /**
     * The prefered locales for this site, or a default list if no preference.
     *
     * @return LocaleInterface[]
     */
    public static function activeLocales(): array
    {
        $code_list = Site::getPreference('LANGUAGES');

        if ($code_list === '') {
            $codes = self::DEFAULT_LOCALES;
        } else {
            $codes = explode(',', $code_list);
        }

        $locales = [];
        foreach ($codes as $code) {
            if (file_exists(WT_ROOT . 'language/' . $code . '.mo')) {
                try {
                    $locales[] = Locale::create($code);
                } catch (Exception $ex) {
                    // No such locale exists?
                }
            }
        }
        usort($locales, '\Fisharebest\Localization\Locale::compare');

        return $locales;
    }

    /**
     * Which MySQL collation should be used for this locale?
     *
     * @return string
     */
    public static function collation()
    {
        $collation = self::$locale->collation();
        switch ($collation) {
            case 'croatian_ci':
            case 'german2_ci':
            case 'vietnamese_ci':
                // Only available in MySQL 5.6
                return 'utf8_unicode_ci';
            default:
                return 'utf8_' . $collation;
        }
    }

    /**
     * What format is used to display dates in the current locale?
     *
     * @return string
     */
    public static function dateFormat(): string
    {
        /* I18N: This is the format string for full dates. See http://php.net/date for codes */
        return self::$translator->translate('%j %F %Y');
    }

    /**
     * Generate consistent I18N for datatables.js
     *
     * @param int[] $lengths An optional array of page lengths
     *
     * @return string
     */
    public static function datatablesI18N(array $lengths = [
        10,
        20,
        30,
        50,
        100,
        -1,
    ]): string
    {
        $length_options = Bootstrap4::select(FunctionsEdit::numericOptions($lengths), '10');

        return
            '"formatNumber": function(n) { return String(n).replace(/[0-9]/g, function(w) { return ("' . self::$locale->digits('0123456789') . '")[+w]; }); },' .
            '"language": {' .
            ' "paginate": {' .
            '  "first":    "' . self::translate('first') . '",' .
            '  "last":     "' . self::translate('last') . '",' .
            '  "next":     "' . self::translate('next') . '",' .
            '  "previous": "' . self::translate('previous') . '"' .
            ' },' .
            ' "emptyTable":     "' . self::translate('No records to display') . '",' .
            ' "info":           "' . /* I18N: %s are placeholders for numbers */
            self::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_') . '",' .
            ' "infoEmpty":      "' . self::translate('Showing %1$s to %2$s of %3$s', self::$locale->digits('0'), self::$locale->digits('0'), self::$locale->digits('0')) . '",' .
            ' "infoFiltered":   "' . /* I18N: %s is a placeholder for a number */
            self::translate('(filtered from %s total entries)', '_MAX_') . '",' .
            ' "lengthMenu":     "' . /* I18N: %s is a number of records per page */
            self::translate('Display %s', addslashes($length_options)) . '",' .
            ' "loadingRecords": "' . self::translate('Loading…') . '",' .
            ' "processing":     "' . self::translate('Loading…') . '",' .
            ' "search":         "' . self::translate('Filter') . '",' .
            ' "zeroRecords":    "' . self::translate('No records to display') . '"' .
            '}';
    }

    /**
     * Convert the digits 0-9 into the local script
     *
     * Used for years, etc., where we do not want thousands-separators, decimals, etc.
     *
     * @param string|int $n
     *
     * @return string
     */
    public static function digits($n): string
    {
        return self::$locale->digits((string) $n);
    }

    /**
     * What is the direction of the current locale
     *
     * @return string "ltr" or "rtl"
     */
    public static function direction(): string
    {
        return self::$locale->direction();
    }

    /**
     * What is the first day of the week.
     *
     * @return int Sunday=0, Monday=1, etc.
     */
    public static function firstDay(): int
    {
        return self::$locale->territory()->firstDay();
    }

    /**
     * Generate i18n markup for the <html> tag, e.g. lang="ar" dir="rtl"
     *
     * @return string
     */
    public static function htmlAttributes(): string
    {
        return self::$locale->htmlAttributes();
    }

    /**
     * Initialise the translation adapter with a locale setting.
     *
     * @param string    $code Use this locale/language code, or choose one automatically
     * @param Tree|null $tree
     *
     * @return string $string
     */
    public static function init(string $code = '', Tree $tree = null): string
    {
        mb_internal_encoding('UTF-8');

        if ($code !== '') {
            // Create the specified locale
            self::$locale = Locale::create($code);
        } elseif (Session::has('locale') && file_exists(WT_ROOT . 'language/' . Session::get('locale') . '.mo')) {
            // Select a previously used locale
            self::$locale = Locale::create(Session::get('locale'));
        } else {
            if ($tree instanceof Tree) {
                $default_locale = Locale::create($tree->getPreference('LANGUAGE', 'en-US'));
            } else {
                $default_locale = new LocaleEnUs();
            }

            // Negotiate with the browser.
            // Search engines don't negotiate.  They get the default locale of the tree.
            self::$locale = Locale::httpAcceptLanguage($_SERVER, self::installedLocales(), $default_locale);
        }

        $cache_dir  = WT_DATA_DIR . 'cache/';
        $cache_file = $cache_dir . 'language-' . self::$locale->languageTag() . '-cache.php';
        if (file_exists($cache_file)) {
            $filemtime = filemtime($cache_file);
        } else {
            $filemtime = 0;
        }

        // Load the translation file(s)
        // Note that glob() returns false instead of an empty array when open_basedir_restriction
        // is in force and no files are found. See PHP bug #47358.
        if (defined('GLOB_BRACE')) {
            $translation_files = array_merge(
                [WT_ROOT . 'language/' . self::$locale->languageTag() . '.mo'],
                glob(Webtrees::MODULES_PATH . '*/language/' . self::$locale->languageTag() . '.{csv,php,mo}', GLOB_BRACE) ?: [],
                glob(WT_DATA_DIR . 'language/' . self::$locale->languageTag() . '.{csv,php,mo}', GLOB_BRACE) ?: []
            );
        } else {
            // Some servers do not have GLOB_BRACE - see http://php.net/manual/en/function.glob.php
            $translation_files = array_merge(
                [WT_ROOT . 'language/' . self::$locale->languageTag() . '.mo'],
                glob(Webtrees::MODULES_PATH . '*/language/' . self::$locale->languageTag() . '.csv') ?: [],
                glob(Webtrees::MODULES_PATH . '*/language/' . self::$locale->languageTag() . '.php') ?: [],
                glob(Webtrees::MODULES_PATH . '*/language/' . self::$locale->languageTag() . '.mo') ?: [],
                glob(WT_DATA_DIR . 'language/' . self::$locale->languageTag() . '.csv') ?: [],
                glob(WT_DATA_DIR . 'language/' . self::$locale->languageTag() . '.php') ?: [],
                glob(WT_DATA_DIR . 'language/' . self::$locale->languageTag() . '.mo') ?: []
            );
        }
        // Rebuild files after one hour
        $rebuild_cache = time() > $filemtime + 3600;
        // Rebuild files if any translation file has been updated
        foreach ($translation_files as $translation_file) {
            if (filemtime($translation_file) > $filemtime) {
                $rebuild_cache = true;
                break;
            }
        }

        if ($rebuild_cache) {
            $translations = [];
            foreach ($translation_files as $translation_file) {
                $translation  = new Translation($translation_file);
                $translations = array_merge($translations, $translation->asArray());
            }
            try {
                File::mkdir($cache_dir);
                file_put_contents($cache_file, '<?php return ' . var_export($translations, true) . ';');
            } catch (Exception $ex) {
                // During setup, we may not have been able to create it.
            }
        } else {
            $translations = include $cache_file;
        }

        // Create a translator
        self::$translator = new Translator($translations, self::$locale->pluralRule());

        /* I18N: This punctuation is used to separate lists of items */
        self::$list_separator = self::translate(', ');

        // Create a collator
        try {
            if (class_exists('Collator')) {
                // Symfony provides a very incomplete polyfill - which cannot be used.
                self::$collator = new Collator(self::$locale->code());
                // Ignore upper/lower case differences
                self::$collator->setStrength(Collator::SECONDARY);
            }
        } catch (Exception $ex) {
            // PHP-INTL is not installed?  We'll use a fallback later.
            self::$collator = null;
        }

        return self::$locale->languageTag();
    }

    /**
     * All locales for which a translation file exists.
     *
     * @return LocaleInterface[]
     */
    public static function installedLocales(): array
    {
        $locales = [];
        foreach (glob(WT_ROOT . 'language/*.mo') as $file) {
            try {
                $locales[] = Locale::create(basename($file, '.mo'));
            } catch (Exception $ex) {
                // Not a recognised locale
            }
        }
        usort($locales, '\Fisharebest\Localization\Locale::compare');

        return $locales;
    }

    /**
     * Return the endonym for a given language - as per http://cldr.unicode.org/
     *
     * @param string $locale
     *
     * @return string
     */
    public static function languageName(string $locale): string
    {
        return Locale::create($locale)->endonym();
    }

    /**
     * Return the script used by a given language
     *
     * @param string $locale
     *
     * @return string
     */
    public static function languageScript(string $locale): string
    {
        return Locale::create($locale)->script()->code();
    }

    /**
     * Translate a number into the local representation.
     *
     * e.g. 12345.67 becomes
     * en: 12,345.67
     * fr: 12 345,67
     * de: 12.345,67
     *
     * @param float $n
     * @param int   $precision
     *
     * @return string
     */
    public static function number(float $n, int $precision = 0): string
    {
        return self::$locale->number(round($n, $precision));
    }

    /**
     * Translate a fraction into a percentage.
     *
     * e.g. 0.123 becomes
     * en: 12.3%
     * fr: 12,3 %
     * de: 12,3%
     *
     * @param float $n
     * @param int   $precision
     *
     * @return string
     */
    public static function percentage(float $n, int $precision = 0): string
    {
        return self::$locale->percent(round($n, $precision + 2));
    }

    /**
     * Translate a plural string
     * echo self::plural('There is an error', 'There are errors', $num_errors);
     * echo self::plural('There is one error', 'There are %s errors', $num_errors);
     * echo self::plural('There is %1$s %2$s cat', 'There are %1$s %2$s cats', $num, $num, $colour);
     *
     * @param string $singular
     * @param string $plural
     * @param int    $count
     * @param string ...$args
     *
     * @return string
     */
    public static function plural(string $singular, string $plural, int $count, ...$args): string
    {
        $message = self::$translator->translatePlural($singular, $plural, $count);

        return sprintf($message, ...$args);
    }

    /**
     * UTF8 version of PHP::strrev()
     *
     * Reverse RTL text for third-party libraries such as GD2 and googlechart.
     *
     * These do not support UTF8 text direction, so we must mimic it for them.
     *
     * Numbers are always rendered LTR, even in RTL text.
     * The visual direction of characters such as parentheses should be reversed.
     *
     * @param string $text Text to be reversed
     *
     * @return string
     */
    public static function reverseText($text): string
    {
        // Remove HTML markup - we can't display it and it is LTR.
        $text = strip_tags($text);
        // Remove HTML entities.
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        // LTR text doesn't need reversing
        if (self::scriptDirection(self::textScript($text)) === 'ltr') {
            return $text;
        }

        // Mirrored characters
        $text = strtr($text, self::MIRROR_CHARACTERS);

        $reversed = '';
        $digits   = '';
        while ($text != '') {
            $letter = mb_substr($text, 0, 1);
            $text   = mb_substr($text, 1);
            if (strpos(self::DIGITS, $letter) !== false) {
                $digits .= $letter;
            } else {
                $reversed = $letter . $digits . $reversed;
                $digits   = '';
            }
        }

        return $digits . $reversed;
    }

    /**
     * Return the direction (ltr or rtl) for a given script
     *
     * The PHP/intl library does not provde this information, so we need
     * our own lookup table.
     *
     * @param string $script
     *
     * @return string
     */
    public static function scriptDirection($script)
    {
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

    /**
     * Perform a case-insensitive comparison of two strings.
     *
     * @param string $string1
     * @param string $string2
     *
     * @return int
     */
    public static function strcasecmp($string1, $string2)
    {
        if (self::$collator instanceof Collator) {
            return self::$collator->compare($string1, $string2);
        } else {
            return strcmp(self::strtolower($string1), self::strtolower($string2));
        }
    }

    /**
     * Convert a string to lower case.
     *
     * @param string $string
     *
     * @return string
     */
    public static function strtolower($string): string
    {
        if (in_array(self::$locale->language()->code(), self::DOTLESS_I_LOCALES)) {
            $string = strtr($string, self::DOTLESS_I_TOLOWER);
        }

        return mb_strtolower($string);
    }

    /**
     * Convert a string to upper case.
     *
     * @param string $string
     *
     * @return string
     */
    public static function strtoupper($string): string
    {
        if (in_array(self::$locale->language()->code(), self::DOTLESS_I_LOCALES)) {
            $string = strtr($string, self::DOTLESS_I_TOUPPER);
        }

        return mb_strtoupper($string);
    }

    /**
     * Identify the script used for a piece of text
     *
     * @param string $string
     *
     * @return string
     */
    public static function textScript($string): string
    {
        $string = strip_tags($string); // otherwise HTML tags show up as latin
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8'); // otherwise HTML entities show up as latin
        $string = str_replace([
            '@N.N.',
            '@P.N.',
        ], '', $string); // otherwise unknown names show up as latin
        $pos    = 0;
        $strlen = strlen($string);
        while ($pos < $strlen) {
            // get the Unicode Code Point for the character at position $pos
            $byte1 = ord($string[$pos]);
            if ($byte1 < 0x80) {
                $code_point = $byte1;
                $chrlen     = 1;
            } elseif ($byte1 < 0xC0) {
                // Invalid continuation character
                return 'Latn';
            } elseif ($byte1 < 0xE0) {
                $code_point = (($byte1 & 0x1F) << 6) + (ord($string[$pos + 1]) & 0x3F);
                $chrlen     = 2;
            } elseif ($byte1 < 0xF0) {
                $code_point = (($byte1 & 0x0F) << 12) + ((ord($string[$pos + 1]) & 0x3F) << 6) + (ord($string[$pos + 2]) & 0x3F);
                $chrlen     = 3;
            } elseif ($byte1 < 0xF8) {
                $code_point = (($byte1 & 0x07) << 24) + ((ord($string[$pos + 1]) & 0x3F) << 12) + ((ord($string[$pos + 2]) & 0x3F) << 6) + (ord($string[$pos + 3]) & 0x3F);
                $chrlen     = 3;
            } else {
                // Invalid UTF
                return 'Latn';
            }

            foreach (self::SCRIPT_CHARACTER_RANGES as $range) {
                if ($code_point >= $range[1] && $code_point <= $range[2]) {
                    return $range[0];
                }
            }
            // Not a recognised script. Maybe punctuation, spacing, etc. Keep looking.
            $pos += $chrlen;
        }

        return 'Latn';
    }

    /**
     * Convert a number of seconds into a relative time. For example, 630 => "10 hours, 30 minutes ago"
     *
     * @param int $seconds
     *
     * @return string
     */
    public static function timeAgo($seconds)
    {
        $minute = 60;
        $hour   = 60 * $minute;
        $day    = 24 * $hour;
        $month  = 30 * $day;
        $year   = 365 * $day;

        if ($seconds > $year) {
            $years = intdiv($seconds, $year);

            return self::plural('%s year ago', '%s years ago', $years, self::number($years));
        }

        if ($seconds > $month) {
            $months = intdiv($seconds, $month);

            return self::plural('%s month ago', '%s months ago', $months, self::number($months));
        }

        if ($seconds > $day) {
            $days = intdiv($seconds, $day);

            return self::plural('%s day ago', '%s days ago', $days, self::number($days));
        }

        if ($seconds > $hour) {
            $hours = intdiv($seconds, $hour);

            return self::plural('%s hour ago', '%s hours ago', $hours, self::number($hours));
        }

        if ($seconds > $minute) {
            $minutes = intdiv($seconds, $minute);

            return self::plural('%s minute ago', '%s minutes ago', $minutes, self::number($minutes));
        }

        return self::plural('%s second ago', '%s seconds ago', $seconds, self::number($seconds));
    }

    /**
     * What format is used to display dates in the current locale?
     *
     * @return string
     */
    public static function timeFormat(): string
    {
        /* I18N: This is the format string for the time-of-day. See http://php.net/date for codes */
        return self::$translator->translate('%H:%i:%s');
    }

    /**
     * Translate a string, and then substitute placeholders
     *
     * echo I18N::translate('Hello World!');
     * echo I18N::translate('The %s sat on the mat', 'cat');
     *
     * @param string $message
     * @param string ...$args
     *
     * @return string
     */
    public static function translate(string $message, ...$args): string
    {
        $message = self::$translator->translate($message);

        return sprintf($message, ...$args);
    }

    /**
     * Context sensitive version of translate.
     * echo I18N::translateContext('NOMINATIVE', 'January');
     * echo I18N::translateContext('GENITIVE', 'January');
     *
     * @param string $context
     * @param string $message
     * @param string ...$args
     *
     * @return string
     */
    public static function translateContext(string $context, string $message, ...$args): string
    {
        $message = self::$translator->translateContext($context, $message);

        return sprintf($message, ...$args);
    }
}
