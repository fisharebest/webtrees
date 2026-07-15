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

namespace Fisharebest\Webtrees\Enums;

/**
 * Plural rules used by gettext for translating plural strings.
 *
 * Each case represents a distinct plural formula. The plural() method takes a number
 * and returns the translation string index (0-based) to use for that number.
 *
 * @see https://www.gnu.org/software/gettext/manual/html_node/Plural-forms.html
 */
enum PluralRule
{
    /**
     * nplurals=1; plural=0
     * One form for all numbers.
     * Used by: Chinese, Japanese, Korean, Thai, Turkish (some), Vietnamese, Indonesian, etc.
     */
    case OneForm;

    /**
     * nplurals=2; plural=n != 1
     * Two forms: singular for n=1, plural for everything else.
     * Used by: English, German, Dutch, Swedish, Danish, Norwegian, Spanish, Italian, Portuguese, etc.
     */
    case TwoFormsSingularForOne;

    /**
     * nplurals=2; plural=n > 1
     * Two forms: singular for n=0 or n=1, plural for n>1.
     * Used by: French, Brazilian Portuguese, Hindi, Turkish, etc.
     */
    case TwoFormsPluralForMoreThanOne;

    /**
     * nplurals=2; plural=n==1 || n%10==1 ? 0 : 1
     * Two forms: singular when n=1 or n%10=1, plural otherwise.
     * Used by: Macedonian.
     */
    case TwoFormsMacedonian;

    /**
     * nplurals=2; plural=n != 1 && n != 2 && n != 3 && (n%10 == 4 || n%10 == 6 || n%10 == 9)
     * Two forms: specific Tagalog/Filipino plural rule.
     * Used by: Tagalog/Filipino.
     */
    case TwoFormsTagalog;

    /**
     * nplurals=3; plural=n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2
     * Three forms: Slavic plural rule.
     * Used by: Russian, Ukrainian, Croatian, Serbian, Bosnian.
     */
    case ThreeFormsSlavic;

    /**
     * nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2
     * Three forms: singular, small plural (2-4), large plural (rest).
     * Used by: Czech, Slovak.
     */
    case ThreeFormsCzechSlovak;

    /**
     * nplurals=3; plural=n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2
     * Three forms: Polish plural rule.
     * Used by: Polish.
     */
    case ThreeFormsPolish;

    /**
     * nplurals=3; plural=n==1 ? 0 : (n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2
     * Three forms: Romanian plural rule.
     * Used by: Romanian.
     */
    case ThreeFormsRomanian;

    /**
     * nplurals=3; plural=n%10==1 && n%100!=11 ? 0 : n%10>=2 && (n%100<10 || n%100>=20) ? 1 : 2
     * Three forms: Lithuanian plural rule.
     * Used by: Lithuanian.
     */
    case ThreeFormsLithuanian;

    /**
     * nplurals=3; plural=n%10==1 && n%100!=11 ? 0 : n != 0 ? 1 : 2
     * Three forms: Latvian plural rule.
     * Used by: Latvian.
     */
    case ThreeFormsLatvian;

    /**
     * nplurals=4; plural=n%100==1 ? 0 : n%100==2 ? 1 : n%100==3 || n%100==4 ? 2 : 3
     * Four forms: Slovenian plural rule.
     * Used by: Slovenian.
     */
    case FourFormsSlovenian;

    /**
     * nplurals=6; plural=n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 ? 4 : 5
     * Six forms: Arabic plural rule.
     * Used by: Arabic.
     */
    case SixFormsArabic;

    /**
     * nplurals=6; plural=(n==0) ? 0 : (n==1) ? 1 : (n==2) ? 2 : (n==3) ? 3 : (n==6) ? 4 : 5
     * Six forms: Welsh plural rule.
     * Used by: Welsh.
     */
    case SixFormsWelsh;

    /**
     * Return the number of plural forms for this rule.
     *
     * @return int The number of plural forms
     */
    public function nplurals(): int
    {
        return match ($this) {
            self::OneForm                      => 1,
            self::TwoFormsSingularForOne,
            self::TwoFormsPluralForMoreThanOne,
            self::TwoFormsMacedonian,
            self::TwoFormsTagalog              => 2,
            self::ThreeFormsSlavic,
            self::ThreeFormsCzechSlovak,
            self::ThreeFormsPolish,
            self::ThreeFormsRomanian,
            self::ThreeFormsLithuanian,
            self::ThreeFormsLatvian            => 3,
            self::FourFormsSlovenian           => 4,
            self::SixFormsArabic,
            self::SixFormsWelsh               => 6,
        };
    }

    /**
     * Apply the plural rule to a number and return the translation string index.
     *
     * @param int $n The number to determine the plural form for
     *
     * @return int The zero-based index of the translation string to use
     */
    public function plural(int $n): int
    {
        return match ($this) {
            self::OneForm => 0,

            self::TwoFormsSingularForOne => $n !== 1 ? 1 : 0,

            self::TwoFormsPluralForMoreThanOne => $n > 1 ? 1 : 0,

            self::TwoFormsMacedonian => $n === 1 || $n % 10 === 1 ? 0 : 1,

            self::TwoFormsTagalog => $n !== 1 && $n !== 2 && $n !== 3 && ($n % 10 === 4 || $n % 10 === 6 || $n % 10 === 9) ? 1 : 0,

            self::ThreeFormsSlavic => match (true) {
                $n % 10 === 1 && $n % 100 !== 11                                    => 0,
                $n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20)  => 1,
                default                                                              => 2,
            },

            self::ThreeFormsCzechSlovak => match (true) {
                $n === 1                  => 0,
                $n >= 2 && $n <= 4        => 1,
                default                   => 2,
            },

            self::ThreeFormsPolish => match (true) {
                $n === 1                                                             => 0,
                $n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20)  => 1,
                default                                                              => 2,
            },

            self::ThreeFormsRomanian => match (true) {
                $n === 1                                          => 0,
                $n === 0 || ($n % 100 > 0 && $n % 100 < 20)     => 1,
                default                                           => 2,
            },

            self::ThreeFormsLithuanian => match (true) {
                $n % 10 === 1 && $n % 100 !== 11                    => 0,
                $n % 10 >= 2 && ($n % 100 < 10 || $n % 100 >= 20)  => 1,
                default                                              => 2,
            },

            self::ThreeFormsLatvian => match (true) {
                $n % 10 === 1 && $n % 100 !== 11 => 0,
                $n !== 0                         => 1,
                default                          => 2,
            },

            self::FourFormsSlovenian => match (true) {
                $n % 100 === 1                          => 0,
                $n % 100 === 2                          => 1,
                $n % 100 === 3 || $n % 100 === 4        => 2,
                default                                  => 3,
            },

            self::SixFormsArabic => match (true) {
                $n === 0                                    => 0,
                $n === 1                                    => 1,
                $n === 2                                    => 2,
                $n % 100 >= 3 && $n % 100 <= 10            => 3,
                $n % 100 >= 11                             => 4,
                default                                     => 5,
            },

            self::SixFormsWelsh => match (true) {
                $n === 0 => 0,
                $n === 1 => 1,
                $n === 2 => 2,
                $n === 3 => 3,
                $n === 6 => 4,
                default  => 5,
            },
        };
    }
}
