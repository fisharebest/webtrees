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
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ModulesMenusAction::class)]
class ModulesMenusActionTest extends TestCase
{

    public function testClass(): void
    {
        self::assertTrue(class_exists(ModulesMenusAction::class));
    }

    public function testHandleUpdatesAndRedirects(): void
    {
        $module_service = $this->createMock(ModuleService::class);
        // updateStatus + updateOrder + updateAccessLevel = 3 calls to findByInterface
        $module_service->expects(self::exactly(3))
            ->method('findByInterface')
            ->willReturn(new Collection());

        $tree_service = $this->createMock(TreeService::class);
        $tree_service->expects(self::once())
            ->method('all')
            ->willReturn(new Collection());

        $handler  = new ModulesMenusAction($module_service, $tree_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], ['order' => []]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
