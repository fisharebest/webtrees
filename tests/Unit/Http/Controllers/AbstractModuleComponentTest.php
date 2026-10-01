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

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\AccessLevel;
use Fisharebest\Webtrees\Http\Controllers\AbstractModuleComponent;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(AbstractModuleComponent::class)]
class AbstractModuleComponentTest extends TestCase
{
    public function testInsertsAndUpdatesModuleAccess(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('test', 'Test');
        $module = new class () extends AbstractModule {
        };
        $module->setName('test-module');
        DB::query(table: 'module')->insert(['module_name' => $module->name()]);

        $module_service = self::createStub(ModuleService::class);
        $module_service->method('findByInterface')->willReturn(new Collection([$module]));
        $tree_service = self::createStub(TreeService::class);
        $tree_service->method('all')->willReturn(new Collection([$tree]));

        $controller = new class ($module_service, $tree_service) extends AbstractModuleComponent {
            public function saveAccess(ServerRequestInterface $request): void
            {
                $this->updateAccessLevel(ModuleInterface::class, $request);
            }
        };

        $key = 'access-' . $module->name() . '-' . $tree->id();
        $controller->saveAccess(self::createRequest(params: [$key => (string) AccessLevel::Member->value]));
        self::assertSame(AccessLevel::Member->value, (int) DB::query(table: 'module_privacy')
            ->whereEqual('module_name', $module->name())
            ->whereEqual('gedcom_id', $tree->id())
            ->whereEqual('interface', ModuleInterface::class)
            ->value('access_level'));
        $controller->saveAccess(self::createRequest(params: [$key => (string) AccessLevel::Manager->value]));

        self::assertSame(1, DB::query(table: 'module_privacy')
            ->whereEqual('module_name', $module->name())
            ->whereEqual('gedcom_id', $tree->id())
            ->whereEqual('interface', ModuleInterface::class)
            ->count());
        self::assertSame(AccessLevel::Manager->value, (int) DB::query(table: 'module_privacy')
            ->whereEqual('module_name', $module->name())
            ->whereEqual('gedcom_id', $tree->id())
            ->whereEqual('interface', ModuleInterface::class)
            ->value('access_level'));
    }
}
