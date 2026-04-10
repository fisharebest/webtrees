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
use Fisharebest\Webtrees\Module\SiteMapModule;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RobotsTxt::class)]
class RobotsTxtTest extends TestCase
{

    public function testClass(): void
    {
        self::assertTrue(class_exists(RobotsTxt::class));
    }

    public function testHandleReturnsPlainText(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('name')->willReturn('tree1');

        $tree_service = $this->createMock(TreeService::class);
        $tree_service->expects(self::once())
            ->method('all')
            ->willReturn(new Collection([$tree]));

        $module_service = $this->createMock(ModuleService::class);
        $module_service->expects(self::once())
            ->method('findByInterface')
            ->with(SiteMapModule::class)
            ->willReturn(new Collection());

        $handler  = new RobotsTxt($module_service, $tree_service);
        $request  = self::createRequest();
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('text/plain', $response->getHeaderLine('content-type'));

        $body = (string) $response->getBody();
        self::assertStringContainsString('User-agent:', $body);
    }

    public function testHandleWithNoTrees(): void
    {
        $tree_service = $this->createMock(TreeService::class);
        $tree_service->expects(self::once())
            ->method('all')
            ->willReturn(new Collection());

        $module_service = $this->createMock(ModuleService::class);
        $module_service->expects(self::once())
            ->method('findByInterface')
            ->with(SiteMapModule::class)
            ->willReturn(new Collection());

        $handler  = new RobotsTxt($module_service, $tree_service);
        $request  = self::createRequest();
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleWithMultipleTrees(): void
    {
        $tree1 = self::createStub(Tree::class);
        $tree1->method('name')->willReturn('tree1');
        $tree2 = self::createStub(Tree::class);
        $tree2->method('name')->willReturn('tree2');

        $tree_service = $this->createMock(TreeService::class);
        $tree_service->expects(self::once())
            ->method('all')
            ->willReturn(new Collection([$tree1, $tree2]));

        $module_service = $this->createMock(ModuleService::class);
        $module_service->expects(self::once())
            ->method('findByInterface')
            ->with(SiteMapModule::class)
            ->willReturn(new Collection());

        $handler  = new RobotsTxt($module_service, $tree_service);
        $request  = self::createRequest();
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('text/plain', $response->getHeaderLine('content-type'));
    }
}
