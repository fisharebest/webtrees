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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Controllers;

use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\Controllers\SiteLogsDownload;
use Fisharebest\Webtrees\Services\SiteLogsService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SiteLogsDownload::class)]
class SiteLogsDownloadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::createDatabase();
    }

    public function testResponse(): void
    {
        $request = self::createRequest();

        DB::query(table: 'log')->insert([
            'log_type'    => 'edit',
            'log_message' => 'foo,bar',
            'ip_address'  => '127.0.0.1',
        ]);

        $site_logs_service = self::createStub(SiteLogsService::class);
        $site_logs_service->method('logsQuery')->willReturn(DB::query()->select('log.*', 'log_message AS user_name', 'log_message AS gedcom_name')->from('log'));

        $controller = new SiteLogsDownload($site_logs_service);
        $response   = $controller->get($request);

        self::assertSame(HttpStatusCode::OK->value, $response->getStatusCode());
        self::assertSame('text/csv; charset=UTF-8', $response->getHeaderLine('content-type'));
        self::assertSame('attachment; filename="webtrees-logs.csv"', $response->getHeaderLine('content-disposition'));
        self::assertStringContainsString('"foo,bar"', (string) $response->getBody());
    }
}
