<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;

/**
 * Test ManageMediaData class.
 *
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\ManageMediaData
 */
class ManageMediaDataTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @return void
     */
    public function testDataLocal(): void
    {
        $datatables_service    = new DatatablesService();
        $gedcom_import_service = new GedcomImportService();
        $linked_record_service = new LinkedRecordService();
        $media_file_service    = new MediaFileService();
        $tree_service          = new TreeService($gedcom_import_service);
        $handler               = new ManageMediaData($datatables_service, $linked_record_service, $media_file_service, $tree_service);
        $request               = self::createRequest(RequestMethodInterface::METHOD_GET, [
            'files'        => 'local',
            'media_folder' => '',
            'subfolders'   => 'include',
            'search'       => ['value' => ''],
            'start'        => '0',
            'length'       => '10',
        ]);
        $response              = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDataExternal(): void
    {
        $datatables_service    = new DatatablesService();
        $gedcom_import_service = new GedcomImportService();
        $linked_record_service = new LinkedRecordService();
        $media_file_service    = new MediaFileService();
        $tree_service          = new TreeService($gedcom_import_service);
        $handler               = new ManageMediaData($datatables_service, $linked_record_service, $media_file_service, $tree_service);
        $request               = self::createRequest(RequestMethodInterface::METHOD_GET, [
            'files'        => 'local',
            'media_folder' => '',
            'subfolders'   => 'include',
            'search'       => ['value' => ''],
            'start'        => '0',
            'length'       => '10',
        ]);
        $response              = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDataUnused(): void
    {
        $datatables_service    = new DatatablesService();
        $gedcom_import_service = new GedcomImportService();
        $linked_record_service = new LinkedRecordService();
        $media_file_service    = new MediaFileService();
        $tree_service          = new TreeService($gedcom_import_service);
        $handler               = new ManageMediaData($datatables_service, $linked_record_service, $media_file_service, $tree_service);
        $request               = self::createRequest(RequestMethodInterface::METHOD_GET, [
            'files'        => 'local',
            'media_folder' => '',
            'subfolders'   => 'include',
            'search'       => ['value' => ''],
            'start'        => '0',
            'length'       => '10',
        ]);
        $response              = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
