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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

#[CoversClass(GedcomExportService::class)]
class GedcomExportServiceTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(GedcomExportService::class));
    }

    public function testWrapLongLinesShortLine(): void
    {
        $response_factory = self::createStub(ResponseFactoryInterface::class);
        $stream_factory   = self::createStub(StreamFactoryInterface::class);
        $service          = new GedcomExportService($response_factory, $stream_factory);

        $input  = '1 NAME John';
        $result = $service->wrapLongLines($input, 255);

        self::assertSame($input, $result);
    }

    public function testWrapLongLinesLongLine(): void
    {
        $response_factory = self::createStub(ResponseFactoryInterface::class);
        $stream_factory   = self::createStub(StreamFactoryInterface::class);
        $service          = new GedcomExportService($response_factory, $stream_factory);

        $long_value = str_repeat('x', 300);
        $input      = '1 NOTE ' . $long_value;
        $result     = $service->wrapLongLines($input, 80);

        self::assertStringContainsString('1 NOTE', $result);
        self::assertStringContainsString('2 CONC', $result);

        // Verify that no individual line exceeds the maximum length.
        foreach (explode("\n", $result) as $line) {
            self::assertLessThanOrEqual(80, mb_strlen($line));
        }

        // Verify that concatenating all CONC fragments reconstructs the original value.
        $reconstructed = '';
        foreach (explode("\n", $result) as $line) {
            if (str_starts_with($line, '1 NOTE ')) {
                $reconstructed .= substr($line, strlen('1 NOTE '));
            } elseif (str_starts_with($line, '2 CONC ')) {
                $reconstructed .= substr($line, strlen('2 CONC '));
            }
        }
        self::assertSame($long_value, $reconstructed);
    }

    public function testWrapLongLinesMultipleLines(): void
    {
        $response_factory = self::createStub(ResponseFactoryInterface::class);
        $stream_factory   = self::createStub(StreamFactoryInterface::class);
        $service          = new GedcomExportService($response_factory, $stream_factory);

        $short_line = '1 NAME John';
        $long_value = str_repeat('y', 200);
        $long_line  = '1 NOTE ' . $long_value;
        $input      = $short_line . "\n" . $long_line;
        $result     = $service->wrapLongLines($input, 80);

        // The short line must pass through unchanged.
        self::assertStringStartsWith($short_line . "\n", $result);

        // The long line must be split.
        self::assertStringContainsString('2 CONC', $result);

        // No line may exceed the limit.
        foreach (explode("\n", $result) as $line) {
            self::assertLessThanOrEqual(80, mb_strlen($line));
        }
    }
}
