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

namespace Fisharebest\Webtrees\Http\ResetHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\RequestHandlers\PasswordResetPage;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PasswordResetPage::class)]
class PasswordResetPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testPasswordResetPageWithValidToken(): void
    {
        $user = $this->createMock(User::class);

        $user_service = $this->createMock(UserService::class);
        $user_service
            ->expects($this->once())
            ->method('findByToken')
            ->with('1234')
            ->willReturn($user);

        $request  = self::createRequest()
            ->withAttribute('token', '1234');
        $handler  = new PasswordResetPage($user_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testPasswordResetPageWithoutValidToken(): void
    {
        $user_service = $this->createMock(UserService::class);
        $user_service
            ->expects($this->once())
            ->method('findByToken')
            ->with('4321')
            ->willReturn(null);

        $request  = self::createRequest()
            ->withAttribute('token', '4321');
        $handler  = new PasswordResetPage($user_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
