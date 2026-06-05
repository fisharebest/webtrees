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
use Fisharebest\Webtrees\Report\LayoutBlockData;
use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Report\TextFlowData;
use Fisharebest\Webtrees\Report\TextRun;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TextFlowData::class)]
class TextFlowDataTest extends TestCase
{
    public function testConstructorAssignsRuns(): void
    {
        $style = new Style('body', '', 12.0);
        $runs  = [
            new TextRun(text: 'Hello ', style: $style, color: '#000000'),
            new TextRun(text: 'world', style: $style, color: '#0000FF'),
        ];

        $data = new TextFlowData(runs: $runs);

        self::assertCount(2, $data->runs);
        self::assertSame('Hello ', $data->runs[0]->text);
        self::assertSame('world', $data->runs[1]->text);
    }

    public function testAcceptsFootnoteRefDataInRuns(): void
    {
        $style = new Style('body', '', 12.0);
        $runs  = [
            new TextRun(text: 'Some text', style: $style, color: ''),
            new FootnoteRefData(number: 1, link_target: 'fn1', style: $style),
        ];

        $data = new TextFlowData(runs: $runs);

        self::assertCount(2, $data->runs);
        self::assertInstanceOf(FootnoteRefData::class, $data->runs[1]);
    }

    public function testImplementsLayoutBlockData(): void
    {
        $data = new TextFlowData(runs: []);

        self::assertInstanceOf(LayoutBlockData::class, $data);
    }
}
