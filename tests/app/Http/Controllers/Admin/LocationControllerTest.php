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
use Fisharebest\Webtrees\Services\GedcomService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;

use function dirname;

/**
 * Test the location admin controller
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\LocationController
 */
class LocationControllerTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testMapData(): void
    {
        $gedcom_service = new GedcomService();
        $tree_service   = new TreeService();
        $controller     = new LocationController($gedcom_service, $tree_service);
        $request        = self::createRequest();
        $response       = $controller->mapData($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testMapDataEdit(): void
    {
        $gedcom_service = new GedcomService();
        $tree_service   = new TreeService();
        $controller     = new LocationController($gedcom_service, $tree_service);
        $request        = self::createRequest(RequestMethodInterface::METHOD_GET, ['place_id' => '0', 'parent_id' => '0']);
        $response       = $controller->mapDataEdit($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testMapDataSave(): void
    {
        $gedcom_service = new GedcomService();
        $tree_service   = new TreeService();
        $controller     = new LocationController($gedcom_service, $tree_service);
        $request        = self::createRequest(RequestMethodInterface::METHOD_POST, [
            'parent_id' => '0',
            'place_id'  => '0',
        ], [
            'new_place_lati'  => '-12.345',
            'new_place_long'  => '-123.45',
            'icon'            => '',
            'new_zoom_factor' => '2',
            'new_place_name'  => 'place',
            'lati_control'    => 'S',
            'long_control'    => 'W',
        ]);
        $response       = $controller->mapDataSave($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testMapDataDelete(): void
    {
        $gedcom_service = new GedcomService();
        $tree_service   = new TreeService();
        $controller     = new LocationController($gedcom_service, $tree_service);
        $request        = self::createRequest(RequestMethodInterface::METHOD_POST, ['parent_id' => '0', 'place_id' => '0']);
        $response       = $controller->mapDataDelete($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testExportLocations(): void
    {
        $gedcom_service = new GedcomService();
        $tree_service   = new TreeService();
        $controller     = new LocationController($gedcom_service, $tree_service);
        $request        = self::createRequest(RequestMethodInterface::METHOD_GET, ['parent_id' => '0', 'format' => 'geojson']);
        $response       = $controller->exportLocations($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertSame($response->getHeaderLine('Content-Type'), 'application/vnd.geo+json');
    }

    /**
     * @return void
     */
    public function testImportLocations(): void
    {
        $gedcom_service = new GedcomService();
        $tree_service   = new TreeService();
        $controller     = new LocationController($gedcom_service, $tree_service);
        $request        = self::createRequest(RequestMethodInterface::METHOD_GET, ['parent_id' => '0']);
        $response       = $controller->importLocations($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testImportLocationsAction(): void
    {
        $gedcom_service = new GedcomService();
        $tree_service   = new TreeService();
        $csv            = $this->createUploadedFile(dirname(__DIR__, 4) . '/data/places.csv', 'text/csv');
        $controller     = new LocationController($gedcom_service, $tree_service);
        $request        = self::createRequest(RequestMethodInterface::METHOD_POST, ['parent_id' => '0'], [], ['csv' => $csv]);
        $response       = $controller->importLocationsAction($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testImportLocationsFromTree(): void
    {
        $gedcom_service = new GedcomService();
        $tree_service   = new TreeService();
        $tree           = $tree_service->create('name', 'title');
        $controller     = new LocationController($gedcom_service, $tree_service);
        $request        = self::createRequest()
            ->withParsedBody(['ged' => $tree->name()]);
        $response = $controller->importLocationsFromTree($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
