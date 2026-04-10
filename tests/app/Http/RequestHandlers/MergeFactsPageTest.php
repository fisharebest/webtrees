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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MergeFactsPage::class)]
class MergeFactsPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(MergeFactsPage::class));
    }

    public function testHandleReturnsOkWithValidRecords(): void
    {
        $tree = $this->importTree('demo.ged');

        $fact1 = self::createStub(Fact::class);
        $fact1->method('id')->willReturn('fact-1');
        $fact1->method('tag')->willReturn('INDI:BIRT');
        $fact1->method('isPendingDeletion')->willReturn(false);

        $fact2 = self::createStub(Fact::class);
        $fact2->method('id')->willReturn('fact-1');
        $fact2->method('tag')->willReturn('INDI:BIRT');
        $fact2->method('isPendingDeletion')->willReturn(false);

        $fact3 = self::createStub(Fact::class);
        $fact3->method('id')->willReturn('fact-2');
        $fact3->method('tag')->willReturn('INDI:DEAT');
        $fact3->method('isPendingDeletion')->willReturn(false);

        $record1 = self::createStub(GedcomRecord::class);
        $record1->method('xref')->willReturn('X1');
        $record1->method('tree')->willReturn($tree);
        $record1->method('tag')->willReturn('INDI');
        $record1->method('isPendingDeletion')->willReturn(false);
        $record1->method('canShow')->willReturn(true);
        $record1->method('fullName')->willReturn('Record 1');
        $record1->method('url')->willReturn('https://webtrees.test/record/X1');
        $record1->method('facts')->willReturn(new Collection([$fact1, $fact3]));

        $record2 = self::createStub(GedcomRecord::class);
        $record2->method('xref')->willReturn('X2');
        $record2->method('tree')->willReturn($tree);
        $record2->method('tag')->willReturn('INDI');
        $record2->method('isPendingDeletion')->willReturn(false);
        $record2->method('canShow')->willReturn(true);
        $record2->method('fullName')->willReturn('Record 2');
        $record2->method('url')->willReturn('https://webtrees.test/record/X2');
        $record2->method('facts')->willReturn(new Collection([$fact2]));

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

        $handler  = new MergeFactsPage();
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            ['xref1' => 'X1', 'xref2' => 'X2'],
            [],
            [],
            ['tree' => $tree],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleRedirectsWhenRecord1IsNull(): void
    {
        $tree = $this->importTree('demo.ged');

        $record_factory = $this->createMock(GedcomRecordFactoryInterface::class);
        $record_factory
            ->expects($this->exactly(2))
            ->method('make')
            ->willReturn(null);

        Registry::gedcomRecordFactory($record_factory);

        $handler  = new MergeFactsPage();
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            ['xref1' => 'X1', 'xref2' => 'X2'],
            [],
            [],
            ['tree' => $tree],
        );
        $response = $handler->handle($request);

        // Redirects back to MergeRecordsPage when validation fails
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleRedirectsWhenTagsDiffer(): void
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

        $handler  = new MergeFactsPage();
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            ['xref1' => 'X1', 'xref2' => 'X2'],
            [],
            [],
            ['tree' => $tree],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
