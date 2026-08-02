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

namespace Fisharebest\Webtrees\Factories;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use DateTimeZone;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\TimestampFactoryInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use InvalidArgumentException;
use Psr\Clock\ClockInterface;

use function date_create_from_format;
use function GregorianToJD;
use function str_replace;

/**
 * Create localized CarbonImmutable instances with the user's timezone and language.
 */
class TimestampFactory implements TimestampFactoryInterface
{
    public function __construct(private readonly ClockInterface $clock)
    {
    }

    public function fromDateTime(DateTimeImmutable $datetime, UserInterface|null $user = null): CarbonImmutable
    {
        $user     ??= Auth::user();
        $timezone = $user->getPreference(UserInterface::PREF_TIME_ZONE, Site::getPreference('TIMEZONE'));

        // Convert to the user's timezone for display.
        $localized = $datetime->setTimezone(new DateTimeZone($timezone));

        // Immutability is only for the date/time.  Locale is mutable!
        return CarbonImmutable::instance($localized)
            ->locale(str_replace('-', '_', I18N::languageTag()));
    }

    public function fromEpoch(int $timestamp, UserInterface|null $user = null): CarbonImmutable
    {
        return $this->fromDateTime(new DateTimeImmutable('@' . $timestamp), $user);
    }

    /**
     * @param string|null $string YYYY-MM-DD HH:MM:SS (as provided by SQL).
     */
    public function fromString(string|null $string, string $format = 'Y-m-d H:i:s', UserInterface|null $user = null): CarbonImmutable
    {
        $string    ??= $this->clock->now()->format($format);
        $timestamp = date_create_from_format($format, $string);

        if ($timestamp === false) {
            throw new InvalidArgumentException('date/time "' . $string . '" does not match pattern "' . $format . '"');
        }

        return $this->fromDateTime(DateTimeImmutable::createFromMutable($timestamp), $user);
    }

    public function now(UserInterface|null $user = null): CarbonImmutable
    {
        return $this->fromDateTime($this->clock->now(), $user);
    }

    public function todayJulianDay(UserInterface|null $user = null): int
    {
        $user     ??= Auth::user();
        $timezone = $user->getPreference(UserInterface::PREF_TIME_ZONE, Site::getPreference('TIMEZONE'));
        $today    = $this->clock->now()->setTimezone(new DateTimeZone($timezone));

        return GregorianToJD((int) $today->format('n'), (int) $today->format('j'), (int) $today->format('Y'));
    }
}
