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

use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\TestCase;

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
        $controller = new MediaController();
        $request    = self::createRequest('GET', ['route' => 'admin-media']);
        $response   = $controller->index($request);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDataLocal(): void
    {
        $controller = new MediaController();
        $request    = self::createRequest('GET', [
            'route'        => 'admin-media-data',
            'files'        => 'local',
            'media_folder' => '',
            'subfolders'   => 'include',
            'search'       => ['value' => ''],
            'start'        => '0',
            'length'       => '10',
        ]);
        $response   = $controller->data($request, new DatatablesService());

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDataExternal(): void
    {
        $controller = new MediaController();
        $request    = self::createRequest('GET', [
            'route'        => 'admin-media-external',
            'files'        => 'local',
            'media_folder' => '',
            'subfolders'   => 'include',
            'search'       => ['value' => ''],
            'start'        => '0',
            'length'       => '10',
        ]);
        $response   = $controller->data($request, new DatatablesService());

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDataUnused(): void
    {
        $controller = new MediaController();
        $request    = self::createRequest('GET', [
            'route'        => 'admin-media-unused',
            'files'        => 'local',
            'media_folder' => '',
            'subfolders'   => 'include',
            'search'       => ['value' => ''],
            'start'        => '0',
            'length'       => '10',
        ]);
        $response   = $controller->data($request, new DatatablesService());

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDelete(): void
    {
        $controller = new MediaController();
        $request    = self::createRequest('POST', ['route' => 'admin-media-delete'], ['file' => 'foo', 'folder' => 'bar']);
        $response   = $controller->delete($request);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpload(): void
    {
        $controller = new MediaController();
        self::createRequest('GET', ['route' => 'admin-media-upload']);
        $response = $controller->upload();

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUploadAction(): void
    {
        $controller = new MediaController();
        $request    = self::createRequest('POST', ['route' => 'admin-media-delete']);
        $response   = $controller->uploadAction($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }
}
