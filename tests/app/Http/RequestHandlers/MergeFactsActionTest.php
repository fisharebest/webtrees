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
use Fisharebest\Webtrees\Contracts\GedcomRecordFactoryInterface;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MergeFactsAction::class)]
class MergeFactsActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(MergeFactsAction::class));
    }

    public function testHandleRedirectsBackWhenRecord1IsNull(): void
    {
        $tree = $this->importTree('demo.ged');

        $record_factory = $this->createMock(GedcomRecordFactoryInterface::class);
        $record_factory
            ->expects($this->exactly(2))
            ->method('make')
            ->willReturn(null);

        Registry::gedcomRecordFactory($record_factory);

        $linked_record_service = self::createStub(LinkedRecordService::class);

        $handler  = new MergeFactsAction($linked_record_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            ['xref1' => 'X1', 'xref2' => 'X2', 'keep1' => [], 'keep2' => []],
            [],
            ['tree' => $tree],
        );
        $response = $handler->handle($request);

        // Redirects back to MergeRecordsPage when validation fails
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleRedirectsBackWhenTagsDiffer(): void
    {
        $tree = $this->importTree('demo.ged');

        $record1 = self::createStub(GedcomRecord::class);
        $record1->method('xref')->willReturn('X1');
        $record1->method('tree')->willReturn($tree);
        $record1->method('tag')->willReturn('INDI');
        $record1->method('isPendingDeletion')->willReturn(false);
        $record1->method('canShow')->willReturn(true);

        $record2 = self::createStub(GedcomRecord::class);
        $record2->method('xref')->willReturn('X2');
        $record2->method('tree')->willReturn($tree);
        $record2->method('tag')->willReturn('FAM');
        $record2->method('isPendingDeletion')->willReturn(false);
        $record2->method('canShow')->willReturn(true);

        $record_factory = $this->createMock(GedcomRecordFactoryInterface::class);
        $record_factory
            ->expects($this->exactly(2))
            ->method('make')
            ->willReturnCallback(static fn (string $xref) => match ($xref) {
                'X1'    => $record1,
                'X2'    => $record2,
                default => null,
            });

        Registry::gedcomRecordFactory($record_factory);

        $linked_record_service = self::createStub(LinkedRecordService::class);

        $handler  = new MergeFactsAction($linked_record_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            ['xref1' => 'X1', 'xref2' => 'X2', 'keep1' => [], 'keep2' => []],
            [],
            ['tree' => $tree],
        );
        $response = $handler->handle($request);

        // Redirects back to MergeRecordsPage when record types differ
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleRedirectsBackWhenRecordIsPendingDeletion(): void
    {
        $tree = $this->importTree('demo.ged');

        $record1 = self::createStub(GedcomRecord::class);
        $record1->method('xref')->willReturn('X1');
        $record1->method('tree')->willReturn($tree);
        $record1->method('tag')->willReturn('INDI');
        $record1->method('isPendingDeletion')->willReturn(true);
        $record1->method('canShow')->willReturn(true);

        $record2 = self::createStub(GedcomRecord::class);
        $record2->method('xref')->willReturn('X2');
        $record2->method('tree')->willReturn($tree);
        $record2->method('tag')->willReturn('INDI');
        $record2->method('isPendingDeletion')->willReturn(false);
        $record2->method('canShow')->willReturn(true);

        $record_factory = $this->createMock(GedcomRecordFactoryInterface::class);
        $record_factory
            ->expects($this->exactly(2))
            ->method('make')
            ->willReturnCallback(static fn (string $xref) => match ($xref) {
                'X1'    => $record1,
                'X2'    => $record2,
                default => null,
            });

        Registry::gedcomRecordFactory($record_factory);

        $linked_record_service = self::createStub(LinkedRecordService::class);

        $handler  = new MergeFactsAction($linked_record_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            ['xref1' => 'X1', 'xref2' => 'X2', 'keep1' => [], 'keep2' => []],
            [],
            ['tree' => $tree],
        );
        $response = $handler->handle($request);

        // Redirects back to MergeRecordsPage when record is pending deletion
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
