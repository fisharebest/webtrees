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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpGoneException;
use Fisharebest\Webtrees\Module\LifespansChartModule;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RedirectLifeSpanPhp::class)]
class RedirectLifeSpanPhpTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testRedirect(): void
    {
        $tree = $this->createMock(Tree::class);
        $tree
            ->method('name')
            ->willReturn('tree1');

        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects($this->once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $module = $this->createMock(LifespansChartModule::class);
        $module
            ->expects($this->once())
            ->method('chartUrl')
            ->willReturn('https://www.example.com');

        $module_service = $this->createMock(ModuleService::class);
        $module_service
            ->expects($this->once())
            ->method('findByComponent')
            ->with(ModuleChartInterface::class)
            ->willReturn(new Collection([$module]));

        $handler = new RedirectLifeSpanPhp($module_service, $tree_service);

        $request = self::createRequest(RequestMethodInterface::METHOD_GET, ['ged' => 'tree1', 'rootid' => 'X123']);

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
        self::assertSame('https://www.example.com', $response->getHeaderLine('Location'));
    }

    public function testModuleDisabled(): void
    {
        $module_service = $this->createMock(ModuleService::class);
        $module_service
            ->expects($this->once())->method('findByComponent')
            ->with(ModuleChartInterface::class)
            ->willReturn(new Collection());

        $tree = $this->createMock(Tree::class);

        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects($this->once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $handler = new RedirectLifeSpanPhp($module_service, $tree_service);

        $request = self::createRequest(RequestMethodInterface::METHOD_GET, ['ged' => 'tree1', 'rootid' => 'X123']);

        $this->expectException(HttpGoneException::class);

        $handler->handle($request);
    }

    public function testNoSuchTree(): void
    {
        $module_service = $this->createMock(ModuleService::class);

        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects($this->once())
            ->method('all')
            ->willReturn(new Collection([]));

        $handler = new RedirectLifeSpanPhp($module_service, $tree_service);

        $request = self::createRequest(RequestMethodInterface::METHOD_GET, ['ged' => 'tree1', 'rootid' => 'X123']);

        $this->expectException(HttpGoneException::class);

        $handler->handle($request);
    }
}
