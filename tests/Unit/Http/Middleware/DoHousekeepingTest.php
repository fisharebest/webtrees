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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Middleware;

use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Http\Middleware\DoHousekeeping;
use Fisharebest\Webtrees\Services\HousekeepingService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

#[CoversClass(DoHousekeeping::class)]
class DoHousekeepingTest extends TestCase
{
    public function testMiddleware(): void
    {
        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response());

        $housekeeping_service = self::createStub(HousekeepingService::class);

        $request    = self::createRequest();
        $middleware = new DoHousekeeping($housekeeping_service);
        $response   = $middleware->process($request, $handler);

        self::assertSame(HttpStatusCode::NoContent->value, $response->getStatusCode());
    }
}
