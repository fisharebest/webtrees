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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\TestCase;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\Attributes\CoversClass;

use function dirname;

#[CoversClass(ImageFactory::class)]
class ImageFactoryTest extends TestCase
{
    public function testReplacementImageResponseSetsContentSecurityPolicyHeader(): void
    {
        $php_service   = $this->createStub(PhpService::class);
        $image_factory = new ImageFactory($php_service);
        $response      = $image_factory->replacementImageResponse('404');

        self::assertSame('image/svg+xml', $response->getHeaderLine('content-type'));
        self::assertSame(
            'default-src none',
            $response->getHeaderLine('content-security-policy'),
        );
    }

    public function testFileResponseAddsDownloadHeaderForSafeSvg(): void
    {
        $php_service   = $this->createStub(PhpService::class);
        $image_factory = new ImageFactory($php_service);
        $filesystem    = $this->mediaFilesystem();

        $php_service
            ->method('extensionLoaded')
            ->willReturnCallback(static fn (string $extension): bool => $extension === 'dom');

        $response = $image_factory->fileResponse(
            filesystem: $filesystem,
            path: 'safe.svg',
            download: true,
        );

        self::assertSame('image/svg+xml', $response->getHeaderLine('content-type'));
        self::assertSame('default-src none', $response->getHeaderLine('content-security-policy'));
        self::assertSame('attachment; filename="safe.svg"', $response->getHeaderLine('content-disposition'));
    }

    public function testFileResponseBlocksSvgWithActiveContent(): void
    {
        $php_service   = $this->createStub(PhpService::class);
        $image_factory = new ImageFactory($php_service);
        $filesystem    = $this->mediaFilesystem();

        $php_service
            ->method('extensionLoaded')
            ->willReturnCallback(static fn (string $extension): bool => $extension === 'dom');

        $response = $image_factory->fileResponse(
            filesystem: $filesystem,
            path: 'unsafe.svg',
            download: false,
        );

        self::assertSame('image/svg+xml', $response->getHeaderLine('content-type'));
        self::assertSame('SVG image blocked due to XSS.', $response->getHeaderLine('x-image-exception'));
    }

    public function testFileResponseBlocksSvgWithoutDomExtension(): void
    {
        $php_service   = $this->createStub(PhpService::class);
        $image_factory = new ImageFactory($php_service);
        $filesystem    = $this->mediaFilesystem();

        $php_service
            ->method('extensionLoaded')
            ->willReturnCallback(static fn (string $extension): bool => false);

        $response = $image_factory->fileResponse(
            filesystem: $filesystem,
            path: 'safe.svg',
            download: false,
        );

        self::assertSame('image/svg+xml', $response->getHeaderLine('content-type'));
        self::assertSame(
            'Need the PHP dom extension to verify SVG files.',
            $response->getHeaderLine('x-image-exception'),
        );
    }

    public function testFileResponseReturnsNotFoundForMissingFile(): void
    {
        $image_factory = new ImageFactory(new PhpService());
        $filesystem    = $this->mediaFilesystem();

        $response = $image_factory->fileResponse(
            filesystem: $filesystem,
            path: 'missing.svg',
            download: false,
        );

        self::assertSame('image/svg+xml', $response->getHeaderLine('content-type'));
        self::assertStringContainsString(
            'UnableToReadFile',
            $response->getHeaderLine('x-file-exception'),
        );
    }

    public function testThumbnailResponseReturnsImageForJpeg(): void
    {
        $image_factory = new ImageFactory(new PhpService());
        $filesystem    = $this->mediaFilesystem();

        $response = $image_factory->thumbnailResponse(
            filesystem: $filesystem,
            path: 'Elizabeth_II.jpg',
            width: 40,
            height: 40,
            fit: 'contain',
        );

        self::assertSame('image/jpeg', $response->getHeaderLine('content-type'));
        self::assertSame('default-src none', $response->getHeaderLine('content-security-policy'));
        self::assertSame('', $response->getHeaderLine('content-disposition'));
        self::assertNotSame('', $response->getBody()->getContents());
    }

    public function testThumbnailResponseReturnsNotFoundForMissingFile(): void
    {
        $image_factory = new ImageFactory(new PhpService());
        $filesystem    = $this->mediaFilesystem();

        $response = $image_factory->thumbnailResponse(
            filesystem: $filesystem,
            path: 'missing.jpg',
            width: 40,
            height: 40,
            fit: 'contain',
        );

        self::assertSame('image/svg+xml', $response->getHeaderLine('content-type'));
        self::assertStringContainsString(
            'UnableTo',
            $response->getHeaderLine('x-thumbnail-exception'),
        );
    }

    private function mediaFilesystem(): FilesystemOperator
    {
        $root = dirname(__DIR__, 2) . '/data/media';

        return new Filesystem(new LocalFilesystemAdapter($root));
    }
}
