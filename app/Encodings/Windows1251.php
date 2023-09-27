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
 * Convert between Windows Code Page 1251 and UTF-8.
 *
 * @link https://en.wikipedia.org/wiki/Windows-1251
 */
class Windows1251 extends AbstractEncoding
{
    public const NAME = 'CP1251';

    protected const TO_UTF8 = [
        "\x80" => UTF8::CYRILLIC_CAPITAL_LETTER_DJE,
        "\x81" => UTF8::CYRILLIC_CAPITAL_LETTER_GJE,
        "\x82" => UTF8::SINGLE_LOW_9_QUOTATION_MARK,
        "\x83" => UTF8::CYRILLIC_SMALL_LETTER_GJE,
        "\x84" => UTF8::DOUBLE_LOW_9_QUOTATION_MARK,
        "\x85" => UTF8::HORIZONTAL_ELLIPSIS,
        "\x86" => UTF8::DAGGER,
        "\x87" => UTF8::DOUBLE_DAGGER,
        "\x88" => UTF8::EURO_SIGN,
        "\x89" => UTF8::PER_MILLE_SIGN,
        "\x8A" => UTF8::CYRILLIC_CAPITAL_LETTER_LJE,
        "\x8B" => UTF8::SINGLE_LEFT_POINTING_ANGLE_QUOTATION_MARK,
        "\x8C" => UTF8::CYRILLIC_CAPITAL_LETTER_NJE,
        "\x8D" => UTF8::CYRILLIC_CAPITAL_LETTER_KJE,
        "\x8E" => UTF8::CYRILLIC_CAPITAL_LETTER_TSHE,
        "\x8F" => UTF8::CYRILLIC_CAPITAL_LETTER_DZHE,
        "\x90" => UTF8::CYRILLIC_SMALL_LETTER_DJE,
        "\x91" => UTF8::LEFT_SINGLE_QUOTATION_MARK,
        "\x92" => UTF8::RIGHT_SINGLE_QUOTATION_MARK,
        "\x93" => UTF8::LEFT_DOUBLE_QUOTATION_MARK,
        "\x94" => UTF8::RIGHT_DOUBLE_QUOTATION_MARK,
        "\x95" => UTF8::BULLET,
        "\x96" => UTF8::EN_DASH,
        "\x97" => UTF8::EM_DASH,
        "\x98" => UTF8::REPLACEMENT_CHARACTER,
        "\x99" => UTF8::TRADE_MARK_SIGN,
        "\x9A" => UTF8::CYRILLIC_SMALL_LETTER_LJE,
        "\x9B" => UTF8::SINGLE_RIGHT_POINTING_ANGLE_QUOTATION_MARK,
        "\x9C" => UTF8::CYRILLIC_SMALL_LETTER_NJE,
        "\x9D" => UTF8::CYRILLIC_SMALL_LETTER_KJE,
        "\x9E" => UTF8::CYRILLIC_SMALL_LETTER_TSHE,
        "\x9F" => UTF8::CYRILLIC_SMALL_LETTER_DZHE,
        "\xA0" => UTF8::NO_BREAK_SPACE,
        "\xA1" => UTF8::CYRILLIC_CAPITAL_LETTER_SHORT_U,
        "\xA2" => UTF8::CYRILLIC_SMALL_LETTER_SHORT_U,
        "\xA3" => UTF8::CYRILLIC_CAPITAL_LETTER_JE,
        "\xA4" => UTF8::CURRENCY_SIGN,
        "\xA5" => UTF8::CYRILLIC_CAPITAL_LETTER_GHE_WITH_UPTURN,
        "\xA6" => UTF8::BROKEN_BAR,
        "\xA7" => UTF8::SECTION_SIGN,
        "\xA8" => UTF8::CYRILLIC_CAPITAL_LETTER_IO,
        "\xA9" => UTF8::COPYRIGHT_SIGN,
        "\xAA" => UTF8::CYRILLIC_CAPITAL_LETTER_UKRANIAN_IE,
        "\xAB" => UTF8::LEFT_POINTING_DOUBLE_ANGLE_QUOTATION_MARK,
        "\xAC" => UTF8::NOT_SIGN,
        "\xAD" => UTF8::SOFT_HYPHEN,
        "\xAE" => UTF8::REGISTERED_SIGN,
        "\xAF" => UTF8::CYRILLIC_CAPITAL_LETTER_YI,
        "\xB0" => UTF8::DEGREE_SIGN,
        "\xB1" => UTF8::PLUS_MINUS_SIGN,
        "\xB2" => UTF8::CYRILLIC_CAPITAL_LETTER_BYELORUSSIAN_UKRAINIAN_I,
        "\xB3" => UTF8::CYRILLIC_SMALL_LETTER_BYELORUSSIAN_UKRAINIAN_I,
        "\xB4" => UTF8::CYRILLIC_SMALL_LETTER_GHE_WITH_UPTURN,
        "\xB5" => UTF8::MICRO_SIGN,
        "\xB6" => UTF8::PILCROW_SIGN,
        "\xB7" => UTF8::MIDDLE_DOT,
        "\xB8" => UTF8::CYRILLIC_SMALL_LETTER_IO,
        "\xB9" => UTF8::NUMERO_SIGN,
        "\xBA" => UTF8::CYRILLIC_SMALL_LETTER_UKRANIAN_IE,
        "\xBB" => UTF8::RIGHT_POINTING_DOUBLE_ANGLE_QUOTATION_MARK,
        "\xBC" => UTF8::CYRILLIC_SMALL_LETTER_JE,
        "\xBD" => UTF8::CYRILLIC_CAPITAL_LETTER_DZE,
        "\xBE" => UTF8::CYRILLIC_SMALL_LETTER_DZE,
        "\xBF" => UTF8::CYRILLIC_SMALL_LETTER_YI,
        "\xC0" => UTF8::CYRILLIC_CAPITAL_LETTER_A,
        "\xC1" => UTF8::CYRILLIC_CAPITAL_LETTER_BE,
        "\xC2" => UTF8::CYRILLIC_CAPITAL_LETTER_VE,
        "\xC3" => UTF8::CYRILLIC_CAPITAL_LETTER_GHE,
        "\xC4" => UTF8::CYRILLIC_CAPITAL_LETTER_DE,
        "\xC5" => UTF8::CYRILLIC_CAPITAL_LETTER_IE,
        "\xC6" => UTF8::CYRILLIC_CAPITAL_LETTER_ZHE,
        "\xC7" => UTF8::CYRILLIC_CAPITAL_LETTER_ZE,
        "\xC8" => UTF8::CYRILLIC_CAPITAL_LETTER_I,
        "\xC9" => UTF8::CYRILLIC_CAPITAL_LETTER_SHORT_I,
        "\xCA" => UTF8::CYRILLIC_CAPITAL_LETTER_KA,
        "\xCB" => UTF8::CYRILLIC_CAPITAL_LETTER_EL,
        "\xCC" => UTF8::CYRILLIC_CAPITAL_LETTER_EM,
        "\xCD" => UTF8::CYRILLIC_CAPITAL_LETTER_EN,
        "\xCE" => UTF8::CYRILLIC_CAPITAL_LETTER_O,
        "\xCF" => UTF8::CYRILLIC_CAPITAL_LETTER_PE,
        "\xD0" => UTF8::CYRILLIC_CAPITAL_LETTER_ER,
        "\xD1" => UTF8::CYRILLIC_CAPITAL_LETTER_ES,
        "\xD2" => UTF8::CYRILLIC_CAPITAL_LETTER_TE,
        "\xD3" => UTF8::CYRILLIC_CAPITAL_LETTER_U,
        "\xD4" => UTF8::CYRILLIC_CAPITAL_LETTER_EF,
        "\xD5" => UTF8::CYRILLIC_CAPITAL_LETTER_HA,
        "\xD6" => UTF8::CYRILLIC_CAPITAL_LETTER_TSE,
        "\xD7" => UTF8::CYRILLIC_CAPITAL_LETTER_CHE,
        "\xD8" => UTF8::CYRILLIC_CAPITAL_LETTER_SHA,
        "\xD9" => UTF8::CYRILLIC_CAPITAL_LETTER_SHCHA,
        "\xDA" => UTF8::CYRILLIC_CAPITAL_LETTER_HARD_SIGN,
        "\xDB" => UTF8::CYRILLIC_CAPITAL_LETTER_YERU,
        "\xDC" => UTF8::CYRILLIC_CAPITAL_LETTER_SOFT_SIGN,
        "\xDD" => UTF8::CYRILLIC_CAPITAL_LETTER_E,
        "\xDE" => UTF8::CYRILLIC_CAPITAL_LETTER_YU,
        "\xDF" => UTF8::CYRILLIC_CAPITAL_LETTER_YA,
        "\xE0" => UTF8::CYRILLIC_SMALL_LETTER_A,
        "\xE1" => UTF8::CYRILLIC_SMALL_LETTER_BE,
        "\xE2" => UTF8::CYRILLIC_SMALL_LETTER_VE,
        "\xE3" => UTF8::CYRILLIC_SMALL_LETTER_GHE,
        "\xE4" => UTF8::CYRILLIC_SMALL_LETTER_DE,
        "\xE5" => UTF8::CYRILLIC_SMALL_LETTER_IE,
        "\xE6" => UTF8::CYRILLIC_SMALL_LETTER_ZHE,
        "\xE7" => UTF8::CYRILLIC_SMALL_LETTER_ZE,
        "\xE8" => UTF8::CYRILLIC_SMALL_LETTER_I,
        "\xE9" => UTF8::CYRILLIC_SMALL_LETTER_SHORT_I,
        "\xEA" => UTF8::CYRILLIC_SMALL_LETTER_KA,
        "\xEB" => UTF8::CYRILLIC_SMALL_LETTER_EL,
        "\xEC" => UTF8::CYRILLIC_SMALL_LETTER_EM,
        "\xED" => UTF8::CYRILLIC_SMALL_LETTER_EN,
        "\xEE" => UTF8::CYRILLIC_SMALL_LETTER_O,
        "\xEF" => UTF8::CYRILLIC_SMALL_LETTER_PE,
        "\xF0" => UTF8::CYRILLIC_SMALL_LETTER_ER,
        "\xF1" => UTF8::CYRILLIC_SMALL_LETTER_ES,
        "\xF2" => UTF8::CYRILLIC_SMALL_LETTER_TE,
        "\xF3" => UTF8::CYRILLIC_SMALL_LETTER_U,
        "\xF4" => UTF8::CYRILLIC_SMALL_LETTER_EF,
        "\xF5" => UTF8::CYRILLIC_SMALL_LETTER_HA,
        "\xF6" => UTF8::CYRILLIC_SMALL_LETTER_TSE,
        "\xF7" => UTF8::CYRILLIC_SMALL_LETTER_CHE,
        "\xF8" => UTF8::CYRILLIC_SMALL_LETTER_SHA,
        "\xF9" => UTF8::CYRILLIC_SMALL_LETTER_SHCHA,
        "\xFA" => UTF8::CYRILLIC_SMALL_LETTER_HARD_SIGN,
        "\xFB" => UTF8::CYRILLIC_SMALL_LETTER_YERU,
        "\xFC" => UTF8::CYRILLIC_SMALL_LETTER_SOFT_SIGN,
        "\xFD" => UTF8::CYRILLIC_SMALL_LETTER_E,
        "\xFE" => UTF8::CYRILLIC_SMALL_LETTER_YU,
        "\xFF" => UTF8::CYRILLIC_SMALL_LETTER_YA,
    ];
}
