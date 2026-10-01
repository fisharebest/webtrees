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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Controllers;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Http\Controllers\MapDataExportGeoJson;
use Fisharebest\Webtrees\Services\MapDataService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function json_decode;

use const JSON_THROW_ON_ERROR;

#[CoversClass(MapDataExportGeoJson::class)]
class MapDataExportGeoJsonTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::createDatabase();
    }

    public function testExportGeoJson(): void
    {
        $parent_id = DB::query(table: 'place_location')->insertGetId([
            'parent_id' => null,
            'place'     => 'Country',
            'latitude'  => 1.25,
            'longitude' => 2.5,
        ], 'id');
        DB::query(table: 'place_location')->insert([
            'parent_id' => $parent_id,
            'place'     => 'Town',
            'latitude'  => 3.75,
            'longitude' => 4.5,
        ]);

        $map_data_service = new MapDataService();
        $controller       = new MapDataExportGeoJson($map_data_service);
        $request          = self::createRequest();
        $response         = $controller->get($request);

        self::assertSame(HttpStatusCode::OK->value, $response->getStatusCode());
        self::assertSame($response->getHeaderLine('content-type'), 'application/vnd.geo+json');
        self::assertSame([
            'type'     => 'FeatureCollection',
            'features' => [
                [
                    'type'       => 'Feature',
                    'geometry'   => ['type' => 'Point', 'coordinates' => [2.5, 1.25]],
                    'properties' => ['name' => 'Country'],
                ],
                [
                    'type'       => 'Feature',
                    'geometry'   => ['type' => 'Point', 'coordinates' => [4.5, 3.75]],
                    'properties' => ['name' => 'Town, Country'],
                ],
            ],
        ], json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }
}
