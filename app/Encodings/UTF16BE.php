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

namespace Fisharebest\Webtrees\Encodings;

use function chr;
use function intdiv;
use function ord;

/**
 * Convert between UTF-16BE and UTF-8.
 */
class UTF16BE extends AbstractUTF16Encoding
{
    public const NAME = 'UTF-16BE';

    public const BYTE_ORDER_MARK       = "\xFE\xFF";
    public const REPLACEMENT_CHARACTER = "\xFF\xFD";

    /**
     * Convert two bytes to a code-point, taking care of byte-order.
     *
     * @param string $character
     *
     * @return int
     */
    protected function characterToCodePoint(string $character): int
    {
        return 256 * ord($character[0]) + ord($character[1]);
    }

    /**
     * Convert a code-point to two bytes, taking care of byte-order.
     *
     * @param int $code_point
     *
     * @return string
     */
    protected function codePointToCharacter(int $code_point): string
    {
        if ($code_point >= 0xD800 && $code_point <= 0xDFFF) {
            return self::REPLACEMENT_CHARACTER;
        }

        return chr(intdiv($code_point, 256)) . chr($code_point % 256);
    }
}
