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

use Fisharebest\Webtrees\Report\FootnoteRefData;
use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FootnoteRefData::class)]
class FootnoteRefDataTest extends TestCase
{
    public function testConstructorAssignsProperties(): void
    {
        $style = new Style('body', '', 10.0);
        $data  = new FootnoteRefData(
            number: 5,
            link_target: 'footnote5',
            style: $style,
        );

        self::assertSame(5, $data->number);
        self::assertSame('footnote5', $data->link_target);
        self::assertSame($style, $data->style);
    }
}
