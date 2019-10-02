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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

/**
 * Test the ModuleMiddleware middleware.
 *
 * @covers \Fisharebest\Webtrees\Http\Middleware\ModuleMiddleware
 */
class ModuleMiddlewareTest extends TestCase
{
    /**
     * @return void
     */
    public function testMiddleware(): void
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response());

        $dummy = $this->createMock(MiddlewareInterface::class);
        $dummy->method('process')->willReturn(response());

        $middlewares = new Collection([$dummy]);

        $module_service = $this->createMock(ModuleService::class);
        $module_service->method('findByInterface')->willReturn($middlewares);

        $request    = self::createRequest();
        $middleware = new ModuleMiddleware($module_service);
        $response   = $middleware->process($request, $handler);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }
}
