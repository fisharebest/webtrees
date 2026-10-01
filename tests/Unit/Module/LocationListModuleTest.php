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
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Module\LocationListModule;
use Fisharebest\Webtrees\Module\WebtreesTheme;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LocationListModule::class)]
class LocationListModuleTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(LocationListModule::class));
    }

    public function testListsLocationsForOneTreeOnly(): void
    {
        self::createDatabase();
        (new ModuleService())->bootModules(new WebtreesTheme());

        $user = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');
        $module       = new LocationListModule();

        self::assertTrue($module->listIsEmpty($tree));
        DB::query(table: 'other')->insertBatch([
            [
                'o_id'     => 'L1',
                'o_file'   => $tree->id(),
                'o_type'   => Location::RECORD_TYPE,
                'o_gedcom' => "0 @L1@ _LOC\n1 NAME Local place",
            ],
            [
                'o_id'     => 'L2',
                'o_file'   => $other_tree->id(),
                'o_type'   => Location::RECORD_TYPE,
                'o_gedcom' => "0 @L2@ _LOC\n1 NAME Other place",
            ],
            [
                'o_id'     => 'N1',
                'o_file'   => $tree->id(),
                'o_type'   => 'NOTE',
                'o_gedcom' => "0 @N1@ NOTE\n1 CONT Note content",
            ],
        ]);

        self::assertFalse($module->listIsEmpty($tree));
        $request = self::createRequest(attributes: ['tree' => $tree])->withAttribute('user', $user);
        $html    = (string) $module->get($request)->getBody();

        self::assertStringContainsString('Local place', $html);
        self::assertStringNotContainsString('Other place', $html);
        self::assertStringNotContainsString('Note content', $html);
    }
}
