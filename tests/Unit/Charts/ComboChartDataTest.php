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

namespace Fisharebest\Webtrees\Tests\Unit\Charts;

use Fisharebest\Webtrees\Charts\ComboChartData;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ComboChartData::class)]
class ComboChartDataTest extends TestCase
{
    public function testComboChartDataSupportsMixedDatasets(): void
    {
        $chart_data = new ComboChartData(['19th'], [
            ['label' => 'Males', 'data' => [31.2], 'type' => 'bar'],
            ['label' => 'Average', 'data' => [30.0], 'type' => 'line'],
        ]);

        self::assertTrue($chart_data->hasData());
        self::assertSame('line', $chart_data->jsonSerialize()['datasets'][1]['type'] ?? null);
    }
}
