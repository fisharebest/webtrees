<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Reports;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Report\RightToLeftSupport;
use Fisharebest\Webtrees\TestCase;

/**
 * Test the RTL functions.  This is very old code, and poorly understood.
 * These tests exist to capture the existing functionality, and prevent regression during refactoring.
 */
class RightToLeftSupportTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     */
    public function testEmptyString(): void
    {
        I18N::init('en-US', true);
        static::assertSame(
            '',
            RightToLeftSupport::spanLtrRtl('')
        );

        I18N::init('he', true);
        static::assertSame(
            '',
            RightToLeftSupport::spanLtrRtl('')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     */
    public function testStripControlCharacters(): void
    {
        I18N::init('en-US', true);
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl('foo&lrm;bar')
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl('foo&rlm;bar')
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\x8Ebar")
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\x8Fbar")
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xADbar")
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xAEbar")
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xAAbar")
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xABbar")
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xACbar")
        );

        I18N::init('he', true);
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl('foo&lrm;bar')
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl('foo&rlm;bar')
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\x8Ebar")
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\x8Fbar")
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xADbar")
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xAEbar")
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xAAbar")
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xABbar")
        );
        static::assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xACbar")
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     */
    public function testNewLinesBecomeHTMLBreaks(): void
    {
        I18N::init('en-US', true);
        static::assertSame(
            '<span dir="ltr">foo</span><br><span dir="ltr">bar</span>',
            RightToLeftSupport::spanLtrRtl("foo\nbar")
        );
        static::assertSame(
            '<span dir="rtl">אבג</span><br><span dir="rtl">דהו</span>',
            RightToLeftSupport::spanLtrRtl("אבג\nדהו")
        );

        I18N::init('he', true);
        static::assertSame(
            '<span dir="ltr">foo</span><br><span dir="ltr">bar</span>',
            RightToLeftSupport::spanLtrRtl("foo\nbar")
        );
        static::assertSame(
            '<span dir="rtl">אבג</span><br><span dir="rtl">דהו</span>',
            RightToLeftSupport::spanLtrRtl("אבג\nדהו")
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     */
    public function testLineBreaks(): void
    {
        I18N::init('en-US', true);
        static::assertSame(
            '<span dir="ltr">foo</span><br><span dir="ltr">bar</span>',
            RightToLeftSupport::spanLtrRtl('foo<br>bar')
        );
        static::assertSame(
            '<span dir="rtl">אבג</span><br><span dir="rtl">דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג<br>דהו')
        );

        I18N::init('he', true);
        static::assertSame(
            '<span dir="ltr">foo</span><br><span dir="ltr">bar</span>',
            RightToLeftSupport::spanLtrRtl('foo<br>bar')
        );
        static::assertSame(
            '<span dir="rtl">אבג</span><br><span dir="rtl">דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג<br>דהו')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     */
    public function testHtmlEntities(): void
    {
        I18N::init('en-US', true);
        static::assertSame(
            '<span dir="ltr">foo&nbsp;bar</span>',
            RightToLeftSupport::spanLtrRtl('foo&nbsp;bar')
        );
        static::assertSame(
            '<span dir="rtl">אבג&nbsp;דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג&nbsp;דהו')
        );
        static::assertSame(
            '<span dir="ltr">foo&bar</span>',
            RightToLeftSupport::spanLtrRtl('foo&bar')
        );

        I18N::init('he', true);
        static::assertSame(
            '<span dir="ltr">foo&nbsp;bar</span>',
            RightToLeftSupport::spanLtrRtl('foo&nbsp;bar')
        );
        static::assertSame(
            '<span dir="rtl">אבג&nbsp;דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג&nbsp;דהו')
        );
        static::assertSame(
            '<span dir="ltr">foo&bar</span>',
            RightToLeftSupport::spanLtrRtl('foo&bar')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     */
    public function testBraces(): void
    {
        I18N::init('en-US', true);
        static::assertSame(
            '<span dir="ltr">foo{{123}}bar</span>',
            RightToLeftSupport::spanLtrRtl('foo{{123}}bar')
        );
        static::assertSame(
            '<span dir="ltr">foo{{bar</span>',
            RightToLeftSupport::spanLtrRtl('foo{{bar')
        );
        static::assertSame(
            '<span dir="rtl">אבג{{123}}דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג{{123}}דהו')
        );

        I18N::init('he', true);
        static::assertSame(
            '<span dir="ltr">foo{{123}}bar</span>',
            RightToLeftSupport::spanLtrRtl('foo{{123}}bar')
        );
        static::assertSame(
            '<span dir="ltr">foo{{bar</span>',
            RightToLeftSupport::spanLtrRtl('foo{{bar')
        );
        static::assertSame(
            '<span dir="rtl">אבג{{123}}דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג{{123}}דהו')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     */
    public function testNumbers(): void
    {
        I18N::init('en-US', true);
        static::assertSame(
            '<span dir="ltr">foo 123,456.789 bar</span>',
            RightToLeftSupport::spanLtrRtl('foo 123,456.789 bar')
        );
        static::assertSame(
            '<span dir="ltr">foo +123,456.789 bar</span>',
            RightToLeftSupport::spanLtrRtl('foo +123,456.789 bar')
        );
        static::assertSame(
            '<span dir="ltr">foo -123,456.789 bar</span>',
            RightToLeftSupport::spanLtrRtl('foo -123,456.789 bar')
        );
        static::assertSame(
            '<span dir="rtl">אבג ‪123,456.789‬ דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג 123,456.789 דהו')
        );
        static::assertSame(
            '<span dir="rtl">אבג ‪+123,456.789‬ דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג +123,456.789 דהו')
        );
        static::assertSame(
            '<span dir="rtl">אבג ‪-123,456.789‬ דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג -123,456.789 דהו')
        );

        I18N::init('he', true);
        static::assertSame(
            '<span dir="ltr">foo 123,456.789 bar</span>',
            RightToLeftSupport::spanLtrRtl('foo 123,456.789 bar')
        );
        static::assertSame(
            '<span dir="ltr">foo +123,456.789 bar</span>',
            RightToLeftSupport::spanLtrRtl('foo +123,456.789 bar')
        );
        static::assertSame(
            '<span dir="ltr">foo -123,456.789 bar</span>',
            RightToLeftSupport::spanLtrRtl('foo -123,456.789 bar')
        );
        static::assertSame(
            '<span dir="rtl">אבג ‪123,456.789‬ דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג 123,456.789 דהו')
        );
        static::assertSame(
            '<span dir="rtl">אבג ‪+123,456.789‬ דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג +123,456.789 דהו')
        );
        static::assertSame(
            '<span dir="rtl">אבג ‪-123,456.789‬ דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג -123,456.789 דהו')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     */
    public function testParentheses(): void
    {
        I18N::init('en-US', true);
        static::assertSame(
            '<span dir="ltr">foo (bar)</span>',
            RightToLeftSupport::spanLtrRtl('foo (bar)')
        );
        static::assertSame(
            '<span dir="ltr">foo </span><span dir="rtl">(אבג)</span>',
            RightToLeftSupport::spanLtrRtl('foo (אבג)')
        );
        static::assertSame(
            '<span dir="rtl">אבג</span><span dir="ltr"> (bar)</span>',
            RightToLeftSupport::spanLtrRtl('אבג (bar)')
        );
        static::assertSame(
            '<span dir="rtl">אבג (דהו)</span>',
            RightToLeftSupport::spanLtrRtl('אבג (דהו)')
        );

        I18N::init('he', true);
        static::assertSame(
            '<span dir="ltr">foo (bar)</span>',
            RightToLeftSupport::spanLtrRtl('foo (bar)')
        );
        static::assertSame(
            '<span dir="ltr">foo </span><span dir="rtl">(אבג)</span>',
            RightToLeftSupport::spanLtrRtl('foo (אבג)')
        );
        static::assertSame(
            '<span dir="rtl">אבג </span><span dir="ltr">(bar)</span>',
            RightToLeftSupport::spanLtrRtl('אבג (bar)')
        );
        static::assertSame(
            '<span dir="rtl">אבג (דהו)</span>',
            RightToLeftSupport::spanLtrRtl('אבג (דהו)')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     */
    public function testUnescapedHtml(): void
    {
        I18N::init('en-US', true);
        static::assertSame(
            '<span dir="ltr">>foo<</span>',
            RightToLeftSupport::spanLtrRtl('>foo<')
        );
        static::assertSame(
            '<span dir="ltr">></span><span dir="rtl">אבג<</span>',
            RightToLeftSupport::spanLtrRtl('>אבג<')
        );

        I18N::init('he', true);
        static::assertSame(
            '<span dir="rtl">></span><span dir="ltr">foo<</span>',
            RightToLeftSupport::spanLtrRtl('>foo<')
        );
        static::assertSame(
            '<span dir="rtl">>אבג<</span>',
            RightToLeftSupport::spanLtrRtl('>אבג<')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     */
    public function testBreakInNumber(): void
    {
        I18N::init('en-US', true);
        static::assertSame(
            '<span dir="ltr">123</span><br><span dir="ltr">456</span>',
            RightToLeftSupport::spanLtrRtl('123<br>456')
        );

        I18N::init('he', true);
        static::assertSame(
            '<span dir="rtl">‪123‬</span><br><span dir="rtl">‪456‬</span>',
            RightToLeftSupport::spanLtrRtl('123<br>456')
        );
    }
}
