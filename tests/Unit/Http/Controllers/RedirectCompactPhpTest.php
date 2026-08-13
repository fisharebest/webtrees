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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Controllers;

use Fisharebest\Webtrees\Enums\HttpRequestMethod;
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Factories\IndividualFactory;
use Fisharebest\Webtrees\Http\Exceptions\HttpGoneException;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\CompactTreeChartModule;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Http\Controllers\RedirectCompactPhp;

#[CoversClass(RedirectCompactPhp::class)]
class RedirectCompactPhpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::createDatabase();
    }

    public function testRedirect(): void
    {
        $tree = self::createStub(Tree::class);
        $tree
            ->method('name')
            ->willReturn('tree1');

        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects($this->once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $individual = self::createStub(Individual::class);

        $individual_factory = $this->createMock(IndividualFactory::class);
        $individual_factory
            ->expects($this->once())
            ->method('make')
            ->with('X123', $tree)
            ->willReturn($individual);

        Registry::individualFactory($individual_factory);

        $module = $this->createMock(CompactTreeChartModule::class);
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

        $handler = new RedirectCompactPhp($module_service, $tree_service);

        $request = self::createRequest(HttpRequestMethod::GET->value, ['ged' => 'tree1', 'rootid' => 'X123']);

        $response = $handler->get($request);

        self::assertSame(HttpStatusCode::MovedPermanently->value, $response->getStatusCode());
        self::assertSame('https://www.example.com', $response->getHeaderLine('Location'));
    }

    public function testModuleDisabled(): void
    {
        $module_service = $this->createMock(ModuleService::class);
        $module_service
            ->expects($this->once())->method('findByComponent')
            ->with(ModuleChartInterface::class)
            ->willReturn(new Collection());

        $tree = self::createStub(Tree::class);

        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects($this->once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $handler = new RedirectCompactPhp($module_service, $tree_service);

        $request = self::createRequest(HttpRequestMethod::GET->value, ['ged' => 'tree1', 'rootid' => 'X123']);

        $this->expectException(HttpGoneException::class);

        $handler->get($request);
    }

    public function testNoSuchTree(): void
    {
        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects($this->once())
            ->method('all')
            ->willReturn(new Collection([]));

        $module_service = self::createStub(ModuleService::class);

        $handler = new RedirectCompactPhp($module_service, $tree_service);

        $request = self::createRequest(HttpRequestMethod::GET->value, ['ged' => 'tree1', 'rootid' => 'X123']);

        $this->expectException(HttpGoneException::class);

        $handler->get($request);
    }
}
