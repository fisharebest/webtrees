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

use LogicException;

use function array_key_exists;
use function preg_match;

final readonly class Style
{
    /**
     * @param array<string,string> $attrs
     */
    public static function fromXmlAttributes(array $attrs): self
    {
        if (!array_key_exists('name', $attrs)) {
            throw new LogicException('The "name" attribute is missing.');
        }

        if ($attrs['name'] === '') {
            throw new LogicException('The "name" attribute is empty.');
        }

        return new self(
            name: $attrs['name'],
            style: $attrs['style'] ?? '',
            size: (float) ($attrs['size'] ?? StyleDefaults::DEFAULT_FONT_SIZE),
        );
    }

    public function __construct(
        public string $name,
        public string $style,
        public float $size,
    ) {
        if (preg_match('/^[biud]*$/', $this->style) !== 1) {
            $message = sprintf('Invalid style flags "%s". Use only lowercase b, i, u, and d.', $this->style);
            throw new LogicException($message);
        }
    }
}
