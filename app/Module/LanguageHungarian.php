<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fisharebest\Localization\Locale\LocaleHu;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\Encodings\UTF8;
use Illuminate\Database\Query\Builder;

use function mb_substr;
use function str_starts_with;

/**
 * Class LanguageHungarian.
 */
class LanguageHungarian extends AbstractModule implements ModuleLanguageInterface
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
            UTF8::LATIN_CAPITAL_LETTER_A_WITH_ACUTE,
            'B',
            'C',
            'CS',
            'D',
            'DZ',
            'DZS',
            'E',
            UTF8::LATIN_CAPITAL_LETTER_E_WITH_ACUTE,
            'F',
            'G',
            'GY',
            'H',
            'I',
            UTF8::LATIN_CAPITAL_LETTER_I_WITH_ACUTE,
            'J',
            'K',
            'L',
            'LY',
            'M',
            'N',
            'NY',
            'O',
            UTF8::LATIN_CAPITAL_LETTER_O_WITH_ACUTE,
            UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
            UTF8::LATIN_CAPITAL_LETTER_O_WITH_DOUBLE_ACUTE,
            'P',
            'Q',
            'R',
            'S',
            'SZ',
            'T',
            'TY',
            'U',
            UTF8::LATIN_CAPITAL_LETTER_U_WITH_ACUTE,
            UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS,
            UTF8::LATIN_CAPITAL_LETTER_U_WITH_DOUBLE_ACUTE,
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'ZS',
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
     * Some languages use digraphs and trigraphs.
     *
     * @param string $string
     *
     * @return string
     */
    public function initialLetter(string $string): string
    {
        foreach (['CS', 'DZS', 'DZ', 'GY', 'LY', 'NY', 'SZ', 'TY', 'ZS'] as $digraph) {
            if (str_starts_with($string, $digraph)) {
                return $digraph;
            }
        }

        return mb_substr($string, 0, 1);
    }

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface
    {
        return new LocaleHu();
    }


    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'A' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_CAPITAL_LETTER_A_WITH_ACUTE,
            'E' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_CAPITAL_LETTER_E_WITH_ACUTE,
            'I' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_CAPITAL_LETTER_I_WITH_ACUTE,
            'O' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_CAPITAL_LETTER_O_WITH_ACUTE,
            'O' . UTF8::COMBINING_DIAERESIS           => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
            'O' . UTF8::COMBINING_DOUBLE_ACUTE_ACCENT => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DOUBLE_ACUTE,
            'U' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_CAPITAL_LETTER_U_WITH_ACUTE,
            'U' . UTF8::COMBINING_DIAERESIS           => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS,
            'U' . UTF8::COMBINING_DOUBLE_ACUTE_ACCENT => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DOUBLE_ACUTE,
            'a' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_SMALL_LETTER_A_WITH_ACUTE,
            'e' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_SMALL_LETTER_E_WITH_ACUTE,
            'i' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_SMALL_LETTER_I_WITH_ACUTE,
            'o' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_SMALL_LETTER_O_WITH_ACUTE,
            'o' . UTF8::COMBINING_DIAERESIS           => UTF8::LATIN_SMALL_LETTER_O_WITH_DIAERESIS,
            'o' . UTF8::COMBINING_DOUBLE_ACUTE_ACCENT => UTF8::LATIN_SMALL_LETTER_O_WITH_DOUBLE_ACUTE,
            'u' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_SMALL_LETTER_U_WITH_ACUTE,
            'u' . UTF8::COMBINING_DIAERESIS           => UTF8::LATIN_SMALL_LETTER_U_WITH_DIAERESIS,
            'u' . UTF8::COMBINING_DOUBLE_ACUTE_ACCENT => UTF8::LATIN_SMALL_LETTER_U_WITH_DOUBLE_ACUTE,
        ];
    }
}
