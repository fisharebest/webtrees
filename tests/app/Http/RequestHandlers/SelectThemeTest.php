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

use Fisharebest\Webtrees\GuestUser;
use Fisharebest\Webtrees\Http\RequestHandlers\SelectTheme;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\SelectTheme
 */
class SelectThemeTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testSelectThemeForGuest(): void
    {
        $user     = new GuestUser();
        $handler  = new SelectTheme($user);
        $request  = self::createRequest('POST', ['route' => 'theme'], ['theme' => 'FOO']);
        $response = $handler->handle($request);

        self::assertSame(self::STATUS_NO_CONTENT, $response->getStatusCode());
        self::assertSame('FOO', $user->getPreference('theme'));
    }

    /**
     * @return void
     */
    public function testSelectThemeForUser(): void
    {
        $user_service = new UserService();
        $user         = $user_service->create('user', 'real', 'email', 'pass');
        $handler      = new SelectTheme($user);
        $request      = self::createRequest('POST', ['route' => 'theme'], ['theme' => 'FOO']);
        $response     = $handler->handle($request);

        self::assertSame(self::STATUS_NO_CONTENT, $response->getStatusCode());
        self::assertSame('FOO', $user->getPreference('theme'));
    }
}

