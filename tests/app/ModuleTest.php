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

namespace Fisharebest\Webtrees;

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

/**
 * Test the modules
 *
 * @coversNothing
 */
class ModuleTest extends \Fisharebest\Webtrees\TestCase
{
    protected static $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\Module::all
     * @covers \Fisharebest\Webtrees\Module::coreModules
     * @covers \Fisharebest\Webtrees\Module::customModules
     * @covers \Fisharebest\Webtrees\Module::moduleSorter
     * @return void
     */
    public function testAll(): void
    {
        $this->assertNotEmpty(Module::all());
    }

    /**
     * @covers \Fisharebest\Webtrees\Module::findByComponent
     * @covers \Fisharebest\Webtrees\Module::menuSorter
     * @covers \Fisharebest\Webtrees\Module::sidebarSorter
     * @covers \Fisharebest\Webtrees\Module::tabSorter
     * @return void
     */
    public function testFindByComponent(): void
    {
        $tree = $this->importTree('demo.ged');
        $user = User::create('UserName', 'RealName', 'user@example.com', 'secret');

        $this->assertNotEmpty(Module::findByComponent('block', $tree, $user)->all());
        $this->assertNotEmpty(Module::findByComponent('chart', $tree, $user)->all());
        $this->assertNotEmpty(Module::findByComponent('menu', $tree, $user)->all());
        $this->assertNotEmpty(Module::findByComponent('report', $tree, $user)->all());
        $this->assertNotEmpty(Module::findByComponent('sidebar', $tree, $user)->all());
        $this->assertNotEmpty(Module::findByComponent('tab', $tree, $user)->all());
    }

    /**
     * @covers \Fisharebest\Webtrees\Module::findByInterface
     * @return void
     */
    public function testFindByInterface(): void
    {
        $this->assertNotEmpty(Module::findByInterface(ModuleAnalyticsInterface::class)->all());
        $this->assertNotEmpty(Module::findByInterface(ModuleBlockInterface::class)->all());
        $this->assertNotEmpty(Module::findByInterface(ModuleChartInterface::class)->all());
        $this->assertNotEmpty(Module::findByInterface(ModuleConfigInterface::class)->all());
        $this->assertNotEmpty(Module::findByInterface(ModuleMenuInterface::class)->all());
        $this->assertNotEmpty(Module::findByInterface(ModuleInterface::class)->all());
        $this->assertNotEmpty(Module::findByInterface(ModuleReportInterface::class)->all());
        $this->assertNotEmpty(Module::findByInterface(ModuleSidebarInterface::class)->all());
        $this->assertNotEmpty(Module::findByInterface(ModuleTabInterface::class)->all());

        // THe core modules do not contain any of these.
        $this->assertEmpty(Module::findByInterface(ModuleHistoricEventsInterface::class)->all());
        $this->assertEmpty(Module::findByInterface(ModuleThemeInterface::class)->all());
    }

    /**
     * @covers \Fisharebest\Webtrees\Module::findByClass
     * @return void
     */
    public function testFindByClass(): void
    {
        $this->assertNull(Module::findByClass('not-a-valid-class-name'));
        $this->assertInstanceOf(TreesMenuModule::class, Module::findByClass(TreesMenuModule::class));
    }

    /**
     * @covers \Fisharebest\Webtrees\Module::findByName
     * @return void
     */
    public function testFindByName(): void
    {
        $this->assertNull(Module::findByName('not-a-valid-module-name'));
        $this->assertInstanceOf(TreesMenuModule::class, Module::findByName('trees-menu'));
    }
}
