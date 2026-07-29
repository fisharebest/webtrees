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

namespace Fisharebest\Webtrees\Tests\Unit\Services;

use DateTimeImmutable;
use DateTimeZone;
use Fisharebest\Webtrees\Clock\FrozenClock;
use Fisharebest\Webtrees\GuestUser;
use Fisharebest\Webtrees\Http\Exceptions\HttpTooManyRequestsException;
use Fisharebest\Webtrees\Services\RateLimitService;
use Fisharebest\Webtrees\Tests\TestCase;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;

use function explode;
use function implode;
use function range;

#[CoversClass(RateLimitService::class)]
class RateLimitServiceTest extends TestCase
{
    public function testTooMuchHistory(): void
    {
        $clock = new FrozenClock(new DateTimeImmutable('now', new DateTimeZone('UTC')));
        $rate_limit_service = new RateLimitService($clock);

        $user = new GuestUser();

        $this->expectException(LogicException::class);

        $rate_limit_service->limitRateForUser($user, 1000, 30, 'rate-limit');
    }

    public function testLimitNotReached(): void
    {
        $clock = new FrozenClock(new DateTimeImmutable('now', new DateTimeZone('UTC')));
        $rate_limit_service = new RateLimitService($clock);

        $user = new GuestUser();

        $rate_limit_service->limitRateForUser($user, 3, 30, 'rate-limit');
        $history = $user->getPreference('rate-limit');
        self::assertCount(1, explode(',', $history));

        $rate_limit_service->limitRateForUser($user, 3, 30, 'rate-limit');
        $history = $user->getPreference('rate-limit');
        self::assertCount(2, explode(',', $history));

        $rate_limit_service->limitRateForUser($user, 3, 30, 'rate-limit');
        $history = $user->getPreference('rate-limit');
        self::assertCount(3, explode(',', $history));
    }

    public function testOldEventsIgnored(): void
    {
        $now   = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $clock = new FrozenClock($now);
        $rate_limit_service = new RateLimitService($clock);

        $user = new GuestUser();

        $timestamp = $now->getTimestamp();
        $history   = implode(',', range($timestamp - 35, $timestamp - 31));
        $user->setPreference('rate-limit', $history);

        $rate_limit_service->limitRateForUser($user, 5, 30, 'rate-limit');
        $history = $user->getPreference('rate-limit');
        self::assertCount(6, explode(',', $history));
    }

    public function testLimitReached(): void
    {
        $now   = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $clock = new FrozenClock($now);
        $rate_limit_service = new RateLimitService($clock);

        $user = new GuestUser();

        $timestamp = $now->getTimestamp();
        $history   = implode(',', range($timestamp - 5, $timestamp - 1));
        $user->setPreference('rate-limit', $history);

        $this->expectException(HttpTooManyRequestsException::class);
        $rate_limit_service->limitRateForUser($user, 5, 30, 'rate-limit');
    }
}
