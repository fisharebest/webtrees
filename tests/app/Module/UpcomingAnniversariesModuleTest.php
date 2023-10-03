<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

/**
 * Test harness for the class UpcomingAnniversariesModule
 *
 * @covers \Fisharebest\Webtrees\Module\UpcomingAnniversariesModule
 */
class UpcomingAnniversariesModuleTest extends TestCase
{
    public function testModuleProperties(): void
    {
        $calendar_service = $this->createStub(CalendarService::class);

        $module = new UpcomingAnniversariesModule($calendar_service);

        self::assertInstanceOf(ModuleBlockInterface::class, $module);
        self::assertTrue($module->loadAjax());
        self::assertTrue($module->isTreeBlock());
        self::assertTrue($module->isUserBlock());
    }
}
