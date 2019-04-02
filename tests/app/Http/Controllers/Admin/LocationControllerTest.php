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

use Fisharebest\Webtrees\Http\Request;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Test the location admin controller
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\LocationController
 */
class LocationControllerTest extends \Fisharebest\Webtrees\TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testMapData(): void
    {
        $controller = app(LocationController::class);
        $response   = app()->dispatch($controller, 'mapData');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testMapDataEdit(): void
    {
        $controller = app(LocationController::class);
        $response   = app()->dispatch($controller, 'mapDataEdit');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testMapDataSave(): void
    {
        $controller = app(LocationController::class);
        $response   = app()->dispatch($controller, 'mapDataSave');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testMapDataDelete(): void
    {
        $controller = app(LocationController::class);
        $response   = app()->dispatch($controller, 'mapDataDelete');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testExportLocations(): void
    {
        $controller = app(LocationController::class);
        $response   = app()->dispatch($controller, 'exportLocations');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testImportLocations(): void
    {
        $controller = app(LocationController::class);
        $response   = app()->dispatch($controller, 'importLocations');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testImportLocationsAction(): void
    {
        $csv = new UploadedFile(dirname(__DIR__, 4) . '/data/places.csv', 'places.csv', 'image/jpeg', UPLOAD_ERR_OK);

        app()->instance(ServerRequestInterface::class, new Request([], [], [], [], ['localfile' => $csv]));

        $controller = app(LocationController::class);
        $response   = app()->dispatch($controller, 'importLocationsAction');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testImportLocationsFromTree(): void
    {
        app()->instance(Tree::class, Tree::create('name', 'title'));

        $controller = app(LocationController::class);
        $response   = app()->dispatch($controller, 'importLocationsFromTree');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
