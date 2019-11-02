<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\Http\ResetHandlers;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\RequestHandlers\PasswordResetPage;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\View;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\PasswordResetPage
 */
class PasswordResetPageTest extends TestCase
{
    /**
     * @return void
     */
    public function testPasswordResetPageWithValidToken(): void
    {
        $user = $this->createMock(User::class);

        $user_service = $this->createMock(UserService::class);
        $user_service->expects($this->once())->method('findByToken')->willReturn($user);

        $request  = self::createRequest();
        $handler  = new PasswordResetPage($user_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testPasswordResetPageWithoutValidToken(): void
    {
        $user_service = $this->createMock(UserService::class);
        $user_service->expects($this->once())->method('findByToken')->willReturn(null);

        $request  = self::createRequest();
        $handler  = new PasswordResetPage($user_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
