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

namespace Fisharebest\Webtrees\Tests\Unit;

use Fisharebest\Webtrees\Container;
use Fisharebest\Webtrees\Tests\TestCase;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ResponseFactoryInterface;
use stdClass;

#[CoversClass(Container::class)]
class ContainerTest extends TestCase
{
    public function testSetAndGet(): void
    {
        $container = new Container();
        $object    = new stdClass();

        $container->set(stdClass::class, $object);

        self::assertSame($object, $container->get(stdClass::class));
    }

    public function testHasReturnsFalseForUnknownId(): void
    {
        $container = new Container();

        self::assertFalse($container->has(stdClass::class));
    }

    public function testHasReturnsTrueAfterSet(): void
    {
        $container = new Container();

        $container->set(stdClass::class, new stdClass());

        self::assertTrue($container->has(stdClass::class));
    }

    public function testHasReturnsTrueAfterBind(): void
    {
        $container = new Container();

        $container->bind(ResponseFactoryInterface::class, Psr17Factory::class);

        self::assertTrue($container->has(ResponseFactoryInterface::class));
    }

    public function testBindLazilyInstantiates(): void
    {
        $container = new Container();

        $container->bind(ResponseFactoryInterface::class, Psr17Factory::class);

        $result = $container->get(ResponseFactoryInterface::class);

        self::assertInstanceOf(Psr17Factory::class, $result);
    }

    public function testBindReturnsSameInstanceOnSubsequentCalls(): void
    {
        $container = new Container();

        $container->bind(ResponseFactoryInterface::class, Psr17Factory::class);

        $first  = $container->get(ResponseFactoryInterface::class);
        $second = $container->get(ResponseFactoryInterface::class);

        self::assertSame($first, $second);
    }

    public function testSetOverridesBinding(): void
    {
        $container = new Container();
        $object    = new Psr17Factory();

        $container->bind(ResponseFactoryInterface::class, Psr17Factory::class);
        $container->set(ResponseFactoryInterface::class, $object);

        self::assertSame($object, $container->get(ResponseFactoryInterface::class));
    }

    public function testAutoResolvesConcreteClass(): void
    {
        $container = new Container();

        $result = $container->get(stdClass::class);

        self::assertInstanceOf(stdClass::class, $result);
    }
}
