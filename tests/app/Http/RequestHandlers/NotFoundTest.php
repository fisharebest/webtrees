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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Http\Middleware\BadBotBlocker;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(NotFound::class)]
class NotFoundTest extends TestCase
{

    public function testClass(): void
    {
        self::assertTrue(class_exists(NotFound::class));
    }

    /**
     * A robot request returns a plain 404 response.
     */
    public function testHandleRobotReturnsNotFound(): void
    {
        $handler  = new NotFound();
        $request  = self::createRequest()
            ->withAttribute(BadBotBlocker::ROBOT_ATTRIBUTE_NAME, true);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * A GET request from a non-robot redirects to the home page.
     */
    public function testHandleGetRequestRedirectsToHomePage(): void
    {
        $handler  = new NotFound();
        $request  = self::createRequest();
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * A POST request from a non-robot throws HttpNotFoundException.
     */
    public function testHandlePostRequestThrowsNotFoundException(): void
    {
        $this->expectException(HttpNotFoundException::class);

        $handler = new NotFound();
        $request = self::createRequest(RequestMethodInterface::METHOD_POST);
        $handler->handle($request);
    }
}
