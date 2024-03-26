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

use Fisharebest\Webtrees\Contracts\TimeFactoryInterface;
use Fisharebest\Webtrees\MockGlobalFunctions;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Mock function.
 *
 * @param mixed ...$args
 *
 * @return mixed
 */
function ini_get(...$args)
{
    if (TestCase::$mock_functions === null) {
        return \ini_get(...$args);
    }

    return TestCase::$mock_functions->iniGet(...$args);
}


#[CoversClass(TimeoutService::class)]
class TimeoutServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::$mock_functions = $this->createMock(MockGlobalFunctions::class);
    }

    protected function tearDown(): void
    {
        parent::setUp();

        self::$mock_functions = null;
    }

    public function testNoTimeOut(): void
    {
        $now = 1500000000.0;

        $timeout_service = new TimeoutService($now);

        self::$mock_functions
            ->method('iniGet')
            ->with('max_execution_time')
            ->willReturn('0');

        self::assertFalse($timeout_service->isTimeNearlyUp());
    }

    public function testTimeOutReached(): void
    {
        $now = 1500000000.0;

        $timeout_service = new TimeoutService($now);

        self::$mock_functions
            ->method('iniGet')
            ->with('max_execution_time')
            ->willReturn('30');

        $time_factory = $this->createMock(TimeFactoryInterface::class);
        $time_factory->method('now')->willReturn($now + 60.0);
        Registry::timeFactory($time_factory);

        self::assertTrue($timeout_service->isTimeNearlyUp());
    }

    public function testTimeOutNotReached(): void
    {
        $now = Registry::timeFactory()->now();

        $timeout_service = new TimeoutService($now);

        self::$mock_functions
            ->method('iniGet')
            ->with('max_execution_time')
            ->willReturn('30');

        $time_factory = $this->createMock(TimeFactoryInterface::class);
        $time_factory->method('now')->willReturn($now + 10.0);
        Registry::timeFactory($time_factory);

        self::assertFalse($timeout_service->isTimeNearlyUp());
    }

    public function testTimeLimitNotReached(): void
    {
        $now = Registry::timeFactory()->now();

        $timeout_service = new TimeoutService($now);

        $time_factory = $this->createMock(TimeFactoryInterface::class);
        $time_factory->method('now')->willReturn($now + 1.4);
        Registry::timeFactory($time_factory);

        self::assertFalse($timeout_service->isTimeLimitUp());
    }

    public function testTimeLimitReached(): void
    {
        $now = Registry::timeFactory()->now();

        $timeout_service = new TimeoutService($now);

        $time_factory = $this->createMock(TimeFactoryInterface::class);
        $time_factory->method('now')->willReturn($now + 1.6);
        Registry::timeFactory($time_factory);

        self::assertTrue($timeout_service->isTimeLimitUp());
    }
}
