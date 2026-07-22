<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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
use Fisharebest\Webtrees\Contracts\LanguageInterface;
use Fisharebest\Webtrees\Enums\Script;
use Fisharebest\Webtrees\Enums\TextDirection;
use Fisharebest\Webtrees\Factories\LanguageFactory;
use Fisharebest\Webtrees\I18N\Translation;
use Fisharebest\Webtrees\I18N\Translator;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleLanguageInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Throwable;

use function file_put_contents;
use function html_entity_decode;
use function is_file;
use function mb_substr;
use function sprintf;
use function str_contains;
use function str_replace;
use function strcmp;
use function strip_tags;
use function strtr;
use function var_export;

/**
 * Internationalization (i18n) and localization (l10n).
 */
class I18N
{
    // Digits are always rendered LTR, even in RTL text.
    private const string DIGITS = '0123456789٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹';

    // Characters that are displayed in mirror form in RTL text.
    private const array MIRROR_CHARACTERS = [
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

    private static LanguageInterface $language;

    private static Translator $translator;

    private static Collator|null $collator = null;

    /**
     * @return array<string,string>
     */
    public static function activeLanguages(): array
    {
        $language_factory = Registry::container()->get(LanguageFactory::class);

        $active_language_modules = Registry::container()->get(ModuleService::class)
            ->findByInterface(ModuleLanguageInterface::class)
            ->all();

        $languages = [];

        foreach ($active_language_modules as $module) {
            $language = $language_factory->fromLanguageTag($module->language()->languageTag());

            $languages[$language->languageTag()] = $language->endonym();
        }

        return $languages;
    }

    /**
     * @return array<string,string>
     */
    public static function allLanguages(): array
    {
        $languages = [];

        foreach (Registry::container()->get(LanguageFactory::class)->allLanguages() as $language) {
            $languages[$language->languageTag()] = $language->endonym();
        }

        return $languages;
    }

    /**
     * What format is used to display timestamps in the current locale?
     */
    public static function dateFormat(): string
    {
        /* I18N: This is the format string for full dates. See https://php.net/date for codes */
        return self::$translator->translate('%j %F %Y');
    }

    /**
     * Convert the digits 0-9 into the local script
     * Used for years, etc., where we do not want thousands-separators, decimals, etc.
     */
    public static function digits(string|int $n): string
    {
        return self::$language->digits($n);
    }


    public static function textDirection(): TextDirection
    {
        return self::$language->textDirection();
    }

    /**
     * Initialize the translation adapter with a locale setting.
     */
    public static function init(string $language_tag): void
    {
        self::$language = Registry::container()->get(LanguageFactory::class)->fromLanguageTag($language_tag);

        // Use the generated translations when they are present, otherwise build them from the source .po file.
        $translation_file = Webtrees::ROOT_DIR . 'resources/lang/' . self::$language->languageTag() . '/messages.php';

        if (is_file($translation_file)) {
            // Official releases of webtrees will have these generated PHP files.
            $translation = Translation::fromPhpFile($translation_file);
        } else {
            // Development versions of webtrees must create them on first use.
            $po_file = Webtrees::ROOT_DIR . 'resources/lang/' . self::$language->languageTag() . '/messages.po';

            $translation = Translation::fromPoFile($po_file);
            file_put_contents(
                $translation_file,
                "<?php\n\nreturn " . var_export($translation->toArray(), true) . ";\n",
            );
        }

        // Add translations from custom modules.
        try {
            $custom_modules = Registry::container()
                ->get(ModuleService::class)
                ->findByInterface(ModuleCustomInterface::class);

            foreach ($custom_modules as $custom_module) {
                $custom_translations = $custom_module->customTranslations(self::$language->languageTag());

                if ($custom_translations !== []) {
                    $translation = $translation->withMessages($custom_translations);
                }
            }
        } catch (Throwable) {
            // During setup, there won't be a database, so won't be any modules.
        }

        // Create a translator
        self::$translator = new Translator($translation->toArray(), self::$language->pluralRule());

        // Create a collator.
        if (extension_loaded('intl')) {
            self::$collator = self::$language->collator();
        }
    }

    /**
     * Translate a string, and then substitute placeholders
     * echo I18N::translate('Hello World!');
     * echo I18N::translate('The %s sat on the mat', 'cat');
     *
     * @param string ...$args
     */
    public static function translate(string $message, ...$args): string
    {
        return sprintf(self::$translator->translate($message), ...$args);
    }

    public static function language(): LanguageInterface
    {
        return self::$language;
    }

    public static function languageTag(): string
    {
        return self::$language->languageTag();
    }

    /** @param array<string> $items */
    public static function list(array $items): string
    {
        return self::$language->formatList($items);
    }

    /** @param array<string> $items */
    public static function listAnd(array $items): string
    {
        return self::$language->formatListAnd($items);
    }

    /** @param array<string> $items */
    public static function listOr(array $items): string
    {
        return self::$language->formatListOr($items);
    }

    /**
     * Translate a number into the local representation.
     * e.g. 12345.67 becomes
     * en: 12,345.67
     * fr: 12 345,67
     * de: 12.345,67
     */
    public static function number(float $n, int $precision = 0): string
    {
        return self::$language->number(round($n, $precision));
    }

    /**
     * Translate a fraction into a percentage.
     * e.g. 0.123 becomes
     * en: 12.3%
     * fr: 12,3 %
     * de: 12,3%
     */
    public static function percentage(float $n, int $precision = 0): string
    {
        return self::$language->percentage(round($n, $precision + 2));
    }

    /**
     * Translate a plural string
     * echo self::plural('There is an error', 'There are errors', $num_errors);
     * echo self::plural('There is one error', 'There are %s errors', $num_errors);
     * echo self::plural('There is %1$s %2$s cat', 'There are %1$s %2$s cats', $num, $num, $colour);
     *
     * @param string ...$args
     */
    public static function plural(string $singular, string $plural, int $count, ...$args): string
    {
        return sprintf(self::$translator->translatePlural($singular, $plural, $count), ...$args);
    }

    /**
     * UTF8 version of PHP::strrev()
     * Reverse RTL text for third-party libraries such as GD2 and googlechart.
     * These do not support UTF8 text direction, so we must mimic it for them.
     * Numbers are always rendered LTR, even in RTL text.
     * The visual direction of characters such as parentheses should be reversed.
     *
     * @param string $text Text to be reversed
     */
    public static function reverseText(string $text): string
    {
        // Remove HTML markup - we can't display it and it is LTR.
        $text = strip_tags($text);
        // Remove HTML entities.
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        // LTR text doesn't need reversing
        if (self::textScript($text)->textDirection() === TextDirection::LTR) {
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
     * Identify the script used for a piece of text
     */
    public static function textScript(string $string): Script
    {
        $string = strip_tags($string); // otherwise HTML tags show up as latin
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8'); // otherwise HTML entities show up as latin
        $string = str_replace([
            Individual::NOMEN_NESCIO,
            Individual::PRAENOMEN_NESCIO,
        ], '', $string);

        return Script::fromText($string);
    }

    /**
     * Compare strings using local collation rules.
     */
    public static function compare(string $first, string $second): int
    {
        if (self::$collator === null) {
            return strcmp(self::strtolower($first), self::strtolower($second));
        }

        return (int) self::$collator->compare($first, $second);
    }

    /**
     * A closure which will compare strings using local collation rules.
     *
     * @return Closure(string,string):int
     */
    public static function comparator(): Closure
    {
        trigger_error(
            'I18N::comparator() is deprecated and will be removed in version 2.3. Use I18N::compare(...) instead.',
            E_USER_DEPRECATED,
        );

        return self::compare(...);
    }

    /**
     * Convert a string to lower case.
     */
    public static function strtolower(string $string): string
    {
        return self::$language->strtolower($string);
    }

    /**
     * Convert a string to upper case.
     */
    public static function strtoupper(string $string): string
    {
        return self::$language->strtoupper($string);
    }

    /**
     * What format is used to display dates in the current locale?
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
     * @param string ...$args
     */
    public static function translateContext(string $context, string $message, ...$args): string
    {
        $message = self::$translator->translateContext($context, $message);

        return sprintf($message, ...$args);
    }
}
