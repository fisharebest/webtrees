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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\GuestUser;
use Fisharebest\Webtrees\Http\Controllers\SelectLanguage;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SelectLanguage::class)]
class SelectLanguageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::createDatabase();
    }

    public function testSelectLanguageForGuest(): void
    {
        $user       = new GuestUser();
        $controller = new SelectLanguage($user);
        $response   = $controller->post('fr');

        self::assertSame(HttpStatusCode::NoContent->value, $response->getStatusCode());
    }

    public function testSelectLanguageForUser(): void
    {
        $user_service = new UserService(new SystemClock());
        $user         = $user_service->create('user', 'real', 'email', 'pass');
        Auth::login($user);
        $controller = new SelectLanguage($user);
        $response   = $controller->post('fr');

        self::assertSame(HttpStatusCode::NoContent->value, $response->getStatusCode());
    }
}
