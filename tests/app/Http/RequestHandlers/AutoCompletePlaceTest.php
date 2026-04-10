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
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AutoCompletePlace::class)]
class AutoCompletePlaceTest extends TestCase
{

    public function testClass(): void
    {
        self::assertTrue(class_exists(AutoCompletePlace::class));
    }

    /**
     * When the SearchService returns places, the handler returns JSON with STATUS_OK.
     */
    public function testHandleReturnsJsonWithResults(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('id')->willReturn(1);

        $place = self::createStub(Place::class);
        $place->method('gedcomName')->willReturn('London, England');

        $search_service = $this->createMock(SearchService::class);
        $search_service->expects(self::once())
            ->method('searchPlaces')
            ->willReturn(new Collection([$place]));

        $module_service = $this->createMock(ModuleService::class);
        // findByInterface should not be called when we have results
        $module_service->method('findByInterface')
            ->willReturn(new Collection());

        $handler  = new AutoCompletePlace($module_service, $search_service);
        $request  = self::createRequest(
            query: ['query' => 'London'],
            attributes: ['tree' => $tree],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertStringContainsString('application/json', $response->getHeaderLine('content-type'));

        $body = (string) $response->getBody();
        self::assertStringContainsString('London', $body);
    }

    /**
     * An empty search result returns an empty JSON array.
     */
    public function testHandleReturnsEmptyJsonForNoResults(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('id')->willReturn(1);

        $search_service = $this->createMock(SearchService::class);
        $search_service->expects(self::once())
            ->method('searchPlaces')
            ->willReturn(new Collection());

        $module_service = $this->createMock(ModuleService::class);
        $module_service->method('findByInterface')
            ->willReturn(new Collection());

        $handler  = new AutoCompletePlace($module_service, $search_service);
        $request  = self::createRequest(
            query: ['query' => 'Nonexistent'],
            attributes: ['tree' => $tree],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $body = (string) $response->getBody();
        self::assertSame('[]', $body);
    }

    /**
     * The response must include a cache-control header from AbstractAutocompleteHandler.
     */
    public function testResponseIncludesCacheHeader(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('id')->willReturn(1);

        $search_service = $this->createMock(SearchService::class);
        $search_service->method('searchPlaces')
            ->willReturn(new Collection());

        $module_service = $this->createMock(ModuleService::class);
        $module_service->method('findByInterface')
            ->willReturn(new Collection());

        $handler  = new AutoCompletePlace($module_service, $search_service);
        $request  = self::createRequest(
            query: ['query' => 'test'],
            attributes: ['tree' => $tree],
        );
        $response = $handler->handle($request);

        self::assertNotEmpty($response->getHeaderLine('cache-control'));
    }
}
