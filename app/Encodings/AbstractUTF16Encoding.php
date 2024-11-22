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

use function chr;
use function intdiv;
use function ord;
use function str_split;
use function strlen;

/**
 * Convert between an encoding and UTF-16.
 */
abstract class AbstractUTF16Encoding implements EncodingInterface
{
    // Concrete classes should implement this.
    public const string REPLACEMENT_CHARACTER = '';

    /**
     * Convert a string from UTF-8 to another encoding.
     *
     * @param string $text
     *
     * @return string
     */
    public function fromUtf8(string $text): string
    {
        $out = '';
        $len = strlen($text);

        for ($n = 0; $n < $len; ++$n) {
            $code_point = ord($text[$n]);

            if ($code_point <= 0x7F) {
                $out .= $this->codePointToCharacter($code_point);
            } elseif ($code_point <= 0xBF) {
                // Invalid
                $out .= static::REPLACEMENT_CHARACTER;
            } elseif ($code_point <= 0xDF) {
                $byte2 = ord($text[++$n]);

                if (($byte2 & 0xC0) !== 0x80) {
                    // Invalid
                    $out .= static::REPLACEMENT_CHARACTER;
                } else {
                    $out .= $this->codePointToCharacter($code_point << 6 + $byte2 & 0x3F);
                }
            } elseif ($code_point <= 0xEF) {
                $byte2 = ord($text[++$n]);
                $byte3 = ord($text[++$n]);

                if (($byte2 & 0xC0) !== 0x80 || ($byte3 & 0xC0) !== 0x80) {
                    // Invalid
                    $out .= static::REPLACEMENT_CHARACTER;
                } else {
                    $out .= $this->codePointToCharacter($code_point << 12 + ($byte2 & 0x3F) << 6 + $byte3 & 0x3F);
                }
            } else {
                // Invalid
                $out .= static::REPLACEMENT_CHARACTER;
            }
        }

        return $out;
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
        $utf8 = '';

        foreach (str_split($text, 2) as $character) {
            $code_point = $this->characterToCodePoint($character);

            if ($code_point <= 0x7F) {
                // 7 bits => 1 byte
                $utf8 .= chr($code_point);
            } elseif ($code_point <= 0xFF) {
                // U+80 - U+FF are invalid
                $utf8 .= UTF8::REPLACEMENT_CHARACTER;
            } elseif ($code_point <= 0x7FF) {
                // 11 bits (5,6) => 2 bytes
                $utf8 .= chr(0xC0 | ($code_point >> 6));
                $utf8 .= chr(0x80 | $code_point & 0x3F);
            } elseif ($code_point <= 0xD7FF || $code_point >= 0xE000) {
                // 16 bits (4,6,6) => 3 bytes
                $utf8 .= chr(0xE0 | ($code_point >> 12));
                $utf8 .= chr(0x80 | ($code_point >> 6) & 0x3F);
                $utf8 .= chr(0x80 | $code_point & 0x3F);
            } else {
                // U+D800 - U+DFFF are invalid
                $utf8 .= UTF8::REPLACEMENT_CHARACTER;
            }
        }

        return $utf8;
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
        return 2 * intdiv(strlen($text), 2);
    }

    /**
     * Convert two bytes to a code-point, taking care of byte-order.
     *
     * @param string $character
     *
     * @return int
     */
    abstract protected function characterToCodePoint(string $character): int;

    /**
     * Convert a code-point to two bytes, taking care of byte-order.
     *
     * @param int $code_point
     *
     * @return string
     */
    abstract protected function codePointToCharacter(int $code_point): string;
}
