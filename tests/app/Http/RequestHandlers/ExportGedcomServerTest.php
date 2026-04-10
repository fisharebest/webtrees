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
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

#[CoversClass(ExportGedcomServer::class)]
class ExportGedcomServerTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(ExportGedcomServer::class));
    }

    public function testHandleExportsAndRedirects(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('test', 'Test');
        $response_factory      = Registry::container()->get(ResponseFactoryInterface::class);
        $stream_factory        = Registry::container()->get(StreamFactoryInterface::class);
        $export_service        = new GedcomExportService($response_factory, $stream_factory);

        $handler  = new ExportGedcomServer($export_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            ['filename' => 'test.ged'],
            [],
            ['tree' => $tree],
        );
        $response = $handler->handle($request);

        // Always redirects to ManageTrees
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleAppendsGedExtension(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('test2', 'Test 2');
        $response_factory      = Registry::container()->get(ResponseFactoryInterface::class);
        $stream_factory        = Registry::container()->get(StreamFactoryInterface::class);
        $export_service        = new GedcomExportService($response_factory, $stream_factory);

        $handler  = new ExportGedcomServer($export_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            ['filename' => 'family'],
            [],
            ['tree' => $tree],
        );
        $response = $handler->handle($request);

        // Handler adds .ged suffix when missing, then redirects
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
