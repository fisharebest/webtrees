<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Factories\IndividualFactory;
use Fisharebest\Webtrees\Http\Exceptions\HttpGoneException;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\InteractiveTreeModule;
use Fisharebest\Webtrees\Module\PedigreeMapModule;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RedirectModulePhp::class)]
class RedirectModulePhpTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testRedirectPedigreeMap(): void
    {
        $tree = $this->createMock(Tree::class);
        $tree
            ->method('name')
            ->willReturn('tree1');

        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects(self::once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $individual = $this->createMock(Individual::class);

        $individual_factory = $this->createMock(IndividualFactory::class);
        $individual_factory
            ->expects(self::once())
            ->method('make')
            ->with('X123', $tree)
            ->willReturn($individual);

        Registry::individualFactory($individual_factory);

        $module = $this->createMock(PedigreeMapModule::class);
        $module
            ->expects(self::once())
            ->method('chartUrl')
            ->willReturn('https://www.example.com');

        $module_service = $this->createMock(ModuleService::class);
        $module_service
            ->expects(self::once())
            ->method('findByInterface')
            ->with(PedigreeMapModule::class)
            ->willReturn(new Collection([$module]));

        $handler = new RedirectModulePhp($module_service, $tree_service);

        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            ['mod' => 'googlemap', 'mod_action' => 'pedigree_map', 'ged' => 'tree1', 'rootid' => 'X123']
        );

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
        self::assertSame('https://www.example.com', $response->getHeaderLine('Location'));
    }

    public function testRedirectInteractiveTree(): void
    {
        $tree = $this->createMock(Tree::class);
        $tree
            ->method('name')
            ->willReturn('tree1');

        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects(self::once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $individual = $this->createMock(Individual::class);

        $individual_factory = $this->createMock(IndividualFactory::class);
        $individual_factory
            ->expects(self::once())
            ->method('make')
            ->with('X123', $tree)
            ->willReturn($individual);

        Registry::individualFactory($individual_factory);

        $module = $this->createMock(InteractiveTreeModule::class);
        $module
            ->expects(self::once())
            ->method('chartUrl')
            ->willReturn('https://www.example.com');

        $module_service = $this->createMock(ModuleService::class);
        $module_service
            ->expects(self::once())
            ->method('findByInterface')
            ->with(InteractiveTreeModule::class)
            ->willReturn(new Collection([$module]));

        $handler = new RedirectModulePhp($module_service, $tree_service);

        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            ['mod' => 'tree', 'mod_action' => 'treeview', 'ged' => 'tree1', 'rootid' => 'X123']
        );

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
        self::assertSame('https://www.example.com', $response->getHeaderLine('Location'));
    }


    public function testNoSuchTree(): void
    {
        $module_service  = $this->createMock(ModuleService::class);
        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects(self::once())
            ->method('all')
            ->willReturn(new Collection([]));

        $handler = new RedirectModulePhp($module_service, $tree_service);

        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            ['ged' => 'tree1', 'rootid' => 'X123']
        );

        $this->expectException(HttpGoneException::class);

        $handler->handle($request);
    }

    public function testNoSuchIndividual(): void
    {
        $tree = $this->createMock(Tree::class);
        $tree
            ->method('name')
            ->willReturn('tree1');

        $individual_factory = $this->createMock(IndividualFactory::class);
        $individual_factory
            ->expects(self::once())
            ->method('make')
            ->with('X123', $tree)
            ->willReturn(null);

        Registry::individualFactory($individual_factory);
        $module_service  = $this->createMock(ModuleService::class);
        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects(self::once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $handler = new RedirectModulePhp($module_service, $tree_service);

        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            ['ged' => 'tree1', 'rootid' => 'X123']
        );

        $this->expectException(HttpGoneException::class);

        $handler->handle($request);
    }

    public function testPedigreeMapModuleDisabled(): void
    {
        $tree = $this->createMock(Tree::class);
        $tree
            ->method('name')
            ->willReturn('tree1');

        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects(self::once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $individual = $this->createMock(Individual::class);

        $individual_factory = $this->createMock(IndividualFactory::class);
        $individual_factory
            ->expects(self::once())
            ->method('make')
            ->with('X123', $tree)
            ->willReturn($individual);

        Registry::individualFactory($individual_factory);

        $module_service = $this->createMock(ModuleService::class);
        $module_service
            ->expects(self::once())
            ->method('findByInterface')
            ->with(PedigreeMapModule::class)
            ->willReturn(new Collection([]));

        $handler = new RedirectModulePhp($module_service, $tree_service);

        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            ['mod' => 'googlemap', 'mod_action' => 'pedigree_map', 'ged' => 'tree1', 'rootid' => 'X123']
        );

        $this->expectException(HttpGoneException::class);

        $handler->handle($request);
    }

    public function testInteractiveTreeModuleDisabled(): void
    {
        $tree = $this->createMock(Tree::class);
        $tree
            ->method('name')
            ->willReturn('tree1');

        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects(self::once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $individual = $this->createMock(Individual::class);

        $individual_factory = $this->createMock(IndividualFactory::class);
        $individual_factory
            ->expects(self::once())
            ->method('make')
            ->with('X123', $tree)
            ->willReturn($individual);

        Registry::individualFactory($individual_factory);

        $module_service = $this->createMock(ModuleService::class);
        $module_service
            ->expects(self::once())
            ->method('findByInterface')
            ->with(InteractiveTreeModule::class)
            ->willReturn(new Collection([]));

        $handler = new RedirectModulePhp($module_service, $tree_service);

        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            ['mod' => 'tree', 'mod_action' => 'treeview', 'ged' => 'tree1', 'rootid' => 'X123']
        );

        $this->expectException(HttpGoneException::class);

        $handler->handle($request);
    }
}
