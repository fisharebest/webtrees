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
use Fisharebest\Webtrees\Services\SiteLogsService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SiteLogsDownload::class)]
class SiteLogsDownloadTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(SiteLogsDownload::class));
    }

    public function testHandleReturnsCsvResponse(): void
    {
        $request = self::createRequest();

        $query1 = self::createStub(Builder::class);
        $query2 = self::createStub(Builder::class);
        $rows1  = self::createStub(Collection::class);
        $rows2  = self::createStub(Collection::class);
        $query1->method('orderBy')->willReturn($query2);
        $query2->method('get')->willReturn($rows1);
        $rows1->method('map')->willReturn($rows2);
        $rows2->method('implode')->willReturn('foo,bar');

        $site_logs_service = self::createStub(SiteLogsService::class);
        $site_logs_service->method('logsQuery')->willReturn($query1);

        $handler  = new SiteLogsDownload($site_logs_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('text/csv; charset=UTF-8', $response->getHeaderLine('content-type'));
        self::assertSame('attachment; filename="webtrees-logs.csv"', $response->getHeaderLine('content-disposition'));
    }

    public function testHandleReturnsCorrectBodyContent(): void
    {
        $request = self::createRequest();

        $row = (object) [
            'log_time'    => '2026-01-01 12:00:00',
            'log_type'    => 'config',
            'log_message' => 'Test message',
            'ip_address'  => '127.0.0.1',
            'user_name'   => 'admin',
            'gedcom_name' => 'tree1',
        ];

        $query1 = self::createStub(Builder::class);
        $query2 = self::createStub(Builder::class);
        $query1->method('orderBy')->willReturn($query2);
        $query2->method('get')->willReturn(new Collection([$row]));

        $site_logs_service = self::createStub(SiteLogsService::class);
        $site_logs_service->method('logsQuery')->willReturn($query1);

        $handler  = new SiteLogsDownload($site_logs_service);
        $response = $handler->handle($request);

        $body = (string) $response->getBody();
        self::assertStringContainsString('2026-01-01 12:00:00', $body);
        self::assertStringContainsString('config', $body);
        self::assertStringContainsString('Test message', $body);
        self::assertStringContainsString('127.0.0.1', $body);
        self::assertStringContainsString('admin', $body);
        self::assertStringContainsString('tree1', $body);
    }

    public function testHandleEscapesDoubleQuotesInCsv(): void
    {
        $request = self::createRequest();

        $row = (object) [
            'log_time'    => '2026-01-01 12:00:00',
            'log_type'    => 'config',
            'log_message' => 'Message with "quotes"',
            'ip_address'  => '127.0.0.1',
            'user_name'   => 'user "name"',
            'gedcom_name' => 'tree "1"',
        ];

        $query1 = self::createStub(Builder::class);
        $query2 = self::createStub(Builder::class);
        $query1->method('orderBy')->willReturn($query2);
        $query2->method('get')->willReturn(new Collection([$row]));

        $site_logs_service = self::createStub(SiteLogsService::class);
        $site_logs_service->method('logsQuery')->willReturn($query1);

        $handler  = new SiteLogsDownload($site_logs_service);
        $response = $handler->handle($request);

        $body = (string) $response->getBody();
        // Double quotes are escaped as "" in CSV
        self::assertStringContainsString('""quotes""', $body);
        self::assertStringContainsString('""name""', $body);
        self::assertStringContainsString('""1""', $body);
    }

    public function testHandleWithEmptyLogSet(): void
    {
        $request = self::createRequest();

        $query1 = self::createStub(Builder::class);
        $query2 = self::createStub(Builder::class);
        $query1->method('orderBy')->willReturn($query2);
        $query2->method('get')->willReturn(new Collection());

        $site_logs_service = self::createStub(SiteLogsService::class);
        $site_logs_service->method('logsQuery')->willReturn($query1);

        $handler  = new SiteLogsDownload($site_logs_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }
}
