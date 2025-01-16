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
use Fisharebest\Localization\Locale\LocaleSrLatn;
use Fisharebest\Webtrees\Encodings\UTF8;

/**
 * Class LanguageSerbianLatin.
 */
class LanguageSerbianLatin extends AbstractModule implements ModuleLanguageInterface
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
            'B',
            'C',
            UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
            UTF8::LATIN_CAPITAL_LETTER_C_WITH_ACUTE,
            'D',
            'D' . UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
            UTF8::LATIN_CAPITAL_LETTER_D_WITH_STROKE,
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'LJ',
            'M',
            'N',
            'NJ',
            'O',
            'P',
            'Q',
            'R',
            'S',
            UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
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
        return new LocaleSrLatn();
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'C' . UTF8::COMBINING_CARON                => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
            'C' . UTF8::COMBINING_CEDILLA              => UTF8::LATIN_CAPITAL_LETTER_C_WITH_ACUTE,
            'D' . UTF8::COMBINING_SHORT_STROKE_OVERLAY => UTF8::LATIN_CAPITAL_LETTER_D_WITH_STROKE,
            'S' . UTF8::COMBINING_CARON                => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
            'Z' . UTF8::COMBINING_CARON                => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
            'c' . UTF8::COMBINING_CARON                => UTF8::LATIN_SMALL_LETTER_C_WITH_CARON,
            'c' . UTF8::COMBINING_CEDILLA              => UTF8::LATIN_SMALL_LETTER_C_WITH_ACUTE,
            'd' . UTF8::COMBINING_SHORT_STROKE_OVERLAY => UTF8::LATIN_SMALL_LETTER_D_WITH_STROKE,
            's' . UTF8::COMBINING_CARON                => UTF8::LATIN_SMALL_LETTER_S_WITH_CARON,
            'z' . UTF8::COMBINING_CARON                => UTF8::LATIN_SMALL_LETTER_Z_WITH_CARON,
        ];
    }
}
