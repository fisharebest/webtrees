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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\ServerCheckService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Ping::class)]
class PingTest extends TestCase
{
    public function testPingOK(): void
    {
        $server_check_service = $this->createMock(ServerCheckService::class);
        $server_check_service->expects($this->once())->method('serverErrors')->willReturn(new Collection());
        $server_check_service->expects($this->once())->method('serverWarnings')->willReturn(new Collection());

        $request  = self::createRequest();
        $handler  = new Ping($server_check_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('OK', (string) $response->getBody());
    }

    public function testPingWarnings(): void
    {
        $server_check_service = $this->createMock(ServerCheckService::class);
        $server_check_service->expects($this->once())->method('serverErrors')->willReturn(new Collection());
        $server_check_service->expects($this->once())->method('serverWarnings')->willReturn(new Collection(['warning']));

        $request  = self::createRequest();
        $handler  = new Ping($server_check_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('WARNING', (string) $response->getBody());
    }

    public function testPingErrors(): void
    {
        $server_check_service = $this->createMock(ServerCheckService::class);
        $server_check_service->expects($this->once())->method('serverErrors')->willReturn(new Collection(['error']));

        $request  = self::createRequest();
        $handler  = new Ping($server_check_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE, $response->getStatusCode());
        self::assertSame('ERROR', (string) $response->getBody());
    }
}
