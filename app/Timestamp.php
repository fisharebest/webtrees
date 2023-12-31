<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Carbon\Carbon;
use Fisharebest\Webtrees\Contracts\TimestampInterface;

/**
 * A localized date-time.
 */
class Timestamp implements TimestampInterface
{
    private Carbon $carbon;

    /**
     * @param int    $timestamp
     * @param string $timezone
     * @param string $locale
     */
    public function __construct(int $timestamp, string $timezone, string $locale)
    {
        $this->carbon = Carbon::createFromTimestamp($timestamp, $timezone);
        $this->carbon->locale($locale);
    }

    public function __clone()
    {
        $this->carbon = clone($this->carbon);
    }

    /**
     * Convert a datetime to the user's Julian day number.
     *
     * @return int
     */
    public function julianDay(): int
    {
        return gregoriantojd($this->carbon->month, $this->carbon->day, $this->carbon->year);
    }

    /**
     * @return string
     */
    public function diffForHumans(): string
    {
        return $this->carbon->diffForHumans();
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public function format(string $format): string
    {
        return $this->carbon->format($format);
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public function isoFormat(string $format): string
    {
        return $this->carbon->isoFormat($format);
    }

    /**
     * @return string
     */
    public function toDateString(): string
    {
        return $this->carbon->format('Y-m-d');
    }

    /**
     * @return string
     */
    public function toDateTimeString(): string
    {
        return $this->carbon->format('Y-m-d H:i:s');
    }

    /**
     * Use UTC instead of the saved timezone.
     *
     * @return string
     */
    public function toUTCDateTimeString(): string
    {
        return Carbon::createFromTimestampUTC($this->carbon->getTimestamp())->format('Y-m-d H:i:s');
    }

    /**
     * @param TimestampInterface $datetime
     *
     * @return int
     */
    public function compare(TimestampInterface $datetime): int
    {
        if ($this->carbon->lt($datetime->carbon)) {
            return -1;
        }

        if ($this->carbon->gt($datetime->carbon)) {
            return 1;
        }

        return 0;
    }

    /**
     * @param int $seconds
     *
     * @return self
     */
    public function addSeconds(int $seconds): TimestampInterface
    {
        $clone = clone($this);

        $clone->carbon->addSeconds($seconds);

        return $clone;
    }

    /**
     * @param int $minutes
     *
     * @return self
     */
    public function addMinutes(int $minutes): TimestampInterface
    {
        $clone = clone($this);

        $clone->carbon->addMinutes($minutes);

        return $this;
    }

    /**
     * @param int $hours
     *
     * @return self
     */
    public function addHours(int $hours): TimestampInterface
    {
        $clone = clone($this);

        $clone->carbon->addHours($hours);

        return $clone;
    }

    /**
     * @param int $days
     *
     * @return self
     */
    public function addDays(int $days): TimestampInterface
    {
        $clone = clone($this);

        $clone->carbon->addDays($days);

        return $clone;
    }

    /**
     * Add to the month portion of the date.
     *
     * Allows overflow, consistent with v2.1.0 ... 2023-10-31 plus 1 month = 2023-12-01.
     *
     * @param int $months
     *
     * @return self
     */
    public function addMonths(int $months): TimestampInterface
    {
        $clone = clone($this);

        $clone->carbon->addMonths($months);

        return $clone;
    }

    /**
     * Add to the year portion of the date.
     *
     * Allows overflow, consistent with v2.1.0 ... 2024-02-29 plus 1 year = 2025-03-01.
     *
     * @param int $years
     *
     * @return self
     */
    public function addYears(int $years): TimestampInterface
    {
        $clone = clone($this);

        $clone->carbon->addYears($years);

        return $clone;
    }

    /**
     * @param int $seconds
     *
     * @return self
     */
    public function subtractSeconds(int $seconds): TimestampInterface
    {
        $clone = clone($this);

        $clone->carbon->subSeconds($seconds);

        return $clone;
    }

    /**
     * @param int $minutes
     *
     * @return self
     */
    public function subtractMinutes(int $minutes): TimestampInterface
    {
        $clone = clone($this);

        $clone->carbon->subMinutes($minutes);

        return $this;
    }

    /**
     * @param int $hours
     *
     * @return self
     */
    public function subtractHours(int $hours): TimestampInterface
    {
        $clone = clone($this);

        $clone->carbon->subHours($hours);

        return $clone;
    }

    /**
     * @param int $days
     *
     * @return self
     */
    public function subtractDays(int $days): TimestampInterface
    {
        $clone = clone($this);

        $clone->carbon->subDays($days);

        return $clone;
    }

    /**
     * Subtract from the month portion of the date.
     *
     * Allows overflow, consistent with v2.1.0 ... 2023-10-31 minus 1 month = 2023-10-01.
     *
     * @param int $months
     *
     * @return self
     */
    public function subtractMonths(int $months): TimestampInterface
    {
        $clone = clone($this);

        $clone->carbon->subMonths($months);

        return $clone;
    }

    /**
     * Subtract from the year portion of the date.
     *
     * Allows overflow, consistent with v2.1.0 ... 2024-02-29 minus 1 year = 2023-03-01.
     *
     * @param int $years
     *
     * @return self
     */
    public function subtractYears(int $years): TimestampInterface
    {
        $clone = clone($this);

        $clone->carbon->subYears($years);

        return $clone;
    }

    /**
     * @return int
     */
    public function timestamp(): int
    {
        return $this->carbon->getTimestamp();
    }
}
