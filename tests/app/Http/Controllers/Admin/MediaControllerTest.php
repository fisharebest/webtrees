<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

    /**
     * @return void
     */
    public function testIndex(): void
    {
        $datatables_service = new DatatablesService();
        $media_file_service = new MediaFileService();
        $tree_service       = new TreeService();
        $data_filesystem    = new Filesystem(new NullAdapter());
        $controller         = new MediaController($datatables_service, $media_file_service, $tree_service);
        $request            = self::createRequest()
            ->withAttribute('filesystem.data', $data_filesystem)
            ->withAttribute('filesystem.data.name', 'data/');
        $response           = $controller->index($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDataLocal(): void
    {
        $datatables_service = new DatatablesService();
        $media_file_service = new MediaFileService();
        $tree_service       = new TreeService();
        $data_filesystem    = new Filesystem(new NullAdapter());
        $controller         = new MediaController($datatables_service, $media_file_service, $tree_service);
        $request            = self::createRequest(RequestMethodInterface::METHOD_GET, [
            'files'        => 'local',
            'media_folder' => '',
            'subfolders'   => 'include',
            'search'       => ['value' => ''],
            'start'        => '0',
            'length'       => '10',
        ])
            ->withAttribute('filesystem.data', $data_filesystem)
            ->withAttribute('filesystem.data.name', 'data/');
        $response           = $controller->data($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDataExternal(): void
    {
        $datatables_service = new DatatablesService();
        $media_file_service = new MediaFileService();
        $tree_service       = new TreeService();
        $data_filesystem    = new Filesystem(new NullAdapter());
        $controller         = new MediaController($datatables_service, $media_file_service, $tree_service);
        $request            = self::createRequest(RequestMethodInterface::METHOD_GET, [
            'files'        => 'local',
            'media_folder' => '',
            'subfolders'   => 'include',
            'search'       => ['value' => ''],
            'start'        => '0',
            'length'       => '10',
        ])
            ->withAttribute('filesystem.data', $data_filesystem)
            ->withAttribute('filesystem.data.name', 'data/');
        $response           = $controller->data($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDataUnused(): void
    {
        $datatables_service = new DatatablesService();
        $media_file_service = new MediaFileService();
        $tree_service       = new TreeService();
        $data_filesystem    = new Filesystem(new NullAdapter());
        $controller         = new MediaController($datatables_service, $media_file_service, $tree_service);
        $request            = self::createRequest(RequestMethodInterface::METHOD_GET, [
            'files'        => 'local',
            'media_folder' => '',
            'subfolders'   => 'include',
            'search'       => ['value' => ''],
            'start'        => '0',
            'length'       => '10',
        ])
            ->withAttribute('filesystem.data', $data_filesystem)
            ->withAttribute('filesystem.data.name', 'data/');
        $response           = $controller->data($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpload(): void
    {
        $datatables_service = new DatatablesService();
        $media_file_service = new MediaFileService();
        $tree_service       = new TreeService();
        $data_filesystem    = new Filesystem(new NullAdapter());
        $controller         = new MediaController($datatables_service, $media_file_service, $tree_service);
        $request            = self::createRequest()->withAttribute('filesystem.data', $data_filesystem);
        $response           = $controller->upload($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUploadAction(): void
    {
        $datatables_service = new DatatablesService();
        $media_file_service = new MediaFileService();
        $tree_service       = new TreeService();
        $data_filesystem    = new Filesystem(new NullAdapter());
        $controller         = new MediaController($datatables_service, $media_file_service, $tree_service);
        $request            = self::createRequest()->withAttribute('filesystem.data', $data_filesystem);
        $response           = $controller->uploadAction($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
