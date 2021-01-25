<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

/**
 * Test harness for the class I18N
 */
class I18NTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\I18N::strtoupper
     *
     * @return void
     */
    public function testStrtoupper(): void
    {
        self::assertSame(I18N::strtoupper(''), '');
        self::assertSame(I18N::strtoupper('Abc'), 'ABC');
    }

    /**
     * @covers \Fisharebest\Webtrees\I18N::strtolower
     *
     * @return void
     */
    public function testStrtolower(): void
    {
        self::assertSame(I18N::strtolower(''), '');
        self::assertSame(I18N::strtolower('Abc'), 'abc');
    }

    /**
     * @covers \Fisharebest\Webtrees\I18N::strcasecmp()
     *
     * @return void
     */
    public function testStrcasecmp(): void
    {
        self::assertSame(I18N::strcasecmp('', ''), 0);
        self::assertSame(I18N::strcasecmp('Abc', 'abc'), 0);
        self::assertTrue(I18N::strcasecmp('Abc', 'bcd') < 0);
        self::assertTrue(I18N::strcasecmp('bcd', 'ABC') > 0);
        self::assertTrue(I18N::strcasecmp('Abc', 'abcd') < 0);
        self::assertTrue(I18N::strcasecmp('Abcd', 'abc') > 0);
    }

    /**
     * @covers \Fisharebest\Webtrees\I18N::reverseText
     *
     * @return void
     */
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
