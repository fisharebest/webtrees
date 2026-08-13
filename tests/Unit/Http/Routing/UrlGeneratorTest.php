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
use Fisharebest\Webtrees\Http\Routing\UrlGenerator;
use Fisharebest\Webtrees\Tests\TestCase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UrlGenerator::class)]
class UrlGeneratorTest extends TestCase
{
    public function testGenerateSimplePath(): void
    {
        $routes = new RouteCollection();
        $routes->add('/ping', 'Ping');

        $generator = new UrlGenerator($routes);

        self::assertSame('/ping', $generator->generate('Ping'));
    }

    public function testGenerateWithRequiredParameters(): void
    {
        $routes = new RouteCollection();
        $routes->add('/tree/{tree}/individual/{xref}', 'Individual');

        $generator = new UrlGenerator($routes);

        self::assertSame(
            '/tree/demo/individual/I001',
            $generator->generate('Individual', ['tree' => 'demo', 'xref' => 'I001'])
        );
    }

    public function testGenerateWithOptionalParameterPresent(): void
    {
        $routes = new RouteCollection();
        $routes->add('/tree/{tree}/individual/{xref}{/slug}', 'Individual');

        $generator = new UrlGenerator($routes);

        self::assertSame(
            '/tree/demo/individual/I001/john-doe',
            $generator->generate('Individual', ['tree' => 'demo', 'xref' => 'I001', 'slug' => 'john-doe'])
        );
    }

    public function testGenerateWithOptionalParameterAbsent(): void
    {
        $routes = new RouteCollection();
        $routes->add('/tree/{tree}/individual/{xref}{/slug}', 'Individual');

        $generator = new UrlGenerator($routes);

        self::assertSame(
            '/tree/demo/individual/I001',
            $generator->generate('Individual', ['tree' => 'demo', 'xref' => 'I001'])
        );
    }

    public function testGenerateWithExtraParametersAsQueryString(): void
    {
        $routes = new RouteCollection();
        $routes->add('/tree/{tree}/search', 'Search');

        $generator = new UrlGenerator($routes);

        self::assertSame(
            '/tree/demo/search?query=smith&page=2',
            $generator->generate('Search', ['tree' => 'demo', 'query' => 'smith', 'page' => '2'])
        );
    }

    public function testGenerateWithBasePath(): void
    {
        $routes = new RouteCollection();
        $routes->add('/ping', 'Ping');

        $generator = new UrlGenerator($routes, '/webtrees');

        self::assertSame('/webtrees/ping', $generator->generate('Ping'));
    }

    public function testGenerateEncodesValues(): void
    {
        $routes = new RouteCollection();
        $routes->add('/tree/{tree}', 'Tree');

        $generator = new UrlGenerator($routes);

        self::assertSame('/tree/my%20tree', $generator->generate('Tree', ['tree' => 'my tree']));
    }

    public function testGenerateWithNonDispatchableRoute(): void
    {
        $routes = new RouteCollection();
        $routes->add('/module/{module}/{action}{/tree}', 'module');

        $generator = new UrlGenerator($routes);

        self::assertSame(
            '/module/charts/show/demo',
            $generator->generate('module', ['module' => 'charts', 'action' => 'show', 'tree' => 'demo'])
        );
    }

    public function testGenerateThrowsOnUnknownRoute(): void
    {
        $routes    = new RouteCollection();
        $generator = new UrlGenerator($routes);

        $this->expectException(InvalidArgumentException::class);
        $generator->generate('NonExistent');
    }
}
