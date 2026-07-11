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

use Fisharebest\Webtrees\Report\FontCoverageIndex;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use RuntimeException;

use function file_put_contents;
use function glob;
use function json_encode;
use function mkdir;
use function rmdir;
use function sprintf;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

use const JSON_THROW_ON_ERROR;

#[CoversClass(FontCoverageIndex::class)]
class FontCoverageIndexTest extends TestCase
{
    private string $temp_directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->temp_directory = sys_get_temp_dir() . '/webtrees-font-coverage-' . uniqid('', true);
        mkdir($this->temp_directory, 0o755, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->temp_directory . '/*') ?: [] as $file_name) {
            unlink($file_name);
        }

        rmdir($this->temp_directory);

        parent::tearDown();
    }

    public function testSupportsCodepointAndFindsFirstSupportingFont(): void
    {
        $this->writeCoverage('fonta', [[65, 90]]);
        $this->writeCoverage('fontb', [[97, 122]]);

        $index = new FontCoverageIndex($this->temp_directory);

        self::assertTrue($index->fontSupportsCodepoint('fonta', 65));
        self::assertFalse($index->fontSupportsCodepoint('fonta', 97));
        self::assertSame('fontb', $index->firstSupportingFont(['fonta', 'fontb'], 98));
        self::assertNull($index->firstSupportingFont(['fonta', 'fontb'], 0x4E00));
    }

    public function testCachesRangesAfterFirstRead(): void
    {
        $this->writeCoverage('fonta', [[65, 90]]);

        $index = new FontCoverageIndex($this->temp_directory);

        self::assertTrue($index->fontSupportsCodepoint('fonta', 65));

        foreach (glob($this->temp_directory . '/*') ?: [] as $file_name) {
            unlink($file_name);
        }

        self::assertTrue($index->fontSupportsCodepoint('fonta', 66));
    }

    public function testThrowsForMissingCoverageFile(): void
    {
        $index = new FontCoverageIndex($this->temp_directory);

        $this->expectException(RuntimeException::class);
        $index->rangesForFont('missing-font');
    }

    private function writeCoverage(string $font_name, array $ranges): void
    {
        $json = sprintf(
            "{\n    \"font\": \"%s\",\n    \"format\": \"tc-lib-pdf-bmp-coverage-v1\",\n    \"ranges\": %s\n}\n",
            $font_name,
            json_encode($ranges, JSON_THROW_ON_ERROR),
        );

        file_put_contents($this->temp_directory . '/' . $font_name . '.coverage.json', $json);
    }
}

