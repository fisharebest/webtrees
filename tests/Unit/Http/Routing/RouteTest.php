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

use Fisharebest\Webtrees\Http\Routing\Route;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Route::class)]
class RouteTest extends TestCase
{
    public function testConstructor(): void
    {
        $route = new Route(
            url: '/admin/broadcast/{to}',
            controller: 'Fisharebest\Webtrees\Http\Controllers\Broadcast',
            middleware: ['AuthAdministrator'],
        );

        self::assertSame('Fisharebest\Webtrees\Http\Controllers\Broadcast', $route->controller);
        self::assertSame('/admin/broadcast/{to}', $route->url);
        self::assertSame(['AuthAdministrator'], $route->middleware);
    }

    public function testDefaultMiddleware(): void
    {
        $route = new Route(url: '/test', controller: 'test', middleware: []);

        self::assertSame([], $route->middleware);
    }

    public function testIsDispatchableWithClass(): void
    {
        // Use a class that definitely exists
        $route = new Route(url: '/test', controller: self::class, middleware: []);

        self::assertTrue($route->isDispatchable());
    }

    public function testIsDispatchableWithPlainString(): void
    {
        $route = new Route(url: '/module/{module}/{action}{/tree}', controller: 'module', middleware: []);

        self::assertFalse($route->isDispatchable());
    }
}
