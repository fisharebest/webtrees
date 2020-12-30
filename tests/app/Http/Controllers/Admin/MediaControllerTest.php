<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers\Admin;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Contracts\FilesystemFactoryInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;

/**
 * Test MediaController class.
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\MediaController
 */
class MediaControllerTest extends TestCase
{
    protected static $uses_database = true;

    public function setUp(): void
    {
        parent::setUp();

        $filesystem_factory = self::createMock(FilesystemFactoryInterface::class);
        $filesystem_factory->method('data')->willReturn(new Filesystem(new NullAdapter()));
        $filesystem_factory->method('dataName')->willReturn('data/');
        Registry::filesystem($filesystem_factory);
    }

    /**
     * @return void
     */
    public function testIndex(): void
    {
        $datatables_service = new DatatablesService();
        $media_file_service = new MediaFileService();
        $tree_service       = new TreeService();
        $controller         = new MediaController($datatables_service, $media_file_service, $tree_service);
        $request            = self::createRequest();
        $response           = $controller->index($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDataLocal(): void
    {
        $datatables_service = new DatatablesService();
        $media_file_service = new MediaFileService();
        $tree_service       = new TreeService();
        $controller         = new MediaController($datatables_service, $media_file_service, $tree_service);
        $request            = self::createRequest(RequestMethodInterface::METHOD_GET, [
            'files'        => 'local',
            'media_folder' => '',
            'subfolders'   => 'include',
            'search'       => ['value' => ''],
            'start'        => '0',
            'length'       => '10',
        ]);
        $response           = $controller->data($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDataExternal(): void
    {
        $datatables_service = new DatatablesService();
        $media_file_service = new MediaFileService();
        $tree_service       = new TreeService();
        $controller         = new MediaController($datatables_service, $media_file_service, $tree_service);
        $request            = self::createRequest(RequestMethodInterface::METHOD_GET, [
            'files'        => 'local',
            'media_folder' => '',
            'subfolders'   => 'include',
            'search'       => ['value' => ''],
            'start'        => '0',
            'length'       => '10',
        ]);
        $response           = $controller->data($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDataUnused(): void
    {
        $datatables_service = new DatatablesService();
        $media_file_service = new MediaFileService();
        $tree_service       = new TreeService();
        $controller         = new MediaController($datatables_service, $media_file_service, $tree_service);
        $request            = self::createRequest(RequestMethodInterface::METHOD_GET, [
            'files'        => 'local',
            'media_folder' => '',
            'subfolders'   => 'include',
            'search'       => ['value' => ''],
            'start'        => '0',
            'length'       => '10',
        ]);
        $response           = $controller->data($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpload(): void
    {
        $datatables_service = new DatatablesService();
        $media_file_service = new MediaFileService();
        $tree_service       = new TreeService();
        $controller         = new MediaController($datatables_service, $media_file_service, $tree_service);
        $request            = self::createRequest();
        $response           = $controller->upload($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUploadAction(): void
    {
        $datatables_service = new DatatablesService();
        $media_file_service = new MediaFileService();
        $tree_service       = new TreeService();
        $controller         = new MediaController($datatables_service, $media_file_service, $tree_service);
        $request            = self::createRequest();
        $response           = $controller->uploadAction($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
