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

namespace Fisharebest\Webtrees\Report;

enum PaperSize: string
{
    private const float INCH_TO_POINTS = 72.0;
    private const float MM_TO_POINTS   = 72.0 / 25.4;

    // ISO 216
    case A0        = 'A0';
    case A1        = 'A1';
    case A2        = 'A2';
    case A3        = 'A3';
    case A4        = 'A4';
    // US
    case USLetter  = 'US-Letter';
    case USLegal   = 'US-Legal';
    case USTabloid = 'US-Tabloid';

    public function width(): float
    {
        return match ($this) {
            self::A0        => 841.0 * self::MM_TO_POINTS,
            self::A1        => 594.0 * self::MM_TO_POINTS,
            self::A2        => 420.0 * self::MM_TO_POINTS,
            self::A3        => 297.0 * self::MM_TO_POINTS,
            self::A4        => 210.0 * self::MM_TO_POINTS,
            self::USLetter  => 8.5 * self::INCH_TO_POINTS,
            self::USLegal   => 8.5 * self::INCH_TO_POINTS,
            self::USTabloid => 11.0 * self::INCH_TO_POINTS,
        };
    }

    public function height(): float
    {
        return match ($this) {
            self::A0        => 1189.0 * self::MM_TO_POINTS,
            self::A1        => 841.0 * self::MM_TO_POINTS,
            self::A2        => 594.0 * self::MM_TO_POINTS,
            self::A3        => 420.0 * self::MM_TO_POINTS,
            self::A4        => 297.0 * self::MM_TO_POINTS,
            self::USLetter  => 11.0 * self::INCH_TO_POINTS,
            self::USLegal   => 14.0 * self::INCH_TO_POINTS,
            self::USTabloid => 17.0 * self::INCH_TO_POINTS,
        };
    }
}
