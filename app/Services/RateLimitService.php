<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpTooManyRequestsException;
use Fisharebest\Webtrees\Site;
use LogicException;

use function array_filter;
use function count;
use function explode;
use function intdiv;
use function strlen;
use function time;

/**
 * Throttle events to prevent abuse.
 */
class RateLimitService
{
    private int $now;

    /**
     *
     */
    public function __construct()
    {
        $this->now = time();
    }

    /**
     * Rate limit for actions related to a user, such as password reset request.
     * Allow $num requests every $seconds
     *
     * @param int    $num     allow this number of events
     * @param int    $seconds in a rolling window of this number of seconds
     * @param string $limit   name of limit to enforce
     *
     * @return void
     */
    public function limitRateForSite(int $num, int $seconds, string $limit): void
    {
        $history = Site::getPreference($limit);

        $history = $this->checkLimitReached($num, $seconds, $history);

        Site::setPreference($limit, $history);
    }

    /**
     * Rate limit for actions related to a user, such as password reset request.
     * Allow $num requests every $seconds
     *
     * @param UserInterface $user    limit events for this user
     * @param int           $num     allow this number of events
     * @param int           $seconds in a rolling window of this number of seconds
     * @param string        $limit   name of limit to enforce
     *
     * @return void
     */
    public function limitRateForUser(UserInterface $user, int $num, int $seconds, string $limit): void
    {
        $history = $user->getPreference($limit);

        $history = $this->checkLimitReached($num, $seconds, $history);

        $user->setPreference($limit, $history);
    }

    /**
     * Rate limit - allow $num requests every $seconds
     *
     * @param int    $num     allow this number of events
     * @param int    $seconds in a rolling window of this number of seconds
     * @param string $history comma-separated list of previous timestamps
     *
     * @return string updated list of timestamps
     * @throws HttpTooManyRequestsException
     */
    private function checkLimitReached(int $num, int $seconds, string $history): string
    {
        // Make sure we can store enough previous timestamps in a database field.
        $max = intdiv(256, strlen($this->now . ','));
        if ($num > $max) {
            throw new LogicException('Cannot store ' . $num . ' previous events in the database');
        }

        // Extract the timestamps.
        $timestamps = array_filter(explode(',', $history));

        // Filter events within our time window.
        $filter    = fn (string $x): bool => (int) $x >= $this->now - $seconds && (int) $x <= $this->now;
        $in_window = array_filter($timestamps, $filter);

        if (count($in_window) >= $num) {
            throw new HttpTooManyRequestsException();
        }

        $timestamps[] = (string) $this->now;

        while (count($timestamps) > $max) {
            array_shift($timestamps);
        }

        return implode(',', $timestamps);
    }
}
