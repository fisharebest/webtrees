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

namespace Fisharebest\Webtrees\Tests\Unit\Report;

use Fisharebest\Webtrees\Report\LayoutBlockData;
use Fisharebest\Webtrees\Report\LineData;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LineData::class)]
class LineDataTest extends TestCase
{
    public function testConstructorAssignsProperties(): void
    {
        $data = new LineData(
            x1: 10.0,
            y1: 20.0,
            x2: 100.0,
            y2: 20.0,
        );

        self::assertSame(10.0, $data->x1);
        self::assertSame(20.0, $data->y1);
        self::assertSame(100.0, $data->x2);
        self::assertSame(20.0, $data->y2);
    }

    public function testImplementsLayoutBlockData(): void
    {
        $data = new LineData(x1: 0.0, y1: 0.0, x2: 0.0, y2: 0.0);

        self::assertInstanceOf(LayoutBlockData::class, $data);
    }
}
