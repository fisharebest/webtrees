<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Services;

use Psr\Http\Message\ServerRequestInterface;

use function app;

/**
 * Check for PHP timeouts.
 */
class TimeoutService
{
    /** @var float Long-running scripts run in small chunks */
    private const TIME_LIMIT = 1.5;

    /** @var float Seconds until we run out of time */
    private const TIME_UP_THRESHOLD = 3.0;

    /** @var float|null The start time of the request */
    private $start_time;

    /**
     * TimeoutService constructor.
     *
     * @param float $start_time
     */
    public function __construct(float $start_time = null)
    {
        $this->start_time = $start_time ?? microtime(true);
    }

    /**
     * Some long-running scripts need to know when to stop.
     *
     * @param float $threshold
     *
     * @return bool
     */
    public function isTimeNearlyUp(float $threshold = self::TIME_UP_THRESHOLD): bool
    {
        $max_execution_time = (int) ini_get('max_execution_time');

        // If there's no time limit, then we can't run out of time.
        if ($max_execution_time === 0) {
            return false;
        }

        $now = microtime(true);

        return $now + $threshold > $this->start_time + (float) $max_execution_time;
    }

    /**
     * Some long running scripts are broken down into small chunks.
     *
     * @param float $limit
     *
     * @return bool
     */
    public function isTimeLimitUp(float $limit = self::TIME_LIMIT): bool
    {
        $now = microtime(true);

        return $now > $this->start_time + $limit;
    }

    /**
     * @return float
     */
    protected function startTime(): float
    {
        if ($this->start_time === null) {
            $request = app(ServerRequestInterface::class);

            $this->start_time = (float) ($request->getServerParams()['REQUEST_TIME_FLOAT'] ?? microtime(true));
        }

        return $this->start_time;
    }
}
