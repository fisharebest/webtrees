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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Contracts\TimeFactoryInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TimeoutService::class)]
class TimeoutServiceTest extends TestCase
{
    public function testNoTimeOut(): void
    {
        $php_service = self::createStub(PhpService::class);
        $php_service->method('maxExecutionTime')->willReturn(0);

        $now = 1500000000.0;

        $timeout_service = new TimeoutService($php_service, $now);

        self::assertFalse($timeout_service->isTimeNearlyUp());
    }

    public function testTimeOutReached(): void
    {
        $php_service = self::createStub(PhpService::class);
        $php_service->method('maxExecutionTime')->willReturn(30);

        $now = 1500000000.0;

        $timeout_service = new TimeoutService($php_service, $now);

        $time_factory = self::createStub(TimeFactoryInterface::class);
        $time_factory->method('now')->willReturn($now + 60.0);
        Registry::timeFactory($time_factory);

        self::assertTrue($timeout_service->isTimeNearlyUp());
    }

    public function testTimeOutNotReached(): void
    {
        $php_service = self::createStub(PhpService::class);
        $php_service->method('maxExecutionTime')->willReturn(30);

        $now = Registry::timeFactory()->now();

        $timeout_service = new TimeoutService($php_service, $now);

        $time_factory = self::createStub(TimeFactoryInterface::class);
        $time_factory->method('now')->willReturn($now + 10.0);
        Registry::timeFactory($time_factory);

        self::assertFalse($timeout_service->isTimeNearlyUp());
    }

    public function testTimeLimitNotReached(): void
    {
        $now = Registry::timeFactory()->now();

        $timeout_service = new TimeoutService(new PhpService(), $now);

        $time_factory = self::createStub(TimeFactoryInterface::class);
        $time_factory->method('now')->willReturn($now + 1.4);
        Registry::timeFactory($time_factory);

        self::assertFalse($timeout_service->isTimeLimitUp());
    }

    public function testTimeLimitReached(): void
    {
        $now = Registry::timeFactory()->now();

        $timeout_service = new TimeoutService(new PhpService(), $now);

        $time_factory = self::createStub(TimeFactoryInterface::class);
        $time_factory->method('now')->willReturn($now + 1.6);
        Registry::timeFactory($time_factory);

        self::assertTrue($timeout_service->isTimeLimitUp());
    }
}
