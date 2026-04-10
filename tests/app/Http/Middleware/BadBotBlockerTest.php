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
use Fisharebest\Webtrees\Services\NetworkService;
use Fisharebest\Webtrees\TestCase;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

#[CoversClass(BadBotBlocker::class)]
class BadBotBlockerTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(BadBotBlocker::class));
    }

    public function testEmptyUserAgentIsBlocked(): void
    {
        $request = new ServerRequest('GET', 'https://webtrees.test/index.php', [], null, '1.1', [
            'HTTP_USER_AGENT' => '',
        ]);
        $request = $request->withAttribute('client-ip', '127.0.0.1');
        $request = $request->withAttribute('base_url', 'https://webtrees.test');

        $inner_handler = $this->createMock(RequestHandlerInterface::class);
        $inner_handler->expects(self::never())->method('handle');

        $middleware = new BadBotBlocker(new NetworkService());
        $response   = $middleware->process($request, $inner_handler);

        self::assertSame(StatusCodeInterface::STATUS_NOT_ACCEPTABLE, $response->getStatusCode());
        self::assertStringContainsString('Not acceptable', (string) $response->getBody());
    }

    public function testBadBotIsBlocked(): void
    {
        $request = new ServerRequest('GET', 'https://webtrees.test/index.php', [], null, '1.1', [
            'HTTP_USER_AGENT' => 'AhrefsBot/7.0',
        ]);
        $request = $request->withAttribute('client-ip', '127.0.0.1');
        $request = $request->withAttribute('base_url', 'https://webtrees.test');

        $inner_handler = $this->createMock(RequestHandlerInterface::class);
        $inner_handler->expects(self::never())->method('handle');

        $middleware = new BadBotBlocker(new NetworkService());
        $response   = $middleware->process($request, $inner_handler);

        self::assertSame(StatusCodeInterface::STATUS_NOT_ACCEPTABLE, $response->getStatusCode());
        self::assertStringContainsString('Not acceptable', (string) $response->getBody());
    }

    public function testNormalUserAgentPassesThrough(): void
    {
        // Use a UA that is not in BAD_ROBOTS, ROBOT_REV_FWD_DNS, ROBOT_REV_ONLY_DNS, or ROBOT_ASNS.
        // Avoid claiming to be a browser (Chrome/Firefox/Opera/Safari) to skip the cookie check.
        $request = new ServerRequest('GET', 'https://webtrees.test/index.php', [], null, '1.1', [
            'HTTP_USER_AGENT' => 'CustomAgent/1.0',
        ]);
        $request = $request->withAttribute('client-ip', '127.0.0.1');
        $request = $request->withAttribute('base_url', 'https://webtrees.test');

        $inner_handler = $this->createMock(RequestHandlerInterface::class);
        $inner_handler->expects(self::once())
            ->method('handle')
            ->willReturn(response('OK', StatusCodeInterface::STATUS_OK));

        $middleware = new BadBotBlocker(new NetworkService());
        $response   = $middleware->process($request, $inner_handler);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
