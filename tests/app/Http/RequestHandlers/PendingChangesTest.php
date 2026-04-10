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
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PendingChanges::class)]
class PendingChangesTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(PendingChanges::class));
    }

    public function testHandleReturnsOkResponse(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('name')->willReturn('test');
        $tree->method('id')->willReturn(1);

        $pending_changes_service = self::createStub(PendingChangesService::class);
        $pending_changes_service->method('pendingXrefs')->willReturn(new Collection());
        $pending_changes_service->method('pendingChanges')->willReturn([]);

        $handler  = new PendingChanges($pending_changes_service);
        $request  = self::createRequest('GET', ['url' => 'https://webtrees.test/'])
            ->withAttribute('tree', $tree);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleWithCustomLimit(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('name')->willReturn('test');
        $tree->method('id')->willReturn(1);

        $pending_changes_service = $this->createMock(PendingChangesService::class);
        $pending_changes_service->expects(self::once())
            ->method('pendingXrefs')
            ->with($tree)
            ->willReturn(new Collection());
        $pending_changes_service->expects(self::once())
            ->method('pendingChanges')
            ->with($tree, 50)
            ->willReturn([]);

        $handler  = new PendingChanges($pending_changes_service);
        $request  = self::createRequest('GET', ['n' => '50', 'url' => 'https://webtrees.test/'])
            ->withAttribute('tree', $tree);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
