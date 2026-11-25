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

use Fisharebest\Webtrees\Module\RelationshipsChartModule;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function view;

#[CoversClass(RelationshipsChartModule::class)]
class RelationshipsChartModuleTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(RelationshipsChartModule::class));
    }

    public function testConnectorDirections(): void
    {
        $directions = ['right', 'up', 'down', 'diagonal-up', 'diagonal-down'];

        foreach ($directions as $direction) {
            $html = view('modules/relationships-chart/connector', [
                'direction' => $direction,
                'label'     => 'mother',
            ]);

            self::assertStringContainsString('wt-relationship-connector-' . $direction, $html);
            self::assertStringContainsString('wt-relationship-connector-label">mother</span>', $html);
            self::assertStringContainsString('wt-relationship-connector-arrow" aria-hidden="true"></span>', $html);
            self::assertStringNotContainsString('fa-arrow-', $html);
            self::assertStringNotContainsString('background:', $html);
        }
    }
}
