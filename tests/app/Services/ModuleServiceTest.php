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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Module\ModuleAnalyticsInterface;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleConfigInterface;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleDataFixInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ModuleService::class)]
class ModuleServiceTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testAll(): void
    {
        $module_service = new ModuleService();

        self::assertNotEmpty($module_service->all()->all());
    }

    public function testFindByComponent(): void
    {
        $user_service   = new UserService();
        $module_service = new ModuleService();

        $tree = $this->importTree('demo.ged');
        $user = $user_service->create('UserName', 'RealName', 'user@example.com', 'secret');

        self::assertNotEmpty($module_service->findByComponent(ModuleBlockInterface::class, $tree, $user)->all());
        self::assertNotEmpty($module_service->findByComponent(ModuleChartInterface::class, $tree, $user)->all());
        self::assertNotEmpty($module_service->findByComponent(ModuleMenuInterface::class, $tree, $user)->all());
        self::assertNotEmpty($module_service->findByComponent(ModuleReportInterface::class, $tree, $user)->all());
        self::assertNotEmpty($module_service->findByComponent(ModuleSidebarInterface::class, $tree, $user)->all());
        self::assertNotEmpty($module_service->findByComponent(ModuleTabInterface::class, $tree, $user)->all());
    }

    public function testFindByInterface(): void
    {
        $module_service = new ModuleService();

        self::assertNotEmpty($module_service->findByInterface(ModuleAnalyticsInterface::class, true)->all());
        self::assertNotEmpty($module_service->findByInterface(ModuleBlockInterface::class, true)->all());
        self::assertNotEmpty($module_service->findByInterface(ModuleChartInterface::class, true)->all());
        self::assertNotEmpty($module_service->findByInterface(ModuleConfigInterface::class, true)->all());
        self::assertNotEmpty($module_service->findByInterface(ModuleDataFixInterface::class, true)->all());
        self::assertNotEmpty($module_service->findByInterface(ModuleMenuInterface::class, true)->all());
        self::assertNotEmpty($module_service->findByInterface(ModuleInterface::class, true)->all());
        self::assertNotEmpty($module_service->findByInterface(ModuleReportInterface::class, true)->all());
        self::assertNotEmpty($module_service->findByInterface(ModuleSidebarInterface::class, true)->all());
        self::assertNotEmpty($module_service->findByInterface(ModuleTabInterface::class, true)->all());
        self::assertNotEmpty($module_service->findByInterface(ModuleThemeInterface::class, true)->all());

        // Search for an invalid module type
        self::assertEmpty($module_service->findByInterface('not-a-valid-class-or-interface')->all());
    }

    public function testOtherModules(): void
    {
        DB::table('module')->insert(['module_name' => 'not-a-module']);

        $module_service = new ModuleService();

        // Ignore any custom modules that happen to be installed in the development environment.
        $modules = $module_service->otherModules()
            ->filter(fn (ModuleInterface $module): bool => !$module instanceof ModuleCustomInterface);

        self::assertCount(4, $modules);
    }

    public function testDeletedModules(): void
    {
        DB::table('module')->insert(['module_name' => 'not-a-module']);

        $module_service = new ModuleService();

        self::assertCount(1, $module_service->deletedModules());
        self::assertSame('not-a-module', $module_service->deletedModules()->first());
    }
}
