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
use Fisharebest\Localization\Locale\LocaleRo;
use Fisharebest\Webtrees\Encodings\UTF8;
use Illuminate\Database\Query\Builder;

/**
 * Class LanguageRomanian.
 */
class LanguageRomanian extends AbstractModule implements ModuleLanguageInterface
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
            UTF8::LATIN_CAPITAL_LETTER_A_WITH_BREVE,
            UTF8::LATIN_CAPITAL_LETTER_A_WITH_CIRCUMFLEX,
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            UTF8::LATIN_CAPITAL_LETTER_I_WITH_CIRCUMFLEX,
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            UTF8::LATIN_CAPITAL_LETTER_S_WITH_CEDILLA,
            'T',
            'Ţ',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
        ];
    }

    /**
     * Should this module be enabled when it is first installed?
     *
     * @return bool
     */
    public function isEnabledByDefault(): bool
    {
        return false;
    }

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface
    {
        return new LocaleRo();
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'A' . UTF8::COMBINING_BREVE             => UTF8::LATIN_CAPITAL_LETTER_A_WITH_BREVE,
            'A' . UTF8::COMBINING_CIRCUMFLEX_ACCENT => UTF8::LATIN_CAPITAL_LETTER_A_WITH_CIRCUMFLEX,
            'I' . UTF8::COMBINING_CIRCUMFLEX_ACCENT => UTF8::LATIN_CAPITAL_LETTER_I_WITH_CIRCUMFLEX,
            'S' . UTF8::COMBINING_CEDILLA           => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CEDILLA,
            'a' . UTF8::COMBINING_BREVE             => UTF8::LATIN_SMALL_LETTER_A_WITH_BREVE,
            'a' . UTF8::COMBINING_CIRCUMFLEX_ACCENT => UTF8::LATIN_SMALL_LETTER_A_WITH_CIRCUMFLEX,
            'i' . UTF8::COMBINING_CIRCUMFLEX_ACCENT => UTF8::LATIN_SMALL_LETTER_I_WITH_CIRCUMFLEX,
            's' . UTF8::COMBINING_CEDILLA           => UTF8::LATIN_SMALL_LETTER_S_WITH_CEDILLA,
        ];
    }
}
