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
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SearchQuickAction::class)]
class SearchQuickActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(SearchQuickAction::class));
    }

    public function testHandleRedirectsToRecordWhenFound(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test', 'Test');

        $record = self::createStub(GedcomRecord::class);
        $record
            ->method('canShow')
            ->willReturn(true);
        $record
            ->method('url')
            ->willReturn('https://webtrees.test/tree/test/individual/I1');

        $factory = $this->createMock(GedcomRecordFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('make')
            ->with('I1', $tree)
            ->willReturn($record);

        Registry::gedcomRecordFactory($factory);

        $handler  = new SearchQuickAction();
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'query' => 'I1',
        ], [], ['tree' => $tree]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertSame('https://webtrees.test/tree/test/individual/I1', $response->getHeaderLine('Location'));
    }

    public function testHandleRedirectsToSearchPageWhenNotFound(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test2', 'Test 2');

        $factory = $this->createMock(GedcomRecordFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('make')
            ->with('NOTFOUND', $tree)
            ->willReturn(null);

        Registry::gedcomRecordFactory($factory);

        $handler  = new SearchQuickAction();
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'query' => 'NOTFOUND',
        ], [], ['tree' => $tree]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertStringContainsString('NOTFOUND', $response->getHeaderLine('Location'));
        self::assertStringContainsString('test2', $response->getHeaderLine('Location'));
    }
}
