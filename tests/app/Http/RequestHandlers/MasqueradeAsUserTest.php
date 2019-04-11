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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\RequestHandlers\MasqueradeAsUser;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\TestCase;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\MasqueradeAsUser
 */
class MasqueradeAsUserTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testMasqueradeAsUser(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('user1', 'real1', 'email1', 'pass1');
        $user2        = $user_service->create('user2', 'real2', 'email2', 'pass2');
        Auth::login($user1);
        $handler  = new MasqueradeAsUser($user1, $user_service);
        $request  = self::createRequest('POST', ['route' => 'masquerade'], ['user_id' => $user2->id()]);
        $response = $handler->handle($request);

        self::assertSame(self::STATUS_NO_CONTENT, $response->getStatusCode());
        self::assertSame($user2->id(), Auth::id());
        self::assertSame('1', Session::get('masquerade'));
    }

    /**
     * @return void
     */
    public function testMasqueradeAsSelf(): void
    {
        $user_service = new UserService();
        $user         = $user_service->create('user', 'real', 'email', 'pass');
        Auth::login($user);
        $handler  = new MasqueradeAsUser($user, $user_service);
        $request  = self::createRequest('POST', ['route' => 'masquerade'], ['user_id' => $user->id()]);
        $response = $handler->handle($request);

        self::assertSame(self::STATUS_NO_CONTENT, $response->getStatusCode());
        self::assertSame($user->id(), Auth::id());
        self::assertNull(Session::get('masquerade'));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage User ID 98765 not found
     * @return void
     */
    public function testMasqueradeAsNonExistingUser(): void
    {
        $user_service = new UserService();
        $user         = $user_service->create('user', 'real', 'email', 'pass');
        Auth::login($user);
        $handler = new MasqueradeAsUser($user, $user_service);
        $request = self::createRequest('POST', ['route' => 'masquerade'], ['user_id' => 98765]);
        $handler->handle($request);
    }
}

