<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Module\ModuleAnalyticsInterface;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleConfigInterface;
use Fisharebest\Webtrees\Module\ModuleHistoricEventsInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Module\TreesMenuModule;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;

/**
 * Test the modules
 *
 * @coversNothing
 */
class ModuleServiceTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\Services\ModuleService::all
     * @covers \Fisharebest\Webtrees\Services\ModuleService::coreModules
     * @covers \Fisharebest\Webtrees\Services\ModuleService::customModules
     * @covers \Fisharebest\Webtrees\Services\ModuleService::moduleSorter
     * @return void
     */
    public function testAll(): void
    {
        $tree = Tree::create('name', 'title');
        app()->instance(Tree::class, $tree);

        $module_service = new ModuleService();

        $this->assertNotEmpty($module_service->all());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\ModuleService::findByComponent
     * @covers \Fisharebest\Webtrees\Services\ModuleService::menuSorter
     * @covers \Fisharebest\Webtrees\Services\ModuleService::sidebarSorter
     * @covers \Fisharebest\Webtrees\Services\ModuleService::tabSorter
     * @return void
     */
    public function testFindByComponent(): void
    {
        $tree = Tree::create('name', 'title');
        app()->instance(Tree::class, $tree);

        $module_service = new ModuleService();

        $tree = $this->importTree('demo.ged');
        $user = User::create('UserName', 'RealName', 'user@example.com', 'secret');

        $this->assertNotEmpty($module_service->findByComponent('block', $tree, $user)->all());
        $this->assertNotEmpty($module_service->findByComponent('chart', $tree, $user)->all());
        $this->assertNotEmpty($module_service->findByComponent('menu', $tree, $user)->all());
        $this->assertNotEmpty($module_service->findByComponent('report', $tree, $user)->all());
        $this->assertNotEmpty($module_service->findByComponent('sidebar', $tree, $user)->all());
        $this->assertNotEmpty($module_service->findByComponent('tab', $tree, $user)->all());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\ModuleService::findByInterface
     * @return void
     */
    public function testFindByInterface(): void
    {
        $tree = Tree::create('name', 'title');
        app()->instance(Tree::class, $tree);

        $module_service = new ModuleService();

        $this->assertNotEmpty($module_service->findByInterface(ModuleAnalyticsInterface::class)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleBlockInterface::class)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleChartInterface::class)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleConfigInterface::class)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleMenuInterface::class)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleInterface::class)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleReportInterface::class)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleSidebarInterface::class)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleTabInterface::class)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleThemeInterface::class)->all());

        // THe core modules do not contain any of these.
        $this->assertEmpty($module_service->findByInterface(ModuleHistoricEventsInterface::class)->all());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\ModuleService::findByClass
     * @return void
     */
    public function testFindByClass(): void
    {
        $tree = Tree::create('name', 'title');
        app()->instance(Tree::class, $tree);

        $module_service = new ModuleService();

        $this->assertNull($module_service->findByClass('not-a-valid-class-name'));
        $this->assertInstanceOf(TreesMenuModule::class, $module_service->findByClass(TreesMenuModule::class));
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\ModuleService::findByName
     * @return void
     */
    public function testFindByName(): void
    {
        $tree = Tree::create('name', 'title');
        app()->instance(Tree::class, $tree);

        $module_service = new ModuleService();

        $this->assertNull($module_service->findByName('not-a-valid-module-name'));
        $this->assertInstanceOf(TreesMenuModule::class, $module_service->findByName('trees-menu'));
    }
}
