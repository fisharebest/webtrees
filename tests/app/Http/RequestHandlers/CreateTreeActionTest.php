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
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreateTreeAction::class)]
class CreateTreeActionTest extends TestCase
{

    public function testClass(): void
    {
        self::assertTrue(class_exists(CreateTreeAction::class));
    }

    public function testHandleCreatesNewTree(): void
    {
        $new_tree = self::createStub(Tree::class);
        $new_tree->method('name')->willReturn('new-tree');

        $tree_service = $this->createMock(TreeService::class);
        $tree_service->expects(self::once())
            ->method('all')
            ->willReturn(new Collection());
        $tree_service->expects(self::once())
            ->method('create')
            ->with('new-tree', 'New Tree Title')
            ->willReturn($new_tree);

        $handler  = new CreateTreeAction($tree_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'name'  => 'new-tree',
            'title' => 'New Tree Title',
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleRedirectsWhenTreeAlreadyExists(): void
    {
        $existing_tree = self::createStub(Tree::class);

        $tree_service = $this->createMock(TreeService::class);
        $tree_service->expects(self::once())
            ->method('all')
            ->willReturn(new Collection(['existing-tree' => $existing_tree]));
        $tree_service->expects(self::never())
            ->method('create');

        $handler  = new CreateTreeAction($tree_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'name'  => 'existing-tree',
            'title' => 'Existing Tree',
        ]);
        $response = $handler->handle($request);

        // Redirects back to CreateTreePage when a duplicate name is used
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
