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

/**
 * Convert between UTF-8 and another encoding.
 */
interface EncodingInterface
{
    // Concrete classes should re-define this.  Use the ICONV name, where possible.
    //public const NAME = '????';

    /**
     * Convert a string from UTF-8 encoding to another encoding.
     *
     * @param string $text
     *
     * @return string
     */
    public function fromUtf8(string $text): string;

    /**
     * Convert a string from another encoding to UTF-8 encoding.
     *
     * @param string $text
     *
     * @return string
     */
    public function toUtf8(string $text): string;

    /**
     * When reading multi-byte encodings using a stream, we must avoid incomplete characters.
     *
     * @param string $text
     *
     * @return int
     */
    public function convertibleBytes(string $text): int;
}
