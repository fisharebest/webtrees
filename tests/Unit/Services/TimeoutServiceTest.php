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
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Services\TimeoutService;

#[CoversClass(TimeoutService::class)]
class TimeoutServiceTest extends TestCase
{
    public function testNoTimeOut(): void
    {
        $php_service = self::createStub(PhpService::class);
        $php_service->method('maxExecutionTime')->willReturn(0);

        $clock = new FrozenClock(new DateTimeImmutable('2017-07-14 02:40:00', new DateTimeZone('UTC')));

        $timeout_service = new TimeoutService($php_service, $clock);

        self::assertFalse($timeout_service->isTimeNearlyUp());
    }

    public function testTimeOutReached(): void
    {
        $php_service = self::createStub(PhpService::class);
        $php_service->method('maxExecutionTime')->willReturn(30);

        $start = new DateTimeImmutable('2017-07-14 02:40:00', new DateTimeZone('UTC'));
        $clock = new FrozenClock($start);

        $timeout_service = new TimeoutService($php_service, $clock);

        // Advance the clock by 60 seconds, well past the 30-second max execution time.
        $clock->setTo($start->modify('+60 seconds'));

        self::assertTrue($timeout_service->isTimeNearlyUp());
    }

    public function testTimeOutNotReached(): void
    {
        $start = new DateTimeImmutable('2017-07-14 02:40:00', new DateTimeZone('UTC'));
        $clock = new FrozenClock($start);

        $php_service = self::createStub(PhpService::class);
        $php_service->method('maxExecutionTime')->willReturn(30);

        $timeout_service = new TimeoutService($php_service, $clock);

        // Advance the clock by 10 seconds, within the 30-second max execution time.
        $clock->setTo($start->modify('+10 seconds'));

        self::assertFalse($timeout_service->isTimeNearlyUp());
    }

    public function testTimeLimitNotReached(): void
    {
        $start = new DateTimeImmutable('2017-07-14 02:40:00', new DateTimeZone('UTC'));
        $clock = new FrozenClock($start);

        $timeout_service = new TimeoutService(new PhpService(), $clock);

        // Advance by 1.4 seconds, under the 1.5 second limit.
        $clock->setTo(new DateTimeImmutable('@' . ((float) $start->format('U') + 1.4)));

        self::assertFalse($timeout_service->isTimeLimitUp());
    }

    public function testTimeLimitReached(): void
    {
        $start = new DateTimeImmutable('2017-07-14 02:40:00', new DateTimeZone('UTC'));
        $clock = new FrozenClock($start);

        $timeout_service = new TimeoutService(new PhpService(), $clock);

        // Advance by 1.6 seconds, over the 1.5 second limit.
        $clock->setTo(new DateTimeImmutable('@' . ((float) $start->format('U') + 1.6)));

        self::assertTrue($timeout_service->isTimeLimitUp());
    }
}
