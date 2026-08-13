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

use Closure;

/**
 * Coordinates internal PDF link allocation, destinations, and URL resolution.
 */
final class PdfInternalLinkService
{
    private readonly InternalLinkRegistry $internal_link_registry;

    public function __construct(InternalLinkRegistry|null $internal_link_registry = null)
    {
        $this->internal_link_registry = $internal_link_registry ?? new InternalLinkRegistry();
    }

    public function createLink(int $current_page): int
    {
        return $this->internal_link_registry->create($current_page);
    }

    public function setDestination(int|string $link, float $y, int $current_page, int $page = -1): void
    {
        $this->internal_link_registry->setDestination((int) $link, $y, $current_page, $page);
    }

    /**
     * @param Closure(int, float): string $create_internal_destination
     */
    public function resolveDestination(string $url, Closure $create_internal_destination): string
    {
        $link_id = (int) $url;

        if (!$this->internal_link_registry->has($link_id)) {
            return $url;
        }

        $destination = $this->internal_link_registry->destination($link_id);

        return $create_internal_destination($destination['page'], $destination['y']);
    }
}
