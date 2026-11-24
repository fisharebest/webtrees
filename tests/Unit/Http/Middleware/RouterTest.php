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

use Fisharebest\Collection\Collection;
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Http\Middleware\Router;
use Fisharebest\Webtrees\Http\Routing\RouteCollection;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(Router::class)]
class RouterTest extends TestCase
{
    public function testRouteWithoutTree(): void
    {
        $request = self::createRequest();
        $request = $request->withUri($request->getUri()->withPath('/'));

        $controller = self::createStub(RequestHandlerInterface::class);
        $controller->method('handle')->willReturn(Registry::responseFactory()->response('ok'));
        Registry::container()->set(RequestHandlerInterface::class, $controller);

        $routes = new RouteCollection();
        $routes->add('/', RequestHandlerInterface::class);

        $module_service = self::createStub(ModuleService::class);
        $module_service->method('findByInterface')->willReturn(new Collection());
        $tree_service = self::createMock(TreeService::class);
        $tree_service->expects(self::never())->method('all');

        $router = new Router($module_service, $routes, $tree_service);

        self::assertSame(HttpStatusCode::OK->value, $router->process($request, $controller)->getStatusCode());
        self::assertNull(Registry::container()->get(ServerRequestInterface::class)->getAttribute('tree'));
    }

    public function testRouteWithTree(): void
    {
        $request = self::createRequest();
        $request = $request->withUri($request->getUri()->withPath('/tree/example'));

        $controller = self::createStub(RequestHandlerInterface::class);
        $controller->method('handle')->willReturn(Registry::responseFactory()->response('ok'));
        Registry::container()->set(RequestHandlerInterface::class, $controller);

        $routes = new RouteCollection();
        $routes->add('/tree/{tree}', RequestHandlerInterface::class);

        $tree = self::createStub(Tree::class);
        $module_service = self::createStub(ModuleService::class);
        $module_service->method('findByInterface')->willReturn(new Collection());
        $tree_service = self::createStub(TreeService::class);
        $tree_service->method('all')->willReturn(new Collection(['example' => $tree]));

        $router = new Router($module_service, $routes, $tree_service);

        self::assertSame(HttpStatusCode::OK->value, $router->process($request, $controller)->getStatusCode());
        self::assertSame($tree, Registry::container()->get(ServerRequestInterface::class)->getAttribute('tree'));
        self::assertSame($tree, Registry::container()->get(Tree::class));
    }
}
