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
use Fisharebest\Webtrees\Contracts\ElementFactoryInterface;
use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\Contracts\GedcomRecordFactoryInterface;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AddNewFact::class)]
class AddNewFactTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(AddNewFact::class));
    }

    public function testHandleReturnsEditFactPage(): void
    {
        $tree = $this->importTree('demo.ged');

        $record = self::createStub(GedcomRecord::class);
        $record->method('xref')->willReturn('X1');
        $record->method('tree')->willReturn($tree);
        $record->method('canEdit')->willReturn(true);
        $record->method('canShow')->willReturn(true);
        $record->method('fullName')->willReturn('Test Record');
        $record->method('url')->willReturn('https://webtrees.test/record/X1');
        $record->method('tag')->willReturn('INDI');

        $record_factory = self::createStub(GedcomRecordFactoryInterface::class);
        $record_factory->method('make')->willReturn($record);

        Registry::gedcomRecordFactory($record_factory);

        $element = self::createStub(ElementInterface::class);
        $element->method('label')->willReturn('Birth');
        $element->method('default')->willReturn('');

        $element_factory = self::createStub(ElementFactoryInterface::class);
        $element_factory->method('make')->willReturn($element);

        Registry::elementFactory($element_factory);

        $gedcom_edit_service = $this->createMock(GedcomEditService::class);
        $gedcom_edit_service
            ->expects($this->exactly(2))
            ->method('insertMissingFactSubtags')
            ->willReturn('1 BIRT');

        $handler  = new AddNewFact($gedcom_edit_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X1', 'fact' => 'BIRT'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleWithUnknownRecordThrowsNotFoundException(): void
    {
        $tree = $this->importTree('demo.ged');

        $record_factory = self::createStub(GedcomRecordFactoryInterface::class);
        $record_factory->method('make')->willReturn(null);

        Registry::gedcomRecordFactory($record_factory);

        $gedcom_edit_service = self::createStub(GedcomEditService::class);

        $handler = new AddNewFact($gedcom_edit_service);
        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X999', 'fact' => 'BIRT'],
        );

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }

    public function testHandleWithHiddenFieldsDifferentUrl(): void
    {
        $tree = $this->importTree('demo.ged');

        $record = self::createStub(GedcomRecord::class);
        $record->method('xref')->willReturn('X1');
        $record->method('tree')->willReturn($tree);
        $record->method('canEdit')->willReturn(true);
        $record->method('canShow')->willReturn(true);
        $record->method('fullName')->willReturn('Test Record');
        $record->method('url')->willReturn('https://webtrees.test/record/X1');
        $record->method('tag')->willReturn('INDI');

        $record_factory = self::createStub(GedcomRecordFactoryInterface::class);
        $record_factory->method('make')->willReturn($record);

        Registry::gedcomRecordFactory($record_factory);

        $element = self::createStub(ElementInterface::class);
        $element->method('label')->willReturn('Birth');
        $element->method('default')->willReturn('');

        $element_factory = self::createStub(ElementFactoryInterface::class);
        $element_factory->method('make')->willReturn($element);

        Registry::elementFactory($element_factory);

        // Return different values for visible vs hidden to trigger hidden_url generation
        $gedcom_edit_service = $this->createMock(GedcomEditService::class);
        $gedcom_edit_service
            ->expects($this->exactly(2))
            ->method('insertMissingFactSubtags')
            ->willReturnOnConsecutiveCalls('1 BIRT', '1 BIRT\n2 DATE\n2 PLAC');

        $handler  = new AddNewFact($gedcom_edit_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X1', 'fact' => 'BIRT'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
