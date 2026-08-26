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

namespace Fisharebest\Webtrees\Tests\Unit\GedcomFilters;

use Fisharebest\Webtrees\Encodings\UTF16BE;
use Fisharebest\Webtrees\Encodings\UTF16LE;
use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\GedcomFilters\GedcomEncodingFilter;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function fclose;
use function fflush;
use function fopen;
use function fwrite;
use function proc_close;
use function proc_open;
use function rewind;
use function str_split;
use function stream_filter_append;
use function stream_get_contents;
use function stream_set_blocking;
use function usleep;

use const STREAM_FILTER_READ;

#[CoversClass(GedcomEncodingFilter::class)]
class GedcomEncodingFilterTest extends TestCase
{
    public function testUtf8WithoutBom(): void
    {
        $gedcom = "0 HEAD\r\n1 SOUR webtrees\r\n1 CHAR UTF-8\r\n0 TRLR\r\n";
        $result = $this->filterString($gedcom, 'UTF-8');

        self::assertSame($gedcom, $result);
    }

    public function testUtf8BomStrippedFromOutput(): void
    {
        $gedcom = "0 HEAD\r\n1 SOUR webtrees\r\n1 CHAR UTF-8\r\n0 TRLR\r\n";
        $input  = UTF8::BYTE_ORDER_MARK . $gedcom;
        $result = $this->filterString($input, 'UTF-8');

        self::assertSame($gedcom, $result);
    }

    public function testUtf8BomOverridesConfiguredEncoding(): void
    {
        $gedcom = "0 HEAD\r\n1 SOUR webtrees\r\n1 CHAR ANSEL\r\n0 TRLR\r\n";
        $input  = UTF8::BYTE_ORDER_MARK . $gedcom;

        // Even when ANSEL is configured, a UTF-8 BOM should override it.
        $result = $this->filterString($input, 'ANSEL');

        self::assertSame($gedcom, $result);
    }

    public function testUtf8BomDetectedWithoutConfiguredEncoding(): void
    {
        $gedcom = "0 HEAD\r\n1 SOUR webtrees\r\n0 TRLR\r\n";
        $input  = UTF8::BYTE_ORDER_MARK . $gedcom;

        // No source encoding configured — BOM detection should determine UTF-8.
        $result = $this->filterString($input, '');

        self::assertSame($gedcom, $result);
    }

    public function testUtf16BeBomStrippedFromOutput(): void
    {
        $gedcom   = "0 HEAD\r\n1 SOUR webtrees\r\n0 TRLR\r\n";
        $encoding = new UTF16BE();
        $input    = UTF16BE::BYTE_ORDER_MARK . $encoding->fromUtf8($gedcom);
        $result   = $this->filterString($input, '');

        self::assertSame($gedcom, $result);
    }

    public function testUtf16LeBomStrippedFromOutput(): void
    {
        $gedcom   = "0 HEAD\r\n1 SOUR webtrees\r\n0 TRLR\r\n";
        $encoding = new UTF16LE();
        $input    = UTF16LE::BYTE_ORDER_MARK . $encoding->fromUtf8($gedcom);
        $result   = $this->filterString($input, '');

        self::assertSame($gedcom, $result);
    }

    public function testUtf8BomOverridesConfiguredEncodingWhenDripFed(): void
    {
        $gedcom = "0 HEAD\r\n1 SOUR webtrees\r\n0 TRLR\r\n";
        $input  = UTF8::BYTE_ORDER_MARK . $gedcom;

        // Drip-feed one byte at a time through a pipe.
        $result = $this->filterDripFeed($input, 'ANSEL');

        self::assertSame($gedcom, $result);
    }

    /**
     * Run data through the GedcomEncodingFilter stream filter.
     */
    private function filterString(string $input, string $src_encoding): string
    {
        $stream = fopen('php://memory', 'r+b');
        fwrite($stream, $input);
        rewind($stream);

        stream_filter_append($stream, GedcomEncodingFilter::class, STREAM_FILTER_READ, ['src_encoding' => $src_encoding]);

        $result = stream_get_contents($stream);
        fclose($stream);

        return $result;
    }

    /**
     * Drip-feed data one byte at a time through a pipe with the filter attached.
     */
    private function filterDripFeed(string $input, string $src_encoding): string
    {
        $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w']];
        $process     = proc_open('cat', $descriptors, $pipes);

        stream_filter_append($pipes[1], GedcomEncodingFilter::class, STREAM_FILTER_READ, ['src_encoding' => $src_encoding]);

        $output = '';

        foreach (str_split($input) as $byte) {
            fwrite($pipes[0], $byte);
            fflush($pipes[0]);
            usleep(1000);
            stream_set_blocking($pipes[1], false);
            $chunk = fread($pipes[1], 8192);

            if ($chunk !== false && $chunk !== '') {
                $output .= $chunk;
            }
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], true);
        $output .= stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        proc_close($process);

        return $output;
    }
}
