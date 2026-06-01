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

namespace Fisharebest\Webtrees\Report;

use DomainException;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(HexColor::class)]
class HexColorTest extends TestCase
{
    public function testParsesWithLeadingHash(): void
    {
        $color = new HexColor('#1A2B3C');
        self::assertSame(0x1A, $color->red);
        self::assertSame(0x2B, $color->green);
        self::assertSame(0x3C, $color->blue);
    }

    public function testParsesMixedCase(): void
    {
        $color = new HexColor('#aBcDeF');
        self::assertSame(0xAB, $color->red);
        self::assertSame(0xCD, $color->green);
        self::assertSame(0xEF, $color->blue);
    }

    /**
     * @return array<string,array{string}>
     */
    public static function invalidColors(): array
    {
        return [
            'empty string'         => [''],
            'missing leading hash' => ['ff8000'],
            'too short'            => ['#12345'],
            'non-hex digit'        => ['#12345g'],
            'too long'             => ['#1234567'],
            'trailing junk'        => ['#112233-extra'],
        ];
    }

    #[DataProvider('invalidColors')]
    public function testRejectsInvalidInput(string $color): void
    {
        $this->expectException(DomainException::class);
        new HexColor($color);
    }
}
