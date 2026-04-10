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
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AccountUpdate::class)]
class AccountUpdateTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(AccountUpdate::class));
    }

    public function testHandleUpdatesAllFieldsWithTree(): void
    {
        $user_service = self::createStub(UserService::class);

        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('setEmail')->with('b');
        $user->expects($this->once())->method('setPassword')->with('e');
        $user->expects($this->once())->method('setRealName')->with('d');
        $user->expects($this->once())->method('setUserName')->with('h');
        $user->expects($this->exactly(4))
            ->method('setPreference')
            ->with(
                self::withConsecutive([
                    UserInterface::PREF_CONTACT_METHOD,
                    UserInterface::PREF_LANGUAGE,
                    UserInterface::PREF_TIME_ZONE,
                    UserInterface::PREF_IS_VISIBLE_ONLINE,
                ]),
                self::withConsecutive(['a', 'c', 'g', ''])
            );

        $tree = $this->createMock(Tree::class);
        $tree->expects($this->once())
            ->method('setUserPreference')
            ->with($user, UserInterface::PREF_TREE_DEFAULT_XREF, 'f');

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
            ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleWithoutPasswordSkipsPasswordChange(): void
    {
        $user_service = self::createStub(UserService::class);

        $user = $this->createMock(User::class);
        $user->expects($this->never())->method('setPassword');
        $user->expects($this->once())->method('setEmail')->with('new@example.com');
        $user->expects($this->once())->method('setRealName')->with('New Name');
        $user->expects($this->once())->method('setUserName')->with('newuser');
        $user->expects($this->exactly(4))->method('setPreference');

        $handler  = new AccountUpdate($user_service);
        $request  = self::createRequest()
            ->withAttribute('user', $user)
            ->withParsedBody([
                'contact-method' => 'mailto',
                'email'          => 'new@example.com',
                'language'       => 'en-US',
                'real_name'      => 'New Name',
                'password'       => '',
                'timezone'       => 'UTC',
                'user_name'      => 'newuser',
                'visible-online' => '',
            ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleWithDuplicateEmailDoesNotChangeEmail(): void
    {
        $user_service = new UserService();
        $real_user    = $user_service->create('accupd1', 'Account Update 1', 'accupd1@example.com', 'pass1');
        $other_user   = $user_service->create('accupd2', 'Account Update 2', 'taken@example.com', 'pass2');

        $handler = new AccountUpdate($user_service);
        $request = self::createRequest()
            ->withAttribute('user', $real_user)
            ->withParsedBody([
                'contact-method' => 'mailto',
                'email'          => 'taken@example.com',
                'language'       => 'en-US',
                'real_name'      => 'Account Update 1',
                'password'       => '',
                'timezone'       => 'UTC',
                'user_name'      => 'accupd1',
                'visible-online' => '',
            ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());

        // Email should NOT have changed
        $refreshed = $user_service->findByIdentifier('accupd1');
        self::assertSame('accupd1@example.com', $refreshed->email());
    }

    public function testHandleWithDuplicateUsernameDoesNotChangeName(): void
    {
        $user_service = new UserService();
        $real_user    = $user_service->create('accupd3', 'Account Update 3', 'accupd3@example.com', 'pass3');
        $other_user   = $user_service->create('takenname', 'Taken Name', 'takenname@example.com', 'pass4');

        $handler = new AccountUpdate($user_service);
        $request = self::createRequest()
            ->withAttribute('user', $real_user)
            ->withParsedBody([
                'contact-method' => 'mailto',
                'email'          => 'accupd3@example.com',
                'language'       => 'en-US',
                'real_name'      => 'Account Update 3',
                'password'       => '',
                'timezone'       => 'UTC',
                'user_name'      => 'takenname',
                'visible-online' => '',
            ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());

        // Username should NOT have changed
        $refreshed = $user_service->findByIdentifier('accupd3');
        self::assertNotNull($refreshed);
        self::assertSame('accupd3', $refreshed->userName());
    }

    public function testHandleWithoutTreeSkipsDefaultXref(): void
    {
        $user_service = self::createStub(UserService::class);

        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('setEmail')->with('x@example.com');
        $user->expects($this->once())->method('setRealName')->with('X');
        $user->expects($this->once())->method('setUserName')->with('xuser');
        $user->expects($this->exactly(4))->method('setPreference');

        $handler  = new AccountUpdate($user_service);
        $request  = self::createRequest()
            ->withAttribute('user', $user)
            ->withParsedBody([
                'contact-method' => 'mailto',
                'email'          => 'x@example.com',
                'language'       => 'en-US',
                'real_name'      => 'X',
                'password'       => '',
                'timezone'       => 'UTC',
                'user_name'      => 'xuser',
                'visible-online' => '',
            ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
