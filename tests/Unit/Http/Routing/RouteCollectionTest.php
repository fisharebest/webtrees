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
use Fisharebest\Webtrees\Tests\TestCase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RouteCollection::class)]
class RouteCollectionTest extends TestCase
{
    public function testAdd(): void
    {
        $routes = new RouteCollection();
        $routes->add('/test', 'TestController');

        self::assertTrue($routes->has('TestController'));

        $route = $routes->get('TestController');
        self::assertSame('/test', $route->url);
        self::assertSame('TestController', $route->controller);
        self::assertSame([], $route->middleware);
    }

    public function testGroup(): void
    {
        $routes = new RouteCollection();

        $routes->group('/admin', ['AuthAdmin'], static function (RouteCollection $routes): void {
            $routes->add('/users', 'UserList');
            $routes->add('/settings', 'Settings');
        });

        $route = $routes->get('UserList');
        self::assertSame('/admin/users', $route->url);
        self::assertSame(['AuthAdmin'], $route->middleware);

        $route = $routes->get('Settings');
        self::assertSame('/admin/settings', $route->url);
        self::assertSame(['AuthAdmin'], $route->middleware);
    }

    public function testNestedGroups(): void
    {
        $routes = new RouteCollection();

        $routes->group('/api', ['ApiAuth'], static function (RouteCollection $routes): void {
            $routes->group('/v1', ['RateLimit'], static function (RouteCollection $routes): void {
                $routes->add('/users', 'ApiUsers');
            });
        });

        $route = $routes->get('ApiUsers');
        self::assertSame('/api/v1/users', $route->url);
        self::assertSame(['ApiAuth', 'RateLimit'], $route->middleware);
    }

    public function testGroupDoesNotAffectOuterScope(): void
    {
        $routes = new RouteCollection();

        $routes->group('/admin', ['AuthAdmin'], static function (RouteCollection $routes): void {
            $routes->add('/users', 'AdminUsers');
        });

        $routes->add('/public', 'PublicPage');

        $route = $routes->get('PublicPage');
        self::assertSame('/public', $route->url);
        self::assertSame([], $route->middleware);
    }

    public function testGetThrowsOnMissing(): void
    {
        $routes = new RouteCollection();

        $this->expectException(InvalidArgumentException::class);
        $routes->get('NonExistent');
    }

    public function testAll(): void
    {
        $routes = new RouteCollection();
        $routes->add('/a', 'RouteA');
        $routes->add('/b', 'RouteB');

        $all = $routes->all();
        self::assertCount(2, $all);
        self::assertArrayHasKey('RouteA', $all);
        self::assertArrayHasKey('RouteB', $all);
    }

    public function testHas(): void
    {
        $routes = new RouteCollection();
        $routes->add('/test', 'Exists');

        self::assertTrue($routes->has('Exists'));
        self::assertFalse($routes->has('DoesNotExist'));
    }
}
