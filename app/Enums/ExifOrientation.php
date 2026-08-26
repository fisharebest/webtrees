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

/**
 * EXIF orientation values from the TIFF/EXIF specification.
 */
enum ExifOrientation: int
{
    case Normal                   = 1;
    case MirrorHorizontal         = 2;
    case Rotate180                = 3;
    case MirrorVertical           = 4;
    case Transpose                = 5;
    case Rotate90Clockwise        = 6;
    case Transverse               = 7;
    case Rotate90CounterClockwise = 8;
}
