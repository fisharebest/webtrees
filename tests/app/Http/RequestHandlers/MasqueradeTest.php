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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\User;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\Masquerade
 */
class MasqueradeTest extends TestCase
{
    /**
     * @return void
     */
    public function testMasqueradeAsUser(): void
    {
        $user1 = self::createMock(User::class);
        $user1->method('id')->willReturn(1);

        $user2 = self::createMock(User::class);
        $user2->method('id')->willReturn(2);

        $user_service = self::createMock(UserService::class);
        $user_service->expects(self::once())->method('find')->willReturn($user2);

        $request = self::createRequest()
            ->withAttribute('user', $user1)
            ->withAttribute('user_id', $user2->id());

        $handler  = new Masquerade($user_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
        self::assertSame($user2->id(), Auth::id());
        self::assertSame('1', Session::get('masquerade'));
    }

    /**
     * @return void
     */
    public function testCannotMasqueradeAsSelf(): void
    {
        $user = self::createMock(User::class);
        $user->method('id')->willReturn(1);

        $user_service = self::createMock(UserService::class);
        $user_service->expects(self::once())->method('find')->willReturn($user);

        $request = self::createRequest()
            ->withAttribute('user', $user)
            ->withAttribute('user_id', $user->id());

        $handler  = new Masquerade($user_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
        self::assertNull(Session::get('masquerade'));
    }

    /**
     * @return void
     */
    public function testMasqueradeAsNonExistingUser(): void
    {
        $this->expectException(HttpNotFoundException::class);
        $this->expectExceptionMessage('User ID 2 not found');

        $user = self::createMock(User::class);
        $user->method('id')->willReturn(1);

        $user_service = self::createMock(UserService::class);
        $user_service->expects(self::once())->method('find')->willReturn(null);

        $request = self::createRequest()
            ->withAttribute('user', $user)
            ->withAttribute('user_id', 2);

        $handler = new Masquerade($user_service);
        $handler->handle($request);
    }
}
