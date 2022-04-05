<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
use Fisharebest\Webtrees\Http\Exceptions\HttpServerErrorException;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use Psr\Http\Server\RequestHandlerInterface;

use function app;

/**
 * Test the HandleExceptions middleware.
 *
 * @covers \Fisharebest\Webtrees\Http\Middleware\HandleExceptions
 */
class HandleExceptionsTest extends TestCase
{
    /**
     * @return void
     */
    public function testMiddleware(): void
    {
        $tree_service = $this->createMock(TreeService::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willThrowException(new HttpServerErrorException('eek'));

        $module_service = $this->createMock(ModuleService::class);
        $module_service->method('findByInterface')->willReturn(new Collection());
        $module_service->method('findByComponent')->willReturn(new Collection());
        app()->instance(ModuleService::class, $module_service);

        $request    = self::createRequest();
        $middleware = new HandleExceptions($tree_service);
        $response   = $middleware->process($request, $handler);

        self::assertSame(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        app()->forgetInstance(ModuleService::class);
        app()->forgetInstance(UserService::class);
    }
}
