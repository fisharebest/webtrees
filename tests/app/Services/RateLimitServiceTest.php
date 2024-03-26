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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\GuestUser;
use Fisharebest\Webtrees\Http\Exceptions\HttpTooManyRequestsException;
use Fisharebest\Webtrees\TestCase;
use LogicException;

use PHPUnit\Framework\Attributes\CoversClass;

use function explode;
use function implode;
use function range;
use function time;


#[CoversClass(RateLimitService::class)]
class RateLimitServiceTest extends TestCase
{
    public function testTooMuchHistory(): void
    {
        $rate_limit_service = new RateLimitService();

        $user = new GuestUser();

        $this->expectException(LogicException::class);

        $rate_limit_service->limitRateForUser($user, 1000, 30, 'rate-limit');
    }

    public function testLimitNotReached(): void
    {
        $rate_limit_service = new RateLimitService();

        $user = new GuestUser();

        $rate_limit_service->limitRateForUser($user, 3, 30, 'rate-limit');
        $history = $user->getPreference('rate-limit');
        static::assertCount(1, explode(',', $history));

        $rate_limit_service->limitRateForUser($user, 3, 30, 'rate-limit');
        $history = $user->getPreference('rate-limit');
        static::assertCount(2, explode(',', $history));

        $rate_limit_service->limitRateForUser($user, 3, 30, 'rate-limit');
        $history = $user->getPreference('rate-limit');
        static::assertCount(3, explode(',', $history));
    }

    public function testOldEventsIgnored(): void
    {
        $rate_limit_service = new RateLimitService();

        $user = new GuestUser();

        $history = implode(',', range(time() - 35, time() - 31));
        $user->setPreference('rate-limit', $history);

        $rate_limit_service->limitRateForUser($user, 5, 30, 'rate-limit');
        $history = $user->getPreference('rate-limit');
        static::assertCount(6, explode(',', $history));
    }

    public function testLimitReached(): void
    {
        $rate_limit_service = new RateLimitService();

        $user = new GuestUser();

        $history = implode(',', range(time() - 5, time() - 1));
        $user->setPreference('rate-limit', $history);

        $this->expectException(HttpTooManyRequestsException::class);
        $rate_limit_service->limitRateForUser($user, 5, 30, 'rate-limit');
    }
}
