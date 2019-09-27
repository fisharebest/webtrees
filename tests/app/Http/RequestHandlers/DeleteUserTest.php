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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\DeleteUser
 */
class DeleteUserTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testDeleteUser(): void
    {
        $user_service = new UserService();
        $user         = $user_service->create('user1', 'real1', 'email1', 'pass1');
        $request      = self::createRequest('POST', ['route' => 'delete-user'], ['user_id' => $user->id()]);
        $response     = app(DeleteUser::class)->handle($request);

        // UserService caches user records
        app('cache.array')->forget(UserService::class . $user->id());

        self::assertSame(self::STATUS_NO_CONTENT, $response->getStatusCode());
        self::assertNull($user_service->find($user->id()));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage User ID 98765 not found
     * @return void
     */
    public function testDeleteNonExistingUser(): void
    {
        $request = self::createRequest('POST', ['route' => 'delete-user'], ['user_id' => 98765]);
        app(DeleteUser::class)->handle($request);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     * @expectedExceptionMessage Cannot delete an administrator
     * @return void
     */
    public function testCannotDeleteAdministrator(): void
    {
        $user = app(UserService::class)->create('user1', 'real1', 'email1', 'pass1');
        $user->setPreference('canadmin', '1');
        $request = self::createRequest('POST', ['route' => 'delete-user'], ['user_id' => $user->id()]);
        app(DeleteUser::class)->handle($request);
    }
}
