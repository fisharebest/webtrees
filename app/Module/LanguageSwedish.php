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
use Fisharebest\Localization\Locale\LocaleSv;
use Fisharebest\Webtrees\Encodings\UTF8;
use Illuminate\Database\Query\Builder;

/**
 * Class LanguageSwedish.
 */
class LanguageSwedish extends AbstractModule implements ModuleLanguageInterface
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
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
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
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_ABOVE,
            UTF8::LATIN_CAPITAL_LETTER_A_WITH_DIAERESIS,
            UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
        ];
    }

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface
    {
        return new LocaleSv();
    }


    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'A' . UTF8::COMBINING_RING_ABOVE => UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_ABOVE,
            'A' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_CAPITAL_LETTER_A_WITH_DIAERESIS,
            'O' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
            'a' . UTF8::COMBINING_RING_ABOVE => UTF8::LATIN_SMALL_LETTER_A_WITH_RING_ABOVE,
            'a' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_SMALL_LETTER_A_WITH_DIAERESIS,
            'o' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_SMALL_LETTER_O_WITH_DIAERESIS,
        ];
    }
}
