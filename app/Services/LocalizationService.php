<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\ExtCalendar\ArabicCalendar;
use Fisharebest\ExtCalendar\CalendarInterface;
use Fisharebest\ExtCalendar\GregorianCalendar;
use Fisharebest\ExtCalendar\JewishCalendar;
use Fisharebest\ExtCalendar\PersianCalendar;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\I18N;

use function strlen;
use function substr_compare;

/**
 * Utilities to support localization.
 *
 * @deprecated - Will be removed in webtrees 2.2 - Use I18N::language() instead
 */
class LocalizationService
{
    // Alphabets used by various scripts and locales.
    private const ARABIC_ALPHABET     = ['ا', 'ب', 'ت', 'ث', 'ج', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ك', 'ل', 'م', 'ن', 'ه', 'و', 'ي', 'آ', 'ة', 'ى', 'ی'];
    private const CZECH_ALPHABET      = ['A', 'Á', 'B', 'C', 'Č', 'D', 'Ď', 'E', 'É', 'Ě', 'F', 'G', 'H', 'CH', 'I', ' 	Í', 'J', 'K', 'L', 'M', 'N', 'Ň', 'O', 'Ó', 'P', 'Q', 'R', 'Ř', 'S', 'Š', 'T', 'Ť', 'U', 'Ú', 'Ů', 'V', 'W', 'X', 'Y', 'Ý', 'Z', 'Ž'];
    private const CYRILLIC_ALPHABET   = ['А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я'];
    private const DUTCH_ALPHABET      = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'IJ'];
    private const ESTONIAN_ALPHABET   = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'Š', 'Z', 'Ž', 'T', 'U', 'V', 'W', 'Õ', 'Ä', 'Ö', 'Ü', 'X', 'Y'];
    private const GREEK_ALPHABET      = ['Α', 'Β', 'Γ', 'Δ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ι', 'Κ', 'Λ', 'Μ', 'Ν', 'Ξ', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ω'];
    private const HEBREW_ALPHABET     = ['א', 'ב', 'ג', 'ד', 'ה', 'ו', 'ז', 'ח', 'ט', 'י', 'כ', 'ל', 'מ', 'נ', 'ס', 'ע', 'פ', 'צ', 'ק', 'ר', 'ש', 'ת'];
    private const LATIN_ALPHABET      = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    private const LITHUANIAN_ALPHABET = ['A', 'Ą', 'B', 'C', 'Č', 'D', 'E', 'Ę', 'Ė', 'F', 'G', 'H', 'I', 'Y', 'Į', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'Š', 'T', 'U', 'Ų', 'Ū', 'V', 'Z', 'Ž'];
    private const HUNGARIAN_ALPHABET  = ['A', 'Á', 'B', 'C', 'CS', 'D', 'DZ', 'DZS', 'E', 'É', 'F', 'G', 'GY', 'H', 'I', 'Í', 'J', 'K', 'L', 'LY', 'M', 'N', 'NY', 'O', 'Ó', 'Ö', 'Ő', 'P', 'Q', 'R', 'S', 'SZ', 'T', 'TY', 'U', 'Ú', 'Ü', 'Ű', 'V', 'W', 'X', 'Y', 'Z', 'ZS'];
    private const NORWEGIAN_ALPHABET  = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Æ', 'Ø', 'Å'];
    private const POLISH_ALPHABET     = ['A', 'B', 'C', 'Ć', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'Ł', 'M', 'N', 'O', 'Ó', 'P', 'Q', 'R', 'S', 'Ś', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ź', 'Ż'];
    private const ROMANIAN_ALPHABET   = ['A', 'Ă', 'Â', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'Î', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'Ş', 'T', 'Ţ', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    private const SERBIAN_ALPHABET    = ['A', 'B', 'C', 'Č', 'Ć', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'Š', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ž'];
    private const SLOVAKIAN_ALPHABET  = ['A', 'Á', 'Ä', 'B', 'C', 'Č', 'D', 'Ď', 'DZ', 'DŽ', 'E', 'É', 'F', 'G', 'H', 'CH', 'I', 'Í', 'J', 'K', 'L', 'Ľ', 'Ĺ', 'M', 'N', 'Ň', 'O', 'Ó', 'Ô', 'P', 'Q', 'R', 'Ŕ', 'S', 'Š', 'T', 'Ť', 'U', 'Ú', 'V', 'W', 'X', 'Y', 'Ý', 'Z', 'Ž'];
    private const SLOVENIAN_ALPHABET  = ['A', 'B', 'C', 'Č', 'Ć', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'Š', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ž'];
    private const SPANISH_ALPHABET    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'Ñ', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    private const SWEDISH_ALPHABET    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Å', 'Ä', 'Ö'];
    private const TURKISH_ALPHABET    = ['A', 'B', 'C', 'Ç', 'D', 'E', 'F', 'G', 'Ğ', 'H', 'I', 'İ', 'J', 'K', 'L', 'M', 'N', 'O', 'Ö', 'P', 'R', 'S', 'Ş', 'T', 'U', 'Ü', 'V', 'Y', 'Z'];

    // Scripts with a default alphabet.
    private const ALPHABETS_FOR_SCRIPT = [
        'Latn' => self::LATIN_ALPHABET,
        'Cyrl' => self::CYRILLIC_ALPHABET,
        'Grek' => self::GREEK_ALPHABET,
        'Hebr' => self::HEBREW_ALPHABET,
    ];

    // Locales that use a non-default alphabet.
    private const ALPHABETS = [
        'ar'      => self::ARABIC_ALPHABET,
        'cs'      => self::CZECH_ALPHABET,
        'da'      => self::NORWEGIAN_ALPHABET,
        'es'      => self::SPANISH_ALPHABET,
        'et'      => self::ESTONIAN_ALPHABET,
        'fa'      => self::ARABIC_ALPHABET,
        'fi'      => self::SWEDISH_ALPHABET,
        'hu'      => self::HUNGARIAN_ALPHABET,
        'lt'      => self::LITHUANIAN_ALPHABET,
        'nb'      => self::NORWEGIAN_ALPHABET,
        'nl'      => self::DUTCH_ALPHABET,
        'nn'      => self::NORWEGIAN_ALPHABET,
        'pl'      => self::POLISH_ALPHABET,
        'ro'      => self::ROMANIAN_ALPHABET,
        'sk'      => self::SLOVAKIAN_ALPHABET,
        'sl'      => self::SLOVENIAN_ALPHABET,
        'sr-Latn' => self::SERBIAN_ALPHABET,
        'tr'      => self::TURKISH_ALPHABET,
        'sv'      => self::SWEDISH_ALPHABET,
    ];

    // Some languages collate names using digraphs (or trigraphs).
    private const DIGRAPHS = [
        'cs' => ['CH' => 'CH'],
        'da' => ['AA' => 'Å'],
        'nb' => ['AA' => 'Å'],
        'hu' => ['CS' => 'CS', 'DZS' => 'DZS', 'DZ' => 'DZ', 'GY' => 'GY', 'LY' => 'LY', 'NY' => 'NY', 'SZ' => 'SZ', 'TY' => 'TY', 'ZS' => 'ZS'],
        'nl' => ['IJ' => 'IJ'],
        'nn' => ['AA' => 'Å'],
        'sk' => ['DZ' => 'DZ', 'DŽ' => 'DŽ','CH' => 'CH'],
    ];

    /**
     * Which alphabet is used in a locale?
     *
     * @param LocaleInterface $locale
     *
     * @return array<string>
     */
    public function alphabet(LocaleInterface $locale): array
    {
        $language = $locale->languageTag();
        $script   = $locale->script()->code();

        return self::ALPHABETS[$language] ?? self::ALPHABETS_FOR_SCRIPT[$script] ?? self::LATIN_ALPHABET;
    }

    /**
     * Which calendar is used in a locale?
     *
     * @param LocaleInterface $locale
     *
     * @return CalendarInterface
     */
    public function calendar(LocaleInterface $locale): CalendarInterface
    {
        $non_gregorian_calendars = [
            'ar' => new ArabicCalendar(),
            'fa' => new PersianCalendar(),
            'he' => new JewishCalendar(),
            'yi' => new JewishCalendar(),
        ];

        return $non_gregorian_calendars[$locale->languageTag()] ?? new GregorianCalendar();
    }

    /**
     * Which MySQL collation should be used for this locale?
     *
     * @param LocaleInterface $locale
     *
     * @return string
     */
    public function collation(LocaleInterface $locale): string
    {
        $collation = $locale->collation();

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
     * Extract the initial letter (or digraph or trigraph) from a name.
     *
     * @param string          $text
     * @param LocaleInterface $locale
     *
     * @return string
     */
    public function initialLetter(string $text, LocaleInterface $locale): string
    {
        $text = I18N::strtoupper($text);

        $digraphs = self::DIGRAPHS[$locale->languageTag()] ?? [];

        foreach ($digraphs as $key => $value) {
            if (substr_compare($text, $key, 0, strlen($key)) === 0) {
                return $value;
            }
        }

        // No special rules - just take the first character
        return mb_substr($text, 0, 1);
    }

    /**
     * Convert a PHP date format string into DMY, MDY or YMD
     *
     * @param string $format
     *
     * @return string
     */
    public function dateFormatToOrder(string $format): string
    {
        if (preg_match('/[yY].*[mnFM].*[dj]/', $format)) {
            return 'YMD';
        }

        if (preg_match('/[mnFM].*[dj].*[yY]/', $format)) {
            return 'MDY';
        }

        return 'DMY';
    }
}
