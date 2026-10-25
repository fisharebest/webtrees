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
use Fisharebest\Webtrees\Enums\Role;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Module\SlideShowModule;

#[CoversClass(SlideShowModule::class)]
class SlideShowModuleTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(SlideShowModule::class));
    }

    /**
     * The import stores the format in upper case.  The block must find it on a case-sensitive database.
     */
    public function testFindsImagesWithUpperCaseFormat(): void
    {
        self::createDatabase();

        $user_service = new UserService(new SystemClock());
        $user         = $user_service->create('test', 'test', 'test', '*');
        Auth::login($user);

        $import_service = new GedcomImportService();
        $tree_service   = new TreeService($import_service);
        $tree           = $tree_service->create('test', 'test');
        $tree->setUserPreference($user, UserInterface::PREF_TREE_ROLE, Role::Manager->value);

        $import_service->importRecord("0 @M1@ OBJE\n1 FILE test.jpg\n2 FORM jpg\n3 TYPE photo", $tree, false);
        $import_service->importRecord("0 @I1@ INDI\n1 NAME Test\n1 OBJE @M1@", $tree, false);

        $module = new SlideShowModule(new LinkedRecordService());
        $html   = $module->getBlock($tree, 0, ModuleBlockInterface::CONTEXT_EMBED);

        self::assertStringContainsString('slide-show-image', $html);
        self::assertStringNotContainsString('This family tree has no images to display.', $html);
    }
}
