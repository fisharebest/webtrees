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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\TestCase;

use function app;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\MasqueradeAsUser
 */
class MasqueradeAsUserTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function xtestMasqueradeAsUser(): void
    {
        $user1 = app(UserService::class)->create('user1', 'real1', 'email1', 'pass1');
        $user2 = app(UserService::class)->create('user2', 'real2', 'email2', 'pass2');

        $request  = self::createRequest('POST', ['route' => 'masquerade'], ['user_id' => $user2->id()])
            ->withAttribute('user', $user1);
        $response = app(MasqueradeAsUser::class)->handle($request);

        self::assertSame(self::STATUS_NO_CONTENT, $response->getStatusCode());
        self::assertSame($user2->id(), Auth::id());
        self::assertSame('1', Session::get('masquerade'));
    }

    /**
     * @return void
     */
    public function testCannotMasqueradeAsSelf(): void
    {
        $user = app(UserService::class)->create('user', 'real', 'email', 'pass');
        Auth::login($user);

        $request  = self::createRequest('POST', ['route' => 'masquerade'], ['user_id' => $user->id()])
            ->withAttribute('user', $user);
        $response = app(MasqueradeAsUser::class)->handle($request);

        self::assertSame(self::STATUS_NO_CONTENT, $response->getStatusCode());
        self::assertSame($user->id(), Auth::id());
        self::assertNull(Session::get('masquerade'));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage User ID 98765 not found
     * @return void
     */
    public function xtestMasqueradeAsNonExistingUser(): void
    {
        $request = self::createRequest('POST', ['route' => 'masquerade'], ['user_id' => 98765]);
        app(MasqueradeAsUser::class)->handle($request);
    }
}
