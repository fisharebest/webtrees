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

/**
 * Immutable report element tree grouped by section.
 */
final readonly class Document
{
    /**
     * @param list<Element> $header_elements
     * @param list<Element> $body_elements
     * @param list<Element> $footer_elements
     */
    public function __construct(
        public string $title,
        public array $header_elements,
        public array $body_elements,
        public array $footer_elements,
    ) {
    }

    public static function empty(string $title = ''): self
    {
        return new self($title, [], [], []);
    }
}
