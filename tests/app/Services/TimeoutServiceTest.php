<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
    if (TimeoutServiceTest::$mock_functions === null) {
        return \ini_get(...$args);
    }

    return TimeoutServiceTest::$mock_functions->ini_get(...$args);
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
    if (TimeoutServiceTest::$mock_functions === null) {
        return \microtime(...$args);
    }

    return TimeoutServiceTest::$mock_functions->microtime(...$args);
}

/**
 * Class mockGlobals
 */
class mockGlobals
{
    public function microtime()
    {
    }
    public function ini_get()
    {
    }
}

/**
 * Test harness for the class TimeoutService
 */
class TimeoutServiceTest extends TestCase
{
    /** @var object */
    public static $mock_functions;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        self::$mock_functions = $this->createMock(mockGlobals::class);
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
        $now = \microtime(true);

        $timeout_service = new TimeoutService($now);

        self::$mock_functions
            ->method('ini_get')
            ->with('max_execution_time')
            ->willReturn('0');

        $this->assertFalse($timeout_service->isTimeNearlyUp());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::__construct
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::isTimeNearlyUp
     *
     * @return void
     */
    public function testTimeOutReached(): void
    {
        $now = \microtime(true);

        $timeout_service = new TimeoutService($now);

        self::$mock_functions
            ->method('ini_get')
            ->with('max_execution_time')
            ->willReturn('30');

        self::$mock_functions
            ->method('microtime')
            ->with(true)
            ->willReturn($now + 60.0);

        $this->assertTrue($timeout_service->isTimeNearlyUp());
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
            ->method('ini_get')
            ->with('max_execution_time')
            ->willReturn('30');

        self::$mock_functions
            ->method('microtime')
            ->with(true)
            ->willReturn($now + 10.0);

        $this->assertFalse($timeout_service->isTimeNearlyUp());
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

        $this->assertFalse($timeout_service->isTimeLimitUp());
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

        $this->assertTrue($timeout_service->isTimeLimitUp());
    }
}
