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
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleLanguageInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Illuminate\Support\Collection;
use function array_merge;
use function class_exists;
use function html_entity_decode;
use function in_array;
use function mb_strtolower;
use function mb_strtoupper;
use function mb_substr;
use function ord;
use function sprintf;
use function str_replace;
use function strcmp;
use function strip_tags;
use function strlen;
use function strpos;
use function strtr;

/**
 * Internationalization (i18n) and localization (l10n).
 */
class I18N
{
    // MO files use special characters for plurals and context.
    public const PLURAL  = "\x00";
    public const CONTEXT = "\x04";
    private const DIGITS = '0123456789٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹';
    private const DOTLESS_I_LOCALES = [
        'az',
        'tr',
    ];
    private const DOTLESS_I_TOLOWER = [
        'I' => 'ı',
        'İ' => 'i',
    ];

    // Digits are always rendered LTR, even in RTL text.
    private const DOTLESS_I_TOUPPER = [
        'ı' => 'I',
        'i' => 'İ',
    ];

    // These locales need special handling for the dotless letter I.
    private const SCRIPT_CHARACTER_RANGES = [
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
    private const MIRROR_CHARACTERS = [
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
    /** @var string Punctuation used to separate list items, typically a comma */
    public static $list_separator;

    // The ranges of characters used by each script.
    /** @var LocaleInterface The current locale (e.g. LocaleEnGb) */
    private static $locale;

    // Characters that are displayed in mirror form in RTL text.
    /** @var Translator An object that performs translation */
    private static $translator;
    /** @var  Collator|null From the php-intl library */
    private static $collator;

    /**
     * The preferred locales for this site, or a default list if no preference.
     *
     * @return LocaleInterface[]
     */
    public static function activeLocales(): array
    {
        /** @var Collection $locales */
        $locales = app(ModuleService::class)
            ->findByInterface(ModuleLanguageInterface::class, false, true)
            ->map(static function (ModuleLanguageInterface $module): LocaleInterface {
                return $module->locale();
            });

        if ($locales->isEmpty()) {
            return [new LocaleEnUs()];
        }

        return $locales->all();
    }

    /**
     * Which MySQL collation should be used for this locale?
     *
     * @return string
     */
    public static function collation(): string
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
     * Convert the digits 0-9 into the local script
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
     * @param string    $code  Use this locale/language code, or choose one automatically
     * @param Tree|null $tree
     * @param bool      $setup During setup, we cannot access the database.
     *
     * @return string $string
     */
    public static function init(string $code = '', Tree $tree = null, $setup = false): string
    {
        if ($code !== '') {
            // Create the specified locale
            self::$locale = Locale::create($code);
        } elseif (Session::has('language')) {
            // Select a previously used locale
            self::$locale = Locale::create(Session::get('language'));
        } else {
            if ($tree instanceof Tree) {
                $default_locale = Locale::create($tree->getPreference('LANGUAGE', 'en-US'));
            } else {
                $default_locale = new LocaleEnUs();
            }

            // Negotiate with the browser.
            // Search engines don't negotiate.  They get the default locale of the tree.
            if ($setup) {
                $installed_locales = app(ModuleService::class)->setupLanguages()
                    ->map(static function (ModuleLanguageInterface $module): LocaleInterface {
                        return $module->locale();
                    });
            } else {
                $installed_locales = self::installedLocales();
            }

            self::$locale = Locale::httpAcceptLanguage($_SERVER, $installed_locales->all(), $default_locale);
        }

        // Load the translation file
        $translation_file = Webtrees::ROOT_DIR . 'resources/lang/' . self::$locale->languageTag() . '/messages.php';

        try {
            $translation  = new Translation($translation_file);
            $translations = $translation->asArray();
        } catch (Exception $ex) {
            // The translations files are created during the build process, and are
            // not included in the source code.
            // Assuming we are using dev code, and build (or rebuild) the files.
            $po_file      = Webtrees::ROOT_DIR . 'resources/lang/' . self::$locale->languageTag() . '/messages.po';
            $translation  = new Translation($po_file);
            $translations = $translation->asArray();
            file_put_contents($translation_file, '<?php return ' . var_export($translations, true) . ';');
        }

        // Add translations from custom modules (but not during setup, as we have no database/modules)
        if (!$setup) {
            $translations = app(ModuleService::class)
                ->findByInterface(ModuleCustomInterface::class)
                ->reduce(static function (array $carry, ModuleCustomInterface $item): array {
                    return array_merge($carry, $item->customTranslations(self::$locale->languageTag()));
                }, $translations);
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
     * @return Collection
     */
    public static function installedLocales(): Collection
    {
        return app(ModuleService::class)
            ->findByInterface(ModuleLanguageInterface::class, true)
            ->map(static function (ModuleLanguageInterface $module): LocaleInterface {
                return $module->locale();
            });
    }

    /**
     * Translate a string, and then substitute placeholders
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
     * Reverse RTL text for third-party libraries such as GD2 and googlechart.
     * These do not support UTF8 text direction, so we must mimic it for them.
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
        while ($text !== '') {
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
     * The PHP/intl library does not provde this information, so we need
     * our own lookup table.
     *
     * @param string $script
     *
     * @return string
     */
    public static function scriptDirection($script): string
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
     * Perform a case-insensitive comparison of two strings.
     *
     * @param string $string1
     * @param string $string2
     *
     * @return int
     */
    public static function strcasecmp($string1, $string2): int
    {
        if (self::$collator instanceof Collator) {
            return self::$collator->compare($string1, $string2);
        }

        return strcmp(self::strtolower($string1), self::strtolower($string2));
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
        if (in_array(self::$locale->language()->code(), self::DOTLESS_I_LOCALES, true)) {
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
        if (in_array(self::$locale->language()->code(), self::DOTLESS_I_LOCALES, true)) {
            $string = strtr($string, self::DOTLESS_I_TOUPPER);
        }

        return mb_strtoupper($string);
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
