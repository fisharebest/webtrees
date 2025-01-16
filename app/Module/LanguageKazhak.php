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
use Fisharebest\Localization\Locale\LocaleKk;
use Fisharebest\Webtrees\Encodings\UTF8;

/**
 * Class LanguageKazhak.
 */
class LanguageKazhak extends AbstractModule implements ModuleLanguageInterface
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
            UTF8::CYRILLIC_CAPITAL_LETTER_A,
            UTF8::CYRILLIC_CAPITAL_LETTER_BE,
            UTF8::CYRILLIC_CAPITAL_LETTER_VE,
            UTF8::CYRILLIC_CAPITAL_LETTER_GHE,
            UTF8::CYRILLIC_CAPITAL_LETTER_DE,
            UTF8::CYRILLIC_CAPITAL_LETTER_IE,
            UTF8::CYRILLIC_CAPITAL_LETTER_IO,
            UTF8::CYRILLIC_CAPITAL_LETTER_ZHE,
            UTF8::CYRILLIC_CAPITAL_LETTER_ZE,
            UTF8::CYRILLIC_CAPITAL_LETTER_I,
            UTF8::CYRILLIC_CAPITAL_LETTER_SHORT_I,
            UTF8::CYRILLIC_CAPITAL_LETTER_KA,
            UTF8::CYRILLIC_CAPITAL_LETTER_EL,
            UTF8::CYRILLIC_CAPITAL_LETTER_EM,
            UTF8::CYRILLIC_CAPITAL_LETTER_EN,
            UTF8::CYRILLIC_CAPITAL_LETTER_O,
            UTF8::CYRILLIC_CAPITAL_LETTER_PE,
            UTF8::CYRILLIC_CAPITAL_LETTER_ER,
            UTF8::CYRILLIC_CAPITAL_LETTER_ES,
            UTF8::CYRILLIC_CAPITAL_LETTER_TE,
            UTF8::CYRILLIC_CAPITAL_LETTER_U,
            UTF8::CYRILLIC_CAPITAL_LETTER_EF,
            UTF8::CYRILLIC_CAPITAL_LETTER_HA,
            UTF8::CYRILLIC_CAPITAL_LETTER_TSE,
            UTF8::CYRILLIC_CAPITAL_LETTER_CHE,
            UTF8::CYRILLIC_CAPITAL_LETTER_SHA,
            UTF8::CYRILLIC_CAPITAL_LETTER_SHCHA,
            UTF8::CYRILLIC_CAPITAL_LETTER_HARD_SIGN,
            UTF8::CYRILLIC_CAPITAL_LETTER_YERU,
            UTF8::CYRILLIC_CAPITAL_LETTER_SOFT_SIGN,
            UTF8::CYRILLIC_CAPITAL_LETTER_E,
            UTF8::CYRILLIC_CAPITAL_LETTER_YU,
            UTF8::CYRILLIC_CAPITAL_LETTER_YA,
        ];
    }

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface
    {
        return new LocaleKk();
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            UTF8::CYRILLIC_CAPITAL_LETTER_IE . UTF8::COMBINING_DIAERESIS => UTF8::CYRILLIC_CAPITAL_LETTER_IO,
            UTF8::CYRILLIC_SMALL_LETTER_IE . UTF8::COMBINING_DIAERESIS   => UTF8::CYRILLIC_SMALL_LETTER_IO,
            UTF8::CYRILLIC_CAPITAL_LETTER_I . UTF8::COMBINING_BREVE      => UTF8::CYRILLIC_CAPITAL_LETTER_SHORT_I,
            UTF8::CYRILLIC_SMALL_LETTER_I . UTF8::COMBINING_BREVE        => UTF8::CYRILLIC_SMALL_LETTER_SHORT_I,
        ];
    }
}
