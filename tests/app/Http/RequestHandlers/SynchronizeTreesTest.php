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
use Fisharebest\Webtrees\Services\AdminService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\StreamFactoryInterface;

#[CoversClass(SynchronizeTrees::class)]
class SynchronizeTreesTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(SynchronizeTrees::class));
    }

    public function testHandleWithNoGedcomFilesRedirects(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        // Create at least one tree so the final redirect has a target
        $tree_service->create('sync-test', 'Sync Test');

        $admin_service = $this->createMock(AdminService::class);
        $admin_service->expects(self::once())
            ->method('gedcomFiles')
            ->willReturn(new Collection());

        $stream_factory  = self::createStub(StreamFactoryInterface::class);
        $timeout_service = self::createStub(TimeoutService::class);

        $handler  = new SynchronizeTrees($admin_service, $stream_factory, $timeout_service, $tree_service);
        $request  = self::createRequest();
        $response = $handler->handle($request);

        // Redirects to ManageTrees after processing
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
