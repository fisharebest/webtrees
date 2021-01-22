<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\MapDataService;
use Fisharebest\Webtrees\TestCase;

/**
 * Test the location export.
 *
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\MapDataExportGeoJson
 */
class MapDataExportGeoJsonTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testExportGeoJson(): void
    {
        $map_data_service = new MapDataService();
        $handler          = new MapDataExportGeoJson($map_data_service);
        $request          = self::createRequest();
        $response         = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame($response->getHeaderLine('Content-Type'), 'application/vnd.geo+json');
    }
}
