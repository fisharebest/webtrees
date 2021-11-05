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
use Fisharebest\Webtrees\TestCase;

/**
 * Test the RTL functions.  This is very old code, and poorly understood.
 * These tests exist to capture the existing functionality, and prevent regression during refactoring.
 */
class RightToLeftSupportTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Reports\RightToLeftSupport
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
     * @covers \Fisharebest\Webtrees\Reports\RightToLeftSupport
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
     * @covers \Fisharebest\Webtrees\Reports\RightToLeftSupport
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
     * @covers \Fisharebest\Webtrees\Reports\RightToLeftSupport
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
     * @covers \Fisharebest\Webtrees\Reports\RightToLeftSupport
     *
     * @return void
     */
    public function testHtmlEntities(): void
    {
        I18N::init('en-US', true);
        $this->assertSame(
            '<span dir="ltr">foo&nbsp;bar</span>',
            RightToLeftSupport::spanLtrRtl("foo&nbsp;bar")
        );
        $this->assertSame(
            '<span dir="rtl">אבג&nbsp;דהו</span>',
            RightToLeftSupport::spanLtrRtl("אבג&nbsp;דהו")
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
    }

    /**
     * @covers \Fisharebest\Webtrees\Reports\RightToLeftSupport
     *
     * @return void
     */
    public function xtestLeftToRight(): void
    {
        $this->assertSame('<span dir="ltr">foo&eacutebar</span>', RightToLeftSupport::spanLtrRtl('foo&eacutebar'));
        // Number
        $this->assertSame('<span dir="ltr">foo 123,456.78 bar</span>', RightToLeftSupport::spanLtrRtl('foo 123,456.78 bar'));
        $this->assertSame('<span dir="ltr">foo -123,456.78 bar</span>', RightToLeftSupport::spanLtrRtl('foo -123,456.78 bar'));
        $this->assertSame('<span dir="ltr">foo +123,456.78 bar</span>', RightToLeftSupport::spanLtrRtl('foo +123,456.78 bar'));
        $this->assertSame('<span dir="rtl">אבג</span><span dir="ltr"> ‪123,456.78‬ bar</span>', RightToLeftSupport::spanLtrRtl('אבג 123,456.78 bar'));
        $this->assertSame('<span dir="rtl">אבג</span><span dir="ltr"> ‪-123,456.78‬ bar</span>', RightToLeftSupport::spanLtrRtl('אבג -123,456.78 bar'));
        $this->assertSame('<span dir="rtl">אבג</span><span dir="ltr"> ‪+123,456.78‬ bar</span>', RightToLeftSupport::spanLtrRtl('אבג +123,456.78 bar'));
        // TCPDF directive
        $this->assertSame('<span dir="ltr">{{FOO BAR}}</span>', RightToLeftSupport::spanLtrRtl('{{FOO BAR}}'));
        // Broken TCPDF directive
        $this->assertSame('<span dir="ltr">{{FOO BAR</span>', RightToLeftSupport::spanLtrRtl('{{FOO BAR'));
        // Starred name.
        $this->assertSame('<span dir="ltr">John&nbsp;<u>Paul</u>&nbsp;Sartre</span>', RightToLeftSupport::spanLtrRtl('John <span class="starredname">Paul</span> Sartre'));
        // Unclosed HTML tag
        $this->assertSame('<span dir="ltr"><foo</span>', RightToLeftSupport::spanLtrRtl('<foo'));
        // All LTR/RTL
        $this->assertSame('<span dir="ltr">foo</span>', RightToLeftSupport::spanLtrRtl('foo'));
        $this->assertSame('<span dir="rtl">אבג</span>', RightToLeftSupport::spanLtrRtl('אבג'));
        // Leading/trailing spaces
        $this->assertSame('<span dir="ltr">   foo   </span>', RightToLeftSupport::spanLtrRtl('   foo   '));
        $this->assertSame('<span dir="ltr">   </span><span dir="rtl">אבג</span><span dir="ltr">   </span>', RightToLeftSupport::spanLtrRtl('   אבג   '));
        $this->assertSame('<span dir="ltr">&nbsp;foo&nbsp;</span>', RightToLeftSupport::spanLtrRtl('&nbsp;foo&nbsp;'));
        $this->assertSame('<span dir="ltr">&nbsp;</span><span dir="rtl">אבג</span><span dir="ltr"> </span>', RightToLeftSupport::spanLtrRtl('&nbsp;אבג&nbsp;'));
        // Spaces stick to the LTR text
        $this->assertSame('<span dir="ltr">foo </span><span dir="rtl">אבג</span>', RightToLeftSupport::spanLtrRtl('foo אבג'));
        $this->assertSame('<span dir="rtl">אבג</span><span dir="ltr"> foo</span>', RightToLeftSupport::spanLtrRtl('אבג foo'));
        // Line breaks
        $this->assertSame('<span dir="ltr">foo</span><br><span dir="rtl">אבג</span><br><span dir="ltr">bar</span>', RightToLeftSupport::spanLtrRtl('foo<br>אבג<br>bar'));
    }
}
