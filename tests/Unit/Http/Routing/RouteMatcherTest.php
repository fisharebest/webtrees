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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Routing;

use Fisharebest\Webtrees\Http\Routing\RouteCollection;
use Fisharebest\Webtrees\Http\Routing\RouteMatcher;
use Fisharebest\Webtrees\Tests\TestCase;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Container\ContainerInterface;

#[CoversClass(RouteMatcher::class)]
class RouteMatcherTest extends TestCase
{
    private function createRouteMatcher(RouteCollection $routes): RouteMatcher
    {
        return new RouteMatcher($routes, self::createStub(ContainerInterface::class));
    }

    public function testMatchesSimplePath(): void
    {
        $routes = new RouteCollection();
        $routes->add('/ping', self::class);

        $matcher = $this->createRouteMatcher($routes);
        $result  = $matcher->match('/ping');

        self::assertTrue($result->isSuccess());
        self::assertSame(self::class, $result->route->controller);
        self::assertSame([], $result->attributes);
    }

    public function testMatchesRequiredParameters(): void
    {
        $routes = new RouteCollection();
        $routes->add('/tree/{tree}/individual/{xref}', self::class);

        $matcher = $this->createRouteMatcher($routes);
        $result  = $matcher->match('/tree/demo/individual/I001');

        self::assertTrue($result->isSuccess());
        self::assertSame(['tree' => 'demo', 'xref' => 'I001'], $result->attributes);
    }

    public function testMatchesOptionalParameterPresent(): void
    {
        $routes = new RouteCollection();
        $routes->add('/tree/{tree}/individual/{xref}{/slug}', self::class);

        $matcher = $this->createRouteMatcher($routes);
        $result  = $matcher->match('/tree/demo/individual/I001/john-doe');

        self::assertTrue($result->isSuccess());
        self::assertSame(['tree' => 'demo', 'xref' => 'I001', 'slug' => 'john-doe'], $result->attributes);
    }

    public function testMatchesOptionalParameterAbsent(): void
    {
        $routes = new RouteCollection();
        $routes->add('/tree/{tree}/individual/{xref}{/slug}', self::class);

        $matcher = $this->createRouteMatcher($routes);
        $result  = $matcher->match('/tree/demo/individual/I001');

        self::assertTrue($result->isSuccess());
        self::assertSame(['tree' => 'demo', 'xref' => 'I001'], $result->attributes);
    }

    public function testNoMatchReturnsNotFound(): void
    {
        $routes = new RouteCollection();
        $routes->add('/ping', self::class);

        $matcher = $this->createRouteMatcher($routes);
        $result  = $matcher->match('/nonexistent');

        self::assertFalse($result->isSuccess());
        self::assertSame('not_found', $result->failure_reason);
    }

    public function testSkipsNonDispatchableRoutes(): void
    {
        $routes = new RouteCollection();
        $routes->add('/module/{module}/{action}{/tree}', 'module'); // plain string, not a class

        $matcher = $this->createRouteMatcher($routes);
        $result  = $matcher->match('/module/charts/show/demo');

        self::assertFalse($result->isSuccess());
    }

    public function testDecodesUrlEncodedValues(): void
    {
        $routes = new RouteCollection();
        $routes->add('/tree/{tree}', self::class);

        $matcher = $this->createRouteMatcher($routes);
        $result  = $matcher->match('/tree/my%20tree');

        self::assertTrue($result->isSuccess());
        self::assertSame(['tree' => 'my tree'], $result->attributes);
    }
}
