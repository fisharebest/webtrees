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

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\SiteLogsDelete
 */
class SiteLogsDeleteTest extends TestCase
{
    public function testResponse(): void
    {
        $request = self::createRequest();

        $query = $this->createStub(Builder::class);
        $query->method('delete');

        $site_logs_service = $this->createStub(SiteLogsService::class);
        $site_logs_service->method('logsQuery')->willReturn($query);

        $handler  = new SiteLogsDelete($site_logs_service);
        $response = $handler->handle($request);

        static::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }
}
