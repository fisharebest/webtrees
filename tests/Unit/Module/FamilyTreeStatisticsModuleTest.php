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
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\WebtreesTheme;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Statistics;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Module\FamilyTreeStatisticsModule;

#[CoversClass(FamilyTreeStatisticsModule::class)]
class FamilyTreeStatisticsModuleTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(FamilyTreeStatisticsModule::class));
    }

    public function testRendersCommonSurnames(): void
    {
        self::createDatabase();
        $module_service = new ModuleService();
        $module_service->bootModules(new WebtreesTheme());

        $user_service = new UserService(new SystemClock());
        $user = $user_service->create('test', 'Test', 'test@example.com', 'secret');
        Auth::login($user);

        $tree = (new TreeService(new GedcomImportService()))->create('first', 'First');
        Registry::container()->set(Statistics::class, new Statistics($module_service, $tree, $user_service));
        $html = (new FamilyTreeStatisticsModule($module_service))
            ->getBlock($tree, 0, ModuleBlockInterface::CONTEXT_EMBED);

        self::assertStringContainsString('DOE', $html);
    }
}
