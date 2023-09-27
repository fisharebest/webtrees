<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\Http\RequestHandlers\HomePage;
use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class RouteFactory
 *
 * @covers \Fisharebest\Webtrees\Factories\RouteFactory
 */
class RouteFactoryTest extends TestCase
{
    /**
     */
    public function testEmptyQueryParameterInURL(): void
    {
        $route_factory = new RouteFactory();

        $url = $route_factory->route(HomePage::class, ['foo' => null, 'bar' => '']);

        static::assertStringNotContainsString('foo=', $url);
        static::assertStringContainsString('bar=', $url);
    }
}
