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

namespace Fisharebest\Webtrees\Encodings;

use function preg_replace;
use function strtr;

/**
 * Convert between UTF-8 and ANSEL encoding.
 *
 * ANSEL is the common name for the MARC-21 encoding, also known as Z39.47, which
 * has a number of editions.  These are denoted by a year suffix.
 *
 * The GEDCOM 5.5.1 specification (1999-10-02) specifies the Z39.47-1985 edition.
 * It adds Es Zett (ß) at CF.
 *
 * According to wikipedia, other non-standard characters are also added.
 *
 * HEX Unicode Glyph Description
 * BE  25A1    □     Empty box
 * BF  25A0    ■     Black box
 * CD  0065    e     Midline e
 * CE  006F    o     Midline o
 * CF  00DF    ß     Es Zett
 * FC  0338    /     Combining slash
 *
 * @link https://en.wikipedia.org/wiki/ANSEL
 *
 * The MARC-21 specification has added a number of additional characters since
 * the 1985 edition.
 *
 * HEX Unicode Glyph Description
 * 88  0098          Start of string
 * 89  009C          String terminator
 * 8D  200D          Zero width joiner
 * 8E  200C          Zero width non-joiner
 * A7  CAB9       ʹ     Single prime
 * AC  C6AF    Ơ     LATIN CAPITAL LETTER O WITH HORN
 * AD  C6AF    Ư     LATIN CAPITAL LETTER U WITH HORN
 * B7  CABA    ʺ     Double prime
 * BC  C6A1    ơ     LATIN SMALL LETTER O WITH HORN
 * BD  C6B0    ư     LATIN SMALL LETTER U WITH HORN
 * C0  C2B0    °     Degree sign
 * C1  E28493  ℓ     Script small L
 * C2  E28497  ℗     Sound recording copyright
 * C4  E282AC  ♯     Music sharp sign
 * C7  00DF    ß     Es Zett
 * C8  20AC    €     Euro sign
 * E0  0309          Hook above
 * EB  0361          Breve (first part / double)
 * EC  0361          Breve (second part)
 * EF  0310          Candrabindu
 * F2  0323          Low dot
 * F3  0324          Diaeresis below
 * F4  0325          Ring below
 * F5  0333          Double underline
 * F7  0332          Underline
 * F8  031C          Comma below
 * F9  032E          Breve below
 * FA  0360          Double tilde (first part / double).
 * FB  0360          Double tilde (second part).
 * FF  0338          Slash
 *
 * @link https://memory.loc.gov/diglib/codetables/45.html
 *
 * Note that this means we can expect two different representations of Es Zett.
 *
 * There are two multi-part diacritics.  There are two ways to represent these.
 *
 * ANSEL       | UTF-8         | UTF-8 (prefered)
 * ------------+---------------+-----------------
 * FA x FB y   | x FE22 y FE23 | x 0360 y
 * EB x EC y   | y FE20 y FE21 | x 0361 y
 */
class ANSEL extends AbstractEncoding
{
    public const NAME = 'ANSEL';

    protected const TO_UTF8 = [
        "\x80" => UTF8::REPLACEMENT_CHARACTER,
        "\x81" => UTF8::REPLACEMENT_CHARACTER,
        "\x82" => UTF8::REPLACEMENT_CHARACTER,
        "\x83" => UTF8::REPLACEMENT_CHARACTER,
        "\x84" => UTF8::REPLACEMENT_CHARACTER,
        "\x85" => UTF8::REPLACEMENT_CHARACTER,
        "\x86" => UTF8::REPLACEMENT_CHARACTER,
        "\x87" => UTF8::REPLACEMENT_CHARACTER,
        "\x88" => UTF8::START_OF_STRING,
        "\x89" => UTF8::STRING_TERMINATOR,
        "\x8A" => UTF8::REPLACEMENT_CHARACTER,
        "\x8B" => UTF8::REPLACEMENT_CHARACTER,
        "\x8C" => UTF8::REPLACEMENT_CHARACTER,
        "\x8D" => UTF8::ZERO_WIDTH_JOINER,
        "\x8E" => UTF8::ZERO_WIDTH_NON_JOINER,
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
        "\xA0" => UTF8::REPLACEMENT_CHARACTER,
        "\xA1" => UTF8::LATIN_CAPITAL_LETTER_L_WITH_STROKE,
        "\xA2" => UTF8::LATIN_CAPITAL_LETTER_O_WITH_STROKE,
        "\xA3" => UTF8::LATIN_CAPITAL_LETTER_D_WITH_STROKE,
        "\xA4" => UTF8::LATIN_CAPITAL_LETTER_THORN,
        "\xA5" => UTF8::LATIN_CAPITAL_LETTER_AE,
        "\xA6" => UTF8::LATIN_CAPITAL_LIGATURE_OE,
        "\xA7" => UTF8::MODIFIER_LETTER_PRIME,
        "\xA8" => UTF8::MIDDLE_DOT,
        "\xA9" => UTF8::MUSIC_FLAT_SIGN,
        "\xAA" => UTF8::REGISTERED_SIGN,
        "\xAB" => UTF8::PLUS_MINUS_SIGN,
        "\xAC" => UTF8::LATIN_CAPITAL_LETTER_O_WITH_HORN,
        "\xAD" => UTF8::LATIN_CAPITAL_LETTER_U_WITH_HORN,
        "\xAE" => UTF8::MODIFIER_LETTER_APOSTROPHE,
        "\xAF" => UTF8::REPLACEMENT_CHARACTER,
        "\xB0" => UTF8::MODIFIER_LETTER_TURNED_COMMA,
        "\xB1" => UTF8::LATIN_SMALL_LETTER_L_WITH_STROKE,
        "\xB2" => UTF8::LATIN_SMALL_LETTER_O_WITH_STROKE,
        "\xB3" => UTF8::LATIN_SMALL_LETTER_D_WITH_STROKE,
        "\xB4" => UTF8::LATIN_SMALL_LETTER_THORN,
        "\xB5" => UTF8::LATIN_SMALL_LETTER_AE,
        "\xB6" => UTF8::LATIN_SMALL_LIGATURE_OE,
        "\xB7" => UTF8::MODIFIER_LETTER_DOUBLE_PRIME,
        "\xB8" => UTF8::LATIN_SMALL_LETTER_DOTLESS_I,
        "\xB9" => UTF8::POUND_SIGN,
        "\xBA" => UTF8::LATIN_SMALL_LETTER_ETH,
        "\xBB" => UTF8::REPLACEMENT_CHARACTER,
        "\xBC" => UTF8::LATIN_SMALL_LETTER_O_WITH_HORN,
        "\xBD" => UTF8::LATIN_SMALL_LETTER_U_WITH_HORN,
        "\xBE" => UTF8::WHITE_SQUARE,
        "\xBF" => UTF8::BLACK_SQUARE,
        "\xC0" => UTF8::DEGREE_SIGN,
        "\xC1" => UTF8::SCRIPT_SMALL_L,
        "\xC2" => UTF8::SOUND_RECORDING_COPYRIGHT,
        "\xC3" => UTF8::COPYRIGHT_SIGN,
        "\xC4" => UTF8::MUSIC_SHARP_SIGN,
        "\xC5" => UTF8::INVERTED_QUESTION_MARK,
        "\xC6" => UTF8::INVERTED_EXCLAMATION_MARK,
        "\xC7" => UTF8::LATIN_CAPITAL_LETTER_SHARP_S,
        "\xC8" => UTF8::EURO_SIGN,
        "\xC9" => UTF8::REPLACEMENT_CHARACTER,
        "\xCA" => UTF8::REPLACEMENT_CHARACTER,
        "\xCB" => UTF8::REPLACEMENT_CHARACTER,
        "\xCC" => UTF8::REPLACEMENT_CHARACTER,
        "\xCD" => UTF8::REPLACEMENT_CHARACTER,
        "\xCE" => UTF8::REPLACEMENT_CHARACTER,
        "\xCF" => UTF8::LATIN_SMALL_LETTER_SHARP_S,
        "\xD0" => UTF8::REPLACEMENT_CHARACTER,
        "\xD1" => UTF8::REPLACEMENT_CHARACTER,
        "\xD2" => UTF8::REPLACEMENT_CHARACTER,
        "\xD3" => UTF8::REPLACEMENT_CHARACTER,
        "\xD4" => UTF8::REPLACEMENT_CHARACTER,
        "\xD5" => UTF8::REPLACEMENT_CHARACTER,
        "\xD6" => UTF8::REPLACEMENT_CHARACTER,
        "\xD7" => UTF8::REPLACEMENT_CHARACTER,
        "\xD8" => UTF8::REPLACEMENT_CHARACTER,
        "\xD9" => UTF8::REPLACEMENT_CHARACTER,
        "\xDA" => UTF8::REPLACEMENT_CHARACTER,
        "\xDB" => UTF8::REPLACEMENT_CHARACTER,
        "\xDC" => UTF8::REPLACEMENT_CHARACTER,
        "\xDD" => UTF8::REPLACEMENT_CHARACTER,
        "\xDE" => UTF8::REPLACEMENT_CHARACTER,
        "\xDF" => UTF8::REPLACEMENT_CHARACTER,
        "\xE0" => UTF8::COMBINING_HOOK_ABOVE,
        "\xE1" => UTF8::COMBINING_GRAVE_ACCENT,
        "\xE2" => UTF8::COMBINING_ACUTE_ACCENT,
        "\xE3" => UTF8::COMBINING_CIRCUMFLEX_ACCENT,
        "\xE4" => UTF8::COMBINING_TILDE,
        "\xE5" => UTF8::COMBINING_MACRON,
        "\xE6" => UTF8::COMBINING_BREVE,
        "\xE7" => UTF8::COMBINING_DOT_ABOVE,
        "\xE8" => UTF8::COMBINING_DIAERESIS,
        "\xE9" => UTF8::COMBINING_CARON,
        "\xEA" => UTF8::COMBINING_RING_ABOVE,
        "\xEB" => UTF8::COMBINING_DOUBLE_INVERTED_BREVE,
        "\xEC" => '',
        "\xED" => UTF8::COMBINING_COMMA_ABOVE_RIGHT,
        "\xEE" => UTF8::COMBINING_DOUBLE_ACUTE_ACCENT,
        "\xEF" => UTF8::COMBINING_CANDRABINDU,
        "\xF0" => UTF8::COMBINING_CEDILLA,
        "\xF1" => UTF8::COMBINING_OGONEK,
        "\xF2" => UTF8::COMBINING_DOT_BELOW,
        "\xF3" => UTF8::COMBINING_DIAERESIS_BELOW,
        "\xF4" => UTF8::COMBINING_RING_BELOW,
        "\xF5" => UTF8::COMBINING_DOUBLE_LOW_LINE,
        "\xF6" => UTF8::COMBINING_LOW_LINE,
        "\xF7" => UTF8::COMBINING_COMMA_BELOW,
        "\xF8" => UTF8::COMBINING_LEFT_HALF_RING_BELOW,
        "\xF9" => UTF8::COMBINING_BREVE_BELOW,
        "\xFA" => UTF8::COMBINING_DOUBLE_TILDE,
        "\xFB" => '',
        "\xFC" => UTF8::REPLACEMENT_CHARACTER,
        "\xFD" => UTF8::REPLACEMENT_CHARACTER,
        "\xFE" => UTF8::COMBINING_COMMA_ABOVE,
        "\xFF" => UTF8::COMBINING_LONG_SOLIDUS_OVERLAY,
    ];

    // The subset of pre-composed UTF8 characters that can be made from ANSEL characters.
    private const PRECOMPOSED_CHARACTERS = [
        'A' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_A_WITH_ACUTE,
        'A' . UTF8::COMBINING_BREVE                                             => UTF8::LATIN_CAPITAL_LETTER_A_WITH_BREVE,
        'A' . UTF8::COMBINING_BREVE . UTF8::COMBINING_ACUTE_ACCENT              => UTF8::LATIN_CAPITAL_LETTER_A_WITH_BREVE_AND_ACUTE,
        'A' . UTF8::COMBINING_BREVE . UTF8::COMBINING_DOT_BELOW                 => UTF8::LATIN_CAPITAL_LETTER_A_WITH_BREVE_AND_DOT_BELOW,
        'A' . UTF8::COMBINING_BREVE . UTF8::COMBINING_GRAVE_ACCENT              => UTF8::LATIN_CAPITAL_LETTER_A_WITH_BREVE_AND_GRAVE,
        'A' . UTF8::COMBINING_BREVE . UTF8::COMBINING_HOOK_ABOVE                => UTF8::LATIN_CAPITAL_LETTER_A_WITH_BREVE_AND_HOOK_ABOVE,
        'A' . UTF8::COMBINING_BREVE . UTF8::COMBINING_TILDE                     => UTF8::LATIN_CAPITAL_LETTER_A_WITH_BREVE_AND_TILDE,
        'A' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_A_WITH_CARON,
        'A' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_CAPITAL_LETTER_A_WITH_CIRCUMFLEX,
        'A' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_ACUTE_ACCENT  => UTF8::LATIN_CAPITAL_LETTER_A_WITH_CIRCUMFLEX_AND_ACUTE,
        'A' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_DOT_BELOW     => UTF8::LATIN_CAPITAL_LETTER_A_WITH_CIRCUMFLEX_AND_DOT_BELOW,
        'A' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_GRAVE_ACCENT  => UTF8::LATIN_CAPITAL_LETTER_A_WITH_CIRCUMFLEX_AND_GRAVE,
        'A' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_HOOK_ABOVE    => UTF8::LATIN_CAPITAL_LETTER_A_WITH_CIRCUMFLEX_AND_HOOK_ABOVE,
        'A' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_TILDE         => UTF8::LATIN_CAPITAL_LETTER_A_WITH_CIRCUMFLEX_AND_TILDE,
        'A' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_CAPITAL_LETTER_A_WITH_DIAERESIS,
        'A' . UTF8::COMBINING_DIAERESIS . UTF8::COMBINING_MACRON                => UTF8::LATIN_CAPITAL_LETTER_A_WITH_DIAERESIS_AND_MACRON,
        'A' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_A_WITH_DOT_ABOVE,
        'A' . UTF8::COMBINING_DOT_ABOVE . UTF8::COMBINING_MACRON                => UTF8::LATIN_CAPITAL_LETTER_A_WITH_DOT_ABOVE_AND_MACRON,
        'A' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_A_WITH_DOT_BELOW,
        'A' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_A_WITH_GRAVE,
        'A' . UTF8::COMBINING_HOOK_ABOVE                                        => UTF8::LATIN_CAPITAL_LETTER_A_WITH_HOOK_ABOVE,
        'A' . UTF8::COMBINING_MACRON                                            => UTF8::LATIN_CAPITAL_LETTER_A_WITH_MACRON,
        'A' . UTF8::COMBINING_OGONEK                                            => UTF8::LATIN_CAPITAL_LETTER_A_WITH_OGONEK,
        'A' . UTF8::COMBINING_RING_ABOVE                                        => UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_ABOVE,
        'A' . UTF8::COMBINING_RING_ABOVE . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_ABOVE_AND_ACUTE,
        'A' . UTF8::COMBINING_RING_BELOW                                        => UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_BELOW,
        'A' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_CAPITAL_LETTER_A_WITH_TILDE,
        'B' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_B_WITH_DOT_ABOVE,
        'B' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_B_WITH_DOT_BELOW,
        'C' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_C_WITH_ACUTE,
        'C' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
        'C' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CEDILLA,
        'C' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CIRCUMFLEX,
        'C' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_C_WITH_DOT_ABOVE,
        'C' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CEDILLA_AND_ACUTE,
        'D' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_D_WITH_CARON,
        'D' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_CAPITAL_LETTER_D_WITH_CEDILLA,
        'D' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_D_WITH_DOT_ABOVE,
        'D' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_D_WITH_DOT_BELOW,
        'E' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_E_WITH_ACUTE,
        'E' . UTF8::COMBINING_BREVE                                             => UTF8::LATIN_CAPITAL_LETTER_E_WITH_BREVE,
        'E' . UTF8::COMBINING_BREVE . UTF8::COMBINING_CEDILLA                   => UTF8::LATIN_CAPITAL_LETTER_E_WITH_CEDILLA_AND_BREVE,
        'E' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_E_WITH_CARON,
        'E' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_CAPITAL_LETTER_E_WITH_CEDILLA,
        'E' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_CAPITAL_LETTER_E_WITH_CIRCUMFLEX,
        'E' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_ACUTE_ACCENT  => UTF8::LATIN_CAPITAL_LETTER_E_WITH_CIRCUMFLEX_AND_ACUTE,
        'E' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_DOT_BELOW     => UTF8::LATIN_CAPITAL_LETTER_E_WITH_CIRCUMFLEX_AND_DOT_BELOW,
        'E' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_GRAVE_ACCENT  => UTF8::LATIN_CAPITAL_LETTER_E_WITH_CIRCUMFLEX_AND_GRAVE,
        'E' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_HOOK_ABOVE    => UTF8::LATIN_CAPITAL_LETTER_E_WITH_CIRCUMFLEX_AND_HOOK_ABOVE,
        'E' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_TILDE         => UTF8::LATIN_CAPITAL_LETTER_E_WITH_CIRCUMFLEX_AND_TILDE,
        'E' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_CAPITAL_LETTER_E_WITH_DIAERESIS,
        'E' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_E_WITH_DOT_ABOVE,
        'E' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_E_WITH_DOT_BELOW,
        'E' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_E_WITH_GRAVE,
        'E' . UTF8::COMBINING_HOOK_ABOVE                                        => UTF8::LATIN_CAPITAL_LETTER_E_WITH_HOOK_ABOVE,
        'E' . UTF8::COMBINING_MACRON                                            => UTF8::LATIN_CAPITAL_LETTER_E_WITH_MACRON,
        'E' . UTF8::COMBINING_MACRON . UTF8::COMBINING_ACUTE_ACCENT             => UTF8::LATIN_CAPITAL_LETTER_E_WITH_MACRON_AND_ACUTE,
        'E' . UTF8::COMBINING_MACRON . UTF8::COMBINING_GRAVE_ACCENT             => UTF8::LATIN_CAPITAL_LETTER_E_WITH_MACRON_AND_GRAVE,
        'E' . UTF8::COMBINING_OGONEK                                            => UTF8::LATIN_CAPITAL_LETTER_E_WITH_OGONEK,
        'E' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_CAPITAL_LETTER_E_WITH_TILDE,
        'F' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_F_WITH_DOT_ABOVE,
        'G' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_G_WITH_ACUTE,
        'G' . UTF8::COMBINING_BREVE                                             => UTF8::LATIN_CAPITAL_LETTER_G_WITH_BREVE,
        'G' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_G_WITH_CARON,
        'G' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_CAPITAL_LETTER_G_WITH_CEDILLA,
        'G' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_CAPITAL_LETTER_G_WITH_CIRCUMFLEX,
        'G' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_G_WITH_DOT_ABOVE,
        'G' . UTF8::COMBINING_MACRON                                            => UTF8::LATIN_CAPITAL_LETTER_G_WITH_MACRON,
        'H' . UTF8::COMBINING_BREVE_BELOW                                       => UTF8::LATIN_CAPITAL_LETTER_H_WITH_BREVE_BELOW,
        'H' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_H_WITH_CARON,
        'H' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_CAPITAL_LETTER_H_WITH_CEDILLA,
        'H' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_CAPITAL_LETTER_H_WITH_CIRCUMFLEX,
        'H' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_CAPITAL_LETTER_H_WITH_DIAERESIS,
        'H' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_H_WITH_DOT_ABOVE,
        'H' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_H_WITH_DOT_BELOW,
        'I' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_I_WITH_ACUTE,
        'I' . UTF8::COMBINING_BREVE                                             => UTF8::LATIN_CAPITAL_LETTER_I_WITH_BREVE,
        'I' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_I_WITH_CARON,
        'I' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_CAPITAL_LETTER_I_WITH_CIRCUMFLEX,
        'I' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_CAPITAL_LETTER_I_WITH_DIAERESIS,
        'I' . UTF8::COMBINING_DIAERESIS . UTF8::COMBINING_ACUTE_ACCENT          => UTF8::LATIN_CAPITAL_LETTER_I_WITH_DIAERESIS_AND_ACUTE,
        'I' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_I_WITH_DOT_ABOVE,
        'I' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_I_WITH_DOT_BELOW,
        'I' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_I_WITH_GRAVE,
        'I' . UTF8::COMBINING_HOOK_ABOVE                                        => UTF8::LATIN_CAPITAL_LETTER_I_WITH_HOOK_ABOVE,
        'I' . UTF8::COMBINING_MACRON                                            => UTF8::LATIN_CAPITAL_LETTER_I_WITH_MACRON,
        'I' . UTF8::COMBINING_OGONEK                                            => UTF8::LATIN_CAPITAL_LETTER_I_WITH_OGONEK,
        'I' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_CAPITAL_LETTER_I_WITH_TILDE,
        'J' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_CAPITAL_LETTER_J_WITH_CIRCUMFLEX,
        'K' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_K_WITH_CARON,
        'K' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_CAPITAL_LETTER_K_WITH_CEDILLA,
        'K' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_CAPITAL_LETTER_K_WITH_ACUTE,
        'K' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_K_WITH_DOT_BELOW,
        'L' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_L_WITH_ACUTE,
        'L' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_L_WITH_CARON,
        'L' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_CAPITAL_LETTER_L_WITH_CEDILLA,
        'L' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_L_WITH_DOT_BELOW,
        'L' . UTF8::COMBINING_DOT_BELOW . UTF8::COMBINING_MACRON                => UTF8::LATIN_CAPITAL_LETTER_L_WITH_DOT_BELOW_AND_MACRON,
        'M' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_M_WITH_ACUTE,
        'M' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_M_WITH_DOT_ABOVE,
        'M' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_M_WITH_DOT_BELOW,
        'N' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_N_WITH_ACUTE,
        'N' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_N_WITH_CARON,
        'N' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_CAPITAL_LETTER_N_WITH_CEDILLA,
        'N' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_N_WITH_DOT_ABOVE,
        'N' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_N_WITH_DOT_BELOW,
        'N' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_N_WITH_GRAVE,
        'N' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_CAPITAL_LETTER_N_WITH_TILDE,
        'O' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_O_WITH_ACUTE,
        'O' . UTF8::COMBINING_BREVE                                             => UTF8::LATIN_CAPITAL_LETTER_O_WITH_BREVE,
        'O' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_O_WITH_CARON,
        'O' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_CAPITAL_LETTER_O_WITH_CIRCUMFLEX,
        'O' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_ACUTE_ACCENT  => UTF8::LATIN_CAPITAL_LETTER_O_WITH_CIRCUMFLEX_AND_ACUTE,
        'O' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_DOT_BELOW     => UTF8::LATIN_CAPITAL_LETTER_O_WITH_CIRCUMFLEX_AND_DOT_BELOW,
        'O' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_GRAVE_ACCENT  => UTF8::LATIN_CAPITAL_LETTER_O_WITH_CIRCUMFLEX_AND_GRAVE,
        'O' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_HOOK_ABOVE    => UTF8::LATIN_CAPITAL_LETTER_O_WITH_CIRCUMFLEX_AND_HOOK_ABOVE,
        'O' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_TILDE         => UTF8::LATIN_CAPITAL_LETTER_O_WITH_CIRCUMFLEX_AND_TILDE,
        'O' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
        'O' . UTF8::COMBINING_DIAERESIS . UTF8::COMBINING_MACRON                => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS_AND_MACRON,
        'O' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DOT_ABOVE,
        'O' . UTF8::COMBINING_DOT_ABOVE . UTF8::COMBINING_MACRON                => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DOT_ABOVE_AND_MACRON,
        'O' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DOT_BELOW,
        'O' . UTF8::COMBINING_DOUBLE_ACUTE_ACCENT                               => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DOUBLE_ACUTE,
        'O' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_O_WITH_GRAVE,
        'O' . UTF8::COMBINING_HOOK_ABOVE                                        => UTF8::LATIN_CAPITAL_LETTER_O_WITH_HOOK_ABOVE,
        'O' . UTF8::COMBINING_MACRON                                            => UTF8::LATIN_CAPITAL_LETTER_O_WITH_MACRON,
        'O' . UTF8::COMBINING_MACRON . UTF8::COMBINING_ACUTE_ACCENT             => UTF8::LATIN_CAPITAL_LETTER_O_WITH_MACRON_AND_ACUTE,
        'O' . UTF8::COMBINING_MACRON . UTF8::COMBINING_GRAVE_ACCENT             => UTF8::LATIN_CAPITAL_LETTER_O_WITH_MACRON_AND_GRAVE,
        'O' . UTF8::COMBINING_OGONEK                                            => UTF8::LATIN_CAPITAL_LETTER_O_WITH_OGONEK,
        'O' . UTF8::COMBINING_OGONEK . UTF8::COMBINING_MACRON                   => UTF8::LATIN_CAPITAL_LETTER_O_WITH_OGONEK_AND_MACRON,
        'O' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_CAPITAL_LETTER_O_WITH_TILDE,
        'O' . UTF8::COMBINING_TILDE . UTF8::COMBINING_ACUTE_ACCENT              => UTF8::LATIN_CAPITAL_LETTER_O_WITH_TILDE_AND_ACUTE,
        'O' . UTF8::COMBINING_TILDE . UTF8::COMBINING_DIAERESIS                 => UTF8::LATIN_CAPITAL_LETTER_O_WITH_TILDE_AND_DIAERESIS,
        'O' . UTF8::COMBINING_TILDE . UTF8::COMBINING_MACRON                    => UTF8::LATIN_CAPITAL_LETTER_O_WITH_TILDE_AND_MACRON,
        'P' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_P_WITH_ACUTE,
        'P' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_P_WITH_DOT_ABOVE,
        'R' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_R_WITH_ACUTE,
        'R' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_R_WITH_CARON,
        'R' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_CAPITAL_LETTER_R_WITH_CEDILLA,
        'R' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_R_WITH_DOT_ABOVE,
        'R' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_R_WITH_DOT_BELOW,
        'R' . UTF8::COMBINING_DOT_BELOW . UTF8::COMBINING_MACRON                => UTF8::LATIN_CAPITAL_LETTER_R_WITH_DOT_BELOW_AND_MACRON,
        'S' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_S_WITH_ACUTE,
        'S' . UTF8::COMBINING_ACUTE_ACCENT . UTF8::COMBINING_DOT_ABOVE          => UTF8::LATIN_CAPITAL_LETTER_S_WITH_ACUTE_AND_DOT_ABOVE,
        'S' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
        'S' . UTF8::COMBINING_CARON . UTF8::COMBINING_DOT_ABOVE                 => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON_AND_DOT_ABOVE,
        'S' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CEDILLA,
        'S' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CIRCUMFLEX,
        'S' . UTF8::COMBINING_COMMA_BELOW                                       => UTF8::LATIN_CAPITAL_LETTER_S_WITH_COMMA_BELOW,
        'S' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_S_WITH_DOT_ABOVE,
        'S' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_S_WITH_DOT_BELOW,
        'S' . UTF8::COMBINING_DOT_BELOW . UTF8::COMBINING_DOT_ABOVE             => UTF8::LATIN_CAPITAL_LETTER_S_WITH_DOT_BELOW_AND_DOT_ABOVE,
        'T' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_T_WITH_CARON,
        'T' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_CAPITAL_LETTER_T_WITH_CEDILLA,
        'T' . UTF8::COMBINING_COMMA_BELOW                                       => UTF8::LATIN_CAPITAL_LETTER_T_WITH_COMMA_BELOW,
        'T' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_T_WITH_DOT_ABOVE,
        'T' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_T_WITH_DOT_BELOW,
        'U' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_U_WITH_ACUTE,
        'U' . UTF8::COMBINING_BREVE                                             => UTF8::LATIN_CAPITAL_LETTER_U_WITH_BREVE,
        'U' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_U_WITH_CARON,
        'U' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_CAPITAL_LETTER_U_WITH_CIRCUMFLEX,
        'U' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS,
        'U' . UTF8::COMBINING_DIAERESIS . UTF8::COMBINING_ACUTE_ACCENT          => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS_AND_ACUTE,
        'U' . UTF8::COMBINING_DIAERESIS . UTF8::COMBINING_CARON                 => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS_AND_CARON,
        'U' . UTF8::COMBINING_DIAERESIS . UTF8::COMBINING_GRAVE_ACCENT          => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS_AND_GRAVE,
        'U' . UTF8::COMBINING_DIAERESIS . UTF8::COMBINING_MACRON                => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS_AND_MACRON,
        'U' . UTF8::COMBINING_DIAERESIS_BELOW                                   => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS_BELOW,
        'U' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DOT_BELOW,
        'U' . UTF8::COMBINING_DOUBLE_ACUTE_ACCENT                               => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DOUBLE_ACUTE,
        'U' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_U_WITH_GRAVE,
        'U' . UTF8::COMBINING_HOOK_ABOVE                                        => UTF8::LATIN_CAPITAL_LETTER_U_WITH_HOOK_ABOVE,
        'U' . UTF8::COMBINING_MACRON                                            => UTF8::LATIN_CAPITAL_LETTER_U_WITH_MACRON,
        'U' . UTF8::COMBINING_MACRON . UTF8::COMBINING_DIAERESIS                => UTF8::LATIN_CAPITAL_LETTER_U_WITH_MACRON_AND_DIAERESIS,
        'U' . UTF8::COMBINING_OGONEK                                            => UTF8::LATIN_CAPITAL_LETTER_U_WITH_OGONEK,
        'U' . UTF8::COMBINING_RING_ABOVE                                        => UTF8::LATIN_CAPITAL_LETTER_U_WITH_RING_ABOVE,
        'U' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_CAPITAL_LETTER_U_WITH_TILDE,
        'U' . UTF8::COMBINING_TILDE . UTF8::COMBINING_ACUTE_ACCENT              => UTF8::LATIN_CAPITAL_LETTER_U_WITH_TILDE_AND_ACUTE,
        'V' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_V_WITH_DOT_BELOW,
        'V' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_CAPITAL_LETTER_V_WITH_TILDE,
        'W' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_W_WITH_ACUTE,
        'W' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_CAPITAL_LETTER_W_WITH_CIRCUMFLEX,
        'W' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_CAPITAL_LETTER_W_WITH_DIAERESIS,
        'W' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_W_WITH_DOT_ABOVE,
        'W' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_W_WITH_DOT_BELOW,
        'W' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_W_WITH_GRAVE,
        'X' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_CAPITAL_LETTER_X_WITH_DIAERESIS,
        'X' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_X_WITH_DOT_ABOVE,
        'Y' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_Y_WITH_ACUTE,
        'Y' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_CAPITAL_LETTER_Y_WITH_CIRCUMFLEX,
        'Y' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_CAPITAL_LETTER_Y_WITH_DIAERESIS,
        'Y' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_Y_WITH_DOT_ABOVE,
        'Y' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_Y_WITH_DOT_BELOW,
        'Y' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_Y_WITH_GRAVE,
        'Y' . UTF8::COMBINING_HOOK_ABOVE                                        => UTF8::LATIN_CAPITAL_LETTER_Y_WITH_HOOK_ABOVE,
        'Y' . UTF8::COMBINING_MACRON                                            => UTF8::LATIN_CAPITAL_LETTER_Y_WITH_MACRON,
        'Y' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_CAPITAL_LETTER_Y_WITH_TILDE,
        'Z' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_ACUTE,
        'Z' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
        'Z' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CIRCUMFLEX,
        'Z' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_DOT_ABOVE,
        'Z' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_DOT_BELOW,
        'a' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_A_WITH_ACUTE,
        'a' . UTF8::COMBINING_BREVE                                             => UTF8::LATIN_SMALL_LETTER_A_WITH_BREVE,
        'a' . UTF8::COMBINING_BREVE . UTF8::COMBINING_ACUTE_ACCENT              => UTF8::LATIN_SMALL_LETTER_A_WITH_BREVE_AND_ACUTE,
        'a' . UTF8::COMBINING_BREVE . UTF8::COMBINING_DOT_BELOW                 => UTF8::LATIN_SMALL_LETTER_A_WITH_BREVE_AND_DOT_BELOW,
        'a' . UTF8::COMBINING_BREVE . UTF8::COMBINING_GRAVE_ACCENT              => UTF8::LATIN_SMALL_LETTER_A_WITH_BREVE_AND_GRAVE,
        'a' . UTF8::COMBINING_BREVE . UTF8::COMBINING_HOOK_ABOVE                => UTF8::LATIN_SMALL_LETTER_A_WITH_BREVE_AND_HOOK_ABOVE,
        'a' . UTF8::COMBINING_BREVE . UTF8::COMBINING_TILDE                     => UTF8::LATIN_SMALL_LETTER_A_WITH_BREVE_AND_TILDE,
        'a' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_A_WITH_CARON,
        'a' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_SMALL_LETTER_A_WITH_CIRCUMFLEX,
        'a' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_ACUTE_ACCENT  => UTF8::LATIN_SMALL_LETTER_A_WITH_CIRCUMFLEX_AND_ACUTE,
        'a' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_DOT_BELOW     => UTF8::LATIN_SMALL_LETTER_A_WITH_CIRCUMFLEX_AND_DOT_BELOW,
        'a' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_GRAVE_ACCENT  => UTF8::LATIN_SMALL_LETTER_A_WITH_CIRCUMFLEX_AND_GRAVE,
        'a' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_HOOK_ABOVE    => UTF8::LATIN_SMALL_LETTER_A_WITH_CIRCUMFLEX_AND_HOOK_ABOVE,
        'a' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_TILDE         => UTF8::LATIN_SMALL_LETTER_A_WITH_CIRCUMFLEX_AND_TILDE,
        'a' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_SMALL_LETTER_A_WITH_DIAERESIS,
        'a' . UTF8::COMBINING_DIAERESIS . UTF8::COMBINING_MACRON                => UTF8::LATIN_SMALL_LETTER_A_WITH_DIAERESIS_AND_MACRON,
        'a' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_A_WITH_DOT_ABOVE,
        'a' . UTF8::COMBINING_DOT_ABOVE . UTF8::COMBINING_MACRON                => UTF8::LATIN_SMALL_LETTER_A_WITH_DOT_ABOVE_AND_MACRON,
        'a' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_A_WITH_DOT_BELOW,
        'a' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_A_WITH_GRAVE,
        'a' . UTF8::COMBINING_HOOK_ABOVE                                        => UTF8::LATIN_SMALL_LETTER_A_WITH_HOOK_ABOVE,
        'a' . UTF8::COMBINING_MACRON                                            => UTF8::LATIN_SMALL_LETTER_A_WITH_MACRON,
        'a' . UTF8::COMBINING_OGONEK                                            => UTF8::LATIN_SMALL_LETTER_A_WITH_OGONEK,
        'a' . UTF8::COMBINING_RING_ABOVE                                        => UTF8::LATIN_SMALL_LETTER_A_WITH_RING_ABOVE,
        'a' . UTF8::COMBINING_RING_ABOVE . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_SMALL_LETTER_A_WITH_RING_ABOVE_AND_ACUTE,
        'a' . UTF8::COMBINING_RING_BELOW                                        => UTF8::LATIN_SMALL_LETTER_A_WITH_RING_BELOW,
        'a' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_SMALL_LETTER_A_WITH_TILDE,
        'b' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_B_WITH_DOT_ABOVE,
        'b' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_B_WITH_DOT_BELOW,
        'c' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_C_WITH_ACUTE,
        'c' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_C_WITH_CARON,
        'c' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_SMALL_LETTER_C_WITH_CEDILLA,
        'c' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_SMALL_LETTER_C_WITH_CIRCUMFLEX,
        'c' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_C_WITH_DOT_ABOVE,
        'c' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_C_WITH_CEDILLA_AND_ACUTE,
        'd' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_D_WITH_CARON,
        'd' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_SMALL_LETTER_D_WITH_CEDILLA,
        'd' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_D_WITH_DOT_ABOVE,
        'd' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_D_WITH_DOT_BELOW,
        'e' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_E_WITH_ACUTE,
        'e' . UTF8::COMBINING_BREVE                                             => UTF8::LATIN_SMALL_LETTER_E_WITH_BREVE,
        'e' . UTF8::COMBINING_BREVE . UTF8::COMBINING_CEDILLA                   => UTF8::LATIN_SMALL_LETTER_E_WITH_CEDILLA_AND_BREVE,
        'e' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_E_WITH_CARON,
        'e' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_SMALL_LETTER_E_WITH_CEDILLA,
        'e' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_SMALL_LETTER_E_WITH_CIRCUMFLEX,
        'e' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_ACUTE_ACCENT  => UTF8::LATIN_SMALL_LETTER_E_WITH_CIRCUMFLEX_AND_ACUTE,
        'e' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_DOT_BELOW     => UTF8::LATIN_SMALL_LETTER_E_WITH_CIRCUMFLEX_AND_DOT_BELOW,
        'e' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_GRAVE_ACCENT  => UTF8::LATIN_SMALL_LETTER_E_WITH_CIRCUMFLEX_AND_GRAVE,
        'e' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_HOOK_ABOVE    => UTF8::LATIN_SMALL_LETTER_E_WITH_CIRCUMFLEX_AND_HOOK_ABOVE,
        'e' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_TILDE         => UTF8::LATIN_SMALL_LETTER_E_WITH_CIRCUMFLEX_AND_TILDE,
        'e' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_SMALL_LETTER_E_WITH_DIAERESIS,
        'e' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_E_WITH_DOT_ABOVE,
        'e' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_E_WITH_DOT_BELOW,
        'e' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_E_WITH_GRAVE,
        'e' . UTF8::COMBINING_HOOK_ABOVE                                        => UTF8::LATIN_SMALL_LETTER_E_WITH_HOOK_ABOVE,
        'e' . UTF8::COMBINING_MACRON                                            => UTF8::LATIN_SMALL_LETTER_E_WITH_MACRON,
        'e' . UTF8::COMBINING_MACRON . UTF8::COMBINING_ACUTE_ACCENT             => UTF8::LATIN_SMALL_LETTER_E_WITH_MACRON_AND_ACUTE,
        'e' . UTF8::COMBINING_MACRON . UTF8::COMBINING_GRAVE_ACCENT             => UTF8::LATIN_SMALL_LETTER_E_WITH_MACRON_AND_GRAVE,
        'e' . UTF8::COMBINING_OGONEK                                            => UTF8::LATIN_SMALL_LETTER_E_WITH_OGONEK,
        'e' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_SMALL_LETTER_E_WITH_TILDE,
        'f' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_F_WITH_DOT_ABOVE,
        'g' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_G_WITH_ACUTE,
        'g' . UTF8::COMBINING_BREVE                                             => UTF8::LATIN_SMALL_LETTER_G_WITH_BREVE,
        'g' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_G_WITH_CARON,
        'g' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_SMALL_LETTER_G_WITH_CEDILLA,
        'g' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_SMALL_LETTER_G_WITH_CIRCUMFLEX,
        'g' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_G_WITH_DOT_ABOVE,
        'g' . UTF8::COMBINING_MACRON                                            => UTF8::LATIN_SMALL_LETTER_G_WITH_MACRON,
        'h' . UTF8::COMBINING_BREVE_BELOW                                       => UTF8::LATIN_SMALL_LETTER_H_WITH_BREVE_BELOW,
        'h' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_H_WITH_CARON,
        'h' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_SMALL_LETTER_H_WITH_CEDILLA,
        'h' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_SMALL_LETTER_H_WITH_CIRCUMFLEX,
        'h' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_SMALL_LETTER_H_WITH_DIAERESIS,
        'h' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_H_WITH_DOT_ABOVE,
        'h' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_H_WITH_DOT_BELOW,
        'i' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_I_WITH_ACUTE,
        'i' . UTF8::COMBINING_BREVE                                             => UTF8::LATIN_SMALL_LETTER_I_WITH_BREVE,
        'i' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_I_WITH_CARON,
        'i' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_SMALL_LETTER_I_WITH_CIRCUMFLEX,
        'i' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_SMALL_LETTER_I_WITH_DIAERESIS,
        'i' . UTF8::COMBINING_DIAERESIS . UTF8::COMBINING_ACUTE_ACCENT          => UTF8::LATIN_SMALL_LETTER_I_WITH_DIAERESIS_AND_ACUTE,
        'i' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_I_WITH_DOT_BELOW,
        'i' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_I_WITH_GRAVE,
        'i' . UTF8::COMBINING_HOOK_ABOVE                                        => UTF8::LATIN_SMALL_LETTER_I_WITH_HOOK_ABOVE,
        'i' . UTF8::COMBINING_MACRON                                            => UTF8::LATIN_SMALL_LETTER_I_WITH_MACRON,
        'i' . UTF8::COMBINING_OGONEK                                            => UTF8::LATIN_SMALL_LETTER_I_WITH_OGONEK,
        'i' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_SMALL_LETTER_I_WITH_TILDE,
        'j' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_J_WITH_CARON,
        'j' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_SMALL_LETTER_J_WITH_CIRCUMFLEX,
        'k' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_K_WITH_CARON,
        'k' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_SMALL_LETTER_K_WITH_CEDILLA,
        'k' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_SMALL_LETTER_K_WITH_ACUTE,
        'k' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_K_WITH_DOT_BELOW,
        'l' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_L_WITH_ACUTE,
        'l' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_L_WITH_CARON,
        'l' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_SMALL_LETTER_L_WITH_CEDILLA,
        'l' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_L_WITH_DOT_BELOW,
        'l' . UTF8::COMBINING_DOT_BELOW . UTF8::COMBINING_MACRON                => UTF8::LATIN_SMALL_LETTER_L_WITH_DOT_BELOW_AND_MACRON,
        'm' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_M_WITH_ACUTE,
        'm' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_M_WITH_DOT_ABOVE,
        'm' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_M_WITH_DOT_BELOW,
        'n' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_N_WITH_ACUTE,
        'n' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_N_WITH_CARON,
        'n' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_SMALL_LETTER_N_WITH_CEDILLA,
        'n' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_N_WITH_DOT_ABOVE,
        'n' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_N_WITH_DOT_BELOW,
        'n' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_N_WITH_GRAVE,
        'n' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_SMALL_LETTER_N_WITH_TILDE,
        'o' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_O_WITH_ACUTE,
        'o' . UTF8::COMBINING_BREVE                                             => UTF8::LATIN_SMALL_LETTER_O_WITH_BREVE,
        'o' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_O_WITH_CARON,
        'o' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_SMALL_LETTER_O_WITH_CIRCUMFLEX,
        'o' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_ACUTE_ACCENT  => UTF8::LATIN_SMALL_LETTER_O_WITH_CIRCUMFLEX_AND_ACUTE,
        'o' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_DOT_BELOW     => UTF8::LATIN_SMALL_LETTER_O_WITH_CIRCUMFLEX_AND_DOT_BELOW,
        'o' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_GRAVE_ACCENT  => UTF8::LATIN_SMALL_LETTER_O_WITH_CIRCUMFLEX_AND_GRAVE,
        'o' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_HOOK_ABOVE    => UTF8::LATIN_SMALL_LETTER_O_WITH_CIRCUMFLEX_AND_HOOK_ABOVE,
        'o' . UTF8::COMBINING_CIRCUMFLEX_ACCENT . UTF8::COMBINING_TILDE         => UTF8::LATIN_SMALL_LETTER_O_WITH_CIRCUMFLEX_AND_TILDE,
        'o' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_SMALL_LETTER_O_WITH_DIAERESIS,
        'o' . UTF8::COMBINING_DIAERESIS . UTF8::COMBINING_MACRON                => UTF8::LATIN_SMALL_LETTER_O_WITH_DIAERESIS_AND_MACRON,
        'o' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_O_WITH_DOT_ABOVE,
        'o' . UTF8::COMBINING_DOT_ABOVE . UTF8::COMBINING_MACRON                => UTF8::LATIN_SMALL_LETTER_O_WITH_DOT_ABOVE_AND_MACRON,
        'o' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_O_WITH_DOT_BELOW,
        'o' . UTF8::COMBINING_DOUBLE_ACUTE_ACCENT                               => UTF8::LATIN_SMALL_LETTER_O_WITH_DOUBLE_ACUTE,
        'o' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_O_WITH_GRAVE,
        'o' . UTF8::COMBINING_HOOK_ABOVE                                        => UTF8::LATIN_SMALL_LETTER_O_WITH_HOOK_ABOVE,
        'o' . UTF8::COMBINING_MACRON                                            => UTF8::LATIN_SMALL_LETTER_O_WITH_MACRON,
        'o' . UTF8::COMBINING_MACRON . UTF8::COMBINING_ACUTE_ACCENT             => UTF8::LATIN_SMALL_LETTER_O_WITH_MACRON_AND_ACUTE,
        'o' . UTF8::COMBINING_MACRON . UTF8::COMBINING_GRAVE_ACCENT             => UTF8::LATIN_SMALL_LETTER_O_WITH_MACRON_AND_GRAVE,
        'o' . UTF8::COMBINING_OGONEK                                            => UTF8::LATIN_SMALL_LETTER_O_WITH_OGONEK,
        'o' . UTF8::COMBINING_OGONEK . UTF8::COMBINING_MACRON                   => UTF8::LATIN_SMALL_LETTER_O_WITH_OGONEK_AND_MACRON,
        'o' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_SMALL_LETTER_O_WITH_TILDE,
        'o' . UTF8::COMBINING_TILDE . UTF8::COMBINING_ACUTE_ACCENT              => UTF8::LATIN_SMALL_LETTER_O_WITH_TILDE_AND_ACUTE,
        'o' . UTF8::COMBINING_TILDE . UTF8::COMBINING_DIAERESIS                 => UTF8::LATIN_SMALL_LETTER_O_WITH_TILDE_AND_DIAERESIS,
        'o' . UTF8::COMBINING_TILDE . UTF8::COMBINING_MACRON                    => UTF8::LATIN_SMALL_LETTER_O_WITH_TILDE_AND_MACRON,
        'p' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_P_WITH_ACUTE,
        'p' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_P_WITH_DOT_ABOVE,
        'r' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_R_WITH_ACUTE,
        'r' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_R_WITH_CARON,
        'r' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_SMALL_LETTER_R_WITH_CEDILLA,
        'r' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_R_WITH_DOT_ABOVE,
        'r' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_R_WITH_DOT_BELOW,
        'r' . UTF8::COMBINING_DOT_BELOW . UTF8::COMBINING_MACRON                => UTF8::LATIN_SMALL_LETTER_R_WITH_DOT_BELOW_AND_MACRON,
        's' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_S_WITH_ACUTE,
        's' . UTF8::COMBINING_ACUTE_ACCENT . UTF8::COMBINING_DOT_ABOVE          => UTF8::LATIN_SMALL_LETTER_S_WITH_ACUTE_AND_DOT_ABOVE,
        's' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_S_WITH_CARON,
        's' . UTF8::COMBINING_CARON . UTF8::COMBINING_DOT_ABOVE                 => UTF8::LATIN_SMALL_LETTER_S_WITH_CARON_AND_DOT_ABOVE,
        's' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_SMALL_LETTER_S_WITH_CEDILLA,
        's' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_SMALL_LETTER_S_WITH_CIRCUMFLEX,
        's' . UTF8::COMBINING_COMMA_BELOW                                       => UTF8::LATIN_SMALL_LETTER_S_WITH_COMMA_BELOW,
        's' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_S_WITH_DOT_ABOVE,
        's' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_S_WITH_DOT_BELOW,
        's' . UTF8::COMBINING_DOT_BELOW . UTF8::COMBINING_DOT_ABOVE             => UTF8::LATIN_SMALL_LETTER_S_WITH_DOT_BELOW_AND_DOT_ABOVE,
        't' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_T_WITH_CARON,
        't' . UTF8::COMBINING_CEDILLA                                           => UTF8::LATIN_SMALL_LETTER_T_WITH_CEDILLA,
        't' . UTF8::COMBINING_COMMA_BELOW                                       => UTF8::LATIN_SMALL_LETTER_T_WITH_COMMA_BELOW,
        't' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_SMALL_LETTER_T_WITH_DIAERESIS,
        't' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_T_WITH_DOT_ABOVE,
        't' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_T_WITH_DOT_BELOW,
        'u' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_U_WITH_ACUTE,
        'u' . UTF8::COMBINING_BREVE                                             => UTF8::LATIN_SMALL_LETTER_U_WITH_BREVE,
        'u' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_U_WITH_CARON,
        'u' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_SMALL_LETTER_U_WITH_CIRCUMFLEX,
        'u' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_SMALL_LETTER_U_WITH_DIAERESIS,
        'u' . UTF8::COMBINING_DIAERESIS . UTF8::COMBINING_ACUTE_ACCENT          => UTF8::LATIN_SMALL_LETTER_U_WITH_DIAERESIS_AND_ACUTE,
        'u' . UTF8::COMBINING_DIAERESIS . UTF8::COMBINING_CARON                 => UTF8::LATIN_SMALL_LETTER_U_WITH_DIAERESIS_AND_CARON,
        'u' . UTF8::COMBINING_DIAERESIS . UTF8::COMBINING_GRAVE_ACCENT          => UTF8::LATIN_SMALL_LETTER_U_WITH_DIAERESIS_AND_GRAVE,
        'u' . UTF8::COMBINING_DIAERESIS . UTF8::COMBINING_MACRON                => UTF8::LATIN_SMALL_LETTER_U_WITH_DIAERESIS_AND_MACRON,
        'u' . UTF8::COMBINING_DIAERESIS_BELOW                                   => UTF8::LATIN_SMALL_LETTER_U_WITH_DIAERESIS_BELOW,
        'u' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_U_WITH_DOT_BELOW,
        'u' . UTF8::COMBINING_DOUBLE_ACUTE_ACCENT                               => UTF8::LATIN_SMALL_LETTER_U_WITH_DOUBLE_ACUTE,
        'u' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_U_WITH_GRAVE,
        'u' . UTF8::COMBINING_HOOK_ABOVE                                        => UTF8::LATIN_SMALL_LETTER_U_WITH_HOOK_ABOVE,
        'u' . UTF8::COMBINING_MACRON                                            => UTF8::LATIN_SMALL_LETTER_U_WITH_MACRON,
        'u' . UTF8::COMBINING_MACRON . UTF8::COMBINING_DIAERESIS                => UTF8::LATIN_SMALL_LETTER_U_WITH_MACRON_AND_DIAERESIS,
        'u' . UTF8::COMBINING_OGONEK                                            => UTF8::LATIN_SMALL_LETTER_U_WITH_OGONEK,
        'u' . UTF8::COMBINING_RING_ABOVE                                        => UTF8::LATIN_SMALL_LETTER_U_WITH_RING_ABOVE,
        'u' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_SMALL_LETTER_U_WITH_TILDE,
        'u' . UTF8::COMBINING_TILDE . UTF8::COMBINING_ACUTE_ACCENT              => UTF8::LATIN_SMALL_LETTER_U_WITH_TILDE_AND_ACUTE,
        'v' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_V_WITH_DOT_BELOW,
        'v' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_SMALL_LETTER_V_WITH_TILDE,
        'w' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_W_WITH_ACUTE,
        'w' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_SMALL_LETTER_W_WITH_CIRCUMFLEX,
        'w' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_SMALL_LETTER_W_WITH_DIAERESIS,
        'w' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_W_WITH_DOT_ABOVE,
        'w' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_W_WITH_DOT_BELOW,
        'w' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_W_WITH_GRAVE,
        'w' . UTF8::COMBINING_RING_ABOVE                                        => UTF8::LATIN_SMALL_LETTER_W_WITH_RING_ABOVE,
        'x' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_SMALL_LETTER_X_WITH_DIAERESIS,
        'x' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_X_WITH_DOT_ABOVE,
        'y' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_Y_WITH_ACUTE,
        'y' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_SMALL_LETTER_Y_WITH_CIRCUMFLEX,
        'y' . UTF8::COMBINING_DIAERESIS                                         => UTF8::LATIN_SMALL_LETTER_Y_WITH_DIAERESIS,
        'y' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_Y_WITH_DOT_ABOVE,
        'y' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_Y_WITH_DOT_BELOW,
        'y' . UTF8::COMBINING_GRAVE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_Y_WITH_GRAVE,
        'y' . UTF8::COMBINING_HOOK_ABOVE                                        => UTF8::LATIN_SMALL_LETTER_Y_WITH_HOOK_ABOVE,
        'y' . UTF8::COMBINING_MACRON                                            => UTF8::LATIN_SMALL_LETTER_Y_WITH_MACRON,
        'y' . UTF8::COMBINING_RING_ABOVE                                        => UTF8::LATIN_SMALL_LETTER_Y_WITH_RING_ABOVE,
        'y' . UTF8::COMBINING_TILDE                                             => UTF8::LATIN_SMALL_LETTER_Y_WITH_TILDE,
        'z' . UTF8::COMBINING_ACUTE_ACCENT                                      => UTF8::LATIN_SMALL_LETTER_Z_WITH_ACUTE,
        'z' . UTF8::COMBINING_CARON                                             => UTF8::LATIN_SMALL_LETTER_Z_WITH_CARON,
        'z' . UTF8::COMBINING_CIRCUMFLEX_ACCENT                                 => UTF8::LATIN_SMALL_LETTER_Z_WITH_CIRCUMFLEX,
        'z' . UTF8::COMBINING_DOT_ABOVE                                         => UTF8::LATIN_SMALL_LETTER_Z_WITH_DOT_ABOVE,
        'z' . UTF8::COMBINING_DOT_BELOW                                         => UTF8::LATIN_SMALL_LETTER_Z_WITH_DOT_BELOW,
        UTF8::LATIN_CAPITAL_LETTER_AE . UTF8::COMBINING_ACUTE_ACCENT            => UTF8::LATIN_CAPITAL_LETTER_AE_WITH_ACUTE,
        UTF8::LATIN_CAPITAL_LETTER_AE . UTF8::COMBINING_MACRON                  => UTF8::LATIN_CAPITAL_LETTER_AE_WITH_MACRON,
        UTF8::LATIN_CAPITAL_LETTER_O_WITH_STROKE . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_CAPITAL_LETTER_O_WITH_STROKE_AND_ACUTE,
        UTF8::LATIN_SMALL_LETTER_AE . UTF8::COMBINING_ACUTE_ACCENT              => UTF8::LATIN_SMALL_LETTER_AE_WITH_ACUTE,
        UTF8::LATIN_SMALL_LETTER_AE . UTF8::COMBINING_MACRON                    => UTF8::LATIN_SMALL_LETTER_AE_WITH_MACRON,
        UTF8::LATIN_SMALL_LETTER_O_WITH_STROKE . UTF8::COMBINING_ACUTE_ACCENT   => UTF8::LATIN_SMALL_LETTER_O_WITH_STROKE_AND_ACUTE,
    ];

    // ANSEL supports O and U with a horn diacritic, but not the combining diacritic.
    private const HORN_CONVERT_STEP_1 = [
        'O' . UTF8::COMBINING_HORN => "\x00O_WITH_HORN\x00",
        'U' . UTF8::COMBINING_HORN => "\x00U_WITH_HORN\x00",
        'o' . UTF8::COMBINING_HORN => "\x00o_WITH_HORN\x00",
        'u' . UTF8::COMBINING_HORN => "\x00u_WITH_HORN\x00",
    ];
    private const HORN_CONVERT_STEP_2 = [
        "\x00O_WITH_HORN\x00" => "\xAC",
        "\x00U_WITH_HORN\x00" => "\xAD",
        "\x00o_WITH_HORN\x00" => "\xBC",
        "\x00u_WITH_HORN\x00" => "\xBD",
    ];

    /**
     * Convert a string from another encoding to UTF-8.
     *
     * @param string $text
     *
     * @return string
     */
    public function toUtf8(string $text): string
    {
        // ANSEL diacritics are prefixes.  UTF-8 diacritics are suffixes.
        $text = preg_replace('/([\xE0-\xFF]+)(.)/', '$2$1', $text);

        // Simple substitution creates denormalized UTF-8.
        $text = strtr($text, self::TO_UTF8);

        // Convert combining diacritics into pre-composed characters.
        return strtr($text, self::PRECOMPOSED_CHARACTERS);
    }

    /**
     * Convert a string from UTF-8 to another encoding.
     *
     * @param string $text
     *
     * @return string
     */
    public function fromUtf8(string $text): string
    {
        // Convert pre-composed characters into combining diacritics.
        $text = strtr($text, array_flip(self::PRECOMPOSED_CHARACTERS));

        // ANSEL supports letters with horns, but not the combining horn.
        $text = strtr($text, self::HORN_CONVERT_STEP_1);

        // Convert characters and combining diacritics separately.
        $text = parent::fromUtf8($text);

        // ANSEL supports two letters with horns, but not the combining horn.
        $text = strtr($text, self::HORN_CONVERT_STEP_2);

        // ANSEL diacritics are prefixes.  UTF-8 diacritics are suffixes.
        $text = preg_replace('/([^\xE0-\xFF])([\xE0-\xFF]+)/', '$2$1', $text);

        return $text;
    }
}
