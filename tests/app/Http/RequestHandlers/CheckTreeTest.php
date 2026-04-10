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
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CheckTree::class)]
class CheckTreeTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(CheckTree::class));
    }

    public function testHandleReturnsOkForEmptyTree(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('check-empty', 'Check Empty');

        $timeout_service = $this->createMock(TimeoutService::class);
        $timeout_service->method('isTimeNearlyUp')->willReturn(false);

        $handler  = new CheckTree(new Gedcom(), $timeout_service);
        $request  = self::createRequest()
            ->withAttribute('tree', $tree);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleReturnsOkWithSkipTo(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('check-skip', 'Check Skip');

        $timeout_service = $this->createMock(TimeoutService::class);
        $timeout_service->method('isTimeNearlyUp')->willReturn(false);

        $handler  = new CheckTree(new Gedcom(), $timeout_service);
        // Passing a skip_to query parameter that does not match any record
        $request  = self::createRequest('GET', ['skip_to' => 'X999'])
            ->withAttribute('tree', $tree);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleReturnsOkWithTimeoutPagination(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('check-timeout', 'Check Timeout');

        // Simulate timeout being nearly up on the first call
        $timeout_service = $this->createMock(TimeoutService::class);
        $timeout_service->method('isTimeNearlyUp')->willReturn(true);

        $handler  = new CheckTree(new Gedcom(), $timeout_service);
        $request  = self::createRequest()
            ->withAttribute('tree', $tree);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
