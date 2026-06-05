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

use Fisharebest\Webtrees\Report\FootnoteBodyData;
use Fisharebest\Webtrees\Report\LayoutBlockData;
use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FootnoteBodyData::class)]
class FootnoteBodyDataTest extends TestCase
{
    public function testConstructorAssignsProperties(): void
    {
        $style = new Style('body', '', 12.0);
        $data  = new FootnoteBodyData(
            number: 3,
            text: 'Source citation text',
            link_target: 'footnote3',
            style: $style,
        );

        self::assertSame(3, $data->number);
        self::assertSame('Source citation text', $data->text);
        self::assertSame('footnote3', $data->link_target);
        self::assertSame($style, $data->style);
    }

    public function testImplementsLayoutBlockData(): void
    {
        $style = new Style('body', '', 12.0);
        $data  = new FootnoteBodyData(
            number: 1,
            text: '',
            link_target: '',
            style: $style,
        );

        self::assertInstanceOf(LayoutBlockData::class, $data);
    }
}
