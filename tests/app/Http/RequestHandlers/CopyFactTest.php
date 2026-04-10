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
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CopyFact::class)]
class CopyFactTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(CopyFact::class));
    }

    public function testHandleCopiesMatchingFact(): void
    {
        $tree = $this->importTree('demo.ged');

        $fact = self::createStub(Fact::class);
        $fact->method('id')->willReturn('fact-123');
        $fact->method('name')->willReturn('Birth');

        $record = self::createStub(GedcomRecord::class);
        $record->method('xref')->willReturn('X1');
        $record->method('tree')->willReturn($tree);
        $record->method('canEdit')->willReturn(true);
        $record->method('canShow')->willReturn(true);
        $record->method('facts')->willReturn(new Collection([$fact]));

        $record_factory = self::createStub(GedcomRecordFactoryInterface::class);
        $record_factory->method('make')->willReturn($record);

        Registry::gedcomRecordFactory($record_factory);

        $clipboard_service = $this->createMock(ClipboardService::class);
        $clipboard_service
            ->expects($this->once())
            ->method('copyFact')
            ->with($fact);

        $handler  = new CopyFact($clipboard_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X1', 'fact_id' => 'fact-123'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }

    public function testHandleWithNoMatchingFact(): void
    {
        $tree = $this->importTree('demo.ged');

        $fact = self::createStub(Fact::class);
        $fact->method('id')->willReturn('fact-other');

        $record = self::createStub(GedcomRecord::class);
        $record->method('xref')->willReturn('X1');
        $record->method('tree')->willReturn($tree);
        $record->method('canEdit')->willReturn(true);
        $record->method('canShow')->willReturn(true);
        $record->method('facts')->willReturn(new Collection([$fact]));

        $record_factory = self::createStub(GedcomRecordFactoryInterface::class);
        $record_factory->method('make')->willReturn($record);

        Registry::gedcomRecordFactory($record_factory);

        $clipboard_service = $this->createMock(ClipboardService::class);
        $clipboard_service
            ->expects($this->never())
            ->method('copyFact');

        $handler  = new CopyFact($clipboard_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X1', 'fact_id' => 'nonexistent'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }

    public function testHandleWithUnknownRecordThrowsNotFoundException(): void
    {
        $tree = $this->importTree('demo.ged');

        $record_factory = self::createStub(GedcomRecordFactoryInterface::class);
        $record_factory->method('make')->willReturn(null);

        Registry::gedcomRecordFactory($record_factory);

        $clipboard_service = self::createStub(ClipboardService::class);

        $handler = new CopyFact($clipboard_service);
        $request = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X999', 'fact_id' => 'fact-123'],
        );

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }
}
