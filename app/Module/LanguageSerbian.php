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
use Fisharebest\Localization\Locale\LocaleSr;
use Fisharebest\Webtrees\Encodings\UTF8;

/**
 * Class LanguageSerbian (Cyrillic).
 */
class LanguageSerbian extends AbstractModule implements ModuleLanguageInterface
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
            UTF8::CYRILLIC_CAPITAL_LETTER_DJE,
            UTF8::CYRILLIC_CAPITAL_LETTER_IE,
            UTF8::CYRILLIC_CAPITAL_LETTER_ZHE,
            UTF8::CYRILLIC_CAPITAL_LETTER_ZE,
            UTF8::CYRILLIC_CAPITAL_LETTER_I,
            UTF8::CYRILLIC_CAPITAL_LETTER_JE,
            UTF8::CYRILLIC_CAPITAL_LETTER_KA,
            UTF8::CYRILLIC_CAPITAL_LETTER_EL,
            UTF8::CYRILLIC_CAPITAL_LETTER_LJE,
            UTF8::CYRILLIC_CAPITAL_LETTER_EM,
            UTF8::CYRILLIC_CAPITAL_LETTER_EN,
            UTF8::CYRILLIC_CAPITAL_LETTER_NJE,
            UTF8::CYRILLIC_CAPITAL_LETTER_O,
            UTF8::CYRILLIC_CAPITAL_LETTER_PE,
            UTF8::CYRILLIC_CAPITAL_LETTER_ER,
            UTF8::CYRILLIC_CAPITAL_LETTER_ES,
            UTF8::CYRILLIC_CAPITAL_LETTER_TE,
            UTF8::CYRILLIC_CAPITAL_LETTER_TSHE,
            UTF8::CYRILLIC_CAPITAL_LETTER_U,
            UTF8::CYRILLIC_CAPITAL_LETTER_EF,
            UTF8::CYRILLIC_CAPITAL_LETTER_HA,
            UTF8::CYRILLIC_CAPITAL_LETTER_TSE,
            UTF8::CYRILLIC_CAPITAL_LETTER_CHE,
            UTF8::CYRILLIC_CAPITAL_LETTER_DZHE,
            UTF8::CYRILLIC_CAPITAL_LETTER_SHA,
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
        return new LocaleSr();
    }
}
