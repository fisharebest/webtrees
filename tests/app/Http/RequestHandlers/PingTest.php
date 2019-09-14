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

use Fisharebest\Webtrees\Services\ServerCheckService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\Ping
 */
class PingTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testPing(): void
    {
        $handler      = new Ping(new ServerCheckService());
        $request      = self::createRequest('GET');
        $response     = $handler->handle($request);

        self::assertSame(self::STATUS_OK, $response->getStatusCode());
        self::assertSame('OK', (string) $response->getBody());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage User ID 98765 not found
     * @return void
     */
    public function testDeleteNonExistingUser(): void
    {
        $user_service = new UserService();
        $user_service->create('user1', 'real1', 'email1', 'pass1');
        $handler = new DeleteUser($user_service);
        $request = self::createRequest('POST', ['route' => 'delete-user'], ['user_id' => 98765]);
        $handler->handle($request);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     * @expectedExceptionMessage Cannot delete an administrator
     * @return void
     */
    public function testCannotDeleteAdministrator(): void
    {
        $user_service = new UserService();
        $user         = $user_service->create('user1', 'real1', 'email1', 'pass1');
        $user->setPreference('canadmin', '1');
        $handler = new DeleteUser($user_service);
        $request = self::createRequest('POST', ['route' => 'delete-user'], ['user_id' => $user->id()]);
        $handler->handle($request);
    }
}
