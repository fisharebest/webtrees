<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
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
        $this->assertSame(I18N::strtoupper(''), '');
        $this->assertSame(I18N::strtoupper('Abc'), 'ABC');
    }

    /**
     * @covers \Fisharebest\Webtrees\I18N::strtolower
     *
     * @return void
     */
    public function testStrtolower(): void
    {
        $this->assertSame(I18N::strtolower(''), '');
        $this->assertSame(I18N::strtolower('Abc'), 'abc');
    }

    /**
     * @covers \Fisharebest\Webtrees\I18N::strcasecmp()
     *
     * @return void
     */
    public function testStrcasecmp(): void
    {
        $this->assertSame(I18N::strcasecmp('', ''), 0);
        $this->assertSame(I18N::strcasecmp('Abc', 'abc'), 0);
        $this->assertTrue(I18N::strcasecmp('Abc', 'bcd') < 0);
        $this->assertTrue(I18N::strcasecmp('bcd', 'ABC') > 0);
        $this->assertTrue(I18N::strcasecmp('Abc', 'abcd') < 0);
        $this->assertTrue(I18N::strcasecmp('Abcd', 'abc') > 0);
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

        $this->assertSame(I18N::reverseText(''), '');
        $this->assertSame(I18N::reverseText('abc123'), 'abc123');
        $this->assertSame(I18N::reverseText('<b>abc</b>123'), 'abc123');
        $this->assertSame(I18N::reverseText('&lt;abc&gt;'), '<abc>');
        $this->assertSame(I18N::reverseText('abc[123]'), 'abc[123]');
        $this->assertSame(I18N::reverseText($rtl_123), $rtl_123);
        $this->assertSame(I18N::reverseText($rtl_abc), $rtl_cba);
        $this->assertSame(I18N::reverseText($rtl_abc . '123'), '123' . $rtl_cba);
        $this->assertSame(I18N::reverseText($rtl_abc . '[123]'), '[123]' . $rtl_cba);
        $this->assertSame(I18N::reverseText('123' . $rtl_abc . '456'), '456' . $rtl_cba . '123');
        $this->assertSame(I18N::reverseText($rtl_abc . '&lt;'), '>' . $rtl_cba);
    }
}
