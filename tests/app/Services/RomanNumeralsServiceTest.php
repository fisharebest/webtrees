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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RomanNumeralsService::class)]
class RomanNumeralsServiceTest extends TestCase
{
    public function testNumberToRomanNumerals(): void
    {
        $service = new RomanNumeralsService();

        self::assertSame('I', $service->numberToRomanNumerals(1));
        self::assertSame('IV', $service->numberToRomanNumerals(4));
        self::assertSame('IX', $service->numberToRomanNumerals(9));
        self::assertSame('XIV', $service->numberToRomanNumerals(14));
        self::assertSame('XLII', $service->numberToRomanNumerals(42));
        self::assertSame('XCIX', $service->numberToRomanNumerals(99));
        self::assertSame('MDCCCLXXXVIII', $service->numberToRomanNumerals(1888));
        self::assertSame('MMXXIV', $service->numberToRomanNumerals(2024));
    }

    public function testNumberToRomanNumeralsEdgeCases(): void
    {
        $service = new RomanNumeralsService();

        self::assertSame('0', $service->numberToRomanNumerals(0));
        self::assertSame('-1', $service->numberToRomanNumerals(-1));
    }

    public function testRomanNumeralsToNumber(): void
    {
        $service = new RomanNumeralsService();

        self::assertSame(1, $service->romanNumeralsToNumber('I'));
        self::assertSame(4, $service->romanNumeralsToNumber('IV'));
        self::assertSame(9, $service->romanNumeralsToNumber('IX'));
        self::assertSame(14, $service->romanNumeralsToNumber('XIV'));
        self::assertSame(42, $service->romanNumeralsToNumber('XLII'));
        self::assertSame(99, $service->romanNumeralsToNumber('XCIX'));
        self::assertSame(1888, $service->romanNumeralsToNumber('MDCCCLXXXVIII'));
        self::assertSame(2024, $service->romanNumeralsToNumber('MMXXIV'));
    }

    public function testRomanNumeralsToNumberEmpty(): void
    {
        $service = new RomanNumeralsService();

        self::assertSame(0, $service->romanNumeralsToNumber(''));
    }
}
