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
 * Convert between ASCII and UTF-8.
 */
class ASCII extends AbstractEncoding
{
    public const NAME = 'ASCII';

    protected const REPLACEMENT_CHARACTER = '?';

    protected const TO_UTF8 = [
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
        "\xA0" => UTF8::REPLACEMENT_CHARACTER,
        "\xA1" => UTF8::REPLACEMENT_CHARACTER,
        "\xA2" => UTF8::REPLACEMENT_CHARACTER,
        "\xA3" => UTF8::REPLACEMENT_CHARACTER,
        "\xA4" => UTF8::REPLACEMENT_CHARACTER,
        "\xA5" => UTF8::REPLACEMENT_CHARACTER,
        "\xA6" => UTF8::REPLACEMENT_CHARACTER,
        "\xA7" => UTF8::REPLACEMENT_CHARACTER,
        "\xA8" => UTF8::REPLACEMENT_CHARACTER,
        "\xA9" => UTF8::REPLACEMENT_CHARACTER,
        "\xAA" => UTF8::REPLACEMENT_CHARACTER,
        "\xAB" => UTF8::REPLACEMENT_CHARACTER,
        "\xAC" => UTF8::REPLACEMENT_CHARACTER,
        "\xAD" => UTF8::REPLACEMENT_CHARACTER,
        "\xAE" => UTF8::REPLACEMENT_CHARACTER,
        "\xAF" => UTF8::REPLACEMENT_CHARACTER,
        "\xB0" => UTF8::REPLACEMENT_CHARACTER,
        "\xB1" => UTF8::REPLACEMENT_CHARACTER,
        "\xB2" => UTF8::REPLACEMENT_CHARACTER,
        "\xB3" => UTF8::REPLACEMENT_CHARACTER,
        "\xB4" => UTF8::REPLACEMENT_CHARACTER,
        "\xB5" => UTF8::REPLACEMENT_CHARACTER,
        "\xB6" => UTF8::REPLACEMENT_CHARACTER,
        "\xB7" => UTF8::REPLACEMENT_CHARACTER,
        "\xB8" => UTF8::REPLACEMENT_CHARACTER,
        "\xB9" => UTF8::REPLACEMENT_CHARACTER,
        "\xBA" => UTF8::REPLACEMENT_CHARACTER,
        "\xBB" => UTF8::REPLACEMENT_CHARACTER,
        "\xBC" => UTF8::REPLACEMENT_CHARACTER,
        "\xBD" => UTF8::REPLACEMENT_CHARACTER,
        "\xBE" => UTF8::REPLACEMENT_CHARACTER,
        "\xBF" => UTF8::REPLACEMENT_CHARACTER,
        "\xC0" => UTF8::REPLACEMENT_CHARACTER,
        "\xC1" => UTF8::REPLACEMENT_CHARACTER,
        "\xC2" => UTF8::REPLACEMENT_CHARACTER,
        "\xC3" => UTF8::REPLACEMENT_CHARACTER,
        "\xC4" => UTF8::REPLACEMENT_CHARACTER,
        "\xC5" => UTF8::REPLACEMENT_CHARACTER,
        "\xC6" => UTF8::REPLACEMENT_CHARACTER,
        "\xC7" => UTF8::REPLACEMENT_CHARACTER,
        "\xC8" => UTF8::REPLACEMENT_CHARACTER,
        "\xC9" => UTF8::REPLACEMENT_CHARACTER,
        "\xCA" => UTF8::REPLACEMENT_CHARACTER,
        "\xCB" => UTF8::REPLACEMENT_CHARACTER,
        "\xCC" => UTF8::REPLACEMENT_CHARACTER,
        "\xCD" => UTF8::REPLACEMENT_CHARACTER,
        "\xCE" => UTF8::REPLACEMENT_CHARACTER,
        "\xCF" => UTF8::REPLACEMENT_CHARACTER,
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
        "\xE0" => UTF8::REPLACEMENT_CHARACTER,
        "\xE1" => UTF8::REPLACEMENT_CHARACTER,
        "\xE2" => UTF8::REPLACEMENT_CHARACTER,
        "\xE3" => UTF8::REPLACEMENT_CHARACTER,
        "\xE4" => UTF8::REPLACEMENT_CHARACTER,
        "\xE5" => UTF8::REPLACEMENT_CHARACTER,
        "\xE6" => UTF8::REPLACEMENT_CHARACTER,
        "\xE7" => UTF8::REPLACEMENT_CHARACTER,
        "\xE8" => UTF8::REPLACEMENT_CHARACTER,
        "\xE9" => UTF8::REPLACEMENT_CHARACTER,
        "\xEA" => UTF8::REPLACEMENT_CHARACTER,
        "\xEB" => UTF8::REPLACEMENT_CHARACTER,
        "\xEC" => UTF8::REPLACEMENT_CHARACTER,
        "\xED" => UTF8::REPLACEMENT_CHARACTER,
        "\xEE" => UTF8::REPLACEMENT_CHARACTER,
        "\xEF" => UTF8::REPLACEMENT_CHARACTER,
        "\xF0" => UTF8::REPLACEMENT_CHARACTER,
        "\xF1" => UTF8::REPLACEMENT_CHARACTER,
        "\xF2" => UTF8::REPLACEMENT_CHARACTER,
        "\xF3" => UTF8::REPLACEMENT_CHARACTER,
        "\xF4" => UTF8::REPLACEMENT_CHARACTER,
        "\xF5" => UTF8::REPLACEMENT_CHARACTER,
        "\xF6" => UTF8::REPLACEMENT_CHARACTER,
        "\xF7" => UTF8::REPLACEMENT_CHARACTER,
        "\xF8" => UTF8::REPLACEMENT_CHARACTER,
        "\xF9" => UTF8::REPLACEMENT_CHARACTER,
        "\xFA" => UTF8::REPLACEMENT_CHARACTER,
        "\xFB" => UTF8::REPLACEMENT_CHARACTER,
        "\xFC" => UTF8::REPLACEMENT_CHARACTER,
        "\xFD" => UTF8::REPLACEMENT_CHARACTER,
        "\xFE" => UTF8::REPLACEMENT_CHARACTER,
        "\xFF" => UTF8::REPLACEMENT_CHARACTER,
    ];
}
