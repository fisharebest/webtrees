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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

#[CoversClass(PublicFiles::class)]
class PublicFilesTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(PublicFiles::class));
    }

    public function testNonPublicPathDelegatesToHandler(): void
    {
        $request = self::createRequest();
        $request = $request->withUri($request->getUri()->withPath('/some/page'));

        $inner_handler = $this->createMock(RequestHandlerInterface::class);
        $inner_handler->expects(self::once())
            ->method('handle')
            ->willReturn(response('OK', StatusCodeInterface::STATUS_OK));

        $middleware = new PublicFiles();
        $response   = $middleware->process($request, $inner_handler);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testPublicPathWithTraversalDelegatesToHandler(): void
    {
        $request = self::createRequest();
        $request = $request->withUri($request->getUri()->withPath('/public/../etc/passwd'));

        $inner_handler = $this->createMock(RequestHandlerInterface::class);
        $inner_handler->expects(self::once())
            ->method('handle')
            ->willReturn(response('OK', StatusCodeInterface::STATUS_OK));

        $middleware = new PublicFiles();
        $response   = $middleware->process($request, $inner_handler);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testPublicPathFileNotFoundDelegatesToHandler(): void
    {
        $request = self::createRequest();
        $request = $request->withUri($request->getUri()->withPath('/public/nonexistent-file.js'));

        $inner_handler = $this->createMock(RequestHandlerInterface::class);
        $inner_handler->expects(self::once())
            ->method('handle')
            ->willReturn(response('Not Found', StatusCodeInterface::STATUS_NOT_FOUND));

        $middleware = new PublicFiles();
        $response   = $middleware->process($request, $inner_handler);

        self::assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }
}
