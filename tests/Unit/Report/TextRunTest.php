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

use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Report\TextRun;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TextRun::class)]
class TextRunTest extends TestCase
{
    public function testConstructorAssignsProperties(): void
    {
        $style = new Style('heading', 'b', 14.0);
        $run   = new TextRun(
            text: 'Chapter 1',
            style: $style,
            color: '#112233',
        );

        self::assertSame('Chapter 1', $run->text);
        self::assertSame($style, $run->style);
        self::assertSame('#112233', $run->color);
    }
}
