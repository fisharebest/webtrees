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

namespace Fisharebest\Webtrees\Tests\Unit;

use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Statistics;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Statistics::class)]
class StatisticsTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(Statistics::class));
    }

    public function testMarriageAgesByCentury(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('test', 'Test');
        $import_service = new GedcomImportService();
        $import_service->importRecord("0 @I1@ INDI\n1 NAME Husband /Example/\n1 BIRT\n2 DATE 1 JAN 1900", $tree, true);
        $import_service->importRecord("0 @I2@ INDI\n1 NAME Wife /Example/\n1 BIRT\n2 DATE 1 JAN 1910", $tree, true);
        $import_service->importRecord("0 @F1@ FAM\n1 HUSB @I1@\n1 WIFE @I2@\n1 MARR\n2 DATE 1 JAN 1930", $tree, true);

        $statistics = new Statistics(new ModuleService(), $tree, new UserService(new SystemClock()));

        $chart = $statistics->statsMarrAge();

        self::assertStringContainsString('wt-chart-container', $chart);
        self::assertStringContainsString('Average age at marriage', $chart);
    }
}
