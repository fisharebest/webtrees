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
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PendingChangesLogDownload::class)]
class PendingChangesLogDownloadTest extends TestCase
{

    public function testClass(): void
    {
        self::assertTrue(class_exists(PendingChangesLogDownload::class));
    }

    public function testHandleReturnsCsvResponse(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('name')->willReturn('test');

        $row = (object) [
            'change_time' => '2026-01-01 12:00:00',
            'status'      => 'pending',
            'xref'        => 'I1',
            'old_gedcom'  => '0 @I1@ INDI',
            'new_gedcom'  => '0 @I1@ INDI\n1 NAME Test /User/',
            'user_name'   => 'admin',
            'gedcom_name' => 'tree1',
        ];

        $query = self::createStub(Builder::class);
        $query->method('get')->willReturn(new Collection([$row]));

        $pending_changes_service = self::createStub(PendingChangesService::class);
        $pending_changes_service->method('changesQuery')->willReturn($query);

        $handler  = new PendingChangesLogDownload($pending_changes_service);
        $request  = self::createRequest()
            ->withAttribute('tree', $tree);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('text/csv; charset=UTF-8', $response->getHeaderLine('content-type'));
        self::assertSame('attachment; filename="changes.csv"', $response->getHeaderLine('content-disposition'));
    }

    public function testHandleReturnsBodyContent(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('name')->willReturn('test');

        $row = (object) [
            'change_time' => '2026-01-01 12:00:00',
            'status'      => 'pending',
            'xref'        => 'I1',
            'old_gedcom'  => '',
            'new_gedcom'  => '0 @I1@ INDI',
            'user_name'   => 'admin',
            'gedcom_name' => 'tree1',
        ];

        $query = self::createStub(Builder::class);
        $query->method('get')->willReturn(new Collection([$row]));

        $pending_changes_service = self::createStub(PendingChangesService::class);
        $pending_changes_service->method('changesQuery')->willReturn($query);

        $handler  = new PendingChangesLogDownload($pending_changes_service);
        $request  = self::createRequest()
            ->withAttribute('tree', $tree);
        $response = $handler->handle($request);

        $body = (string) $response->getBody();
        self::assertStringContainsString('2026-01-01 12:00:00', $body);
        self::assertStringContainsString('pending', $body);
        self::assertStringContainsString('I1', $body);
        self::assertStringContainsString('admin', $body);
        self::assertStringContainsString('tree1', $body);
    }

    public function testHandleEscapesDoubleQuotesInCsv(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('name')->willReturn('test');

        $row = (object) [
            'change_time' => '2026-01-01 12:00:00',
            'status'      => 'pending',
            'xref'        => 'I1',
            'old_gedcom'  => 'old "data"',
            'new_gedcom'  => 'new "data"',
            'user_name'   => 'user "name"',
            'gedcom_name' => 'tree "1"',
        ];

        $query = self::createStub(Builder::class);
        $query->method('get')->willReturn(new Collection([$row]));

        $pending_changes_service = self::createStub(PendingChangesService::class);
        $pending_changes_service->method('changesQuery')->willReturn($query);

        $handler  = new PendingChangesLogDownload($pending_changes_service);
        $request  = self::createRequest()
            ->withAttribute('tree', $tree);
        $response = $handler->handle($request);

        $body = (string) $response->getBody();
        // Double quotes are escaped as "" in CSV
        self::assertStringContainsString('""data""', $body);
        self::assertStringContainsString('""name""', $body);
        self::assertStringContainsString('""1""', $body);
    }

    public function testHandleWithEmptyChanges(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('name')->willReturn('test');

        $query = self::createStub(Builder::class);
        $query->method('get')->willReturn(new Collection());

        $pending_changes_service = self::createStub(PendingChangesService::class);
        $pending_changes_service->method('changesQuery')->willReturn($query);

        $handler  = new PendingChangesLogDownload($pending_changes_service);
        $request  = self::createRequest()
            ->withAttribute('tree', $tree);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }
}
