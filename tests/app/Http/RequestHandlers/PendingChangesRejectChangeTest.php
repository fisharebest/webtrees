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
use Fisharebest\Webtrees\Contracts\GedcomRecordFactoryInterface;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PendingChangesRejectChange::class)]
class PendingChangesRejectChangeTest extends TestCase
{

    public function testClass(): void
    {
        self::assertTrue(class_exists(PendingChangesRejectChange::class));
    }

    public function testHandleRejectsChangeForExistingRecord(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('id')->willReturn(1);

        $record = self::createStub(GedcomRecord::class);
        $record->method('xref')->willReturn('I1');
        $record->method('canShow')->willReturn(true);

        $record_factory = $this->createMock(GedcomRecordFactoryInterface::class);
        $record_factory->expects(self::once())
            ->method('make')
            ->with('I1', $tree)
            ->willReturn($record);

        Registry::gedcomRecordFactory($record_factory);

        $pending_changes_service = $this->createMock(PendingChangesService::class);
        $pending_changes_service->expects(self::once())
            ->method('rejectChange')
            ->with($record, '42');

        $handler  = new PendingChangesRejectChange($pending_changes_service);
        $request  = self::createRequest()
            ->withAttribute('tree', $tree)
            ->withAttribute('xref', 'I1')
            ->withAttribute('change', '42');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }

    public function testHandleSkipsRejectWhenRecordNotFound(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('id')->willReturn(1);

        $record_factory = $this->createMock(GedcomRecordFactoryInterface::class);
        $record_factory->expects(self::once())
            ->method('make')
            ->with('X999', $tree)
            ->willReturn(null);

        Registry::gedcomRecordFactory($record_factory);

        $pending_changes_service = $this->createMock(PendingChangesService::class);
        $pending_changes_service->expects(self::never())
            ->method('rejectChange');

        $handler  = new PendingChangesRejectChange($pending_changes_service);
        $request  = self::createRequest()
            ->withAttribute('tree', $tree)
            ->withAttribute('xref', 'X999')
            ->withAttribute('change', '42');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }
}
