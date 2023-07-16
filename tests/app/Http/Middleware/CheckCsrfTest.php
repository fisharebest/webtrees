<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\TestCase;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;
use function response;

/**
 * Test the CheckCsrf middleware.
 *
 * @covers \Fisharebest\Webtrees\Http\Middleware\CheckCsrf
 */
class CheckCsrfTest extends TestCase
{
    /**
     * @return void
     */
    public function testMiddleware(): void
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response());

        $request = self::createRequest(RequestMethodInterface::METHOD_POST)
            ->withUri(app(UriFactoryInterface::class)->createUri('https://example.com'));

        $middleware = new CheckCsrf();
        $response   = $middleware->process($request, $handler);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertSame('https://example.com', $response->getHeaderLine('Location'));
    }
}
