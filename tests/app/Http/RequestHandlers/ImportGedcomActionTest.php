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
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_OK;

#[CoversClass(ImportGedcomAction::class)]
class ImportGedcomActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(ImportGedcomAction::class));
    }

    public function testHandleClientUploadWithNoFile(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('import-nofile', 'Import No File');

        $stream_factory = self::createStub(StreamFactoryInterface::class);

        $handler = new ImportGedcomAction($stream_factory, $tree_service);

        // Simulate client upload with no file selected
        $uploaded_file = self::createStub(UploadedFileInterface::class);
        $uploaded_file->method('getError')->willReturn(UPLOAD_ERR_NO_FILE);

        $request = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            [
                'source'             => 'client',
                'keep_media'         => '0',
                'WORD_WRAPPED_NOTES' => '0',
                'GEDCOM_MEDIA_PATH'  => '',
                'encoding'           => '',
            ],
            ['client_file' => $uploaded_file]
        )->withAttribute('tree', $tree);

        $response = $handler->handle($request);

        // Redirects back to import page with a flash message
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleClientUploadWithValidFile(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('import-valid', 'Import Valid');

        $stream_factory = self::createStub(StreamFactoryInterface::class);

        // Create a minimal GEDCOM stream
        $stream = self::createStub(StreamInterface::class);
        $stream->method('__toString')->willReturn("0 HEAD\n1 SOUR test\n0 TRLR\n");
        $stream->method('eof')->willReturn(false, true);
        $stream->method('read')->willReturn("0 HEAD\n1 SOUR test\n0 TRLR\n");

        $uploaded_file = self::createStub(UploadedFileInterface::class);
        $uploaded_file->method('getError')->willReturn(UPLOAD_ERR_OK);
        $uploaded_file->method('getClientFilename')->willReturn('test.ged');
        $uploaded_file->method('getStream')->willReturn($stream);

        $mock_tree_service = $this->createMock(TreeService::class);
        $mock_tree_service
            ->expects(self::once())
            ->method('importGedcomFile');

        $handler = new ImportGedcomAction($stream_factory, $mock_tree_service);
        $request = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            [
                'source'             => 'client',
                'keep_media'         => '0',
                'WORD_WRAPPED_NOTES' => '0',
                'GEDCOM_MEDIA_PATH'  => '',
                'encoding'           => '',
            ],
            ['client_file' => $uploaded_file]
        )->withAttribute('tree', $tree);

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleClientUploadWithNoFileAttached(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('import-none', 'Import None');

        $stream_factory = self::createStub(StreamFactoryInterface::class);

        $handler = new ImportGedcomAction($stream_factory, $tree_service);

        // No client_file in uploaded files at all
        $request = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            [
                'source'             => 'client',
                'keep_media'         => '0',
                'WORD_WRAPPED_NOTES' => '0',
                'GEDCOM_MEDIA_PATH'  => '',
                'encoding'           => '',
            ]
        )->withAttribute('tree', $tree);

        $response = $handler->handle($request);

        // Redirects back with flash message about no file received
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
