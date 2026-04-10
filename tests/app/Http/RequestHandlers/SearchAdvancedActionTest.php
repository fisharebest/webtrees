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
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SearchAdvancedAction::class)]
class SearchAdvancedActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(SearchAdvancedAction::class));
    }

    public function testHandleRedirects(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test', 'Test');

        $handler  = new SearchAdvancedAction();
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'fields'      => ['NAME' => 'John'],
            'modifiers'   => ['NAME' => 'exact'],
            'other_field' => '',
        ], [], ['tree' => $tree]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleWithOtherField(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test2', 'Test 2');

        $handler  = new SearchAdvancedAction();
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'fields'      => ['NAME' => 'Jane'],
            'modifiers'   => [],
            'other_field' => 'BIRT:PLAC',
        ], [], ['tree' => $tree]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertStringContainsString('BIRT%3APLAC', $response->getHeaderLine('location'));
    }
}
