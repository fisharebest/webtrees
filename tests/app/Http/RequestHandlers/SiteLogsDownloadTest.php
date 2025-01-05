<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
    public function testResponse(): void
    {
        $request = self::createRequest();

        $query1 = $this->createMock(Builder::class);
        $query2 = $this->createMock(Builder::class);
        $rows1  = $this->createMock(Collection::class);
        $rows2  = $this->createMock(Collection::class);
        $query1->method('orderBy')->willReturn($query2);
        $query2->method('get')->willReturn($rows1);
        $rows1->method('map')->willReturn($rows2);
        $rows2->method('implode')->willReturn('foo,bar');

        $site_logs_service = $this->createMock(SiteLogsService::class);
        $site_logs_service->method('logsQuery')->willReturn($query1);

        $handler  = new SiteLogsDownload($site_logs_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('text/csv; charset=UTF-8', $response->getHeaderLine('content-type'));
        self::assertSame('attachment; filename="webtrees-logs.csv"', $response->getHeaderLine('content-disposition'));
    }
}
