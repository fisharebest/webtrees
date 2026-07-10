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

namespace Fisharebest\Webtrees\Tests\Unit\Factories;

use Fisharebest\Webtrees\Enums\ImageOperation;
use Fisharebest\Webtrees\Exceptions\ImageException;
use Fisharebest\Webtrees\Factories\ImageFactory;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\Attributes\CoversClass;

use function dirname;

#[CoversClass(ImageFactory::class)]
class ImageFactoryTest extends TestCase
{
    // Happy-path behavior.

    public function testHappyPathFileContentsReturnsSafeSvgContent(): void
    {
        $php_service   = $this->createStub(PhpService::class);
        $image_factory = new ImageFactory($php_service);
        $filesystem    = $this->mediaFilesystem();

        $php_service
            ->method('extensionLoaded')
            ->willReturnCallback(static fn (string $extension): bool => $extension === 'dom');

        $contents = $image_factory->fileContents($filesystem, 'safe.svg');

        self::assertStringContainsString('<svg', $contents);
    }

    public function testHappyPathThumbnailContentsReturnsImageForJpeg(): void
    {
        $image_factory = new ImageFactory(new PhpService());
        $filesystem    = $this->mediaFilesystem();

        $thumbnail = $image_factory->thumbnailContents(
            filesystem: $filesystem,
            path: 'Elizabeth_II.jpg',
            width: 40,
            height: 40,
            operation: ImageOperation::Contain,
        );

        self::assertNotSame('', $thumbnail);
    }

    public function testHappyPathThumbnailContentsReturnsImageForJpegWithCrop(): void
    {
        $image_factory = new ImageFactory(new PhpService());
        $filesystem    = $this->mediaFilesystem();

        $thumbnail = $image_factory->thumbnailContents(
            filesystem: $filesystem,
            path: 'Elizabeth_II.jpg',
            width: 40,
            height: 40,
            operation: ImageOperation::Crop,
        );

        self::assertNotSame('', $thumbnail);
    }

    public function testHappyPathMediaFileContentsReturnsOriginalContentsWhenNoWatermarkRequested(): void
    {
        $image_factory     = new ImageFactory(new PhpService());
        $filesystem        = $this->mediaFilesystem();
        $expected_contents = $filesystem->read('Elizabeth_II.jpg');
        $media_file        = $this->createMediaFileStub(
            filesystem: $filesystem,
            filename: 'Elizabeth_II.jpg',
            mime_type: 'image/jpeg',
            is_image: true,
        );

        $contents = $image_factory->mediaFileContents(media_file: $media_file, add_watermark: false);

        self::assertSame($expected_contents, $contents);
    }

    public function testHappyPathMediaFileContentsReturnsOriginalContentsForNonImageFile(): void
    {
        $image_factory = new ImageFactory(new PhpService());
        $filesystem    = $this->createStub(FilesystemOperator::class);

        $filesystem
            ->method('mimeType')
            ->willReturn('application/pdf');

        $filesystem
            ->method('read')
            ->willReturn('PDF');

        $media_file = $this->createMediaFileStub(
            filesystem: $filesystem,
            filename: 'document.pdf',
            mime_type: 'application/pdf',
            is_image: false,
        );

        $contents = $image_factory->mediaFileContents(media_file: $media_file, add_watermark: true);

        self::assertSame('PDF', $contents);
    }

    public function testHappyPathMediaFileThumbnailReturnsImageWithoutWatermark(): void
    {
        $image_factory = new ImageFactory(new PhpService());
        $filesystem    = $this->mediaFilesystem();
        $media_file    = $this->createMediaFileStub(
            filesystem: $filesystem,
            filename: 'Elizabeth_II.jpg',
            mime_type: 'image/jpeg',
            is_image: true,
        );

        $thumbnail = $image_factory->mediaFileThumbnail(
            media_file: $media_file,
            width: 40,
            height: 40,
            operation: ImageOperation::Contain,
            add_watermark: false,
        );

        self::assertNotSame('', $thumbnail);
    }

    public function testHappyPathMediaFileThumbnailReturnsImageWithWatermark(): void
    {
        $image_factory = new ImageFactory(new PhpService());
        $filesystem    = $this->mediaFilesystem();
        $media_file    = $this->createMediaFileStub(
            filesystem: $filesystem,
            filename: 'Elizabeth_II.jpg',
            mime_type: 'image/jpeg',
            is_image: true,
        );

        $thumbnail = $image_factory->mediaFileThumbnail(
            media_file: $media_file,
            width: 40,
            height: 40,
            operation: ImageOperation::Contain,
            add_watermark: true,
        );

        self::assertNotSame('', $thumbnail);
    }

    // Guardrails and failure handling.

    public function testGuardrailFileContentsBlocksSvgWithActiveContent(): void
    {
        $php_service   = $this->createStub(PhpService::class);
        $image_factory = new ImageFactory($php_service);
        $filesystem    = $this->mediaFilesystem();

        $php_service
            ->method('extensionLoaded')
            ->willReturnCallback(static fn (string $extension): bool => $extension === 'dom');

        $this->expectException(ImageException::class);
        $this->expectExceptionCode(403);

        try {
            $image_factory->fileContents(filesystem: $filesystem, path: 'unsafe.svg');
        } catch (ImageException $exception) {
            self::assertSame('SVG contains active content', $exception->getMessage());

            throw $exception;
        }
    }

    public function testGuardrailFileContentsBlocksSvgWithoutDomExtension(): void
    {
        $php_service   = $this->createStub(PhpService::class);
        $image_factory = new ImageFactory($php_service);
        $filesystem    = $this->mediaFilesystem();

        $php_service
            ->method('extensionLoaded')
            ->willReturnCallback(static fn (string $extension): bool => false);

        $this->expectException(ImageException::class);
        $this->expectExceptionCode(500);

        try {
            $image_factory->fileContents(filesystem: $filesystem, path: 'safe.svg');
        } catch (ImageException $exception) {
            self::assertSame('PHP extension ext-dom is not installed', $exception->getMessage());

            throw $exception;
        }
    }

    public function testGuardrailFileContentsBlocksMalformedSvgAsActiveContent(): void
    {
        $php_service = $this->createStub(PhpService::class);
        $filesystem  = $this->createStub(FilesystemOperator::class);

        $php_service
            ->method('extensionLoaded')
            ->willReturnCallback(static fn (string $extension): bool => $extension === 'dom');

        $filesystem
            ->method('mimeType')
            ->willReturn('image/svg+xml');

        $filesystem
            ->method('read')
            ->willReturn('<svg><g></svg>');

        $image_factory = new ImageFactory($php_service);

        $this->expectException(ImageException::class);
        $this->expectExceptionCode(403);
        $this->expectExceptionMessage('SVG contains active content');

        $image_factory->fileContents(filesystem: $filesystem, path: 'broken.svg');
    }

    public function testGuardrailFileContentsThrowsNotFoundForMissingFile(): void
    {
        $image_factory = new ImageFactory(new PhpService());
        $filesystem    = $this->mediaFilesystem();

        $this->expectException(ImageException::class);
        $this->expectExceptionCode(404);

        try {
            $image_factory->fileContents(filesystem: $filesystem, path: 'missing.svg');
        } catch (ImageException $exception) {
            self::assertStringStartsWith('Unable to read MIME type:', $exception->getMessage());

            throw $exception;
        }
    }

    public function testGuardrailThumbnailContentsThrowsNotFoundForMissingFile(): void
    {
        $image_factory = new ImageFactory(new PhpService());
        $filesystem    = $this->mediaFilesystem();

        $this->expectException(ImageException::class);
        $this->expectExceptionCode(404);

        $image_factory->thumbnailContents(filesystem: $filesystem, path: 'missing.jpg', width: 40, height: 40, operation: ImageOperation::Contain);
    }

    public function testGuardrailThumbnailContentsThrowsWhenGdExtensionMissing(): void
    {
        $php_service      = $this->createStub(PhpService::class);
        $filesystem       = $this->createStub(FilesystemOperator::class);
        $fixture_contents = $this->mediaFilesystem()->read('Elizabeth_II.jpg');

        $php_service
            ->method('extensionLoaded')
            ->willReturn(false);

        $filesystem
            ->method('mimeType')
            ->willReturn('image/jpeg');

        $filesystem
            ->method('read')
            ->willReturn($fixture_contents);

        $image_factory = new ImageFactory($php_service);

        $this->expectException(ImageException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('PHP extension ext-gd is not installed');

        $image_factory->thumbnailContents(filesystem: $filesystem, path: 'photo.jpg', width: 40, height: 40, operation: ImageOperation::Contain);
    }



    private function createMediaFileStub(FilesystemOperator $filesystem, string $filename, string $mime_type, bool $is_image): MediaFile
    {
        $tree = $this->createStub(Tree::class);
        $tree
            ->method('mediaFilesystem')
            ->willReturn($filesystem);

        $media = $this->createStub(Media::class);
        $media
            ->method('tree')
            ->willReturn($tree);

        $media_file = $this->createStub(MediaFile::class);
        $media_file
            ->method('media')
            ->willReturn($media);
        $media_file
            ->method('filename')
            ->willReturn($filename);
        $media_file
            ->method('mimeType')
            ->willReturn($mime_type);
        $media_file
            ->method('isImage')
            ->willReturn($is_image);

        return $media_file;
    }

    private function mediaFilesystem(): FilesystemOperator
    {
        $root = dirname(__DIR__, 2) . '/data/media';

        return new Filesystem(new LocalFilesystemAdapter($root));
    }
}
