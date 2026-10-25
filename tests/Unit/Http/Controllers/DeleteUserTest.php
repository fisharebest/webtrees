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
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Http\Controllers\DeleteUser;
use Fisharebest\Webtrees\Http\Exceptions\HttpForbiddenException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DeleteUser::class)]
class DeleteUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::createDatabase();
    }

    public function testDeleteUser(): void
    {
        $user = self::createStub(User::class);
        $user->method('id')->willReturn(1);

        $user_service = $this->createMock(UserService::class);
        $user_service->expects($this->once())->method('find')->willReturn($user);

        $request    = self::createRequest()
            ->withAttribute('user_id', $user->id());
        $controller = new DeleteUser($user_service);
        $response   = $controller->post($request);

        self::assertSame(HttpStatusCode::NoContent->value, $response->getStatusCode());
    }

    public function testDeleteNonExistingUser(): void
    {
        $this->expectException(HttpNotFoundException::class);
        $this->expectExceptionMessage('You do not have permission to view this page.');

        $user_service = $this->createMock(UserService::class);
        $user_service->expects($this->once())->method('find')->willReturn(null);

        $request    = self::createRequest()
            ->withAttribute('user_id', 98765);
        $controller = new DeleteUser($user_service);
        $controller->post($request);
    }

    public function testCannotDeleteAdministrator(): void
    {
        $this->expectException(HttpForbiddenException::class);
        $this->expectExceptionMessage('You do not have permission to view this page.');

        $user = $this->createMock(User::class);
        $user->method('id')->willReturn(1);
        $user->expects($this->once())->method('getPreference')->with(UserInterface::PREF_IS_ADMINISTRATOR)->willReturn('1');

        $user_service = $this->createMock(UserService::class);
        $user_service->expects($this->once())->method('find')->willReturn($user);

        $request    = self::createRequest()
            ->withAttribute('user_id', $user->id());
        $controller = new DeleteUser($user_service);
        $controller->post($request);
    }
}
