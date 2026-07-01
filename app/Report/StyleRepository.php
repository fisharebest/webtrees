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
 * Collects named report styles during XML parsing.
 */
final class StyleRepository
{
    /** @var array<string, Style> */
    private array $styles = [];

    public function add(Style $style): void
    {
        $this->styles[$style->name] = $style;
    }

    public function get(string $name): Style
    {
        return $this->styles[$name];
    }

    /** @return array<string, Style> */
    public function all(): array
    {
        return $this->styles;
    }
}
