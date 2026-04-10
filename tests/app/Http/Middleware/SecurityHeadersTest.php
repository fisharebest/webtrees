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

#[CoversClass(SecurityHeaders::class)]
class SecurityHeadersTest extends TestCase
{
    public function testSecurityHeadersAreAdded(): void
    {
        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response('', StatusCodeInterface::STATUS_OK));

        $request    = self::createRequest();
        $middleware = new SecurityHeaders();
        $response   = $middleware->process($request, $handler);

        self::assertSame('browsing-topics=()', $response->getHeaderLine('Permissions-Policy'));
        self::assertSame('same-origin', $response->getHeaderLine('Referrer-Policy'));
        self::assertSame('nosniff', $response->getHeaderLine('X-Content-Type-Options'));
        self::assertSame('SAMEORIGIN', $response->getHeaderLine('X-Frame-Options'));
        self::assertSame('1; mode=block', $response->getHeaderLine('X-XSS-Protection'));
        self::assertSame('max-age=31536000', $response->getHeaderLine('Strict-Transport-Security'));
    }

    public function testExistingHeadersAreNotOverwritten(): void
    {
        $inner_response = response('', StatusCodeInterface::STATUS_OK)
            ->withHeader('X-Frame-Options', 'DENY');

        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($inner_response);

        $request    = self::createRequest();
        $middleware = new SecurityHeaders();
        $response   = $middleware->process($request, $handler);

        self::assertSame('DENY', $response->getHeaderLine('X-Frame-Options'));
    }

    public function testHstsOnlyForHttps(): void
    {
        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response('', StatusCodeInterface::STATUS_OK));

        $request    = self::createRequest('GET', [], [], [], ['base_url' => 'http://webtrees.test']);
        $middleware = new SecurityHeaders();
        $response   = $middleware->process($request, $handler);

        self::assertSame('', $response->getHeaderLine('Strict-Transport-Security'));
        // Other security headers should still be present.
        self::assertSame('nosniff', $response->getHeaderLine('X-Content-Type-Options'));
    }
}
