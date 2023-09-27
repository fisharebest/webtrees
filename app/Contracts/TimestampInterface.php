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

namespace Fisharebest\Webtrees\Contracts;

/**
 * A localized date-time.
 */
interface TimestampInterface
{
    /**
     * Convert a datetime to the user's Julian day number.
     *
     * @return int
     */
    public function julianDay(): int;

    /**
     * @return string
     */
    public function diffForHumans(): string;

    /**
     * @param string $format
     *
     * @return string
     */
    public function format(string $format): string;

    /**
     * @param string $format
     *
     * @return string
     */
    public function isoFormat(string $format): string;

    /**
     * @return string
     */
    public function toDateString(): string;

    /**
     * @return string
     */
    public function toDateTimeString(): string;

    /**
     * @param TimestampInterface $datetime
     *
     * @return int
     */
    public function compare(TimestampInterface $datetime): int;

    /**
     * @param int $seconds
     *
     * @return self
     */
    public function addSeconds(int $seconds): TimestampInterface;

    /**
     * @param int $minutes
     *
     * @return self
     */
    public function addMinutes(int $minutes): TimestampInterface;

    /**
     * @param int $hours
     *
     * @return self
     */
    public function addHours(int $hours): TimestampInterface;

    /**
     * @param int $days
     *
     * @return self
     */
    public function addDays(int $days): TimestampInterface;

    /**
     * @param int $months
     *
     * @return self
     */
    public function addMonths(int $months): TimestampInterface;

    /**
     * @param int $years
     *
     * @return self
     */
    public function addYears(int $years): TimestampInterface;

    /**
     * @param int $seconds
     *
     * @return self
     */
    public function subtractSeconds(int $seconds): TimestampInterface;

    /**
     * @param int $minutes
     *
     * @return self
     */
    public function subtractMinutes(int $minutes): TimestampInterface;

    /**
     * @param int $hours
     *
     * @return self
     */
    public function subtractHours(int $hours): TimestampInterface;

    /**
     * @param int $days
     *
     * @return self
     */
    public function subtractDays(int $days): TimestampInterface;

    /**
     * @param int $months
     *
     * @return self
     */
    public function subtractMonths(int $months): TimestampInterface;

    /**
     * @param int $years
     *
     * @return self
     */
    public function subtractYears(int $years): TimestampInterface;

    /**
     * @return int
     */
    public function timestamp(): int;
}
