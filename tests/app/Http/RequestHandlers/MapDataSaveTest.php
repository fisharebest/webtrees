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
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MapDataSave::class)]
class MapDataSaveTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(MapDataSave::class));
    }

    public function testHandleCreatesNewLocationAndRedirects(): void
    {
        $url = 'https://webtrees.test/index.php';

        $handler  = new MapDataSave();
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'parent_id'      => '',
            'place_id'       => '',
            'new_place_lati' => '51.50853',
            'new_place_long' => '-0.12574',
            'new_place_name' => 'London',
            'url'            => $url,
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleWithEmptyCoordinatesCreatesLocationWithNullCoords(): void
    {
        $url = 'https://webtrees.test/index.php';

        $handler  = new MapDataSave();
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'parent_id'      => '',
            'place_id'       => '',
            'new_place_lati' => '',
            'new_place_long' => '',
            'new_place_name' => 'Unknown Place',
            'url'            => $url,
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
