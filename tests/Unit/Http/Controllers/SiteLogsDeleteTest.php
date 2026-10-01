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
use Fisharebest\Webtrees\Http\Controllers\SiteLogsDelete;
use Fisharebest\Webtrees\Services\SiteLogsService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SiteLogsDelete::class)]
class SiteLogsDeleteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::createDatabase();
    }

    public function testResponse(): void
    {
        $request = self::createRequest();

        DB::query(table: 'log')->insert(['log_type' => 'edit', 'log_message' => 'delete me', 'ip_address' => '127.0.0.1']);
        DB::query(table: 'log')->insert(['log_type' => 'auth', 'log_message' => 'keep me', 'ip_address' => '127.0.0.1']);

        $site_logs_service = self::createStub(SiteLogsService::class);
        $site_logs_service->method('logsQuery')->willReturn(DB::query()->select('log.*')->from('log')->whereEqual('log_type', 'edit'));

        $controller = new SiteLogsDelete($site_logs_service);
        $response   = $controller->post($request);

        self::assertSame(HttpStatusCode::NoContent->value, $response->getStatusCode());
        self::assertSame([], DB::query()->from('log')->whereEqual('log_message', 'delete me')->pluck('log_message'));
        self::assertSame(['keep me'], DB::query()->from('log')->whereEqual('log_message', 'keep me')->pluck('log_message'));
    }
}
