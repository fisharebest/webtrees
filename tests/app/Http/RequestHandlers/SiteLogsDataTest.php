<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\SiteLogsService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Database\Query\Builder;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\SiteLogsData
 */
class SiteLogsDataTest extends TestCase
{
    /**
     * @return void
     */
    public function testResponse(): void
    {
        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            ['tree' => 'a', 'from' => 'b', 'to' => 'c', 'type' => 'd', 'text' => 'e', 'ip' => 'f', 'username' => 'g']
        );

        $query = $this->createStub(Builder::class);

        $site_logs_service = $this->createStub(SiteLogsService::class);
        $site_logs_service->method('logsQuery')->willReturn($query);

        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(StatusCodeInterface::STATUS_OK);

        $data_tables_service = $this->createStub(DatatablesService::class);
        $data_tables_service->method('handleQuery')->willReturn($response);

        $handler  = new SiteLogsData($data_tables_service, $site_logs_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
