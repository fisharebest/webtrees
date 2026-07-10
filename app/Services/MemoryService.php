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

namespace Fisharebest\Webtrees\Services;

/**
 * Check for PHP memory limits.
 */
class MemoryService
{
    // Bytes until we run out of memory
    private const int MEMORY_UP_THRESHOLD = 8 * 1024 * 1024;

    /**
     * Some long-running scripts need to know when to stop.
     */
    public function isMemoryNearlyUp(int $threshold = self::MEMORY_UP_THRESHOLD): bool
    {
        $memory_limit = $this->php_service->memoryLimit();

        // If there's no memory limit, then we can't run out of memory.
        if ($memory_limit <= 0) {
            return false;
        }

        $memory_usage = $this->php_service->memoryGetUsage(true);

        return $memory_usage + $threshold > $memory_limit;
    }

    public function __construct(private PhpService $php_service)
    {
    }
}

