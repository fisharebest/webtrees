<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees;

use PHPUnit\Framework\Attributes\CoversClass;


#[CoversClass(I18N::class)]
class I18NTest extends TestCase
{
    public function testStrtoupper(): void
    {
        self::assertSame(I18N::strtoupper(''), '');
        self::assertSame(I18N::strtoupper('Abc'), 'ABC');
    }

    public function testStrtolower(): void
    {
        self::assertSame(I18N::strtolower(''), '');
        self::assertSame(I18N::strtolower('Abc'), 'abc');
    }

    public function testComparator(): void
    {
        $comparator = I18N::comparator();

        self::assertSame($comparator('', ''), 0);
        self::assertSame($comparator('Abc', 'abc'), 0);
        self::assertTrue($comparator('Abc', 'bcd') < 0);
        self::assertTrue($comparator('bcd', 'ABC') > 0);
        self::assertTrue($comparator('Abc', 'abcd') < 0);
        self::assertTrue($comparator('Abcd', 'abc') > 0);
    }

    public function testReverseText(): void
    {
        // Create these strings carefully, as text editors can display them in confusing ways.
        $rtl_abc = 'א' . 'ב' . 'ג';
        $rtl_cba = 'ג' . 'ב' . 'א';
        $rtl_123 = '١' . '٢' . '٣';

        self::assertSame(I18N::reverseText(''), '');
        self::assertSame(I18N::reverseText('abc123'), 'abc123');
        self::assertSame(I18N::reverseText('<b>abc</b>123'), 'abc123');
        self::assertSame(I18N::reverseText('&lt;abc&gt;'), '<abc>');
        self::assertSame(I18N::reverseText('abc[123]'), 'abc[123]');
        self::assertSame(I18N::reverseText($rtl_123), $rtl_123);
        self::assertSame(I18N::reverseText($rtl_abc), $rtl_cba);
        self::assertSame(I18N::reverseText($rtl_abc . '123'), '123' . $rtl_cba);
        self::assertSame(I18N::reverseText($rtl_abc . '[123]'), '[123]' . $rtl_cba);
        self::assertSame(I18N::reverseText('123' . $rtl_abc . '456'), '456' . $rtl_cba . '123');
        self::assertSame(I18N::reverseText($rtl_abc . '&lt;'), '>' . $rtl_cba);
    }
}
