<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Factories\MediaFactory;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ServerRequestInterface;

use function app;

/**
 * Test harness for the class XrefMedia
 *
 * @covers \Fisharebest\Webtrees\Elements\AbstractElement
 * @covers \Fisharebest\Webtrees\Elements\AbstractXrefElement
 * @covers \Fisharebest\Webtrees\Elements\XrefMedia
 */
class XrefMediaTest extends TestCase
{
    /**
     * @return void
     */
    public function testEdit(): void
    {
        $element = new XrefMedia('');

        $tree = $this->createMock(Tree::class);

        $factory = $this->createMock(MediaFactory::class);

        $factory->expects(self::once())
            ->method('make')
            ->willReturn(null);

        Registry::mediaFactory($factory);

        $request = self::createRequest();

        app()->instance(ServerRequestInterface::class, $request);

        $html = $element->edit('some-id', 'some-name', '@X123@', $tree);
        $dom  = new DOMDocument();
        $dom->loadHTML($html);

        $select_nodes = $dom->getElementsByTagName('select');
        self::assertEquals(1, $select_nodes->count());

        $option_nodes = $select_nodes[0]->getElementsByTagName('option');
        self::assertEquals(1, $option_nodes->count());
    }
    /**
     * @return void
     */
    public function testEscape(): void
    {
        $element = new XrefMedia('');

        self::assertSame('@X123@', $element->escape('@X123@'));
    }

    /**
     * @return void
     */
    public function testValueXrefLink(): void
    {
        $element = new XrefMedia('');

        $record = $this->createMock(Media::class);

        $record->expects(self::once())
            ->method('fullName')
            ->willReturn('Full Name');

        $record->expects(self::once())
            ->method('url')
            ->willReturn('https://url');

        $tree = $this->createMock(Tree::class);

        $factory = $this->createMock(MediaFactory::class);

        $factory->expects(self::once())
            ->method('make')
            ->willReturn($record);


        Registry::mediaFactory($factory);

        self::assertSame('<a href="https://url">Full Name</a>', $element->value('@X123@', $tree));
    }

    /**
     * @return void
     */
    public function testValueXrefLinkWithInvalidXref(): void
    {
        $element = new XrefMedia('');

        $tree = $this->createMock(Tree::class);

        self::assertSame('<span class="error">invalid</span>', $element->value('invalid', $tree));
    }

    /**
     * @return void
     */
    public function testValueXrefLinkWithMissingRecord(): void
    {
        $element = new XrefMedia('');

        $tree = $this->createMock(Tree::class);

        $factory = $this->createMock(MediaFactory::class);

        $factory->expects(self::once())
            ->method('make')
            ->willReturn(null);

        Registry::mediaFactory($factory);

        self::assertSame('<span class="error">@X321@</span>', $element->value('@X321@', $tree));
    }
}
