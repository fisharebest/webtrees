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

use Fisharebest\Webtrees\StatisticsFormat;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(StatisticsFormat::class)]
class StatisticsFormatTest extends TestCase
{
    public function testQualitativeColorsReturnsRequestedCount(): void
    {
        $format = new StatisticsFormat();

        self::assertCount(5, $format->qualitativeColors(5));
        self::assertSame('#8FA8C9', $format->qualitativeColors(1)[0]);
    }

    public function testQualitativeColorsCyclesWhenCountExceedsPaletteSize(): void
    {
        $format = new StatisticsFormat();
        $colors = $format->qualitativeColors(17);

        self::assertCount(17, $colors);
        self::assertSame($colors[0], $colors[16]);
    }
}
