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
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PendingChangesAcceptTree::class)]
class PendingChangesAcceptTreeTest extends TestCase
{

    public function testClass(): void
    {
        self::assertTrue(class_exists(PendingChangesAcceptTree::class));
    }

    public function testHandleAcceptsTreeChanges(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('id')->willReturn(1);
        $tree->method('title')->willReturn('Test Tree');

        $pending_changes_service = $this->createMock(PendingChangesService::class);
        $pending_changes_service
            ->expects(self::once())
            ->method('acceptTree')
            ->with($tree, 25);

        $handler = new PendingChangesAcceptTree($pending_changes_service);
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, ['n' => '25'])
            ->withAttribute('tree', $tree);

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }
}
