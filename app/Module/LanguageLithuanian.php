<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Localization\Locale\LocaleLt;
use Fisharebest\Webtrees\Encodings\UTF8;
use Illuminate\Database\Query\Builder;

/**
 * Class LanguageLithuanian.
 */
class LanguageLithuanian extends AbstractModule implements ModuleLanguageInterface
{
    use ModuleLanguageTrait;

    /**
     * Phone-book ordering of letters.
     *
     * @return array<int,string>
     */
    public function alphabet(): array
    {
        return [
            'A',
            UTF8::LATIN_CAPITAL_LETTER_A_WITH_OGONEK,
            'B',
            'C',
            UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
            'D',
            'E',
            UTF8::LATIN_CAPITAL_LETTER_E_WITH_OGONEK,
            UTF8::LATIN_CAPITAL_LETTER_E_WITH_DOT_ABOVE,
            'F',
            'G',
            'H',
            'I',
            'Y',
            UTF8::LATIN_CAPITAL_LETTER_I_WITH_OGONEK,
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'R',
            'S',
            UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
            'T',
            'U',
            UTF8::LATIN_CAPITAL_LETTER_U_WITH_OGONEK,
            UTF8::LATIN_CAPITAL_LETTER_U_WITH_MACRON,
            'V',
            'Z',
            UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
        ];
    }

    /**
     * One of: 'DMY', 'MDY', 'YMD'.
     *
     * @return string
     */
    public function dateOrder(): string
    {
        return 'YMD';
    }

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface
    {
        return new LocaleLt();
    }


    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'A' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_A_WITH_OGONEK,
            'C' . UTF8::COMBINING_CARON     => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
            'E' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_E_WITH_OGONEK,
            'E' . UTF8::COMBINING_DOT_ABOVE => UTF8::LATIN_CAPITAL_LETTER_E_WITH_DOT_ABOVE,
            'I' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_I_WITH_OGONEK,
            'S' . UTF8::COMBINING_CARON     => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
            'U' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_U_WITH_OGONEK,
            'U' . UTF8::COMBINING_MACRON    => UTF8::LATIN_CAPITAL_LETTER_U_WITH_MACRON,
            'Z' . UTF8::COMBINING_CARON     => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
            'a' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_A_WITH_OGONEK,
            'c' . UTF8::COMBINING_CARON     => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
            'e' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_E_WITH_OGONEK,
            'e' . UTF8::COMBINING_DOT_ABOVE => UTF8::LATIN_CAPITAL_LETTER_E_WITH_DOT_ABOVE,
            'i' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_I_WITH_OGONEK,
            's' . UTF8::COMBINING_CARON     => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
            'u' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_U_WITH_OGONEK,
            'u' . UTF8::COMBINING_MACRON    => UTF8::LATIN_CAPITAL_LETTER_U_WITH_MACRON,
            'z' . UTF8::COMBINING_CARON     => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
        ];
    }
}
