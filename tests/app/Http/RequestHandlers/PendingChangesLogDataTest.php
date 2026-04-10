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
use Fisharebest\Algorithm\MyersDiff;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Query\Builder;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ResponseInterface;

#[CoversClass(PendingChangesLogData::class)]
class PendingChangesLogDataTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(PendingChangesLogData::class));
    }

    public function testHandleDelegatesQueryToDatatablesService(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('name')->willReturn('test');

        $query = self::createStub(Builder::class);

        $pending_changes_service = $this->createMock(PendingChangesService::class);
        $pending_changes_service->expects(self::once())
            ->method('changesQuery')
            ->willReturn($query);

        $expected_response = self::createStub(ResponseInterface::class);
        $expected_response->method('getStatusCode')->willReturn(StatusCodeInterface::STATUS_OK);

        $datatables_service = $this->createMock(DatatablesService::class);
        $datatables_service->expects(self::once())
            ->method('handleQuery')
            ->willReturn($expected_response);

        $myers_diff = self::createStub(MyersDiff::class);

        $handler  = new PendingChangesLogData($datatables_service, $myers_diff, $pending_changes_service);
        $request  = self::createRequest()
            ->withAttribute('tree', $tree);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
