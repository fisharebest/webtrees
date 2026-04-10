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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\PlaceLocation;
use Fisharebest\Webtrees\Services\LeafletJsService;
use Fisharebest\Webtrees\Services\MapDataService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MapDataEdit::class)]
class MapDataEditTest extends TestCase
{

    public function testClass(): void
    {
        self::assertTrue(class_exists(MapDataEdit::class));
    }

    public function testHandleNonExistentLocationRedirects(): void
    {
        // findById returns a PlaceLocation with id() === null for unknown IDs
        $location = new PlaceLocation('');

        $map_data_service = $this->createMock(MapDataService::class);
        $map_data_service->expects(self::once())
            ->method('findById')
            ->with(999)
            ->willReturn($location);

        $leaflet_js_service = $this->createMock(LeafletJsService::class);

        $handler  = new MapDataEdit($leaflet_js_service, $map_data_service);
        $request  = self::createRequest('GET', [], [], [], ['location_id' => '999']);
        $response = $handler->handle($request);

        // Non-existent location redirects to the list
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
