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

namespace Fisharebest\Webtrees\Tests\Unit\Factories;

use Fisharebest\Webtrees\Factories\RouteFactory;
use Fisharebest\Webtrees\Http\Controllers\HomePage;
use Fisharebest\Webtrees\Http\Controllers\Ping;
use Fisharebest\Webtrees\Http\Routing\RouteCollection;
use Fisharebest\Webtrees\Http\Routing\UrlGenerator;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RouteFactory::class)]
class RouteFactoryTest extends TestCase
{
    public function testEmptyQueryParameterInURL(): void
    {
        $route_factory = new RouteFactory();

        $url = $route_factory->route(HomePage::class, ['foo' => null, 'bar' => '']);

        self::assertStringNotContainsString('foo=', $url);
        self::assertStringContainsString('bar=', $url);
    }

    public function testUglyUrlIncludesSubfolderInRouteParameter(): void
    {
        self::createRequest(attributes: ['base_url' => 'https://webtrees.test/webtrees']);

        $routes = Registry::container()->get(RouteCollection::class);
        Registry::container()->set(UrlGenerator::class, new UrlGenerator($routes, '/webtrees'));

        $url = (new RouteFactory())->route(Ping::class, ['foo' => 'bar']);

        self::assertSame(
            'https://webtrees.test/webtrees/index.php?route=%2Fwebtrees%2Fping&foo=bar',
            $url,
        );
    }
}
