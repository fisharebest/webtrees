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

use Fisharebest\Webtrees\Services\GedcomService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
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
        $controller = new LocationController(new GedcomService());
        $request    = self::createRequest('GET', ['route' => 'map-data']);
        $response   = $controller->mapData($request);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testMapDataEdit(): void
    {
        $controller = new LocationController(new GedcomService());
        $request    = self::createRequest('GET', ['route' => 'map-data-edit', 'place_id' => '0', 'parent_id' => '0']);
        $response   = $controller->mapDataEdit($request);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testMapDataSave(): void
    {
        $controller = new LocationController(new GedcomService());
        $request    = self::createRequest('POST', ['route' => 'map-data-edit'], [
            'parent_id' => '0',
            'place_id' => '0',
            'new_place_lati' => '-12.345',
            'new_place_long' => '-123.45',
            'icon' => '',
            'new_zoom_factor' => '2',
            'new_place_name' => 'place',
            'lati_control' => 'S',
            'long_control' => 'W',
        ]);
        $response   = $controller->mapDataSave($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testMapDataDelete(): void
    {
        $controller = new LocationController(new GedcomService());
        $request    = self::createRequest('POST', ['route' => 'map-data-delete'], ['parent_id' => '0', 'place_id' => '0']);
        $response   = $controller->mapDataDelete($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testExportLocations(): void
    {
        $controller = new LocationController(new GedcomService());
        $request    = self::createRequest('GET', ['route' => 'locations-export', 'parent_id' => '0', 'format' => 'geojson']);
        $response   = $controller->exportLocations($request);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
        $this->assertSame($response->getHeaderLine('Content-type'), 'application/vnd.geo+json');
    }

    /**
     * @return void
     */
    public function testImportLocations(): void
    {
        $controller = new LocationController(new GedcomService());
        $request    = self::createRequest('GET', ['route' => 'locations-import'], ['parent_id' => '0']);
        $response   = $controller->importLocations($request);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testImportLocationsAction(): void
    {
        $csv        = $this->createUploadedFile(dirname(__DIR__, 4) . '/data/places.csv', 'text/csv');
        $controller = new LocationController(new GedcomService());
        $request    = self::createRequest('POST', ['route' => 'locations-import'], ['parent_id' => '0'], ['csv' => $csv]);
        $response   = $controller->importLocationsAction($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testImportLocationsFromTree(): void
    {
        $tree       = Tree::create('name', 'title');
        $controller = new LocationController(new GedcomService());
        self::createRequest('POST', ['route' => 'locations-import-from-tree']);
        $response = $controller->importLocationsFromTree($tree);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }
}
