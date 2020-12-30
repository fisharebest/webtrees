<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\RequestHandlers\AccountDelete;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;

/**
 * Test the AccountDelete request handler.
 *
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\AccountDelete
 */
class AccountDeleteTest extends TestCase
{
    /**
     * @return void
     */
    public function testHandler(): void
    {
        $user_service = self::createMock(UserService::class);
        $handler      = new AccountDelete($user_service);
        $request      = self::createRequest();
        $response     = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
