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

namespace Fisharebest\Webtrees\Tests\Unit\Services;

use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Tests\TestCase;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\Attributes\CoversClass;

use function array_diff;
use function is_dir;
use function mkdir;
use function rmdir;
use function scandir;
use function sprintf;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

#[CoversClass(UpgradeService::class)]
class UpgradeServiceTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(UpgradeService::class));
    }

    public function testMoveFilesDeletesNowEmptySourceDirectories(): void
    {
        $source_root       = $this->createTemporaryDirectory('source');
        $destination_root  = $this->createTemporaryDirectory('destination');
        $source_filesystem = $this->filesystem($source_root);

        $source_filesystem->write('root.txt', 'root');
        $source_filesystem->write('alpha/beta/nested.txt', 'nested');

        $upgrade_service = new UpgradeService($this->timeoutServiceNeverTimesOut());

        try {
            $upgrade_service->moveFiles($source_filesystem, $this->filesystem($destination_root));

            self::assertFalse($source_filesystem->fileExists('root.txt'));
            self::assertFalse($source_filesystem->fileExists('alpha/beta/nested.txt'));
            self::assertFalse($source_filesystem->directoryExists('alpha'));
            self::assertFalse($source_filesystem->directoryExists('alpha/beta'));

            self::assertTrue($this->filesystem($destination_root)->fileExists('root.txt'));
            self::assertTrue($this->filesystem($destination_root)->fileExists('alpha/beta/nested.txt'));
        } finally {
            $this->deleteTemporaryDirectory($source_root);
            $this->deleteTemporaryDirectory($destination_root);
        }
    }

    private function timeoutServiceNeverTimesOut(): TimeoutService
    {
        $timeout_service = $this->createStub(TimeoutService::class);
        $timeout_service
            ->method('isTimeNearlyUp')
            ->willReturn(false);

        return $timeout_service;
    }

    private function filesystem(string $root): FilesystemOperator
    {
        return new Filesystem(new LocalFilesystemAdapter($root));
    }

    private function createTemporaryDirectory(string $name): string
    {
        $directory = sprintf('%s/%s-%s', sys_get_temp_dir(), $name, uniqid('', true));
        mkdir($directory, 0755, true);

        return $directory;
    }

    private function deleteTemporaryDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $files = scandir($directory);

        if ($files === false) {
            return;
        }

        foreach (array_diff($files, ['.', '..']) as $file) {
            $path = $directory . '/' . $file;

            if (is_dir($path)) {
                $this->deleteTemporaryDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($directory);
    }
}
