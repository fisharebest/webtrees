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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Report\RightToLeftFormatter;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Test the RTL functions.  This is very old code, and poorly understood.
 * These tests exist to capture the existing functionality, and prevent regression during refactoring.
 */
#[CoversClass(RightToLeftFormatter::class)]
class RightToLeftFormatterTest extends TestCase
{
    public function testEmptyString(): void
    {
        I18N::init('en-US', true);
        self::assertSame(
            '',
            (new RightToLeftFormatter())->format('')
        );

        I18N::init('he', true);
        self::assertSame(
            '',
            (new RightToLeftFormatter())->format('')
        );
    }

    public function testStripControlCharacters(): void
    {
        I18N::init('en-US', true);
        self::assertSame(
            '<span dir="ltr">foobar</span>',
            (new RightToLeftFormatter())->format("foo\xE2\x80\x8Ebar")
        );
        self::assertSame(
            '<span dir="ltr">foobar</span>',
            (new RightToLeftFormatter())->format("foo\xE2\x80\x8Fbar")
        );
        self::assertSame(
            '<span dir="ltr">foobar</span>',
            (new RightToLeftFormatter())->format("foo\xE2\x80\xADbar")
        );
        self::assertSame(
            '<span dir="ltr">foobar</span>',
            (new RightToLeftFormatter())->format("foo\xE2\x80\xAEbar")
        );
        self::assertSame(
            '<span dir="ltr">foobar</span>',
            (new RightToLeftFormatter())->format("foo\xE2\x80\xAAbar")
        );
        self::assertSame(
            '<span dir="ltr">foobar</span>',
            (new RightToLeftFormatter())->format("foo\xE2\x80\xABbar")
        );
        self::assertSame(
            '<span dir="ltr">foobar</span>',
            (new RightToLeftFormatter())->format("foo\xE2\x80\xACbar")
        );

        I18N::init('he', true);
        self::assertSame(
            '<span dir="ltr">foobar</span>',
            (new RightToLeftFormatter())->format("foo\xE2\x80\x8Ebar")
        );
        self::assertSame(
            '<span dir="ltr">foobar</span>',
            (new RightToLeftFormatter())->format("foo\xE2\x80\x8Fbar")
        );
        self::assertSame(
            '<span dir="ltr">foobar</span>',
            (new RightToLeftFormatter())->format("foo\xE2\x80\xADbar")
        );
        self::assertSame(
            '<span dir="ltr">foobar</span>',
            (new RightToLeftFormatter())->format("foo\xE2\x80\xAEbar")
        );
        self::assertSame(
            '<span dir="ltr">foobar</span>',
            (new RightToLeftFormatter())->format("foo\xE2\x80\xAAbar")
        );
        self::assertSame(
            '<span dir="ltr">foobar</span>',
            (new RightToLeftFormatter())->format("foo\xE2\x80\xABbar")
        );
        self::assertSame(
            '<span dir="ltr">foobar</span>',
            (new RightToLeftFormatter())->format("foo\xE2\x80\xACbar")
        );
    }

    public function testNewLinesBecomeHTMLBreaks(): void
    {
        I18N::init('en-US', true);
        self::assertSame(
            '<span dir="ltr">foo</span><br><span dir="ltr">bar</span>',
            (new RightToLeftFormatter())->format("foo\nbar")
        );
        self::assertSame(
            '<span dir="rtl">אבג</span><br><span dir="rtl">דהו</span>',
            (new RightToLeftFormatter())->format("אבג\nדהו")
        );

        I18N::init('he', true);
        self::assertSame(
            '<span dir="ltr">foo</span><br><span dir="ltr">bar</span>',
            (new RightToLeftFormatter())->format("foo\nbar")
        );
        self::assertSame(
            '<span dir="rtl">אבג</span><br><span dir="rtl">דהו</span>',
            (new RightToLeftFormatter())->format("אבג\nדהו")
        );
    }

    public function testLineBreaks(): void
    {
        I18N::init('en-US', true);
        self::assertSame(
            '<span dir="ltr">foo</span><br><span dir="ltr">bar</span>',
            (new RightToLeftFormatter())->format('foo<br>bar')
        );
        self::assertSame(
            '<span dir="rtl">אבג</span><br><span dir="rtl">דהו</span>',
            (new RightToLeftFormatter())->format('אבג<br>דהו')
        );

        I18N::init('he', true);
        self::assertSame(
            '<span dir="ltr">foo</span><br><span dir="ltr">bar</span>',
            (new RightToLeftFormatter())->format('foo<br>bar')
        );
        self::assertSame(
            '<span dir="rtl">אבג</span><br><span dir="rtl">דהו</span>',
            (new RightToLeftFormatter())->format('אבג<br>דהו')
        );
    }


    public function testBraces(): void
    {
        I18N::init('en-US', true);
        self::assertSame(
            '<span dir="ltr">foo{{123}}bar</span>',
            (new RightToLeftFormatter())->format('foo{{123}}bar')
        );
        self::assertSame(
            '<span dir="ltr">foo{{bar</span>',
            (new RightToLeftFormatter())->format('foo{{bar')
        );
        self::assertSame(
            '<span dir="rtl">אבג{{123}}דהו</span>',
            (new RightToLeftFormatter())->format('אבג{{123}}דהו')
        );

        I18N::init('he', true);
        self::assertSame(
            '<span dir="ltr">foo{{123}}bar</span>',
            (new RightToLeftFormatter())->format('foo{{123}}bar')
        );
        self::assertSame(
            '<span dir="ltr">foo{{bar</span>',
            (new RightToLeftFormatter())->format('foo{{bar')
        );
        self::assertSame(
            '<span dir="rtl">אבג{{123}}דהו</span>',
            (new RightToLeftFormatter())->format('אבג{{123}}דהו')
        );
    }

    public function testNumbers(): void
    {
        I18N::init('en-US', true);
        self::assertSame(
            '<span dir="ltr">foo 123,456.789 bar</span>',
            (new RightToLeftFormatter())->format('foo 123,456.789 bar')
        );
        self::assertSame(
            '<span dir="ltr">foo +123,456.789 bar</span>',
            (new RightToLeftFormatter())->format('foo +123,456.789 bar')
        );
        self::assertSame(
            '<span dir="ltr">foo -123,456.789 bar</span>',
            (new RightToLeftFormatter())->format('foo -123,456.789 bar')
        );
        self::assertSame(
            '<span dir="rtl">אבג ‪123,456.789‬ דהו</span>',
            (new RightToLeftFormatter())->format('אבג 123,456.789 דהו')
        );
        self::assertSame(
            '<span dir="rtl">אבג ‪+123,456.789‬ דהו</span>',
            (new RightToLeftFormatter())->format('אבג +123,456.789 דהו')
        );
        self::assertSame(
            '<span dir="rtl">אבג ‪-123,456.789‬ דהו</span>',
            (new RightToLeftFormatter())->format('אבג -123,456.789 דהו')
        );

        I18N::init('he', true);
        self::assertSame(
            '<span dir="ltr">foo 123,456.789 bar</span>',
            (new RightToLeftFormatter())->format('foo 123,456.789 bar')
        );
        self::assertSame(
            '<span dir="ltr">foo +123,456.789 bar</span>',
            (new RightToLeftFormatter())->format('foo +123,456.789 bar')
        );
        self::assertSame(
            '<span dir="ltr">foo -123,456.789 bar</span>',
            (new RightToLeftFormatter())->format('foo -123,456.789 bar')
        );
        self::assertSame(
            '<span dir="rtl">אבג ‪123,456.789‬ דהו</span>',
            (new RightToLeftFormatter())->format('אבג 123,456.789 דהו')
        );
        self::assertSame(
            '<span dir="rtl">אבג ‪+123,456.789‬ דהו</span>',
            (new RightToLeftFormatter())->format('אבג +123,456.789 דהו')
        );
        self::assertSame(
            '<span dir="rtl">אבג ‪-123,456.789‬ דהו</span>',
            (new RightToLeftFormatter())->format('אבג -123,456.789 דהו')
        );
    }

    public function testParentheses(): void
    {
        I18N::init('en-US', true);
        self::assertSame(
            '<span dir="ltr">foo (bar)</span>',
            (new RightToLeftFormatter())->format('foo (bar)')
        );
        self::assertSame(
            '<span dir="ltr">foo </span><span dir="rtl">(אבג)</span>',
            (new RightToLeftFormatter())->format('foo (אבג)')
        );
        self::assertSame(
            '<span dir="rtl">אבג</span><span dir="ltr"> (bar)</span>',
            (new RightToLeftFormatter())->format('אבג (bar)')
        );
        self::assertSame(
            '<span dir="rtl">אבג (דהו)</span>',
            (new RightToLeftFormatter())->format('אבג (דהו)')
        );

        I18N::init('he', true);
        self::assertSame(
            '<span dir="ltr">foo (bar)</span>',
            (new RightToLeftFormatter())->format('foo (bar)')
        );
        self::assertSame(
            '<span dir="ltr">foo </span><span dir="rtl">(אבג)</span>',
            (new RightToLeftFormatter())->format('foo (אבג)')
        );
        self::assertSame(
            '<span dir="rtl">אבג </span><span dir="ltr">(bar)</span>',
            (new RightToLeftFormatter())->format('אבג (bar)')
        );
        self::assertSame(
            '<span dir="rtl">אבג (דהו)</span>',
            (new RightToLeftFormatter())->format('אבג (דהו)')
        );
    }

    public function testUnescapedHtml(): void
    {
        I18N::init('en-US', true);
        self::assertSame(
            '<span dir="ltr">>foo<</span>',
            (new RightToLeftFormatter())->format('>foo<')
        );
        self::assertSame(
            '<span dir="ltr">></span><span dir="rtl">אבג<</span>',
            (new RightToLeftFormatter())->format('>אבג<')
        );

        I18N::init('he', true);
        self::assertSame(
            '<span dir="rtl">></span><span dir="ltr">foo<</span>',
            (new RightToLeftFormatter())->format('>foo<')
        );
        self::assertSame(
            '<span dir="rtl">>אבג<</span>',
            (new RightToLeftFormatter())->format('>אבג<')
        );
    }

    public function testBreakInNumber(): void
    {
        I18N::init('en-US', true);
        self::assertSame(
            '<span dir="ltr">123</span><br><span dir="ltr">456</span>',
            (new RightToLeftFormatter())->format('123<br>456')
        );

        I18N::init('he', true);
        self::assertSame(
            '<span dir="rtl">‪123‬</span><br><span dir="rtl">‪456‬</span>',
            (new RightToLeftFormatter())->format('123<br>456')
        );
    }
}
