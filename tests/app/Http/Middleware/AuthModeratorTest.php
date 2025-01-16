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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\GuestUser;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

/**
 * Test the AuthModerator middleware.
 *
 * @covers \Fisharebest\Webtrees\Http\Middleware\AuthModerator
 */
class AuthModeratorTest extends TestCase
{
    public function testAllowed(): void
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response('lorem ipsum'));

        $user = $this->createMock(User::class);
        $user->method('getPreference')->with(UserInterface::PREF_IS_ADMINISTRATOR)->willReturn('');

        $tree = $this->createMock(Tree::class);
        $tree->method('getUserPreference')->with($user, UserInterface::PREF_TREE_ROLE)->willReturn(UserInterface::ROLE_MODERATOR);

        $request    = self::createRequest()->withAttribute('tree', $tree)->withAttribute('user', $user);
        $middleware = new AuthModerator();
        $response   = $middleware->process($request, $handler);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('lorem ipsum', (string) $response->getBody());
    }

    public function testNotAllowed(): void
    {
        $this->expectException(HttpAccessDeniedException::class);
        $this->expectExceptionMessage('You do not have permission to view this page.');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response('lorem ipsum'));

        $user = $this->createMock(User::class);
        $user->method('getPreference')->with(UserInterface::PREF_IS_ADMINISTRATOR)->willReturn('');

        $tree = $this->createMock(Tree::class);
        $tree->method('getUserPreference')->with($user, UserInterface::PREF_TREE_ROLE)->willReturn(UserInterface::ROLE_EDITOR);

        $request    = self::createRequest()->withAttribute('tree', $tree)->withAttribute('user', $user);
        $middleware = new AuthModerator();

        $middleware->process($request, $handler);
    }

    public function testNotLoggedIn(): void
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response('lorem ipsum'));

        $tree = $this->createMock(Tree::class);

        $request    = self::createRequest()->withAttribute('tree', $tree)->withAttribute('user', new GuestUser());
        $middleware = new AuthModerator();
        $response   = $middleware->process($request, $handler);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
