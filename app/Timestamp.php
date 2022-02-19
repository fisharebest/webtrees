<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use DateInterval;
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
     * @param TimestampInterface $datetime
     *
     * @return int
     */
    public function compare(TimestampInterface $datetime): int
    {
        if ($this->carbon->lt($datetime)) {
            return -1;
        }

        if ($this->carbon->gt($datetime)) {
            return 1;
        }

        return 0;
    }

    /**
     * @param int $seconds
     *
     * @return $this
     */
    public function addSeconds(int $seconds): static
    {
        if ($seconds < 0) {
            return $this->subtractSeconds(-$seconds);
        }

        $clone = clone($this);

        $clone->carbon->add(new DateInterval('PT' . $seconds . 'S'));

        return $clone;
    }

    /**
     * @param int $minutes
     *
     * @return $this
     */
    public function addMinutes(int $minutes): static
    {
        if ($minutes < 0) {
            return $this->subtractMinutes(-$minutes);
        }

        $clone = clone($this);

        $clone->carbon->add(new DateInterval('PT' . $minutes . 'M'));

        return $this;
    }

    /**
     * @param int $hours
     *
     * @return $this
     */
    public function addHours(int $hours): static
    {
        if ($hours < 0) {
            return $this->subtractHours(-$hours);
        }

        $clone = clone($this);

        $clone->carbon->add(new DateInterval('PT' . $hours . 'H'));

        return $clone;
    }

    /**
     * @param int $days
     *
     * @return $this
     */
    public function addDays(int $days): static
    {
        if ($days < 0) {
            return $this->subtractHours(-$days);
        }

        $clone = clone($this);

        $clone->carbon->add(new DateInterval('P' . $days . 'D'));

        return $clone;
    }

    /**
     * @param int $months
     *
     * @return $this
     */
    public function addMonths(int $months): static
    {
        if ($months < 0) {
            return $this->subtractMonths(-$months);
        }

        $clone = clone($this);

        $clone->carbon->add(new DateInterval('P' . $months . 'M'));

        return $clone;
    }

    /**
     * @param int $years
     *
     * @return $this
     */
    public function addYears(int $years): static
    {
        if ($years < 0) {
            return $this->subtractYears(-$years);
        }

        $clone = clone($this);

        $clone->carbon->add(new DateInterval('P' . $years . 'Y'));

        return $clone;
    }

    /**
     * @param int $seconds
     *
     * @return $this
     */
    public function subtractSeconds(int $seconds): static
    {
        if ($seconds < 0) {
            return $this->addSeconds(-$seconds);
        }

        $clone = clone($this);

        $clone->carbon->sub(new DateInterval('PT' . $seconds . 'S'));

        return $clone;
    }

    /**
     * @param int $minutes
     *
     * @return $this
     */
    public function subtractMinutes(int $minutes): static
    {
        if ($minutes < 0) {
            return $this->addMinutes(-$minutes);
        }

        $clone = clone($this);

        $clone->carbon->sub(new DateInterval('PT' . $minutes . 'M'));

        return $clone;
    }

    /**
     * @param int $hours
     *
     * @return $this
     */
    public function subtractHours(int $hours): static
    {
        if ($hours < 0) {
            return $this->addHours(-$hours);
        }

        $clone = clone($this);

        $clone->carbon->sub(new DateInterval('PT' . $hours . 'H'));

        return $clone;
    }

    /**
     * @param int $days
     *
     * @return $this
     */
    public function subtractDays(int $days): static
    {
        if ($days < 0) {
            return $this->addDays(-$days);
        }

        $clone = clone($this);

        $clone->carbon->sub(new DateInterval('P' . $days . 'D'));

        return $clone;
    }

    /**
     * @param int $months
     *
     * @return $this
     */
    public function subtractMonths(int $months): static
    {
        if ($months < 0) {
            return $this->addMonths(-$months);
        }

        $clone = clone($this);

        $clone->carbon->sub(new DateInterval('P' . $months . 'M'));

        return $clone;
    }

    /**
     * @param int $years
     *
     * @return $this
     */
    public function subtractYears(int $years): static
    {
        if ($years < 0) {
            return $this->addYears(-$years);
        }

        $clone = clone($this);

        $clone->carbon->sub(new DateInterval('P' . $years . 'Y'));

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
