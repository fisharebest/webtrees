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

use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Report\PaperSize;

#[CoversClass(PaperSize::class)]
class PaperSizeTest extends TestCase
{
    public function testA4Dimensions(): void
    {
        $a4 = PaperSize::A4;

        // A4 is 210mm x 297mm
        $expected_width  = 210.0 * 72.0 / 25.4;
        $expected_height = 297.0 * 72.0 / 25.4;

        self::assertEqualsWithDelta($expected_width, $a4->width(), 0.001);
        self::assertEqualsWithDelta($expected_height, $a4->height(), 0.001);
    }

    public function testUSLetterDimensions(): void
    {
        $letter = PaperSize::USLetter;

        // US Letter is 8.5" x 11"
        $expected_width  = 8.5 * 72.0;
        $expected_height = 11.0 * 72.0;

        self::assertEqualsWithDelta($expected_width, $letter->width(), 0.001);
        self::assertEqualsWithDelta($expected_height, $letter->height(), 0.001);
    }

    public function testFromString(): void
    {
        self::assertSame(PaperSize::A4, PaperSize::from('A4'));
        self::assertSame(PaperSize::USLetter, PaperSize::from('US-Letter'));
    }

    public function testTryFromInvalidReturnsNull(): void
    {
        self::assertNull(PaperSize::tryFrom('Unknown'));
    }

    public function testWidthIsAlwaysPositive(): void
    {
        foreach (PaperSize::cases() as $size) {
            self::assertGreaterThan(0.0, $size->width());
        }
    }

    public function testHeightIsAlwaysPositive(): void
    {
        foreach (PaperSize::cases() as $size) {
            self::assertGreaterThan(0.0, $size->height());
        }
    }
}
