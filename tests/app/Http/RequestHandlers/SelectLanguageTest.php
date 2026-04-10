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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\GuestUser;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SelectLanguage::class)]
class SelectLanguageTest extends TestCase
{
    public function testSelectLanguageForGuest(): void
    {
        $user = $this->createMock(GuestUser::class);
        $user->expects(self::once())
            ->method('setPreference')
            ->with(UserInterface::PREF_LANGUAGE, 'fr');

        $handler  = new SelectLanguage();
        $request  = self::createRequest()
            ->withAttribute('user', $user)
            ->withAttribute('language', 'fr');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
        self::assertSame('fr', Session::get('language'));
    }

    public function testSelectLanguageForUser(): void
    {
        $user = $this->createMock(User::class);
        $user->expects(self::once())
            ->method('setPreference')
            ->with(UserInterface::PREF_LANGUAGE, 'de');

        $handler  = new SelectLanguage();
        $request  = self::createRequest()
            ->withAttribute('user', $user)
            ->withAttribute('language', 'de');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
        self::assertSame('de', Session::get('language'));
    }
}
