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
use Fisharebest\Webtrees\Report\TextWrapper;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function array_slice;
use function count;
use function implode;
use function mb_strlen;

#[CoversClass(TextWrapper::class)]
class TextWrapperTest extends TestCase
{
    public function testWrapTextSplitsLongUrlTokenToFitWidth(): void
    {
        $style = new Style('body', '', 10.0);
        $adaptor = new TextWrapper(new FixedWidthTextMeasurer());
        $text = 'https://example.com/path/to/a/very/long/resource?query=abcdef';

        $lines = $adaptor->wrapText($text, $style, 12.0);

        self::assertGreaterThan(1, count($lines));
        self::assertSame($text, implode('', $lines));

        foreach ($lines as $line) {
            self::assertLessThanOrEqual(12, mb_strlen($line));
        }
    }

    public function testWrapTextKeepsPrefixAndSplitsFollowingLongToken(): void
    {
        $style = new Style('body', '', 10.0);
        $adaptor = new TextWrapper(new FixedWidthTextMeasurer());
        $text = 'Citation https://example.com/path/to/a/very/long/resource';

        $lines = $adaptor->wrapText($text, $style, 14.0);

        // "Citation " (9 chars) + "https" (5 chars) fills the first 14-char line.
        // The URL remainder is split across subsequent lines.
        self::assertSame('Citation https', $lines[0]);
        self::assertSame('://example.com/path/to/a/very/long/resource', implode('', array_slice($lines, 1)));

        foreach ($lines as $line) {
            self::assertLessThanOrEqual(14, mb_strlen($line));
        }
    }
}
