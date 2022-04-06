<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Module\PlaceHierarchyListModule;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\RedirectPlaceListPhp
 */
class RedirectPlaceListPhpTest extends TestCase
{
    /**
     * @return void
     */
    public function testRedirect(): void
    {
        $tree = $this->createStub(Tree::class);
        $tree
            ->method('name')
            ->willReturn('tree1');

        $tree_service = $this->createStub(TreeService::class);
        $tree_service
            ->expects(self::once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $module = $this->createStub(PlaceHierarchyListModule::class);
        $module
            ->expects(self::once())
            ->method('listUrl')
            ->willReturn('https://www.example.com');

        $module_service = $this->createStub(ModuleService::class);
        $module_service
            ->expects(self::once())
            ->method('findByInterface')
            ->with(PlaceHierarchyListModule::class)
            ->willReturn(new Collection([$module]));

        $handler = new RedirectPlaceListPhp($module_service, $tree_service);

        $request = self::createRequest(RequestMethodInterface::METHOD_GET, ['ged' => 'tree1']);

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
        self::assertSame('https://www.example.com', $response->getHeaderLine('Location'));
    }

    /**
     * @return void
     */
    public function testModuleDisabled(): void
    {
        $module_service = $this->createStub(ModuleService::class);
        $module_service
            ->expects(self::once())->method('findByInterface')
            ->with(PlaceHierarchyListModule::class)
            ->willReturn(new Collection());

        $tree = $this->createStub(Tree::class);

        $tree_service = $this->createStub(TreeService::class);
        $tree_service
            ->expects(self::once())
            ->method('all')
            ->willReturn(new Collection([$tree]));

        $handler = new RedirectPlaceListPhp($module_service, $tree_service);

        $request = self::createRequest(RequestMethodInterface::METHOD_GET, ['ged' => 'tree1']);

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }

    /**
     * @return void
     */
    public function testNoSuchTree(): void
    {
        $module = $this->createStub(PlaceHierarchyListModule::class);

        $module_service = $this->createStub(ModuleService::class);
        $module_service
            ->expects(self::once())
            ->method('findByInterface')
            ->with(PlaceHierarchyListModule::class)
            ->willReturn(new Collection([$module]));

        $tree_service = $this->createStub(TreeService::class);
        $tree_service
            ->expects(self::once())
            ->method('all')
            ->willReturn(new Collection([]));

        $handler = new RedirectPlaceListPhp($module_service, $tree_service);

        $request = self::createRequest(RequestMethodInterface::METHOD_GET, ['ged' => 'tree1']);

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }
}
