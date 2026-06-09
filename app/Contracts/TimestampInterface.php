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

namespace Fisharebest\Webtrees\Contracts;

/**
 * A localized date-time.
 */
interface TimestampInterface
{
    /**
     * Convert a datetime to the user's Julian day number.
     */
    public function julianDay(): int;

    public function diffForHumans(): string;

    public function format(string $format): string;

    public function isoFormat(string $format): string;

    public function toDateString(): string;

    public function toDateTimeString(): string;

    public function compare(TimestampInterface $timestamp): int;

    /**
     *
     * @return self
     */
    public function addSeconds(int $seconds): TimestampInterface;

    /**
     *
     * @return self
     */
    public function addMinutes(int $minutes): TimestampInterface;

    /**
     *
     * @return self
     */
    public function addHours(int $hours): TimestampInterface;

    /**
     *
     * @return self
     */
    public function addDays(int $days): TimestampInterface;

    /**
     *
     * @return self
     */
    public function addMonths(int $months): TimestampInterface;

    /**
     *
     * @return self
     */
    public function addYears(int $years): TimestampInterface;

    /**
     *
     * @return self
     */
    public function subtractSeconds(int $seconds): TimestampInterface;

    /**
     *
     * @return self
     */
    public function subtractMinutes(int $minutes): TimestampInterface;

    /**
     *
     * @return self
     */
    public function subtractHours(int $hours): TimestampInterface;

    /**
     *
     * @return self
     */
    public function subtractDays(int $days): TimestampInterface;

    /**
     *
     * @return self
     */
    public function subtractMonths(int $months): TimestampInterface;

    /**
     *
     * @return self
     */
    public function subtractYears(int $years): TimestampInterface;

    public function timestamp(): int;
}
