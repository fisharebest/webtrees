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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\User;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\DeleteUser
 */
class DeleteUserTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @return void
     */
    public function testDeleteUser(): void
    {
        $user = $this->createMock(User::class);
        $user->method('id')->willReturn(1);

        $user_service = $this->createMock(UserService::class);
        $user_service->expects(self::once())->method('find')->willReturn($user);

        $request  = self::createRequest()
            ->withAttribute('user_id', $user->id());
        $handler  = new DeleteUser($user_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDeleteNonExistingUser(): void
    {
        $this->expectException(HttpNotFoundException::class);
        $this->expectExceptionMessage('User ID 98765 not found');

        $user_service = $this->createMock(UserService::class);
        $user_service->expects(self::once())->method('find')->willReturn(null);

        $request  = self::createRequest()
            ->withAttribute('user_id', 98765);
        $handler  = new DeleteUser($user_service);
        $handler->handle($request);
    }

    /**
     * @return void
     */
    public function testCannotDeleteAdministrator(): void
    {
        $this->expectException(HttpAccessDeniedException::class);
        $this->expectExceptionMessage('Cannot delete an administrator');

        $user = $this->createMock(User::class);
        $user->method('id')->willReturn(1);
        $user->expects(self::once())->method('getPreference')->with(UserInterface::PREF_IS_ADMINISTRATOR)->willReturn('1');

        $user_service = $this->createMock(UserService::class);
        $user_service->expects(self::once())->method('find')->willReturn($user);

        $request  = self::createRequest()
            ->withAttribute('user_id', $user->id());
        $handler = new DeleteUser($user_service);
        $handler->handle($request);
    }
}
