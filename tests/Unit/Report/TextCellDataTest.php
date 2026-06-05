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

use Fisharebest\Webtrees\Report\CellAlign;
use Fisharebest\Webtrees\Report\LayoutBlockData;
use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Report\TextCellData;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TextCellData::class)]
class TextCellDataTest extends TestCase
{
    public function testConstructorAssignsProperties(): void
    {
        $style = new Style('body', '', 12.0);
        $data  = new TextCellData(
            text: 'Hello World',
            style: $style,
            align: CellAlign::Right,
            background_color: '#FFFFFF',
            border: 'LRTB',
            border_color: '#000000',
            text_color: '#333333',
            url: 'https://example.com',
        );

        self::assertSame('Hello World', $data->text);
        self::assertSame($style, $data->style);
        self::assertSame(CellAlign::Right, $data->align);
        self::assertSame('#FFFFFF', $data->background_color);
        self::assertSame('LRTB', $data->border);
        self::assertSame('#000000', $data->border_color);
        self::assertSame('#333333', $data->text_color);
        self::assertSame('https://example.com', $data->url);
    }

    public function testImplementsLayoutBlockData(): void
    {
        $style = new Style('body', '', 12.0);
        $data  = new TextCellData(
            text: '',
            style: $style,
            align: CellAlign::Left,
            background_color: '',
            border: '',
            border_color: '',
            text_color: '',
            url: '',
        );

        self::assertInstanceOf(LayoutBlockData::class, $data);
    }
}
