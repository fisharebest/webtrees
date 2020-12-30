<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\MockGlobalFunctions;
use Fisharebest\Webtrees\TestCase;

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

/**
 * Mock function.
 *
 * @param mixed ...$args
 *
 * @return mixed
 */
function microtime(...$args)
{
    if (TestCase::$mock_functions === null) {
        return \microtime(...$args);
    }

    return TestCase::$mock_functions->microtime(...$args);
}

/**
 * Test harness for the class TimeoutService
 */
class TimeoutServiceTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        self::$mock_functions = $this->getMockForAbstractClass(MockGlobalFunctions::class);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::setUp();

        self::$mock_functions = null;
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::__construct
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::isTimeNearlyUp
     *
     * @return void
     */
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

    /**
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::__construct
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::isTimeNearlyUp
     *
     * @return void
     */
    public function testTimeOutReached(): void
    {
        $now = 1500000000.0;

        $timeout_service = new TimeoutService($now);

        self::$mock_functions
            ->method('iniGet')
            ->with('max_execution_time')
            ->willReturn('30');

        self::$mock_functions
            ->method('microtime')
            ->with(true)
            ->willReturn($now + 60.0);

        self::assertTrue($timeout_service->isTimeNearlyUp());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::__construct
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::isTimeNearlyUp
     *
     * @return void
     */
    public function testTimeOutNotReached(): void
    {
        $now = \microtime(true);

        $timeout_service = new TimeoutService($now);

        self::$mock_functions
            ->method('iniGet')
            ->with('max_execution_time')
            ->willReturn('30');

        self::$mock_functions
            ->method('microtime')
            ->with(true)
            ->willReturn($now + 10.0);

        self::assertFalse($timeout_service->isTimeNearlyUp());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::__construct
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::isTimeLimitUp
     *
     * @return void
     */
    public function testTimeLimitNotReached(): void
    {
        $now = \microtime(true);

        $timeout_service = new TimeoutService($now);

        self::$mock_functions
            ->method('microtime')
            ->with(true)
            ->willReturn($now + 1.4);

        self::assertFalse($timeout_service->isTimeLimitUp());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::__construct
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::isTimeLimitUp
     *
     * @return void
     */
    public function testTimeLimitReached(): void
    {
        $now = \microtime(true);

        $timeout_service = new TimeoutService($now);

        self::$mock_functions
            ->method('microtime')
            ->with(true)
            ->willReturn($now + 1.6);

        self::assertTrue($timeout_service->isTimeLimitUp());
    }
}
