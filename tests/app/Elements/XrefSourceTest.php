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

namespace Fisharebest\Webtrees\Elements;

use DOMDocument;
use Fisharebest\Webtrees\Factories\SourceFactory;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Fisharebest\Webtrees\Elements\AbstractElement
 * @covers \Fisharebest\Webtrees\Elements\AbstractXrefElement
 * @covers \Fisharebest\Webtrees\Elements\XrefSource
 */
class XrefSourceTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testEdit(): void
    {
        $element = new XrefSource('');

        $tree = $this->createMock(Tree::class);

        $factory = $this->createMock(SourceFactory::class);

        $factory->expects(self::once())
            ->method('make')
            ->willReturn(null);

        Registry::sourceFactory($factory);

        $request = self::createRequest();

        Webtrees::set(ServerRequestInterface::class, $request);

        $html = $element->edit('some-id', 'some-name', '@X123@', $tree);
        $dom  = new DOMDocument();
        $dom->loadHTML($html);

        $select_nodes = $dom->getElementsByTagName('select');
        self::assertEquals(1, $select_nodes->count());

        foreach ($select_nodes as $select_node) {
            $option_nodes = $select_node->getElementsByTagName('option');
            self::assertEquals(1, $option_nodes->count());
        }
    }

    public function testEditInlineSource(): void
    {
        $element = new XrefSource('');

        $tree = $this->createMock(Tree::class);

        $request = self::createRequest();

        Webtrees::set(ServerRequestInterface::class, $request);

        $html = $element->edit('some-id', 'some-name', 'An inline source', $tree);
        $dom  = new DOMDocument();
        $dom->loadHTML($html);

        $textarea_nodes = $dom->getElementsByTagName('textarea');
        self::assertEquals(1, $textarea_nodes->count());
    }

    public function testEscape(): void
    {
        $element = new XrefSource('');

        self::assertSame('@X123@', $element->escape('@X123@'));
    }

    public function testValueXrefLink(): void
    {
        $element = new XrefSource('');

        $record = $this->createMock(Source::class);

        $record->expects(self::once())
            ->method('fullName')
            ->willReturn('Full Name');

        $record->expects(self::once())
            ->method('url')
            ->willReturn('https://url');

        $tree = $this->createMock(Tree::class);

        $factory = $this->createMock(SourceFactory::class);

        $factory->expects(self::once())
            ->method('make')
            ->willReturn($record);

        Registry::sourceFactory($factory);

        self::assertSame('<a href="https://url">Full Name</a>', $element->value('@X123@', $tree));
    }

    public function testValueXrefLinkWithInvalidXref(): void
    {
        $element = new XrefSource('');

        $tree = $this->createMock(Tree::class);

        self::assertSame('<span class="error">@invalid@</span>', $element->value('@invalid@', $tree));
    }

    public function testValueXrefLinkWithInlineData(): void
    {
        $element = new XrefSource('');

        $tree = $this->createMock(Tree::class);

        self::assertSame('<p>invalid</p>', $element->value('invalid', $tree));
    }

    public function testValueXrefLinkWithMissingRecord(): void
    {
        $element = new XrefSource('');

        $tree = $this->createMock(Tree::class);

        $factory = $this->createMock(SourceFactory::class);

        $factory->expects(self::once())
            ->method('make')
            ->willReturn(null);

        Registry::sourceFactory($factory);

        self::assertSame('<span class="error">@X321@</span>', $element->value('@X321@', $tree));
    }
}
