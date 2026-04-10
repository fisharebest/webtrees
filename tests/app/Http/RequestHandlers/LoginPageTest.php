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
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LoginPage::class)]
class LoginPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testLoginPage(): void
    {
        $tree_service = $this->createMock(TreeService::class);
        $tree_service->method('all')->willReturn(new Collection());

        $handler  = new LoginPage($tree_service);
        $request  = self::createRequest();
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testLoginPageAlreadyLoggedIn(): void
    {
        $tree_service = $this->createMock(TreeService::class);

        $user    = self::createStub(User::class);
        $handler = new LoginPage($tree_service);
        $request = self::createRequest()->withAttribute('user', $user);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testLoginPageWithTree(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('name')->willReturn('demo');

        $tree_service = $this->createMock(TreeService::class);

        $handler  = new LoginPage($tree_service);
        $request  = self::createRequest()
            ->withAttribute('tree', $tree);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testLoginPageWithNoTreeRedirectsToDefault(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('name')->willReturn('default');

        $tree_service = $this->createMock(TreeService::class);
        $tree_service->method('all')->willReturn(new Collection(['default' => $tree]));

        $handler  = new LoginPage($tree_service);
        $request  = self::createRequest();
        $response = $handler->handle($request);

        // Redirects to login page with tree parameter
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
