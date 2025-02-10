<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Carbon\CarbonImmutable;
use Fisharebest\Webtrees\Contracts\TimestampInterface;

use function GregorianToJD;

/**
 * A localized date-time.
 */
class Timestamp implements TimestampInterface
{
    private CarbonImmutable $carbon;

    public function __construct(int $timestamp, string $timezone, string $locale)
    {
        $this->carbon = CarbonImmutable::createFromTimestamp($timestamp, $timezone);
        $this->carbon->locale($locale);
    }

    public function __clone()
    {
        $this->carbon = clone($this->carbon);
    }

    public function julianDay(): int
    {
        return GregorianToJD($this->carbon->month, $this->carbon->day, $this->carbon->year);
    }

    public function diffForHumans(): string
    {
        return $this->carbon->diffForHumans();
    }

    public function format(string $format): string
    {
        return $this->carbon->format($format);
    }

    public function isoFormat(string $format): string
    {
        return $this->carbon->isoFormat($format);
    }

    public function toDateString(): string
    {
        return $this->carbon->format('Y-m-d');
    }

    public function toDateTimeString(): string
    {
        return $this->carbon->format('Y-m-d H:i:s');
    }

    public function compare(TimestampInterface $timestamp): int
    {
        return $this->timestamp() <=> $timestamp->timestamp();
    }

    public function addSeconds(int $seconds): TimestampInterface
    {
        return new self(
            $this->carbon->addSeconds($seconds)->getTimestamp(),
            $this->carbon->timezone->getName(),
            $this->carbon->locale
        );
    }

    public function addMinutes(int $minutes): TimestampInterface
    {
        return new self(
            $this->carbon->addMinutes($minutes)->getTimestamp(),
            $this->carbon->timezone->getName(),
            $this->carbon->locale
        );
    }

    public function addHours(int $hours): TimestampInterface
    {
        return new self(
            $this->carbon->addHours($hours)->getTimestamp(),
            $this->carbon->timezone->getName(),
            $this->carbon->locale
        );
    }

    public function addDays(int $days): TimestampInterface
    {
        return new self(
            $this->carbon->addDays($days)->getTimestamp(),
            $this->carbon->timezone->getName(),
            $this->carbon->locale
        );
    }

    public function addMonths(int $months): TimestampInterface
    {
        return new self(
            $this->carbon->addMonths($months)->getTimestamp(),
            $this->carbon->timezone->getName(),
            $this->carbon->locale
        );
    }

    public function addYears(int $years): TimestampInterface
    {
        return new self(
            $this->carbon->addYears($years)->getTimestamp(),
            $this->carbon->timezone->getName(),
            $this->carbon->locale
        );
    }

    public function subtractSeconds(int $seconds): TimestampInterface
    {
        return new self(
            $this->carbon->subSeconds($seconds)->getTimestamp(),
            $this->carbon->timezone->getName(),
            $this->carbon->locale
        );
    }

    public function subtractMinutes(int $minutes): TimestampInterface
    {
        return new self(
            $this->carbon->subMinutes($minutes)->getTimestamp(),
            $this->carbon->timezone->getName(),
            $this->carbon->locale
        );
    }

    public function subtractHours(int $hours): TimestampInterface
    {
        return new self(
            $this->carbon->subHours($hours)->getTimestamp(),
            $this->carbon->timezone->getName(),
            $this->carbon->locale
        );
    }

    public function subtractDays(int $days): TimestampInterface
    {
        return new self(
            $this->carbon->subDays($days)->getTimestamp(),
            $this->carbon->timezone->getName(),
            $this->carbon->locale
        );
    }

    public function subtractMonths(int $months): TimestampInterface
    {
        return new self(
            $this->carbon->subMonths($months)->getTimestamp(),
            $this->carbon->timezone->getName(),
            $this->carbon->locale
        );
    }

    public function subtractYears(int $years): TimestampInterface
    {
        return new self(
            $this->carbon->subYears($years)->getTimestamp(),
            $this->carbon->timezone->getName(),
            $this->carbon->locale
        );
    }

    public function timestamp(): int
    {
        return $this->carbon->getTimestamp();
    }
}
