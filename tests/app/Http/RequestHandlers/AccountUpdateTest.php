<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AccountUpdate::class)]
class AccountUpdateTest extends TestCase
{
    public function testHandler(): void
    {
        $user_service = $this->createMock(UserService::class);

        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('setEmail')->with('b');
        $user->expects($this->once())->method('setPassword')->with('e');
        $user->expects($this->once())->method('setRealName')->with('d');
        $user->expects($this->once())->method('setUserName')->with('h');
        $user->expects($this->once())->method('setSecret')->with('j');
        $user->expects(self::exactly(5))
            ->method('setPreference')
            ->with(
                self::withConsecutive([UserInterface::PREF_CONTACT_METHOD, UserInterface::PREF_LANGUAGE, UserInterface::PREF_TIME_ZONE, UserInterface::PREF_IS_VISIBLE_ONLINE, UserInterface::PREF_IS_STATUS_MFA]),
                self::withConsecutive(['a', 'c', 'g', '', ''])
            );

        $tree = $this->createMock(Tree::class);
        $tree->expects($this->once())->method('setUserPreference')->with($user, UserInterface::PREF_TREE_DEFAULT_XREF, 'f');

        $handler  = new AccountUpdate($user_service);
        $request  = self::createRequest()
            ->withAttribute('tree', $tree)
            ->withAttribute('user', $user)
            ->withParsedBody([
                'contact-method' => 'a',
                'email'          => 'b',
                'language'       => 'c',
                'real_name'      => 'd',
                'password'       => 'e',
                'default-xref'   => 'f',
                'timezone'       => 'g',
                'user_name'      => 'h',
                'visible-online' => 'i',
                'secret'         => 'j',
                'status-mfa'     => 'k',
            ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
