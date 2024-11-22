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

namespace Fisharebest\Webtrees\Encodings;

/**
 * Convert between ISO 8859-1 and UTF-8.
 *
 * @link https://en.wikipedia.org/wiki/ISO/IEC_8859-1
 */
class ISO88591 extends AbstractEncoding
{
    public const string NAME = 'ISO-8859-1';

    protected const array TO_UTF8 = [
        "\x80" => UTF8::REPLACEMENT_CHARACTER,
        "\x81" => UTF8::REPLACEMENT_CHARACTER,
        "\x82" => UTF8::REPLACEMENT_CHARACTER,
        "\x83" => UTF8::REPLACEMENT_CHARACTER,
        "\x84" => UTF8::REPLACEMENT_CHARACTER,
        "\x85" => UTF8::REPLACEMENT_CHARACTER,
        "\x86" => UTF8::REPLACEMENT_CHARACTER,
        "\x87" => UTF8::REPLACEMENT_CHARACTER,
        "\x88" => UTF8::REPLACEMENT_CHARACTER,
        "\x89" => UTF8::REPLACEMENT_CHARACTER,
        "\x8A" => UTF8::REPLACEMENT_CHARACTER,
        "\x8B" => UTF8::REPLACEMENT_CHARACTER,
        "\x8C" => UTF8::REPLACEMENT_CHARACTER,
        "\x8D" => UTF8::REPLACEMENT_CHARACTER,
        "\x8E" => UTF8::REPLACEMENT_CHARACTER,
        "\x8F" => UTF8::REPLACEMENT_CHARACTER,
        "\x90" => UTF8::REPLACEMENT_CHARACTER,
        "\x91" => UTF8::REPLACEMENT_CHARACTER,
        "\x92" => UTF8::REPLACEMENT_CHARACTER,
        "\x93" => UTF8::REPLACEMENT_CHARACTER,
        "\x94" => UTF8::REPLACEMENT_CHARACTER,
        "\x95" => UTF8::REPLACEMENT_CHARACTER,
        "\x96" => UTF8::REPLACEMENT_CHARACTER,
        "\x97" => UTF8::REPLACEMENT_CHARACTER,
        "\x98" => UTF8::REPLACEMENT_CHARACTER,
        "\x99" => UTF8::REPLACEMENT_CHARACTER,
        "\x9A" => UTF8::REPLACEMENT_CHARACTER,
        "\x9B" => UTF8::REPLACEMENT_CHARACTER,
        "\x9C" => UTF8::REPLACEMENT_CHARACTER,
        "\x9D" => UTF8::REPLACEMENT_CHARACTER,
        "\x9E" => UTF8::REPLACEMENT_CHARACTER,
        "\x9F" => UTF8::REPLACEMENT_CHARACTER,
        "\xA0" => UTF8::NO_BREAK_SPACE,
        "\xA1" => UTF8::INVERTED_EXCLAMATION_MARK,
        "\xA2" => UTF8::CENT_SIGN,
        "\xA3" => UTF8::POUND_SIGN,
        "\xA4" => UTF8::CURRENCY_SIGN,
        "\xA5" => UTF8::YEN_SIGN,
        "\xA6" => UTF8::BROKEN_BAR,
        "\xA7" => UTF8::SECTION_SIGN,
        "\xA8" => UTF8::DIAERESIS,
        "\xA9" => UTF8::COPYRIGHT_SIGN,
        "\xAA" => UTF8::FEMININE_ORDINAL_INDICATOR,
        "\xAB" => UTF8::LEFT_POINTING_DOUBLE_ANGLE_QUOTATION_MARK,
        "\xAC" => UTF8::NOT_SIGN,
        "\xAD" => UTF8::SOFT_HYPHEN,
        "\xAE" => UTF8::REGISTERED_SIGN,
        "\xAF" => UTF8::MACRON,
        "\xB0" => UTF8::DEGREE_SIGN,
        "\xB1" => UTF8::PLUS_MINUS_SIGN,
        "\xB2" => UTF8::SUPERSCRIPT_TWO,
        "\xB3" => UTF8::SUPERSCRIPT_THREE,
        "\xB4" => UTF8::ACUTE_ACCENT,
        "\xB5" => UTF8::MICRO_SIGN,
        "\xB6" => UTF8::PILCROW_SIGN,
        "\xB7" => UTF8::MIDDLE_DOT,
        "\xB8" => UTF8::CEDILLA,
        "\xB9" => UTF8::SUPERSCRIPT_ONE,
        "\xBA" => UTF8::MASCULINE_ORDINAL_INDICATOR,
        "\xBB" => UTF8::RIGHT_POINTING_DOUBLE_ANGLE_QUOTATION_MARK,
        "\xBC" => UTF8::VULGAR_FRACTION_ONE_QUARTER,
        "\xBD" => UTF8::VULGAR_FRACTION_ONE_HALF,
        "\xBE" => UTF8::VULGAR_FRACTION_THREE_QUARTERS,
        "\xBF" => UTF8::INVERTED_QUESTION_MARK,
        "\xC0" => UTF8::LATIN_CAPITAL_LETTER_A_WITH_GRAVE,
        "\xC1" => UTF8::LATIN_CAPITAL_LETTER_A_WITH_ACUTE,
        "\xC2" => UTF8::LATIN_CAPITAL_LETTER_A_WITH_CIRCUMFLEX,
        "\xC3" => UTF8::LATIN_CAPITAL_LETTER_A_WITH_TILDE,
        "\xC4" => UTF8::LATIN_CAPITAL_LETTER_A_WITH_DIAERESIS,
        "\xC5" => UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_ABOVE,
        "\xC6" => UTF8::LATIN_CAPITAL_LETTER_AE,
        "\xC7" => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CEDILLA,
        "\xC8" => UTF8::LATIN_CAPITAL_LETTER_E_WITH_GRAVE,
        "\xC9" => UTF8::LATIN_CAPITAL_LETTER_E_WITH_ACUTE,
        "\xCA" => UTF8::LATIN_CAPITAL_LETTER_E_WITH_CIRCUMFLEX,
        "\xCB" => UTF8::LATIN_CAPITAL_LETTER_E_WITH_DIAERESIS,
        "\xCC" => UTF8::LATIN_CAPITAL_LETTER_I_WITH_GRAVE,
        "\xCD" => UTF8::LATIN_CAPITAL_LETTER_I_WITH_ACUTE,
        "\xCE" => UTF8::LATIN_CAPITAL_LETTER_I_WITH_CIRCUMFLEX,
        "\xCF" => UTF8::LATIN_CAPITAL_LETTER_I_WITH_DIAERESIS,
        "\xD0" => UTF8::LATIN_CAPITAL_LETTER_ETH,
        "\xD1" => UTF8::LATIN_CAPITAL_LETTER_N_WITH_TILDE,
        "\xD2" => UTF8::LATIN_CAPITAL_LETTER_O_WITH_GRAVE,
        "\xD3" => UTF8::LATIN_CAPITAL_LETTER_O_WITH_ACUTE,
        "\xD4" => UTF8::LATIN_CAPITAL_LETTER_O_WITH_CIRCUMFLEX,
        "\xD5" => UTF8::LATIN_CAPITAL_LETTER_O_WITH_TILDE,
        "\xD6" => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
        "\xD7" => UTF8::MULTIPLICATION_SIGN,
        "\xD8" => UTF8::LATIN_CAPITAL_LETTER_O_WITH_STROKE,
        "\xD9" => UTF8::LATIN_CAPITAL_LETTER_U_WITH_GRAVE,
        "\xDA" => UTF8::LATIN_CAPITAL_LETTER_U_WITH_ACUTE,
        "\xDB" => UTF8::LATIN_CAPITAL_LETTER_U_WITH_CIRCUMFLEX,
        "\xDC" => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS,
        "\xDD" => UTF8::LATIN_CAPITAL_LETTER_Y_WITH_ACUTE,
        "\xDE" => UTF8::LATIN_CAPITAL_LETTER_THORN,
        "\xDF" => UTF8::LATIN_SMALL_LETTER_SHARP_S,
        "\xE0" => UTF8::LATIN_SMALL_LETTER_A_WITH_GRAVE,
        "\xE1" => UTF8::LATIN_SMALL_LETTER_A_WITH_ACUTE,
        "\xE2" => UTF8::LATIN_SMALL_LETTER_A_WITH_CIRCUMFLEX,
        "\xE3" => UTF8::LATIN_SMALL_LETTER_A_WITH_TILDE,
        "\xE4" => UTF8::LATIN_SMALL_LETTER_A_WITH_DIAERESIS,
        "\xE5" => UTF8::LATIN_SMALL_LETTER_A_WITH_RING_ABOVE,
        "\xE6" => UTF8::LATIN_SMALL_LETTER_AE,
        "\xE7" => UTF8::LATIN_SMALL_LETTER_C_WITH_CEDILLA,
        "\xE8" => UTF8::LATIN_SMALL_LETTER_E_WITH_GRAVE,
        "\xE9" => UTF8::LATIN_SMALL_LETTER_E_WITH_ACUTE,
        "\xEA" => UTF8::LATIN_SMALL_LETTER_E_WITH_CIRCUMFLEX,
        "\xEB" => UTF8::LATIN_SMALL_LETTER_E_WITH_DIAERESIS,
        "\xEC" => UTF8::LATIN_SMALL_LETTER_I_WITH_GRAVE,
        "\xED" => UTF8::LATIN_SMALL_LETTER_I_WITH_ACUTE,
        "\xEE" => UTF8::LATIN_SMALL_LETTER_I_WITH_CIRCUMFLEX,
        "\xEF" => UTF8::LATIN_SMALL_LETTER_I_WITH_DIAERESIS,
        "\xF0" => UTF8::LATIN_SMALL_LETTER_ETH,
        "\xF1" => UTF8::LATIN_SMALL_LETTER_N_WITH_TILDE,
        "\xF2" => UTF8::LATIN_SMALL_LETTER_O_WITH_GRAVE,
        "\xF3" => UTF8::LATIN_SMALL_LETTER_O_WITH_ACUTE,
        "\xF4" => UTF8::LATIN_SMALL_LETTER_O_WITH_CIRCUMFLEX,
        "\xF5" => UTF8::LATIN_SMALL_LETTER_O_WITH_TILDE,
        "\xF6" => UTF8::LATIN_SMALL_LETTER_O_WITH_DIAERESIS,
        "\xF7" => UTF8::DIVISION_SIGN,
        "\xF8" => UTF8::LATIN_SMALL_LETTER_O_WITH_STROKE,
        "\xF9" => UTF8::LATIN_SMALL_LETTER_U_WITH_GRAVE,
        "\xFA" => UTF8::LATIN_SMALL_LETTER_U_WITH_ACUTE,
        "\xFB" => UTF8::LATIN_SMALL_LETTER_U_WITH_CIRCUMFLEX,
        "\xFC" => UTF8::LATIN_SMALL_LETTER_U_WITH_DIAERESIS,
        "\xFD" => UTF8::LATIN_SMALL_LETTER_Y_WITH_ACUTE,
        "\xFE" => UTF8::LATIN_SMALL_LETTER_THORN,
        "\xFF" => UTF8::LATIN_SMALL_LETTER_Y_WITH_DIAERESIS,
    ];
}
