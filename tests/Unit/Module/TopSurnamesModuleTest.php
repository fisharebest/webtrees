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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\WebtreesTheme;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Module\TopSurnamesModule;

#[CoversClass(TopSurnamesModule::class)]
class TopSurnamesModuleTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(TopSurnamesModule::class));
    }

    public function testCountsSurnamesInTheSelectedTree(): void
    {
        self::createDatabase();
        $module_service = new ModuleService();
        $module_service->bootModules(new WebtreesTheme());

        $user = (new UserService(new SystemClock()))->create('test', 'Test', 'test@example.com', 'secret');
        Auth::login($user);

        $import_service = new GedcomImportService();
        $tree_service   = new TreeService($import_service);
        $tree           = $tree_service->create('first', 'First');
        $other_tree     = $tree_service->create('second', 'Second');
        foreach (['I1', 'I2'] as $xref) {
            $import_service->importRecord("0 @" . $xref . "@ INDI\n1 NAME Test /Smith/", $tree, false);
            DB::query(table: 'name')->whereEqual('n_file', $tree->id())->whereEqual('n_id', $xref)
                ->update(['n_surn' => 'SMITH', 'n_surname' => 'Smith']);
        }
        $import_service->importRecord("0 @I3@ INDI\n1 NAME Eve /Jones/", $other_tree, false);
        DB::query(table: 'name')->whereEqual('n_file', $other_tree->id())->whereEqual('n_id', 'I3')
            ->update(['n_surn' => 'JONES', 'n_surname' => 'Jones']);

        $html = (new TopSurnamesModule($module_service))
            ->getBlock($tree, 0, ModuleBlockInterface::CONTEXT_EMBED);

        self::assertStringContainsString('Smith', $html);
        self::assertStringContainsString('data-sort="2"', $html);
        self::assertStringNotContainsString('Jones', $html);
    }
}
