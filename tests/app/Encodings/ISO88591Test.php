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

namespace Fisharebest\Webtrees\Tests\Encodings;

use Fisharebest\Webtrees\Encodings\AbstractEncoding;
use Fisharebest\Webtrees\Encodings\ISO88591;
use Fisharebest\Webtrees\Encodings\UTF8;
use PHPUnit\Framework\TestCase;

use function chr;
use function dechex;
use function iconv;
use function range;

/**
 * @covers \Fisharebest\Webtrees\Encodings\AbstractEncoding
 * @covers \Fisharebest\Webtrees\Encodings\ISO88591
 */
class ISO88591Test extends TestCase
{
    public function testToUtf8(): void
    {
        $encoding = new ISO88591();

        // iconv uses CP1252, and therefore adds chacaters to 0x80 - 0x9F
        $ranges = [
            range(0, 0x7F),
            range(0xA0, 0xFF),
        ];

        foreach (range(0x80, 0x9F) as $code_point) {
            $character = chr($code_point);
            $actual    = $encoding->toUtf8($character);

            self::assertSame(UTF8::REPLACEMENT_CHARACTER, $actual, dechex($code_point) . '=>' . $actual);
        }

        foreach ($ranges as $range) {
            foreach ($range as $code_point) {
                $character = chr($code_point);
                $actual    = $encoding->toUtf8($character);
                $expected  = iconv(ISO88591::NAME, UTF8::NAME, $character);
                $expected  = $expected === '' ? UTF8::REPLACEMENT_CHARACTER : $expected;

                self::assertSame($expected, $actual, dechex($code_point) . '=>' . $actual . ' ' . $expected);
            }
        }
    }
}
