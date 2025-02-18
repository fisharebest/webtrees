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
use Fisharebest\Webtrees\Encodings\MacRoman;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Fisharebest\Webtrees\Encodings\AbstractEncoding
 * @covers \Fisharebest\Webtrees\Encodings\MacRoman
 */
class MacRomanTest extends TestCase
{
    public function testToUtf8HexStrings(): void
    {
        $encoding = new MacRoman();

        self::assertSame("\x00\x01\x02\x03\x04\x05\x06\x07", $encoding->toUtf8("\x00\x01\x02\x03\x04\x05\x06\x07"));
        self::assertSame("\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F", $encoding->toUtf8("\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F"));
        self::assertSame("\x10\x11\x12\x13\x14\x15\x16\x17", $encoding->toUtf8("\x10\x11\x12\x13\x14\x15\x16\x17"));
        self::assertSame("\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F", $encoding->toUtf8("\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F"));
        self::assertSame("\x20\x21\x22\x23\x24\x25\x26\x27", $encoding->toUtf8("\x20\x21\x22\x23\x24\x25\x26\x27"));
        self::assertSame("\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F", $encoding->toUtf8("\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F"));
        self::assertSame("\x30\x31\x32\x33\x34\x35\x36\x37", $encoding->toUtf8("\x30\x31\x32\x33\x34\x35\x36\x37"));
        self::assertSame("\x38\x39\x3A\x3B\x3C\x3D\x3E\x3F", $encoding->toUtf8("\x38\x39\x3A\x3B\x3C\x3D\x3E\x3F"));
        self::assertSame("\x40\x41\x42\x43\x44\x45\x46\x47", $encoding->toUtf8("\x40\x41\x42\x43\x44\x45\x46\x47"));
        self::assertSame("\x48\x49\x4A\x4B\x4C\x4D\x4E\x4F", $encoding->toUtf8("\x48\x49\x4A\x4B\x4C\x4D\x4E\x4F"));
        self::assertSame("\x50\x51\x52\x53\x54\x55\x56\x57", $encoding->toUtf8("\x50\x51\x52\x53\x54\x55\x56\x57"));
        self::assertSame("\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F", $encoding->toUtf8("\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F"));
        self::assertSame("\x60\x61\x62\x63\x64\x65\x66\x67", $encoding->toUtf8("\x60\x61\x62\x63\x64\x65\x66\x67"));
        self::assertSame("\x68\x69\x6A\x6B\x6C\x6D\x6E\x6F", $encoding->toUtf8("\x68\x69\x6A\x6B\x6C\x6D\x6E\x6F"));
        self::assertSame("\x70\x71\x72\x73\x74\x75\x76\x77", $encoding->toUtf8("\x70\x71\x72\x73\x74\x75\x76\x77"));
        self::assertSame("\x78\x79\x7A\x7B\x7C\x7D\x7E\x7F", $encoding->toUtf8("\x78\x79\x7A\x7B\x7C\x7D\x7E\x7F"));
        self::assertSame(
            "\xC3\x84\xC3\x85\xC3\x87\xC3\x89\xC3\x91\xC3\x96\xC3\x9C\xC3\xA1",
            $encoding->toUtf8("\x80\x81\x82\x83\x84\x85\x86\x87")
        );
        self::assertSame(
            "\xC3\xA0\xC3\xA2\xC3\xA4\xC3\xA3\xC3\xA5\xC3\xA7\xC3\xA9\xC3\xA8",
            $encoding->toUtf8("\x88\x89\x8A\x8B\x8C\x8D\x8E\x8F")
        );
        self::assertSame(
            "\xE2\x80\xA0\xC2\xB0\xC2\xA2\xC2\xA3\xC2\xA7\xE2\x80\xA2\xC2\xB6\xC3\x9F",
            $encoding->toUtf8("\xA0\xA1\xA2\xA3\xA4\xA5\xA6\xA7")
        );
        self::assertSame(
            "\xC2\xAE\xC2\xA9\xE2\x84\xA2\xC2\xB4\xC2\xA8\xE2\x89\xA0\xC3\x86\xC3\x98",
            $encoding->toUtf8("\xA8\xA9\xAA\xAB\xAC\xAD\xAE\xAF")
        );
        self::assertSame(
            "\xE2\x88\x9E\xC2\xB1\xE2\x89\xA4\xE2\x89\xA5\xC2\xA5\xC2\xB5\xE2\x88\x82\xE2\x88\x91",
            $encoding->toUtf8("\xB0\xB1\xB2\xB3\xB4\xB5\xB6\xB7")
        );
        self::assertSame(
            "\xE2\x88\x8F\xCF\x80\xE2\x88\xAB\xC2\xAA\xC2\xBA\xCE\xA9\xC3\xA6\xC3\xB8",
            $encoding->toUtf8("\xB8\xB9\xBA\xBB\xBC\xBD\xBE\xBF")
        );
        self::assertSame(
            "\xC2\xBF\xC2\xA1\xC2\xAC\xE2\x88\x9A\xC6\x92\xE2\x89\x88\xE2\x88\x86\xC2\xAB",
            $encoding->toUtf8("\xC0\xC1\xC2\xC3\xC4\xC5\xC6\xC7")
        );
        self::assertSame(
            "\xC2\xBB\xE2\x80\xA6\xC2\xA0\xC3\x80\xC3\x83\xC3\x95\xC5\x92\xC5\x93",
            $encoding->toUtf8("\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF")
        );
        self::assertSame(
            "\xE2\x80\x93\xE2\x80\x94\xE2\x80\x9C\xE2\x80\x9D\xE2\x80\x98\xE2\x80\x99\xC3\xB7\xE2\x97\x8A",
            $encoding->toUtf8("\xD0\xD1\xD2\xD3\xD4\xD5\xD6\xD7")
        );
        self::assertSame(
            "\xC3\xBF\xC5\xB8\xE2\x81\x84\xE2\x82\xAC\xE2\x80\xB9\xE2\x80\xBA\xEF\xAC\x81\xEF\xAC\x82",
            $encoding->toUtf8("\xD8\xD9\xDA\xDB\xDC\xDD\xDE\xDF")
        );
    }

    public function testToUtf8StringLiterals(): void
    {
        $encoding = new MacRoman();

        self::assertSame(' !"#$%&\'', $encoding->toUtf8("\x20\x21\x22\x23\x24\x25\x26\x27"));
        self::assertSame('()*+,-./', $encoding->toUtf8("\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F"));
        self::assertSame('01234567', $encoding->toUtf8("\x30\x31\x32\x33\x34\x35\x36\x37"));
        self::assertSame('89:;<=>?', $encoding->toUtf8("\x38\x39\x3A\x3B\x3C\x3D\x3E\x3F"));
        self::assertSame('@ABCDEFG', $encoding->toUtf8("\x40\x41\x42\x43\x44\x45\x46\x47"));
        self::assertSame('HIJKLMNO', $encoding->toUtf8("\x48\x49\x4A\x4B\x4C\x4D\x4E\x4F"));
        self::assertSame('PQRSTUVW', $encoding->toUtf8("\x50\x51\x52\x53\x54\x55\x56\x57"));
        self::assertSame('XYZ[\\]^_', $encoding->toUtf8("\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F"));
        self::assertSame('`abcdefg', $encoding->toUtf8("\x60\x61\x62\x63\x64\x65\x66\x67"));
        self::assertSame('hijklmno', $encoding->toUtf8("\x68\x69\x6A\x6B\x6C\x6D\x6E\x6F"));
        self::assertSame('pqrstuvw', $encoding->toUtf8("\x70\x71\x72\x73\x74\x75\x76\x77"));
        self::assertSame("xyz{|}~\x7F", $encoding->toUtf8("\x78\x79\x7A\x7B\x7C\x7D\x7E\x7F"));
        self::assertSame('ÄÅÇÉÑÖÜá', $encoding->toUtf8("\x80\x81\x82\x83\x84\x85\x86\x87"));
        self::assertSame('àâäãåçéè', $encoding->toUtf8("\x88\x89\x8A\x8B\x8C\x8D\x8E\x8F"));
        self::assertSame('êëíìîïñó', $encoding->toUtf8("\x90\x91\x92\x93\x94\x95\x96\x97"));
        self::assertSame('òôöõúùûü', $encoding->toUtf8("\x98\x99\x9A\x9B\x9C\x9D\x9E\x9F"));
        self::assertSame('†°¢£§•¶ß', $encoding->toUtf8("\xA0\xA1\xA2\xA3\xA4\xA5\xA6\xA7"));
        self::assertSame('®©™´¨≠ÆØ', $encoding->toUtf8("\xA8\xA9\xAA\xAB\xAC\xAD\xAE\xAF"));
        self::assertSame('∞±≤≥¥µ∂∑', $encoding->toUtf8("\xB0\xB1\xB2\xB3\xB4\xB5\xB6\xB7"));
        self::assertSame('∏π∫ªºΩæø', $encoding->toUtf8("\xB8\xB9\xBA\xBB\xBC\xBD\xBE\xBF"));
        self::assertSame('¿¡¬√ƒ≈∆«', $encoding->toUtf8("\xC0\xC1\xC2\xC3\xC4\xC5\xC6\xC7"));
        self::assertSame('»… ÀÃÕŒœ', $encoding->toUtf8("\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF"));
        self::assertSame('–—“”‘’÷◊', $encoding->toUtf8("\xD0\xD1\xD2\xD3\xD4\xD5\xD6\xD7"));
        self::assertSame('ÿŸ⁄€‹›ﬁﬂ', $encoding->toUtf8("\xD8\xD9\xDA\xDB\xDC\xDD\xDE\xDF"));
    }
}
