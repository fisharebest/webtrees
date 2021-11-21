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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;

/**
 * Test harness for the class GedcomEditService
 *
 * @covers \Fisharebest\Webtrees\Factories\MarkdownFactory
 */
class MarkdownFactoryTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Factories\MarkdownFactory
     */
    public function testAutoLinkWithoutTree(): void
    {
        $factory  = new MarkdownFactory();
        $autolink = $factory->autolink();

        $this->assertSame(
            "<p>FOO <a href=\"https://example.com\">https://example.com</a> BAR</p>\n",
            $autolink->convertToHtml('FOO https://example.com BAR')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\CommonMark\XrefExtension
     * @covers \Fisharebest\Webtrees\Factories\MarkdownFactory
     */
    public function testAutoLinkWithTree(): void
    {
        $tree = $this->createStub(Tree::class);

        $factory  = new MarkdownFactory();
        $autolink = $factory->autolink($tree);

        $this->assertSame(
            "<p>FOO <a href=\"https://example.com\">https://example.com</a> BAR</p>\n",
            $autolink->convertToHtml('FOO https://example.com BAR')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\CommonMark\XrefExtension
     * @covers \Fisharebest\Webtrees\Factories\MarkdownFactory
     */
    public function testAutoLinkWithHtml(): void
    {
        $factory  = new MarkdownFactory();
        $autolink = $factory->autolink();

        $this->assertSame(
            "<p>&lt;b&gt; <a href=\"https://example.com\">https://example.com</a> &lt;/b&gt;</p>\n",
            $autolink->convertToHtml('<b> https://example.com </b>')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\MarkdownFactory
     */
    public function testMarkdownWithoutTree(): void
    {
        $factory  = new MarkdownFactory();
        $Markdown = $factory->Markdown();

        $this->assertSame(
            "<p>FOO https://example.com BAR</p>\n",
            $Markdown->convertToHtml('FOO https://example.com BAR')
        );

        $this->assertSame(
            "<p>FOO <a href=\"https://example.com\">https://example.com</a> BAR</p>\n",
            $Markdown->convertToHtml('FOO <https://example.com> BAR')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\CommonMark\XrefExtension
     * @covers \Fisharebest\Webtrees\Factories\MarkdownFactory
     */
    public function testMarkdownWithTree(): void
    {
        $tree = $this->createStub(Tree::class);

        $factory  = new MarkdownFactory();
        $Markdown = $factory->Markdown($tree);

        $this->assertSame(
            "<p>FOO https://example.com BAR</p>\n",
            $Markdown->convertToHtml('FOO https://example.com BAR')
        );

        $this->assertSame(
            "<p>FOO <a href=\"https://example.com\">https://example.com</a> BAR</p>\n",
            $Markdown->convertToHtml('FOO <https://example.com> BAR')
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\CommonMark\XrefExtension
     * @covers \Fisharebest\Webtrees\Factories\MarkdownFactory
     */
    public function testMarkdownWithHtml(): void
    {
        $factory  = new MarkdownFactory();
        $markdown = $factory->Markdown();

        $this->assertSame(
            "<p>&lt;b&gt; <a href=\"https://example.com\">https://example.com</a> &lt;/b&gt;</p>\n",
            $markdown->convertToHtml('<b> <https://example.com> </b>')
        );
    }
}
