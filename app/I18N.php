<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Closure;
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

use function array_merge;
use function class_exists;
use function html_entity_decode;
use function in_array;
use function mb_strtolower;
use function mb_strtoupper;
use function mb_substr;
use function ord;
use function sprintf;
use function str_contains;
use function str_replace;
use function strcmp;
use function strip_tags;
use function strlen;
use function strtr;
use function var_export;

/**
 * Internationalization (i18n) and localization (l10n).
 */
class I18N
{
    // MO files use special characters for plurals and context.
    public const PLURAL  = "\x00";
    public const CONTEXT = "\x04";

    // Digits are always rendered LTR, even in RTL text.
    private const DIGITS = '0123456789٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹';

    // These locales need special handling for the dotless letter I.
    private const DOTLESS_I_LOCALES = [
        'az',
        'tr',
    ];

    private const DOTLESS_I_TOLOWER = [
        'I' => 'ı',
        'İ' => 'i',
    ];

    private const DOTLESS_I_TOUPPER = [
        'ı' => 'I',
        'i' => 'İ',
    ];

    // The ranges of characters used by each script.
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

    // Characters that are displayed in mirror form in RTL text.
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

    // Punctuation used to separate list items, typically a comma
    public static string $list_separator;

    private static ?ModuleLanguageInterface $language;

    private static LocaleInterface $locale;

    private static Translator $translator;

    private static ?Collator $collator = null;

    /**
     * The preferred locales for this site, or a default list if no preference.
     *
     * @return array<LocaleInterface>
     */
    public static function activeLocales(): array
    {
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
        /* I18N: This is the format string for full dates. See https://php.net/date for codes */
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
     * Initialise the translation adapter with a locale setting.
     *
     * @param string $code
     * @param bool   $setup
     *
     * @return void
     */
    public static function init(string $code, bool $setup = false): void
    {
        self::$locale = Locale::create($code);

        // Load the translation file
        $translation_file = __DIR__ . '/../resources/lang/' . self::$locale->languageTag() . '/messages.php';

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
            file_put_contents($translation_file, "<?php\n\nreturn " . var_export($translations, true) . ";\n");
        }

        // Add translations from custom modules (but not during setup, as we have no database/modules)
        if (!$setup) {
            $module_service = app(ModuleService::class);

            $translations = $module_service
                ->findByInterface(ModuleCustomInterface::class)
                ->reduce(static function (array $carry, ModuleCustomInterface $item): array {
                    return array_merge($carry, $item->customTranslations(self::$locale->languageTag()));
                }, $translations);

            self::$language = $module_service
                ->findByInterface(ModuleLanguageInterface::class)
                ->first(fn (ModuleLanguageInterface $module): bool => $module->locale()->languageTag() === $code);
        }

        // Create a translator
        self::$translator = new Translator($translations, self::$locale->pluralRule());

        /* I18N: This punctuation is used to separate lists of items */
        self::$list_separator = self::translate(', ');

        // Create a collator
        try {
            // Symfony provides a very incomplete polyfill - which cannot be used.
            if (class_exists('Collator')) {
                // Need phonebook collation rules for German Ä, Ö and Ü.
                if (str_contains(self::$locale->code(), '@')) {
                    self::$collator = new Collator(self::$locale->code() . ';collation=phonebook');
                } else {
                    self::$collator = new Collator(self::$locale->code() . '@collation=phonebook');
                }
                // Ignore upper/lower case differences
                self::$collator->setStrength(Collator::SECONDARY);
            }
        } catch (Exception $ex) {
            // PHP-INTL is not installed?  We'll use a fallback later.
        }
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
     * @return string
     */
    public static function languageTag(): string
    {
        return self::$locale->languageTag();
    }

    /**
     * @return LocaleInterface
     */
    public static function locale(): LocaleInterface
    {
        return self::$locale;
    }

    /**
     * @return ModuleLanguageInterface
     */
    public static function language(): ModuleLanguageInterface
    {
        return self::$language;
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
    public static function reverseText(string $text): string
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
            if (str_contains(self::DIGITS, $letter)) {
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
    public static function scriptDirection(string $script): string
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
    public static function textScript(string $string): string
    {
        $string = strip_tags($string); // otherwise HTML tags show up as latin
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8'); // otherwise HTML entities show up as latin
        $string = str_replace([
            Individual::NOMEN_NESCIO,
            Individual::PRAENOMEN_NESCIO,
        ], '', $string);
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
     * A closure which will compare strings using local collation rules.
     *
     * @return Closure
     */
    public static function comparator(): Closure
    {
        $collator = self::$collator;

        if ($collator instanceof Collator) {
            return static fn (string $x, string $y): int => (int) $collator->compare($x, $y);
        }

        return static fn (string $x, string $y): int => strcmp(self::strtolower($x), self::strtolower($y));
    }



    /**
     * Convert a string to lower case.
     *
     * @param string $string
     *
     * @return string
     */
    public static function strtolower(string $string): string
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
    public static function strtoupper(string $string): string
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
        /* I18N: This is the format string for the time-of-day. See https://php.net/date for codes */
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
