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

namespace Fisharebest\Webtrees\Tests\Unit\Cli\Commands;

use Fisharebest\Webtrees\Cli\Commands\Repl;
use Fisharebest\Webtrees\Http\Routing\RouteCollection;
use Fisharebest\Webtrees\Http\Routing\UrlGenerator;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionMethod;

#[CoversClass(Repl::class)]
class ReplTest extends TestCase
{
    public function testStoreRoutingServicesInContainerRegistersShortNameAliases(): void
    {
        $command          = new Repl();
        $route_collection = new RouteCollection();
        $url_generator    = new UrlGenerator($route_collection, '');

        $this->invokePrivateMethod($command, 'storeRoutingServicesInContainer', $route_collection, $url_generator);

        self::assertSame($route_collection, Registry::container()->get(RouteCollection::class));
        self::assertSame($route_collection, Registry::container()->get('RouteCollection'));
        self::assertSame($url_generator, Registry::container()->get(UrlGenerator::class));
        self::assertSame($url_generator, Registry::container()->get('UrlGenerator'));
    }

    private function invokePrivateMethod(Repl $command, string $method_name, mixed ...$arguments): mixed
    {
        return (new ReflectionMethod($command, $method_name))->invokeArgs($command, $arguments);
    }
}
