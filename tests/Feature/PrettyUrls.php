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

namespace Fisharebest\Webtrees\Tests\Feature;

use Fisharebest\Webtrees\Http\Middleware\Router;
use Fisharebest\Webtrees\Http\Routing\RouteCollection;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

class PrettyUrls extends TestCase
{
    public function testUglyUrlsAreRedirectedToPrettyUrls(): void
    {
        $request = self::createRequest(
            method: 'GET',
            query: ['route' => 'foo-bar'],
            attributes: ['base_url' => 'http://localhost', 'rewrite_urls' => '1'],
        );

        $handler = self::createMock(RequestHandlerInterface::class);

        $router = new Router(
            self::createMock(ModuleService::class),
            new RouteCollection(),
            self::createMock(TreeService::class)
        );

        $response = $router->process($request, $handler);

        $location = $response->getHeaderLine('Location');

        self::assertSame('http://localhost/foo-bar', $location);
    }
}
