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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TomSelectNote::class)]
class TomSelectNoteTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(TomSelectNote::class));
    }

    public function testHandleEmptyQueryReturnsEmptyResults(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('name')->willReturn('test');

        $search_service = $this->createMock(SearchService::class);
        $search_service->expects(self::never())
            ->method('searchNotes');

        $handler  = new TomSelectNote($search_service);
        $request  = self::createRequest('GET', ['query' => '', 'at' => ''], [], [], ['tree' => $tree]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $data = json_decode((string) $response->getBody(), true);
        self::assertIsArray($data);
        self::assertEmpty($data['data']);
    }

    public function testHandleWithQueryReturnsJsonResponse(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('name')->willReturn('test');

        $search_service = $this->createMock(SearchService::class);
        $search_service->expects(self::once())
            ->method('searchNotes')
            ->willReturn(new Collection());

        $handler  = new TomSelectNote($search_service);
        $request  = self::createRequest('GET', ['query' => 'Research', 'at' => ''], [], [], ['tree' => $tree]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $data = json_decode((string) $response->getBody(), true);
        self::assertIsArray($data);
        self::assertArrayHasKey('data', $data);
        self::assertArrayHasKey('nextUrl', $data);
    }
}
