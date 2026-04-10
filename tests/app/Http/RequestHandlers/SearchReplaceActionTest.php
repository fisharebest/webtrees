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
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SearchReplaceAction::class)]
class SearchReplaceActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(SearchReplaceAction::class));
    }

    public function testHandleContextAll(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test', 'Test');

        $search_service = $this->createMock(SearchService::class);
        $search_service->expects(self::once())
            ->method('searchIndividuals')
            ->with([$tree], ['old-text'])
            ->willReturn(new Collection());
        $search_service->expects(self::once())
            ->method('searchFamilies')
            ->with([$tree], ['old-text'])
            ->willReturn(new Collection());
        $search_service->expects(self::once())
            ->method('searchRepositories')
            ->with([$tree], ['old-text'])
            ->willReturn(new Collection());
        $search_service->expects(self::once())
            ->method('searchSources')
            ->with([$tree], ['old-text'])
            ->willReturn(new Collection());
        $search_service->expects(self::once())
            ->method('searchNotes')
            ->with([$tree], ['old-text'])
            ->willReturn(new Collection());

        $handler  = new SearchReplaceAction($search_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'search'  => 'old-text',
            'replace' => 'new-text',
            'context' => 'all',
        ], [], ['tree' => $tree]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleContextName(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test2', 'Test 2');

        $search_service = $this->createMock(SearchService::class);
        $search_service->expects(self::once())
            ->method('searchIndividuals')
            ->with([$tree], ['old-text'])
            ->willReturn(new Collection());
        $search_service->expects(self::never())
            ->method('searchFamilies');

        $handler  = new SearchReplaceAction($search_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'search'  => 'old-text',
            'replace' => 'new-text',
            'context' => 'name',
        ], [], ['tree' => $tree]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleContextPlace(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test3', 'Test 3');

        $search_service = $this->createMock(SearchService::class);
        $search_service->expects(self::once())
            ->method('searchIndividuals')
            ->with([$tree], ['old-text'])
            ->willReturn(new Collection());
        $search_service->expects(self::once())
            ->method('searchFamilies')
            ->with([$tree], ['old-text'])
            ->willReturn(new Collection());
        $search_service->expects(self::never())
            ->method('searchRepositories');
        $search_service->expects(self::never())
            ->method('searchSources');
        $search_service->expects(self::never())
            ->method('searchNotes');

        $handler  = new SearchReplaceAction($search_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'search'  => 'old-text',
            'replace' => 'new-text',
            'context' => 'place',
        ], [], ['tree' => $tree]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
