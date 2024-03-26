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
use Fisharebest\Webtrees\Encodings\UTF16BE;
use Fisharebest\Webtrees\Encodings\UTF8;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function chr;
use function dechex;
use function iconv;
use function intdiv;
use function range;


#[CoversClass(AbstractEncoding::class)]
#[CoversClass(UTF16BE::class)]
class UTF16BETest extends TestCase
{
    public function testToUtf8(): void
    {
        $encoding = new UTF16BE();

        foreach (range(0, 0x7F) as $code) {
            $char     = chr(intdiv($code, 256)) . chr($code % 256);
            $expected = iconv(UTF16BE::NAME, UTF8::NAME, $char);
            $actual   = $encoding->toUtf8($char);

            static::assertSame($expected, $actual, 'U+' . dechex($code));
        }

        foreach (range(0x80, 0xFF) as $code) {
            $char   = chr(intdiv($code, 256)) . chr($code % 256);
            $actual = $encoding->toUtf8($char);

            static::assertSame(UTF8::REPLACEMENT_CHARACTER, $actual, 'U+' . dechex($code));
        }

        foreach (range(0x100, 0xD7FF) as $code) {
            $char     = chr(intdiv($code, 256)) . chr($code % 256);
            $expected = iconv(UTF16BE::NAME, UTF8::NAME, $char);
            $actual   = $encoding->toUtf8($char);

            static::assertSame($expected, $actual, 'U+' . dechex($code));
        }

        foreach (range(0xD800, 0xDFFF) as $code) {
            $char   = chr(intdiv($code, 256)) . chr($code % 256);
            $actual = $encoding->toUtf8($char);

            static::assertSame(UTF8::REPLACEMENT_CHARACTER, $actual, 'U+' . dechex($code));
        }

        foreach (range(0xE000, 0xFFFF) as $code) {
            $char     = chr(intdiv($code, 256)) . chr($code % 256);
            $expected = iconv(UTF16BE::NAME, UTF8::NAME, $char);
            $actual   = $encoding->toUtf8($char);

            static::assertSame($expected, $actual, 'U+' . dechex($code));
        }
    }
}
