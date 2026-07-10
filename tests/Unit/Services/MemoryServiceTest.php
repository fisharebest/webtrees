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

use Fisharebest\Webtrees\Services\MemoryService;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MemoryService::class)]
class MemoryServiceTest extends TestCase
{
    public function testNoMemoryLimit(): void
    {
        $php_service = self::createStub(PhpService::class);
        $php_service->method('memoryLimit')->willReturn(-1);

        $memory_service = new MemoryService($php_service);

        self::assertFalse($memory_service->isMemoryNearlyUp());
    }

    public function testMemoryLimitReached(): void
    {
        $php_service = self::createStub(PhpService::class);
        $php_service->method('memoryLimit')->willReturn(128 * 1024 * 1024);
        $php_service->method('memoryGetUsage')->willReturn(121 * 1024 * 1024);

        $memory_service = new MemoryService($php_service);

        self::assertTrue($memory_service->isMemoryNearlyUp());
    }

    public function testMemoryLimitNotReached(): void
    {
        $php_service = self::createStub(PhpService::class);
        $php_service->method('memoryLimit')->willReturn(128 * 1024 * 1024);
        $php_service->method('memoryGetUsage')->willReturn(100 * 1024 * 1024);

        $memory_service = new MemoryService($php_service);

        self::assertFalse($memory_service->isMemoryNearlyUp());
    }

    public function testCustomThreshold(): void
    {
        $php_service = self::createStub(PhpService::class);
        $php_service->method('memoryLimit')->willReturn(128 * 1024 * 1024);
        $php_service->method('memoryGetUsage')->willReturn(126 * 1024 * 1024);

        $memory_service = new MemoryService($php_service);

        self::assertFalse($memory_service->isMemoryNearlyUp(1 * 1024 * 1024));
        self::assertTrue($memory_service->isMemoryNearlyUp(3 * 1024 * 1024));
    }
}

