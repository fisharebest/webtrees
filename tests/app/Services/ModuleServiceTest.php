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
use Fisharebest\Webtrees\Module\ModuleDataFixInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Database\Capsule\Manager as DB;

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
        $user_service   = new UserService();
        $module_service = new ModuleService();

        $tree = $this->importTree('demo.ged');
        $user = $user_service->create('UserName', 'RealName', 'user@example.com', 'secret');

        $this->assertNotEmpty($module_service->findByComponent(ModuleBlockInterface::class, $tree, $user)->all());
        $this->assertNotEmpty($module_service->findByComponent(ModuleChartInterface::class, $tree, $user)->all());
        $this->assertNotEmpty($module_service->findByComponent(ModuleMenuInterface::class, $tree, $user)->all());
        $this->assertNotEmpty($module_service->findByComponent(ModuleReportInterface::class, $tree, $user)->all());
        $this->assertNotEmpty($module_service->findByComponent(ModuleSidebarInterface::class, $tree, $user)->all());
        $this->assertNotEmpty($module_service->findByComponent(ModuleTabInterface::class, $tree, $user)->all());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\ModuleService::findByInterface
     * @return void
     */
    public function testFindByInterface(): void
    {
        $module_service = new ModuleService();

        $this->assertNotEmpty($module_service->findByInterface(ModuleAnalyticsInterface::class, true)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleBlockInterface::class, true)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleChartInterface::class, true)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleConfigInterface::class, true)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleDataFixInterface::class, true)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleMenuInterface::class, true)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleInterface::class, true)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleReportInterface::class, true)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleSidebarInterface::class, true)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleTabInterface::class, true)->all());
        $this->assertNotEmpty($module_service->findByInterface(ModuleThemeInterface::class, true)->all());

        // Search for an invalid module type
        $this->assertEmpty($module_service->findByInterface('not-a-valid-class-or-interface')->all());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\ModuleService::otherModules
     * @return void
     */
    public function testOtherModules(): void
    {
        DB::table('module')->insert(['module_name' => 'not-a-module']);

        $module_service = new ModuleService();

        $this->assertSame(3, $module_service->otherModules()->count());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\ModuleService::deletedModules
     * @return void
     */
    public function testDeletedModules(): void
    {
        DB::table('module')->insert(['module_name' => 'not-a-module']);

        $module_service = new ModuleService();

        $this->assertSame(1, $module_service->deletedModules()->count());
        $this->assertSame('not-a-module', $module_service->deletedModules()->first());
    }
}
