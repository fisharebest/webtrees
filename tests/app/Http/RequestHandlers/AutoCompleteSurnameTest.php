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

#[CoversClass(AutoCompleteSurname::class)]
class AutoCompleteSurnameTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testHandleReturnsJsonWithResults(): void
    {
        $tree = $this->importTree('demo.ged');

        $search_service = new SearchService(new TreeService(new GedcomImportService()));
        $handler = new AutoCompleteSurname($search_service);

        // Use a query that matches surnames in the demo.ged n_surname column
        $request = self::createRequest(query: ['query' => 'a'])
            ->withAttribute('tree', $tree);

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $json = json_decode((string) $response->getBody(), true);
        self::assertIsArray($json);
        // Single character should match some surnames
        self::assertNotEmpty($json, 'Should find surnames containing "a"');
    }

    public function testHandleReturnsEmptyForNonMatch(): void
    {
        $tree = $this->importTree('demo.ged');

        $search_service = new SearchService(new TreeService(new GedcomImportService()));
        $handler = new AutoCompleteSurname($search_service);

        $request = self::createRequest(query: ['query' => 'Xyznonexistent'])
            ->withAttribute('tree', $tree);

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $json = json_decode((string) $response->getBody(), true);
        self::assertIsArray($json);
        self::assertEmpty($json);
    }
}
