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

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\DirectoryListing;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\FilesystemReader;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MediaFileService::class)]
class MediaFileServiceTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(MediaFileService::class));
    }

    public function testUnusedFilesExcludesLinkedFilesInItsTreeAndRemoteUrls(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('first', 'First');
        $other_tree = (new TreeService(new GedcomImportService()))->create('second', 'Second');
        foreach (
            [
                [$tree->id(), 'used.jpg'],
                [$tree->id(), 'https://example.com/remote.jpg'],
                [$other_tree->id(), 'other-tree.jpg'],
            ] as [$tree_id, $path]
        ) {
            DB::query(table: 'media_file')->insert(values: [
                'm_id' => 'M1', 'm_file' => $tree_id, 'multimedia_file_refn' => $path,
                'multimedia_format' => 'JPG', 'source_media_type' => '', 'descriptive_title' => '',
            ]);
        }

        $filesystem = self::createStub(FilesystemOperator::class);
        $filesystem->method('listContents')->willReturn(new DirectoryListing([
            new FileAttributes('used.jpg'),
            new FileAttributes('other-tree.jpg'),
            new FileAttributes('free.jpg'),
        ]));
        $tree_with_files = self::createStub(Tree::class);
        $tree_with_files->method('id')->willReturn($tree->id());
        $tree_with_files->method('mediaFilesystem')->willReturn($filesystem);

        $service = new MediaFileService(self::createStub(PhpService::class));

        self::assertSame([
            'free.jpg' => 'free.jpg',
            'other-tree.jpg' => 'other-tree.jpg',
        ], $service->unusedFiles($tree_with_files));
    }

    public function testAllMediaFoldersCombinesDatabaseAndFilesystemFolders(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('first', 'First');
        DB::query(table: 'media_file')->insert(values: [
            'm_id' => 'M1', 'm_file' => $tree->id(), 'multimedia_file_refn' => 'photos/portrait.jpg',
            'multimedia_format' => 'JPG', 'source_media_type' => '', 'descriptive_title' => '',
        ]);
        DB::query(table: 'media_file')->insert(values: [
            'm_id' => 'M2', 'm_file' => $tree->id(), 'multimedia_file_refn' => 'https://example.com/image.jpg',
            'multimedia_format' => 'JPG', 'source_media_type' => '', 'descriptive_title' => '',
        ]);

        $filesystem = self::createMock(FilesystemOperator::class);
        $filesystem->expects(self::once())
            ->method('listContents')
            ->with($tree->mediaFolder(), FilesystemReader::LIST_DEEP)
            ->willReturn(new DirectoryListing([
                new DirectoryAttributes('media/disk'),
                new DirectoryAttributes('media/thumbs'),
            ]));

        $service = new MediaFileService(self::createStub(PhpService::class));

        self::assertSame([
            'media/' => 'media/',
            'media/disk/' => 'media/disk/',
            'media/photos/' => 'media/photos/',
        ], $service->allMediaFolders($filesystem)->all());
    }

    public function testAllFilesInDatabaseEscapesFolderAndRespectsSubfolders(): void
    {
        self::createDatabase();

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');
        $media_folder = 'media\\_%/';

        DB::query(table: 'gedcom')
            ->whereEqual('gedcom_id', $tree->id())
            ->update(['media_folder' => $media_folder]);
        DB::query(table: 'gedcom')
            ->whereEqual('gedcom_id', $other_tree->id())
            ->update(['media_folder' => 'media\\aother/']);

        foreach (
            [
                [$tree->id(), 'portrait.jpg'],
                [$tree->id(), 'subfolder/portrait.jpg'],
                [$tree->id(), 'http://example.com/image.jpg'],
                [$tree->id(), 'https://example.com/image.jpg'],
                [$other_tree->id(), 'unrelated.jpg'],
            ] as [$tree_id, $filename]
        ) {
            DB::query(table: 'media_file')->insert(values: [
                'm_id' => 'M1',
                'm_file' => $tree_id,
                'multimedia_file_refn' => $filename,
                'multimedia_format' => 'JPG',
                'source_media_type' => '',
                'descriptive_title' => '',
            ]);
        }

        $service = new MediaFileService(self::createStub(PhpService::class));

        self::assertSame(
            [$media_folder . 'portrait.jpg', $media_folder . 'subfolder/portrait.jpg'],
            $service->allFilesInDatabase($media_folder, true)->all(),
        );
        self::assertSame(
            [$media_folder . 'portrait.jpg'],
            $service->allFilesInDatabase($media_folder, false)->all(),
        );
    }
}
