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

namespace Fisharebest\Webtrees\Tests\Unit\Elements;

use Fisharebest\Webtrees\Elements\Census;
use Fisharebest\Webtrees\Factories\IndividualFactory;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Report\Element;
use Fisharebest\Webtrees\Services\ModuleService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Element::class)]
#[CoversClass(Census::class)]
class CensusTest extends AbstractElementTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $module_service = self::createStub(ModuleService::class);
        $module_service->method('findByInterface')->willReturn(new Collection());
        Registry::container()->set(ModuleService::class, $module_service);

        Registry::individualFactory(self::createStub(IndividualFactory::class));

        self::$element = new Census('label');
    }
}
