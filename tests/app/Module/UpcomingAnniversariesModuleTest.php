<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Services\CalendarService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;

/**
 * Test harness for the class UpcomingAnniversariesModule
 *
 * @covers \Fisharebest\Webtrees\Module\UpcomingAnniversariesModule
 */
class UpcomingAnniversariesModuleTest extends TestCase
{
    /**
     * @return void
     */
    public function testModule(): void
    {
        $tree = $this->createStub(Tree::class);
        $calendar_service = $this->createStub(CalendarService::class);

        $module = new UpcomingAnniversariesModule($calendar_service);

        self::assertInstanceOf(ModuleBlockInterface::class, $module);
        self::assertTrue($module->loadAjax());
        self::assertTrue($module->isTreeBlock());
        self::assertTrue($module->isUserBlock());
        self::assertIsString($module->editBlockConfiguration($tree, 1));

        $request = self::createRequest();
        $module->saveBlockConfiguration($request, 1);
        self::assertIsString($module->getBlock($tree, 1, ModuleBlockInterface::CONTEXT_EMBED));
    }
}
