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

namespace Fisharebest\Webtrees;

use function explode;
use function implode;
use function preg_match;

/**
 * The part of an image to show, from a GEDCOM 7 CROP structure on a media link.
 * All values are pixels.  A missing height or width means "to the edge of the image".
 */
final readonly class MediaCrop
{
    public function __construct(
        public int $top,
        public int $left,
        public int|null $height,
        public int|null $width,
    ) {
    }

    /**
     * Read the CROP structure from a media link, such as "1 OBJE @M1@\n2 CROP\n3 TOP 10".
     */
    public static function fromGedcom(string $gedcom): self|null
    {
        if (preg_match('/\n2 CROP((?:\n[3-9] .*)*)/', $gedcom, $match) !== 1) {
            return null;
        }

        $value = static function (string $tag) use ($match): int|null {
            if (preg_match('/\n3 ' . $tag . ' (\d+)/', $match[1], $value) === 1) {
                return (int) $value[1];
            }

            return null;
        };

        return new self(
            top: $value('TOP') ?? 0,
            left: $value('LEFT') ?? 0,
            height: $value('HEIGHT'),
            width: $value('WIDTH'),
        );
    }

    /**
     * Read a crop from a URL parameter, as created by toString().
     */
    public static function fromString(string $string): self|null
    {
        if (preg_match('/^(\d+),(\d+),(\d*),(\d*)$/', $string) !== 1) {
            return null;
        }

        [$top, $left, $height, $width] = explode(',', $string);

        return new self(
            top: (int) $top,
            left: (int) $left,
            height: $height === '' ? null : (int) $height,
            width: $width === '' ? null : (int) $width,
        );
    }

    public function toString(): string
    {
        return implode(',', [$this->top, $this->left, $this->height ?? '', $this->width ?? '']);
    }
}
