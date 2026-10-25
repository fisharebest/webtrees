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

use Fisharebest\Webtrees\Charts\PieChartData;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PieChartData::class)]
class PieChartDataTest extends TestCase
{
    public function testPieChartDataSerializesChartJsShape(): void
    {
        $chart_data = new PieChartData(['A', 'B'], [12, 7], ['#000000', '#ffffff']);

        self::assertTrue($chart_data->hasData());
        self::assertSame(
            [
                'labels'   => ['A', 'B'],
                'datasets' => [[
                    'data'            => [12, 7],
                    'backgroundColor' => ['#000000', '#ffffff'],
                ]],
            ],
            $chart_data->jsonSerialize(),
        );
    }
}
