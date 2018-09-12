<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
namespace Fisharebest\Webtrees\Services;

use Mockery;

/**
 * Mock function.
 *
 * @param mixed ...$args
 *
 * @return mixed
 */
function ini_get(...$args) {
    return TimeoutServiceTest::$mock_functions->ini_get(...$args);
}

/**
 * Mock function.
 *
 * @param mixed ...$args
 *
 * @return mixed
 */
function microtime(...$args) {
    return TimeoutServiceTest::$mock_functions->microtime(...$args);
}

/**
 * Test harness for the class TimeoutServiceTest
 */
class TimeoutServiceTest extends \Fisharebest\Webtrees\TestCase
{
    /** @var object */
    public static $mock_functions;

    /**
     * Initialize the test script
     */
    public function setUp() {
        self::$mock_functions = Mockery::mock();
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::isTimeNearlyUp()
     */
    public function testNoTimeOut()
    {
        $now = \microtime(true);

        $timeout_service = new TimeoutService($now);

        self::$mock_functions
            ->shouldReceive('ini_get')
            ->with('max_execution_time')
            ->once()
            ->andReturn('0');

        $this->assertFalse($timeout_service->isTimeNearlyUp());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::isTimeNearlyUp()
     */
    public function testTimeOutReached()
    {
        $now = \microtime(true);

        $timeout_service = new TimeoutService($now);

        self::$mock_functions
            ->shouldReceive('ini_get')
            ->with('max_execution_time')
            ->once()
            ->andReturn('30');

        self::$mock_functions
            ->shouldReceive('microtime')
            ->with('true')
            ->once()
            ->andReturn($now + 60.0);

        $this->assertTrue($timeout_service->isTimeNearlyUp());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::isTimeNearlyUp()
     */
    public function testTimeOutNotReached()
    {
        $now = \microtime(true);

        $timeout_service = new TimeoutService($now);

        self::$mock_functions
            ->shouldReceive('ini_get')
            ->with('max_execution_time')
            ->once()
            ->andReturn('30');

        self::$mock_functions
            ->shouldReceive('microtime')
            ->with('true')
            ->once()
            ->andReturn($now + 10.0);

        $this->assertFalse($timeout_service->isTimeNearlyUp());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::isTimeLimitUp()
     */
    public function testTimeLimitNotReached()
    {
        $now = \microtime(true);

        $timeout_service = new TimeoutService($now);

        self::$mock_functions
            ->shouldReceive('microtime')
            ->with('true')
            ->once()
            ->andReturn($now + 1.4);

        $this->assertFalse($timeout_service->isTimeLimitUp());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\TimeoutService::isTimeLimitUp()
     */
    public function testTimeLimitReached()
    {
        $now = \microtime(true);

        $timeout_service = new TimeoutService($now);

        self::$mock_functions
            ->shouldReceive('microtime')
            ->with('true')
            ->once()
            ->andReturn($now + 1.6);

        $this->assertTrue($timeout_service->isTimeLimitUp());
    }
}
