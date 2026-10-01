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

namespace Fisharebest\Webtrees\Tests\Unit\Module;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\AccessLevel;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AbstractModule::class)]
class AbstractModuleTest extends TestCase
{
    public function testSelectsModuleSettingsAndPrivacy(): void
    {
        self::createDatabase();

        $module = new class () extends AbstractModule {
            public function blockSetting(int $block_id, string $setting_name, string $default = ''): string
            {
                return $this->getBlockSetting($block_id, $setting_name, $default);
            }

            public function saveBlockSetting(int $block_id, string $setting_name, string $setting_value): void
            {
                $this->setBlockSetting($block_id, $setting_name, $setting_value);
            }
        };
        $module->setName('test-module');

        DB::query(table: 'module')->insert(['module_name' => $module->name()]);
        $tree = (new TreeService(new GedcomImportService()))->create('test-tree', 'Test tree');
        $block_id = DB::query(table: 'block')->insertGetId([
            'block_order' => 1,
            'module_name' => $module->name(),
        ], 'block_id');

        $module->saveBlockSetting($block_id, 'first', 'one');
        $module->saveBlockSetting($block_id, 'second', 'two');
        $module->setPreference('setting', 'configured');
        DB::query(table: 'module_privacy')->insert([
            'module_name'  => $module->name(),
            'gedcom_id'    => $tree->id(),
            'interface'    => ModuleInterface::class,
            'access_level' => AccessLevel::Member->value,
        ]);

        self::assertSame('one', $module->blockSetting($block_id, 'first'));
        self::assertSame('two', $module->blockSetting($block_id, 'second'));
        self::assertSame('default', $module->blockSetting($block_id, 'missing', 'default'));
        self::assertSame('configured', $module->getPreference('setting'));
        self::assertSame('default', $module->getPreference('missing', 'default'));
        self::assertSame(AccessLevel::Member, $module->accessLevel($tree, ModuleInterface::class));
        self::assertSame(AccessLevel::Public, $module->accessLevel($tree, AbstractModule::class));

        $module->saveBlockSetting($block_id, 'first', 'updated');
        $module->setPreference('setting', 'updated');

        self::assertSame('updated', DB::query()
            ->from('block_setting')
            ->whereEqual('block_id', $block_id)
            ->whereEqual('setting_name', 'first')
            ->value('setting_value'));
        self::assertSame('updated', $module->getPreference('setting'));
        self::assertSame(1, DB::query()
            ->from('block_setting')
            ->whereEqual('block_id', $block_id)
            ->whereEqual('setting_name', 'first')
            ->count());
        self::assertSame(1, DB::query()
            ->from('module_setting')
            ->whereEqual('module_name', $module->name())
            ->whereEqual('setting_name', 'setting')
            ->count());
    }
}
