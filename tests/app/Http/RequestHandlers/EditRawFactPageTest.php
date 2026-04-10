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
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(EditRawFactPage::class)]
class EditRawFactPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(EditRawFactPage::class));
    }

    public function testHandleReturnsEditPageWhenFactFound(): void
    {
        $tree = $this->importTree('demo.ged');

        $record = self::createStub(GedcomRecord::class);
        $record->method('xref')->willReturn('X1');
        $record->method('tree')->willReturn($tree);
        $record->method('canEdit')->willReturn(true);
        $record->method('canShow')->willReturn(true);
        $record->method('fullName')->willReturn('Test Record');
        $record->method('url')->willReturn('https://webtrees.test/record/X1');

        $fact = self::createStub(Fact::class);
        $fact->method('id')->willReturn('fact-123');
        $fact->method('record')->willReturn($record);

        $record->method('facts')->willReturn(new Collection([$fact]));

        $record_factory = $this->createMock(GedcomRecordFactoryInterface::class);
        $record_factory
            ->expects($this->once())
            ->method('make')
            ->with('X1', $tree)
            ->willReturn($record);

        Registry::gedcomRecordFactory($record_factory);

        $handler  = new EditRawFactPage();
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X1', 'fact_id' => 'fact-123'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleRedirectsWhenFactNotFound(): void
    {
        $tree = $this->importTree('demo.ged');

        $record = self::createStub(GedcomRecord::class);
        $record->method('xref')->willReturn('X1');
        $record->method('tree')->willReturn($tree);
        $record->method('canEdit')->willReturn(true);
        $record->method('canShow')->willReturn(true);
        $record->method('fullName')->willReturn('Test Record');
        $record->method('url')->willReturn('https://webtrees.test/record/X1');
        $record->method('facts')->willReturn(new Collection());

        $record_factory = $this->createMock(GedcomRecordFactoryInterface::class);
        $record_factory
            ->expects($this->once())
            ->method('make')
            ->with('X1', $tree)
            ->willReturn($record);

        Registry::gedcomRecordFactory($record_factory);

        $handler  = new EditRawFactPage();
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X1', 'fact_id' => 'nonexistent'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleWithUnknownRecordThrowsNotFoundException(): void
    {
        $tree = $this->importTree('demo.ged');

        $record_factory = $this->createMock(GedcomRecordFactoryInterface::class);
        $record_factory
            ->expects($this->once())
            ->method('make')
            ->with('X999', $tree)
            ->willReturn(null);

        Registry::gedcomRecordFactory($record_factory);

        $handler = new EditRawFactPage();
        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X999', 'fact_id' => 'fact-123'],
        );

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }
}
