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
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PendingChangesLogPage::class)]
class PendingChangesLogPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(PendingChangesLogPage::class));
    }

    public function testHandleReturnsOkResponse(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('id')->willReturn(1);

        $tree_service = self::createStub(TreeService::class);
        $tree_service->method('titles')->willReturn([]);

        $user = self::createStub(User::class);
        $user->method('userName')->willReturn('admin');
        $user->method('getPreference')
            ->willReturn('UTC');

        $user_service = self::createStub(UserService::class);
        $user_service->method('all')->willReturn(new Collection([$user]));

        $handler  = new PendingChangesLogPage($tree_service, $user_service);
        $request  = self::createRequest()
            ->withAttribute('tree', $tree);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
