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

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

#[CoversClass(CheckCsrf::class)]
class CheckCsrfTest extends TestCase
{
    public function testMiddleware(): void
    {
        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response());

        $uri_factory = Registry::container()->get(UriFactoryInterface::class);
        self::assertInstanceOf(UriFactoryInterface::class, $uri_factory);

        $request = self::createRequest(RequestMethodInterface::METHOD_POST)
            ->withUri($uri_factory->createUri('https://example.com'));

        $middleware = new CheckCsrf();
        $response   = $middleware->process($request, $handler);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertSame('https://example.com', $response->getHeaderLine('Location'));
    }
}
