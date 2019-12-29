<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class LocalizationService
 */
class LocalizationServiceTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Services\LocalizationService::dateFormatToOrder
     *
     * @return void
     */
    public function testDateFormatToOrder(): void
    {
        $localization_service = new LocalizationService();

        // Chinese
        $this->assertSame('YMD', $localization_service->dateFormatToOrder('%Y年 %n月 %j日'));

        // American
        $this->assertSame('MDY', $localization_service->dateFormatToOrder('%F %j, %Y'));

        // European
        $this->assertSame('DMY', $localization_service->dateFormatToOrder('%j %F %Y'));
    }
}
