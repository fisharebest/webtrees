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

use function array_flip;
use function array_map;
use function implode;
use function ord;
use function preg_split;
use function strlen;
use function strrpos;
use function strtr;

use const PREG_SPLIT_NO_EMPTY;

/**
 * Convert between an encoding and UTF-8.
 */
abstract class AbstractEncoding implements EncodingInterface
{
    protected const REPLACEMENT_CHARACTER = '?';

    /** @var array<string,string> Encoded character => utf8 character */
    protected const TO_UTF8 = [];

    /**
     * Convert a string from UTF-8 to another encoding.
     *
     * @param string $text
     *
     * @return string
     */
    public function fromUtf8(string $text): string
    {
        $utf8  = array_flip(static::TO_UTF8);
        $utf8[UTF8::REPLACEMENT_CHARACTER] = static::REPLACEMENT_CHARACTER;

        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $chars = array_map(static function (string $char) use ($utf8): string {
            if (ord($char[0]) < 128) {
                return $char;
            }

            return $utf8[$char] ?? static::REPLACEMENT_CHARACTER;
        }, $chars);

        return implode('', $chars);
    }

    /**
     * Convert a string from another encoding to UTF-8.
     *
     * @param string $text
     *
     * @return string
     */
    public function toUtf8(string $text): string
    {
        return strtr($text, static::TO_UTF8);
    }

    /**
     * When reading multi-byte encodings using a stream, we must avoid incomplete characters.
     *
     * @param string $text
     *
     * @return int
     */
    public function convertibleBytes(string $text): int
    {
        $safe_chars = [
            $this->fromUtf8("\n"),
            $this->fromUtf8("\r"),
            $this->fromUtf8(' '),
        ];

        foreach ($safe_chars as $char) {
            $pos = strrpos($text, $char);

            if ($pos !== false) {
                return $pos + strlen($char);
            }
        }

        return 0;
    }
}
