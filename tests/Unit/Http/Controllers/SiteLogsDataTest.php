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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Enums\HttpRequestMethod;
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Http\Controllers\SiteLogsData;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\SiteLogsService;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Database\Query\Builder;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ResponseInterface;

#[CoversClass(SiteLogsData::class)]
class SiteLogsDataTest extends TestCase
{
    public function testResponse(): void
    {
        $request = self::createRequest(
            HttpRequestMethod::GET->value,
            ['tree' => 'a', 'from' => 'b', 'to' => 'c', 'type' => 'd', 'text' => 'e', 'ip' => 'f', 'username' => 'g'],
        );

        $query = self::createStub(Builder::class);

        $site_logs_service = self::createStub(SiteLogsService::class);
        $site_logs_service->method('logsQuery')->willReturn($query);

        $response = self::createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(HttpStatusCode::OK->value);

        $data_tables_service = self::createStub(DatatablesService::class);
        $data_tables_service->method('handleQuery')->willReturn($response);

        $controller = new SiteLogsData(self::createStub(UserInterface::class), $data_tables_service, $site_logs_service);
        $response   = $controller->post($request);

        self::assertSame(HttpStatusCode::OK->value, $response->getStatusCode());
    }
}
