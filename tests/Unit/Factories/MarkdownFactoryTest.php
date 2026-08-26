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

namespace Fisharebest\Webtrees\Tests\Unit\Factories;

use Fisharebest\Webtrees\CommonMark\XrefExtension;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Factories\MarkdownFactory;

#[CoversClass(MarkdownFactory::class)]
#[CoversClass(XrefExtension::class)]
class MarkdownFactoryTest extends TestCase
{
    public function testAutoLinkWithoutTree(): void
    {
        $factory  = new MarkdownFactory();

        self::assertSame(
            '<div class="wt-markdown"><p>FOO <a href="https://example.com">https://example.com</a> BAR</p></div>',
            $factory->autolink('FOO https://example.com BAR')
        );
    }

    public function testAutoLinkWithTree(): void
    {
        $factory = new MarkdownFactory();
        $tree    = self::createStub(Tree::class);

        self::assertSame(
            '<div class="wt-markdown"><p>FOO <a href="https://example.com">https://example.com</a> BAR</p></div>',
            $factory->autolink('FOO https://example.com BAR', $tree)
        );
    }

    public function testAutoLinkWithHtml(): void
    {
        $factory  = new MarkdownFactory();

        self::assertSame(
            '<div class="wt-markdown"><p>&lt;b&gt; <a href="https://example.com">https://example.com</a> &lt;/b&gt;</p></div>',
            $factory->autolink('<b> https://example.com </b>')
        );
    }

    public function testMarkdownWithoutTree(): void
    {
        $factory = new MarkdownFactory();

        self::assertSame(
            '<div class="wt-markdown"><p>FOO https://example.com BAR</p></div>',
            $factory->markdown('FOO https://example.com BAR')
        );

        self::assertSame(
            '<div class="wt-markdown"><p>FOO <a href="https://example.com">https://example.com</a> BAR</p></div>',
            $factory->markdown('FOO <https://example.com> BAR')
        );
    }

    public function testMarkdownWithTree(): void
    {
        $tree    = self::createStub(Tree::class);
        $factory = new MarkdownFactory();

        self::assertSame(
            '<div class="wt-markdown"><p>FOO https://example.com BAR</p></div>',
            $factory->markdown('FOO https://example.com BAR', $tree)
        );

        self::assertSame(
            '<div class="wt-markdown"><p>FOO <a href="https://example.com">https://example.com</a> BAR</p></div>',
            $factory->markdown('FOO <https://example.com> BAR', $tree)
        );
    }

    public function testMarkdownWithHtml(): void
    {
        $factory = new MarkdownFactory();

        self::assertSame(
            '<div class="wt-markdown"><p>&lt;b&gt; <a href="https://example.com">https://example.com</a> &lt;/b&gt;</p></div>',
            $factory->markdown('<b> <https://example.com> </b>')
        );
    }

    public function testSoftLineBreaks(): void
    {
        $factory = new MarkdownFactory();

        self::assertSame(
            '<div class="wt-markdown"><p>alpha<br />beta<br />gamma<br />delta</p></div>',
            $factory->autolink("alpha\nbeta\ngamma  \ndelta")
        );

        self::assertSame(
            '<div class="wt-markdown"><p>alpha<br />beta<br />gamma<br />delta</p></div>',
            $factory->markdown("alpha\nbeta\ngamma  \ndelta")
        );
    }

    public function testMultipleParagraphs(): void
    {
        $factory = new MarkdownFactory();

        self::assertSame(
            '<div class="wt-markdown"><p>alpha<br />beta</p><p>gamma<br />delta</p></div>',
            $factory->autolink("alpha\nbeta\n\n\n\ngamma\ndelta")
        );

        self::assertSame(
            '<div class="wt-markdown"><p>alpha<br />beta</p><p>gamma<br />delta</p></div>',
            $factory->markdown("alpha\nbeta\n\n\n\ngamma\ndelta")
        );
    }
}
