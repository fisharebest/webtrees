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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Exceptions\ImageException;
use Fisharebest\Webtrees\Http\Exceptions\HttpServerErrorException;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Server\RequestHandlerInterface;
use Fisharebest\Webtrees\Http\Middleware\HandleExceptions;

#[CoversClass(HandleExceptions::class)]
class HandleExceptionsTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testMiddleware(): void
    {
        $tree_service = self::createStub(TreeService::class);

        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willThrowException(new HttpServerErrorException('eek'));

        $module_service = self::createStub(ModuleService::class);
        $module_service->method('findByInterface')->willReturn(new Collection());
        $module_service->method('findByComponent')->willReturn(new Collection());
        Registry::container()->set(ModuleService::class, $module_service);

        $request    = self::createRequest();
        $middleware = new HandleExceptions(new PhpService(), $tree_service);
        $response   = $middleware->process($request, $handler);

        self::assertSame(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testMiddlewareRendersSvgForImageException(): void
    {
        $tree_service = self::createStub(TreeService::class);

        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willThrowException(new ImageException(500, 'broken.jpg', 'File is corrupt'));

        $request    = self::createRequest();
        $middleware = new HandleExceptions(new PhpService(), $tree_service);
        $response   = $middleware->process($request, $handler);
        $body       = $response->getBody()->getContents();

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('image/svg+xml', $response->getHeaderLine('content-type'));
        self::assertStringContainsString('500', $body);
        self::assertStringContainsString('broken.jpg', $body);
        self::assertStringContainsString('File is corrupt', $body);
    }
}
