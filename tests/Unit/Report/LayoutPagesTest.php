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

use Fisharebest\Webtrees\Report\LayoutBlock;
use Fisharebest\Webtrees\Report\LayoutPages;
use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LayoutPages::class)]
class LayoutPagesTest extends TestCase
{
    public function testConstructorAssignsPages(): void
    {
        $block = LayoutBlock::pageBreak(page: 0);
        $pages = new LayoutPages(pages: [0 => [$block]]);

        self::assertCount(1, $pages->pages);
        self::assertSame($block, $pages->pages[0][0]);
    }

    public function testFlattenReturnsBlocksInPageOrder(): void
    {
        $style  = new Style('body', '', 12.0);
        $block0 = LayoutBlock::text(0, 0.0, 0.0, 100.0, 12.0, 'page0', $style, '');
        $block1 = LayoutBlock::text(1, 0.0, 0.0, 100.0, 12.0, 'page1', $style, '');
        $block2 = LayoutBlock::text(2, 0.0, 0.0, 100.0, 12.0, 'page2', $style, '');

        // Provide pages out of order to verify ksort behavior
        $pages = new LayoutPages(pages: [
            2 => [$block2],
            0 => [$block0],
            1 => [$block1],
        ]);

        $flattened = $pages->flatten();

        self::assertCount(3, $flattened);
        self::assertSame($block0, $flattened[0]);
        self::assertSame($block1, $flattened[1]);
        self::assertSame($block2, $flattened[2]);
    }

    public function testFlattenWithEmptyPages(): void
    {
        $pages = new LayoutPages(pages: []);

        self::assertSame([], $pages->flatten());
    }
}
