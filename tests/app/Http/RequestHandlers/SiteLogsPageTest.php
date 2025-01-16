<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\SiteLogsPage
 */
class SiteLogsPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testResponse(): void
    {
        $request = self::createRequest();

        $tree_service = $this->createStub(TreeService::class);
        $tree_service->method('all')->willReturn(new Collection());

        $user_service = $this->createStub(UserService::class);
        $user_service->method('all')->willReturn(new Collection());

        $handler  = new SiteLogsPage($tree_service, $user_service);
        $response = $handler->handle($request);

        static::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
