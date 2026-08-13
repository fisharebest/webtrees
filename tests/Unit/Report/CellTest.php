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

use Fisharebest\Webtrees\Report\Cell;
use Fisharebest\Webtrees\Report\Element;
use Fisharebest\Webtrees\Report\CellAlign;
use Fisharebest\Webtrees\Report\CellNewline;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Cell::class)]
#[CoversClass(Element::class)]
class CellTest extends TestCase
{
    use ElementTestTrait;

    public function testStoresTextAndReportsConfiguredDimensions(): void
    {
        $cell = new Cell(50.0, 10.0, '', CellAlign::Left, '', $this->makeStyle('text', '', 12.0), CellNewline::NextLine, 0.0, 0.0, '', '');
        $cell->addText('Hello');
        self::assertSame('Hello', $cell->getValue());
    }
}
