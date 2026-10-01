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
use Fisharebest\Webtrees\Enums\HttpRequestMethod;
use Fisharebest\Webtrees\Http\Controllers\ModuleDeleteSettings;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ModuleDeleteSettings::class)]
class ModuleDeleteSettingsTest extends TestCase
{
    public function testDeletesOnlyTheSelectedModulesBlocksAndSettings(): void
    {
        self::createDatabase();

        foreach ([['test-module-1', 101], ['test-module-2', 102]] as [$module_name, $block_id]) {
            DB::query(table: 'module')->insert(['module_name' => $module_name]);
            DB::query(table: 'block')->insert([
                'block_id'    => $block_id,
                'block_order' => 1,
                'module_name' => $module_name,
            ]);
            DB::query(table: 'block_setting')->insert([
                'block_id'      => $block_id,
                'setting_name'  => 'test',
                'setting_value' => 'value',
            ]);
        }

        (new ModuleDeleteSettings())->post(self::createRequest(
            HttpRequestMethod::POST->value,
            params: ['module_name' => 'test-module-1'],
        ));

        self::assertSame([102], DB::query()->from('block_setting')->pluck('block_id'));
        self::assertSame([102], DB::query()->from('block')->pluck('block_id'));
        self::assertSame(['test-module-2'], DB::query()->from('module')->whereLike('module_name', 'test-module-%')->pluck('module_name'));
    }
}
