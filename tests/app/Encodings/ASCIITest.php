<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Encodings\ASCII;
use Fisharebest\Webtrees\Encodings\UTF8;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function chr;
use function iconv;
use function range;


#[CoversClass(AbstractEncoding::class)]
#[CoversClass(ASCII::class)]
class ASCIITest extends TestCase
{
    public function testToUtf8(): void
    {
        $encoding = new ASCII();

        foreach (range(0, 127) as $code_point) {
            $character = chr($code_point);
            $actual    = $encoding->toUtf8($character);
            $expected  = iconv(ASCII::NAME, UTF8::NAME, $character);

            static::assertSame($expected, $actual);
        }

        foreach (range(128, 255) as $code_point) {
            $character = chr($code_point);
            $actual    = $encoding->toUtf8($character);
            $expected  = UTF8::REPLACEMENT_CHARACTER;

            static::assertSame($expected, $actual);
        }
    }
}
