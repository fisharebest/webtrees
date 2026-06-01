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

namespace Fisharebest\Webtrees\Report;

use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(Utf8WordWrap::class)]
class Utf8WordWrapTest extends TestCase
{
    /**
     * @return array<string,array{string,int,string}>
     */
    public static function wrapCases(): array
    {
        return [
            'empty string'                 => ['', 10, ''],
            'shorter than width'           => ['hello', 10, 'hello'],
            'exactly width'                => ['1234567890', 10, '1234567890'],
            'breaks at space'              => ['hello world foo', 11, "hello world\nfoo"],
            'word ends exactly at width+1' => ['hello world', 5, "hello\nworld"],
            'long word is cut'             => ['abcdefghij', 4, "abcd\nefgh\nij"],
            'multiple lines'               => ['one two three four five', 8, "one two\nthree\nfour\nfive"],
            // Multi-byte: each Cyrillic letter is one code-point but two bytes.
            // A naive byte-based implementation would split inside a character.
            'multibyte respects codepoints' => ['абвгде жзий', 6, "абвгде\nжзий"],
            'multibyte long word cut'      => ['абвгдежзий', 4, "абвг\nдежз\nий"],
        ];
    }

    #[DataProvider('wrapCases')]
    public function testWrap(string $input, int $width, string $expected): void
    {
        self::assertSame($expected, Utf8WordWrap::wrap($input, $width));
    }

    public function testNonPositiveWidthReturnsInputUnchanged(): void
    {
        self::assertSame('hello world', Utf8WordWrap::wrap('hello world', 0));
        self::assertSame('hello world', Utf8WordWrap::wrap('hello world', -1));
    }
}
