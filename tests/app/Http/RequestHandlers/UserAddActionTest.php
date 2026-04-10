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

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserAddAction::class)]
class UserAddActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(UserAddAction::class));
    }

    public function testHandleCreatesNewUser(): void
    {
        $user_service = new UserService();
        $handler      = new UserAddAction($user_service);
        $request      = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'username'  => 'newuser1',
            'email'     => 'newuser1@example.com',
            'real_name' => 'New User One',
            'password'  => 'Secret1234',
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());

        // Verify user was actually created
        $created = $user_service->findByUserName('newuser1');
        self::assertNotNull($created);
        self::assertSame('New User One', $created->realName());
        self::assertSame('newuser1@example.com', $created->email());
    }

    public function testHandleRedirectsOnDuplicateUsername(): void
    {
        $user_service = new UserService();
        $user_service->create('existinguser', 'Existing User', 'existing@example.com', 'existpass');

        $handler  = new UserAddAction($user_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'username'  => 'existinguser',
            'email'     => 'different@example.com',
            'real_name' => 'Another User',
            'password'  => 'Secret1234',
        ]);
        $response = $handler->handle($request);

        // Redirects back to add page with prefilled params
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertStringContainsString('username', $response->getHeaderLine('Location'));
    }

    public function testHandleRedirectsOnDuplicateEmail(): void
    {
        $user_service = new UserService();
        $user_service->create('emailowner', 'Email Owner', 'taken@example.com', 'ownerpass');

        $handler  = new UserAddAction($user_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'username'  => 'differentuser',
            'email'     => 'taken@example.com',
            'real_name' => 'Different User',
            'password'  => 'Secret1234',
        ]);
        $response = $handler->handle($request);

        // Redirects back to add page
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertStringContainsString('email', $response->getHeaderLine('Location'));
    }

    public function testHandleRedirectsOnBothDuplicateUsernameAndEmail(): void
    {
        $user_service = new UserService();
        $user_service->create('dupboth', 'Dup Both', 'dupboth@example.com', 'duppass');

        $handler  = new UserAddAction($user_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'username'  => 'dupboth',
            'email'     => 'dupboth@example.com',
            'real_name' => 'Dup Both Again',
            'password'  => 'Secret1234',
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
