<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Enums;

use InvalidArgumentException;

/**
 * Byte order for reading binary data (e.g. .MO files).
 * The values are PHP pack() format characters.
 */
enum ByteOrder: string
{
    // Magic strings used in .MO file headers
    private const string MO_MAGIC_LITTLE_ENDIAN = '950412de';
    private const string MO_MAGIC_BIG_ENDIAN    = 'de120495';

    case BigEndian    = 'N';
    case LittleEndian = 'V';

    /**
     * Determine the byte order from the magic string in a .MO file header.
     */
    public static function fromMoMagicString(string $magic): self
    {
        return match ($magic) {
            self::MO_MAGIC_LITTLE_ENDIAN => self::LittleEndian,
            self::MO_MAGIC_BIG_ENDIAN    => self::BigEndian,
            default                      => throw new InvalidArgumentException('Invalid .MO file'),
        };
    }
}
