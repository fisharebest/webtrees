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

use Fisharebest\Webtrees\Http\Routing\RouteCollection;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Http\Middleware\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(Router::class)]
class RouterTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(Router::class));
    }

    public function testUglyUrlWithoutRouteParameterIsTheHomePage(): void
    {
        $request = self::createRequest(attributes: ['base_url' => 'https://webtrees.test']);

        $response = $this->routeToHomePage($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('home', (string) $response->getBody());
    }

    public function testPrettyUrlOfTheBasePathIsTheHomePage(): void
    {
        $request = self::createRequest(attributes: ['base_url' => 'https://webtrees.test/webtrees', 'rewrite_urls' => '1']);
        $request = $request->withUri($request->getUri()->withPath('/webtrees')->withQuery(''));

        $response = $this->routeToHomePage($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('home', (string) $response->getBody());
    }

    private function routeToHomePage(ServerRequestInterface $request): ResponseInterface
    {
        $home_page = self::createStub(RequestHandlerInterface::class);
        $home_page->method('handle')->willReturn(Registry::responseFactory()->response('home'));
        Registry::container()->set('home-page', $home_page);

        $routes = new RouteCollection();
        $routes->add('/', 'home-page');

        $module_service = self::createStub(ModuleService::class);
        $module_service->method('findByInterface')->willReturn(new Collection());

        $tree_service = self::createStub(TreeService::class);
        $tree_service->method('all')->willReturn(new Collection());

        $router = new Router($module_service, $routes, $tree_service);

        return $router->process($request, self::createStub(RequestHandlerInterface::class));
    }
}
