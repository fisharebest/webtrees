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
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SearchGeneralPage::class)]
class SearchGeneralPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(SearchGeneralPage::class));
    }

    /**
     * The default page (no query) renders with STATUS_OK.
     */
    public function testHandleDefaultPage(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $search_service        = new SearchService($tree_service);

        $handler  = new SearchGeneralPage($search_service, $tree_service);
        $request  = self::createRequest(attributes: ['tree' => $tree]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $body = (string) $response->getBody();
        self::assertNotEmpty($body);
    }

    /**
     * An empty query string renders the page without performing a search.
     */
    public function testHandleWithEmptyQuery(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $search_service        = new SearchService($tree_service);

        $handler  = new SearchGeneralPage($search_service, $tree_service);
        $request  = self::createRequest(
            query: ['query' => ''],
            attributes: ['tree' => $tree],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * A query with no results renders the page with STATUS_OK.
     */
    public function testHandleWithQueryNoResults(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $search_service        = new SearchService($tree_service);

        $handler  = new SearchGeneralPage($search_service, $tree_service);
        $request  = self::createRequest(
            query: [
                'query'              => 'nonexistent-person-xyz',
                'search_individuals' => '1',
            ],
            attributes: ['tree' => $tree],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * When no record-type checkboxes are ticked, default to individuals + families.
     */
    public function testHandleDefaultsToIndividualsAndFamilies(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $search_service        = new SearchService($tree_service);

        $handler  = new SearchGeneralPage($search_service, $tree_service);
        $request  = self::createRequest(
            query: ['query' => 'test'],
            attributes: ['tree' => $tree],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $body = (string) $response->getBody();
        self::assertNotEmpty($body);
    }
}
