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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\UploadedFileInterface;

use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_OK;

#[CoversClass(UploadMediaAction::class)]
class UploadMediaActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(UploadMediaAction::class));
    }

    public function testHandleWithNoUploadedFiles(): void
    {
        $media_file_service = new MediaFileService(php_service: new PhpService());
        $handler            = new UploadMediaAction($media_file_service);
        $request            = self::createRequest();
        $response           = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleSkipsNoFileUploads(): void
    {
        // An uploaded file with UPLOAD_ERR_NO_FILE should be silently skipped
        $uploaded_file = self::createStub(UploadedFileInterface::class);
        $uploaded_file->method('getError')->willReturn(UPLOAD_ERR_NO_FILE);

        $media_file_service = new MediaFileService(php_service: new PhpService());
        $handler            = new UploadMediaAction($media_file_service);
        $request            = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            [],
            ['mediafile0' => $uploaded_file]
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleRejectsScriptExtension(): void
    {
        // Files with script extensions (.php) should be rejected with a flash message
        $uploaded_file = self::createStub(UploadedFileInterface::class);
        $uploaded_file->method('getError')->willReturn(UPLOAD_ERR_OK);
        $uploaded_file->method('getClientFilename')->willReturn('evil.php');

        $media_file_service = $this->createMock(MediaFileService::class);
        $media_file_service
            ->expects(self::once())
            ->method('allMediaFolders')
            ->willReturn(new Collection(['']));

        $handler = new UploadMediaAction($media_file_service);
        $request = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            ['folder0' => '', 'filename0' => 'evil.php'],
            ['mediafile0' => $uploaded_file]
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleRejectsColonInFilename(): void
    {
        // Filenames containing a colon should be rejected
        $uploaded_file = self::createStub(UploadedFileInterface::class);
        $uploaded_file->method('getError')->willReturn(UPLOAD_ERR_OK);
        $uploaded_file->method('getClientFilename')->willReturn('file:name.jpg');

        $media_file_service = $this->createMock(MediaFileService::class);
        $media_file_service
            ->expects(self::once())
            ->method('allMediaFolders')
            ->willReturn(new Collection(['']));

        $handler = new UploadMediaAction($media_file_service);
        $request = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            ['folder0' => '', 'filename0' => 'file:name.jpg'],
            ['mediafile0' => $uploaded_file]
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleRejectsInvalidFolder(): void
    {
        // If the requested folder is not in allMediaFolders, processing stops
        $uploaded_file = self::createStub(UploadedFileInterface::class);
        $uploaded_file->method('getError')->willReturn(UPLOAD_ERR_OK);
        $uploaded_file->method('getClientFilename')->willReturn('photo.jpg');

        $media_file_service = $this->createMock(MediaFileService::class);
        $media_file_service
            ->expects(self::once())
            ->method('allMediaFolders')
            ->willReturn(new Collection(['valid/']));

        $handler = new UploadMediaAction($media_file_service);
        $request = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            ['folder0' => 'invalid/', 'filename0' => 'photo.jpg'],
            ['mediafile0' => $uploaded_file]
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
