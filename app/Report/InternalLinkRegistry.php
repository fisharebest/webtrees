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
 * Tracks internal PDF links and their destinations.
 */
final class InternalLinkRegistry
{
    private int $next_link_id = 0;

    /**
     * @var array<int, array{page: int, y: float}>
     */
    private array $destinations = [];

    public function create(int $current_page): int
    {
        $this->next_link_id++;
        $this->destinations[$this->next_link_id] = [
            'page' => $current_page,
            'y' => 0.0,
        ];

        return $this->next_link_id;
    }

    public function has(int $link_id): bool
    {
        return isset($this->destinations[$link_id]);
    }

    /**
     * @return array{page: int, y: float}
     */
    public function destination(int $link_id): array
    {
        return $this->destinations[$link_id];
    }

    public function setDestination(int $link_id, float $y, int $current_page, int $page = -1): void
    {
        if (!$this->has($link_id)) {
            return;
        }

        $this->destinations[$link_id] = [
            'page' => $page >= 0 ? $page : $current_page,
            'y' => $y,
        ];
    }
}
