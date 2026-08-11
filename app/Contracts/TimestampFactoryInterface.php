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

use Carbon\CarbonImmutable;

/**
 * Create localized CarbonImmutable instances with the user's timezone and language.
 */
interface TimestampFactoryInterface
{
    /** Create a localized CarbonImmutable from a DateTimeImmutable. */
    public function fromDateTime(\DateTimeImmutable $datetime, UserInterface|null $user = null): CarbonImmutable;

    /** Create a localized CarbonImmutable from a unix epoch integer. */
    public function fromEpoch(int $timestamp, UserInterface|null $user = null): CarbonImmutable;

    /**
     * Create a localized CarbonImmutable from a date-time string.
     *
     * @param string|null $string YYYY-MM-DD HH:MM:SS (as provided by SQL).
     */
    public function fromString(string|null $string, string $format = 'Y-m-d H:i:s', UserInterface|null $user = null): CarbonImmutable;

    /** Create a localized CarbonImmutable for the current moment. */
    public function now(UserInterface|null $user = null): CarbonImmutable;

    /** Today's Julian Day number in the user's timezone. */
    public function todayJulianDay(UserInterface|null $user = null): int;
}
