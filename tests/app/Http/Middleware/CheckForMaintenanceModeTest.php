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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\MaintenanceModeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

#[CoversClass(CheckForMaintenanceMode::class)]
class CheckForMaintenanceModeTest extends TestCase
{
    public function testSiteIsOffline(): void
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response());

        $maintenance_mode_service = $this->createMock(MaintenanceModeService::class);
        $maintenance_mode_service->method('isOffline')->willReturn(true);
        $maintenance_mode_service->method('message')->willReturn('XYZZY');

        $request    = self::createRequest();
        $middleware = new CheckForMaintenanceMode($maintenance_mode_service);
        $response   = $middleware->process($request, $handler);

        self::assertSame(StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE, $response->getStatusCode());
        self::assertStringContainsString('XYZZY', $response->getBody()->getContents());
    }

    public function testSiteIsOnline(): void
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response());

        $maintenance_mode_service = $this->createMock(MaintenanceModeService::class);
        $maintenance_mode_service->method('isOffline')->willReturn(false);

        $request    = self::createRequest();
        $middleware = new CheckForMaintenanceMode($maintenance_mode_service);
        $response   = $middleware->process($request, $handler);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }
}
