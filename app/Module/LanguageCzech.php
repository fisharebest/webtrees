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

use Fisharebest\Localization\Locale\LocaleCs;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\Encodings\UTF8;
use Illuminate\Database\Query\Builder;

use function mb_substr;
use function str_starts_with;

/**
 * Class LanguageCzech.
 */
class LanguageCzech extends AbstractModule implements ModuleLanguageInterface
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
            UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
            'D',
            UTF8::LATIN_CAPITAL_LETTER_D_WITH_CARON,
            'E',
            UTF8::LATIN_CAPITAL_LETTER_E_WITH_ACUTE,
            UTF8::LATIN_CAPITAL_LETTER_E_WITH_CARON,
            'F',
            'G',
            'H',
            'CH',
            'I',
            UTF8::LATIN_CAPITAL_LETTER_I_WITH_ACUTE,
            'J',
            'K',
            'L',
            'M',
            'N',
            UTF8::LATIN_CAPITAL_LETTER_N_WITH_CARON,
            'O',
            UTF8::LATIN_CAPITAL_LETTER_O_WITH_ACUTE,
            'P',
            'Q',
            'R',
            UTF8::LATIN_CAPITAL_LETTER_R_WITH_CARON,
            'S',
            UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
            'T',
            UTF8::LATIN_CAPITAL_LETTER_T_WITH_CARON,
            'U',
            UTF8::LATIN_CAPITAL_LETTER_U_WITH_ACUTE,
            UTF8::LATIN_CAPITAL_LETTER_U_WITH_RING_ABOVE,
            'V',
            'W',
            'X',
            'Y',
            UTF8::LATIN_CAPITAL_LETTER_Y_WITH_ACUTE,
            'Z',
            UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
        ];
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
        if (str_starts_with($string, 'CS')) {
            return 'CS';
        }

        return mb_substr($string, 0, 1);
    }

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface
    {
        return new LocaleCs();
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'A' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_A_WITH_ACUTE,
            'C' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
            'D' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_D_WITH_CARON,
            'E' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_CAPITAL_LETTER_E_WITH_ACUTE,
            'E' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_E_WITH_CARON,
            'I' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_CAPITAL_LETTER_I_WITH_ACUTE,
            'N' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_N_WITH_CARON,
            'O' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_CAPITAL_LETTER_O_WITH_ACUTE,
            'R' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_R_WITH_CARON,
            'S' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
            'T' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_T_WITH_CARON,
            'U' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_CAPITAL_LETTER_U_WITH_ACUTE,
            'U' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_U_WITH_RING_ABOVE,
            'Y' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_CAPITAL_LETTER_Y_WITH_ACUTE,
            'Z' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
            'a' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_A_WITH_ACUTE,
            'c' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_C_WITH_CARON,
            'd' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_D_WITH_CARON,
            'e' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_SMALL_LETTER_E_WITH_ACUTE,
            'e' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_E_WITH_CARON,
            'i' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_SMALL_LETTER_I_WITH_ACUTE,
            'n' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_N_WITH_CARON,
            'o' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_SMALL_LETTER_O_WITH_ACUTE,
            'r' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_R_WITH_CARON,
            's' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_S_WITH_CARON,
            't' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_T_WITH_CARON,
            'u' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_SMALL_LETTER_U_WITH_ACUTE,
            'u' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_U_WITH_RING_ABOVE,
            'y' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_SMALL_LETTER_Y_WITH_ACUTE,
            'z' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_Z_WITH_CARON,
        ];
    }
}
