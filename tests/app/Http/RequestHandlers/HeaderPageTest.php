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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Contracts\HeaderFactoryInterface;
use Fisharebest\Webtrees\Contracts\SlugFactoryInterface;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(HeaderPage::class)]
class HeaderPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(HeaderPage::class));
    }

    public function testHandleReturnsOkForVisibleHeader(): void
    {
        $tree = $this->importTree('demo.ged');

        $header = self::createStub(Header::class);
        $header->method('xref')->willReturn('H1');
        $header->method('tree')->willReturn($tree);
        $header->method('canShow')->willReturn(true);
        $header->method('canEdit')->willReturn(false);
        $header->method('fullName')->willReturn('Test Header');
        $header->method('url')->willReturn('https://webtrees.test/header/H1');
        $header->method('facts')->willReturn(new Collection());

        $header_factory = $this->createMock(HeaderFactoryInterface::class);
        $header_factory
            ->expects($this->once())
            ->method('make')
            ->with('H1', $tree)
            ->willReturn($header);

        Registry::headerFactory($header_factory);

        $slug_factory = $this->createMock(SlugFactoryInterface::class);
        $slug_factory->method('make')->willReturn('');

        Registry::slugFactory($slug_factory);

        $handler  = new HeaderPage();
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'H1', 'slug' => ''],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleRedirectsOnSlugMismatch(): void
    {
        $tree = $this->importTree('demo.ged');

        $header = self::createStub(Header::class);
        $header->method('xref')->willReturn('H1');
        $header->method('tree')->willReturn($tree);
        $header->method('canShow')->willReturn(true);
        $header->method('canEdit')->willReturn(false);
        $header->method('url')->willReturn('https://webtrees.test/header/H1/test-header');

        $header_factory = $this->createMock(HeaderFactoryInterface::class);
        $header_factory
            ->expects($this->once())
            ->method('make')
            ->with('H1', $tree)
            ->willReturn($header);

        Registry::headerFactory($header_factory);

        $slug_factory = $this->createMock(SlugFactoryInterface::class);
        $slug_factory->method('make')->willReturn('test-header');

        Registry::slugFactory($slug_factory);

        $handler  = new HeaderPage();
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'H1', 'slug' => 'wrong-slug'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
    }

    public function testHandleWithUnknownHeaderThrowsNotFoundException(): void
    {
        $tree = $this->importTree('demo.ged');

        $header_factory = $this->createMock(HeaderFactoryInterface::class);
        $header_factory
            ->expects($this->once())
            ->method('make')
            ->with('X999', $tree)
            ->willReturn(null);

        Registry::headerFactory($header_factory);

        $handler = new HeaderPage();
        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X999', 'slug' => ''],
        );

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }
}
