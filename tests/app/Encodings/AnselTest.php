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
use Fisharebest\Webtrees\Encodings\ANSEL;
use Fisharebest\Webtrees\Encodings\UTF8;
use Normalizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function bin2hex;
use function count;
use function ctype_alpha;
use function dechex;
use function in_array;
use function preg_split;
use function range;
use function strlen;

use const PREG_SPLIT_NO_EMPTY;

#[CoversClass(AbstractEncoding::class)]
#[CoversClass(ANSEL::class)]
#[CoversClass(UTF8::class)]
class AnselTest extends TestCase
{
    private const TEST_DATA = [
        "\x00\x01\x02\x03\x04\x05\x06\x07"         => "\x00\x01\x02\x03\x04\x05\x06\x07",
        "\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F"         => "\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F",
        "\x10\x11\x12\x13\x14\x15\x16\x17"         => "\x10\x11\x12\x13\x14\x15\x16\x17",
        "\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F"         => "\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F",
        ' !"#$%&\''                                => "\x20\x21\x22\x23\x24\x25\x26\x27",
        '()*+,-./'                                 => "\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F",
        '01234567'                                 => "\x30\x31\x32\x33\x34\x35\x36\x37",
        '89:;<=>?'                                 => "\x38\x39\x3A\x3B\x3C\x3D\x3E\x3F",
        '@ABCDEFG'                                 => "\x40\x41\x42\x43\x44\x45\x46\x47",
        'HIJKLMNO'                                 => "\x48\x49\x4A\x4B\x4C\x4D\x4E\x4F",
        'PQRSTUVW'                                 => "\x50\x51\x52\x53\x54\x55\x56\x57",
        'XYZ[\\]^_'                                => "\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F",
        '`abcdefg'                                 => "\x60\x61\x62\x63\x64\x65\x66\x67",
        'hijklmno'                                 => "\x68\x69\x6A\x6B\x6C\x6D\x6E\x6F",
        'pqrstuvw'                                 => "\x70\x71\x72\x73\x74\x75\x76\x77",
        "xyz{|}~\x7F"                              => "\x78\x79\x7A\x7B\x7C\x7D\x7E\x7F",
        "\xC2\x98\xC2\x9C\xE2\x80\x8D\xE2\x80\x8C" => "\x88\x89\x8D\x8E",
        'ŁØĐÞÆŒʹ'                                  => "\xA1\xA2\xA3\xA4\xA5\xA6\xA7",
        '·♭®±ƠƯʼ'                                  => "\xA8\xA9\xAA\xAB\xAC\xAD\xAE",
        'ʻłøđþæœʺ'                                 => "\xB0\xB1\xB2\xB3\xB4\xB5\xB6\xB7",
        'ı£ðơư'                                    => "\xB8\xB9\xBA\xBC\xBD",
        '°ℓ℗©♯¿¡ẞ€'                                => "\xC0\xC1\xC2\xC3\xC4\xC5\xC6\xC7\xC8",
        // Combining diacritics
        'ảàáâãāăȧ'                                 => "\xE0a\xE1a\xE2a\xE3a\xE4a\xE5a\xE6a\xE7a",
        'äǎåa͡a̕a̋a̐'                              => "\xE8a\xE9a\xEAa\xEBa\xEDa\xEEa\xEFa",
        'a̧ąạa̤ḁa̳a̲a̦'                            => "\xF0a\xF1a\xF2a\xF3a\xF4a\xF5a\xF6a\xF7a",
        'a̜a̮a͠a̓a̸'                               => "\xF8a\xF9a\xFAa\xFEa\xFFa",
        // Diacritics with non-ascii
        'ǣ'                                        => "\xE5\xB5",
        // LATIN CAPITAL LETTER O WITH DIAERESIS AND MACRON
        'Ō̈'                                       => "\xE5\xE8O",
        // LATIN CAPITAL LETTER O WITH MACRON AND DIAERESIS
        'Ȫ'                                        => "\xE8\xE5O",
    ];

    private const UNPRINTABLE = [
        "\x80\x81\x82\x83\x84\x85\x86\x87",
        "\x8A\x8B\x8C\x8F",
        "\x90\x91\x92\x93\x94\x95\x96\x97",
        "\x98\x99\x9A\x9B\x9C\x9D\x9E\x9F",
        "\xA0",
        "\xAF",
        "\xBB",
        "\xC9\xCA\xCB\xCC\xCD\xCE",
        "\xD0\xD1\xD2\xD3\xD4\xD5\xD6\xD7",
        "\xD8\xD9\xDA\xDB\xDC\xDD\xDE\xDF",
        "\xFC\xFD",
    ];

    private const MULTIPART_DIACRITIC = ["\xEC", "\xFB"];

    public function testPreComposedCharacters(): void
    {
        $latin_code_blocks = [
            range(0x80, 0xFF),
            range(0x100, 0x17F),
            range(0x180, 0x24F),
            range(0x1E00, 0x1EFF),
        ];

        $ansel_combining_characters = [
            UTF8::COMBINING_HOOK_ABOVE,
            UTF8::COMBINING_GRAVE_ACCENT,
            UTF8::COMBINING_ACUTE_ACCENT,
            UTF8::COMBINING_CIRCUMFLEX_ACCENT,
            UTF8::COMBINING_TILDE,
            UTF8::COMBINING_MACRON,
            UTF8::COMBINING_BREVE,
            UTF8::COMBINING_DOT_ABOVE,
            UTF8::COMBINING_DIAERESIS,
            UTF8::COMBINING_CARON,
            UTF8::COMBINING_RING_ABOVE,
            UTF8::COMBINING_DOUBLE_INVERTED_BREVE,
            UTF8::COMBINING_COMMA_ABOVE_RIGHT,
            UTF8::COMBINING_DOUBLE_ACUTE_ACCENT,
            UTF8::COMBINING_CANDRABINDU,
            UTF8::COMBINING_CEDILLA,
            UTF8::COMBINING_OGONEK,
            UTF8::COMBINING_DOT_BELOW,
            UTF8::COMBINING_DIAERESIS_BELOW,
            UTF8::COMBINING_RING_BELOW,
            UTF8::COMBINING_DOUBLE_LOW_LINE,
            UTF8::COMBINING_LOW_LINE,
            UTF8::COMBINING_COMMA_BELOW,
            UTF8::COMBINING_LEFT_HALF_RING_BELOW,
            UTF8::COMBINING_BREVE_BELOW,
            UTF8::COMBINING_DOUBLE_TILDE,
            UTF8::REPLACEMENT_CHARACTER,
            UTF8::REPLACEMENT_CHARACTER,
            UTF8::COMBINING_COMMA_ABOVE,
            UTF8::COMBINING_LONG_SOLIDUS_OVERLAY,
        ];

        $encoding = new ANSEL();

        foreach ($latin_code_blocks as $codes) {
            foreach ($codes as $code) {
                $utf8 = UTF8::chr($code);
                $norm = Normalizer::normalize($utf8, Normalizer::FORM_D);

                if ($norm !== $utf8) {
                    $chars = preg_split('//u', $norm, -1, PREG_SPLIT_NO_EMPTY);
                    if (!ctype_alpha($chars[0])) {
                        continue;
                    }
                    if (!in_array($chars[1], $ansel_combining_characters, true)) {
                        continue;
                    }
                    if (count($chars) >= 3 && !in_array($chars[2], $ansel_combining_characters, true)) {
                        continue;
                    }

                    static::assertSame($utf8, $encoding->toUtf8($encoding->fromUtf8($utf8)), 'U+' . dechex($code));
                }
            }
        }
    }

    public function testToUtf8(): void
    {
        $encoding = new ANSEL();

        foreach (self::TEST_DATA as $utf8 => $ansel) {
            self::assertSame($utf8, $encoding->toUtf8($ansel), bin2hex($utf8) . ' ' . bin2hex($encoding->toUtf8($ansel)));
        }
    }

    public function testFromUtf8(): void
    {
        $encoding = new ANSEL();

        foreach (self::TEST_DATA as $utf8 => $other) {
            self::assertSame($other, $encoding->fromUtf8($utf8));
        }
    }

    public function testUnprintable(): void
    {
        $encoding = new ANSEL();

        foreach (self::UNPRINTABLE as $chars) {
            $expected = str_repeat(UTF8::REPLACEMENT_CHARACTER, strlen($chars));
            self::assertSame($expected, $encoding->toUtf8($chars));
        }
    }

    public function testMultiPartDiacritic(): void
    {
        $encoding = new ANSEL();

        foreach (self::MULTIPART_DIACRITIC as $chars) {
            self::assertSame('', $encoding->toUtf8($chars));
        }
    }
}
