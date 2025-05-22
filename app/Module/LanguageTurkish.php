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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Localization\Locale\LocaleTr;
use Fisharebest\Webtrees\Encodings\UTF8;

class LanguageTurkish extends AbstractModule implements ModuleLanguageInterface
{
    use ModuleLanguageTrait;

    /**
     * @return array<int,string>
     */
    public function alphabet(): array
    {
        return [
            'A',
            'B',
            'C',
            UTF8::LATIN_CAPITAL_LETTER_C_WITH_CEDILLA,
            'D',
            'E',
            'F',
            'G',
            UTF8::LATIN_CAPITAL_LETTER_G_WITH_BREVE,
            'H',
            'I',
            UTF8::LATIN_CAPITAL_LETTER_I_WITH_DOT_ABOVE,
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
            'P',
            'R',
            'S',
            UTF8::LATIN_CAPITAL_LETTER_S_WITH_CEDILLA,
            'T',
            'U',
            UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS,
            'V',
            'Y',
            'Z',
        ];
    }

    public function locale(): LocaleInterface
    {
        return new LocaleTr();
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'C' . UTF8::COMBINING_CEDILLA   => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CEDILLA,
            'G' . UTF8::COMBINING_BREVE     => UTF8::LATIN_CAPITAL_LETTER_G_WITH_BREVE,
            'I' . UTF8::COMBINING_DOT_ABOVE => UTF8::LATIN_CAPITAL_LETTER_I_WITH_DOT_ABOVE,
            'O' . UTF8::COMBINING_DIAERESIS => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
            'S' . UTF8::COMBINING_CEDILLA   => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CEDILLA,
            'U' . UTF8::COMBINING_DIAERESIS => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS,
            'c' . UTF8::COMBINING_CEDILLA   => UTF8::LATIN_SMALL_LETTER_C_WITH_CEDILLA,
            'g' . UTF8::COMBINING_BREVE     => UTF8::LATIN_SMALL_LETTER_G_WITH_BREVE,
            'o' . UTF8::COMBINING_DIAERESIS => UTF8::LATIN_SMALL_LETTER_O_WITH_DIAERESIS,
            's' . UTF8::COMBINING_CEDILLA   => UTF8::LATIN_SMALL_LETTER_S_WITH_CEDILLA,
            'u' . UTF8::COMBINING_DIAERESIS => UTF8::LATIN_SMALL_LETTER_U_WITH_DIAERESIS,
        ];
    }
}
