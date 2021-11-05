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
     *
     * @return void
     */
    public function testEmptyString(): void
    {
        I18N::init('en-US', true);
        $this->assertSame(
            '',
            RightToLeftSupport::spanLtrRtl('')
        );

        I18N::init('he', true);
        $this->assertSame(
            '',
            RightToLeftSupport::spanLtrRtl('')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     *
     * @return void
     */
    public function testStripControlCharacters(): void
    {
        I18N::init('en-US', true);
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl('foo&lrm;bar')
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl('foo&rlm;bar')
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\x8Ebar")
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\x8Fbar")
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xADbar")
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xAEbar")
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xAAbar")
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xABbar")
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xACbar")
        );

        I18N::init('he', true);
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl('foo&lrm;bar')
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl('foo&rlm;bar')
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\x8Ebar")
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\x8Fbar")
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xADbar")
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xAEbar")
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xAAbar")
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xABbar")
        );
        $this->assertSame(
            '<span dir="ltr">foobar</span>',
            RightToLeftSupport::spanLtrRtl("foo\xE2\x80\xACbar")
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     *
     * @return void
     */
    public function testNewLinesBecomeHTMLBreaks(): void
    {
        I18N::init('en-US', true);
        $this->assertSame(
            '<span dir="ltr">foo</span><br><span dir="ltr">bar</span>',
            RightToLeftSupport::spanLtrRtl("foo\nbar")
        );
        $this->assertSame(
            '<span dir="rtl">אבג</span><br><span dir="rtl">דהו</span>',
            RightToLeftSupport::spanLtrRtl("אבג\nדהו")
        );

        I18N::init('he', true);
        $this->assertSame(
            '<span dir="ltr">foo</span><br><span dir="ltr">bar</span>',
            RightToLeftSupport::spanLtrRtl("foo\nbar")
        );
        $this->assertSame(
            '<span dir="rtl">אבג</span><br><span dir="rtl">דהו</span>',
            RightToLeftSupport::spanLtrRtl("אבג\nדהו")
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     *
     * @return void
     */
    public function testLineBreaks(): void
    {
        I18N::init('en-US', true);
        $this->assertSame(
            '<span dir="ltr">foo</span><br><span dir="ltr">bar</span>',
            RightToLeftSupport::spanLtrRtl("foo<br>bar")
        );
        $this->assertSame(
            '<span dir="rtl">אבג</span><br><span dir="rtl">דהו</span>',
            RightToLeftSupport::spanLtrRtl("אבג<br>דהו")
        );

        I18N::init('he', true);
        $this->assertSame(
            '<span dir="ltr">foo</span><br><span dir="ltr">bar</span>',
            RightToLeftSupport::spanLtrRtl("foo<br>bar")
        );
        $this->assertSame(
            '<span dir="rtl">אבג</span><br><span dir="rtl">דהו</span>',
            RightToLeftSupport::spanLtrRtl("אבג<br>דהו")
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     *
     * @return void
     */
    public function testHtmlEntities(): void
    {
        I18N::init('en-US', true);
        $this->assertSame(
            '<span dir="ltr">foo&nbsp;bar</span>',
            RightToLeftSupport::spanLtrRtl('foo&nbsp;bar')
        );
        $this->assertSame(
            '<span dir="rtl">אבג&nbsp;דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג&nbsp;דהו')
        );
        $this->assertSame(
            '<span dir="ltr">foo&bar</span>',
            RightToLeftSupport::spanLtrRtl('foo&bar')
        );

        I18N::init('he', true);
        $this->assertSame(
            '<span dir="ltr">foo&nbsp;bar</span>',
            RightToLeftSupport::spanLtrRtl("foo&nbsp;bar")
        );
        $this->assertSame(
            '<span dir="rtl">אבג&nbsp;דהו</span>',
            RightToLeftSupport::spanLtrRtl("אבג&nbsp;דהו")
        );
        $this->assertSame(
            '<span dir="ltr">foo&bar</span>',
            RightToLeftSupport::spanLtrRtl('foo&bar')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     *
     * @return void
     */
    public function testBraces(): void
    {
        I18N::init('en-US', true);
        $this->assertSame(
            '<span dir="ltr">foo{{123}}bar</span>',
            RightToLeftSupport::spanLtrRtl('foo{{123}}bar')
        );
        $this->assertSame(
            '<span dir="ltr">foo{{bar</span>',
            RightToLeftSupport::spanLtrRtl('foo{{bar')
        );
        $this->assertSame(
            '<span dir="rtl">אבג{{123}}דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג{{123}}דהו')
        );

        I18N::init('he', true);
        $this->assertSame(
            '<span dir="ltr">foo{{123}}bar</span>',
            RightToLeftSupport::spanLtrRtl('foo{{123}}bar')
        );
        $this->assertSame(
            '<span dir="ltr">foo{{bar</span>',
            RightToLeftSupport::spanLtrRtl('foo{{bar')
        );
        $this->assertSame(
            '<span dir="rtl">אבג{{123}}דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג{{123}}דהו')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     *
     * @return void
     */
    public function testNumbers(): void
    {
        I18N::init('en-US', true);
        $this->assertSame(
            '<span dir="ltr">foo 123,456.789 bar</span>',
            RightToLeftSupport::spanLtrRtl('foo 123,456.789 bar')
        );
        $this->assertSame(
            '<span dir="ltr">foo +123,456.789 bar</span>',
            RightToLeftSupport::spanLtrRtl('foo +123,456.789 bar')
        );
        $this->assertSame(
            '<span dir="ltr">foo -123,456.789 bar</span>',
            RightToLeftSupport::spanLtrRtl('foo -123,456.789 bar')
        );
        $this->assertSame(
            '<span dir="rtl">אבג ‪123,456.789‬ דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג 123,456.789 דהו')
        );
        $this->assertSame(
            '<span dir="rtl">אבג ‪+123,456.789‬ דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג +123,456.789 דהו')
        );
        $this->assertSame(
            '<span dir="rtl">אבג ‪-123,456.789‬ דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג -123,456.789 דהו')
        );

        I18N::init('he', true);
        $this->assertSame(
            '<span dir="ltr">foo 123,456.789 bar</span>',
            RightToLeftSupport::spanLtrRtl('foo 123,456.789 bar')
        );
        $this->assertSame(
            '<span dir="ltr">foo +123,456.789 bar</span>',
            RightToLeftSupport::spanLtrRtl('foo +123,456.789 bar')
        );
        $this->assertSame(
            '<span dir="ltr">foo -123,456.789 bar</span>',
            RightToLeftSupport::spanLtrRtl('foo -123,456.789 bar')
        );
        $this->assertSame(
            '<span dir="rtl">אבג ‪123,456.789‬ דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג 123,456.789 דהו')
        );
        $this->assertSame(
            '<span dir="rtl">אבג ‪+123,456.789‬ דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג +123,456.789 דהו')
        );
        $this->assertSame(
            '<span dir="rtl">אבג ‪-123,456.789‬ דהו</span>',
            RightToLeftSupport::spanLtrRtl('אבג -123,456.789 דהו')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     *
     * @return void
     */
    public function testParentheses(): void
    {
        I18N::init('en-US', true);
        $this->assertSame(
            '<span dir="ltr">foo (bar)</span>',
            RightToLeftSupport::spanLtrRtl('foo (bar)')
        );
        $this->assertSame(
            '<span dir="ltr">foo </span><span dir="rtl">(אבג)</span>',
            RightToLeftSupport::spanLtrRtl('foo (אבג)')
        );
        $this->assertSame(
            '<span dir="rtl">אבג</span><span dir="ltr"> (bar)</span>',
            RightToLeftSupport::spanLtrRtl('אבג (bar)')
        );
        $this->assertSame(
            '<span dir="rtl">אבג (דהו)</span>',
            RightToLeftSupport::spanLtrRtl('אבג (דהו)')
        );

        I18N::init('he', true);
        $this->assertSame(
            '<span dir="ltr">foo (bar)</span>',
            RightToLeftSupport::spanLtrRtl('foo (bar)')
        );
        $this->assertSame(
            '<span dir="ltr">foo </span><span dir="rtl">(אבג)</span>',
            RightToLeftSupport::spanLtrRtl('foo (אבג)')
        );
        $this->assertSame(
            '<span dir="rtl">אבג </span><span dir="ltr">(bar)</span>',
            RightToLeftSupport::spanLtrRtl('אבג (bar)')
        );
        $this->assertSame(
            '<span dir="rtl">אבג (דהו)</span>',
            RightToLeftSupport::spanLtrRtl('אבג (דהו)')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     *
     * @return void
     */
    public function testUnescapedHtml(): void
    {
        I18N::init('en-US', true);
        $this->assertSame(
            '<span dir="ltr">>foo<</span>',
            RightToLeftSupport::spanLtrRtl(">foo<")
        );
        $this->assertSame(
            '<span dir="ltr">></span><span dir="rtl">אבג<</span>',
            RightToLeftSupport::spanLtrRtl(">אבג<")
        );

        I18N::init('he', true);
        $this->assertSame(
            '<span dir="rtl">></span><span dir="ltr">foo<</span>',
            RightToLeftSupport::spanLtrRtl(">foo<")
        );
        $this->assertSame(
            '<span dir="rtl">>אבג<</span>',
            RightToLeftSupport::spanLtrRtl(">אבג<")
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Report\RightToLeftSupport
     *
     * @return void
     */
    public function testBreakInNumber(): void
    {
        I18N::init('en-US', true);
        $this->assertSame(
            '<span dir="ltr">123</span><br><span dir="ltr">456</span>',
            RightToLeftSupport::spanLtrRtl("123<br>456")
        );

        I18N::init('he', true);
        $this->assertSame(
            '<span dir="rtl">‪123‬</span><br><span dir="rtl">‪456‬</span>',
            RightToLeftSupport::spanLtrRtl("123<br>456")
        );
    }
}
