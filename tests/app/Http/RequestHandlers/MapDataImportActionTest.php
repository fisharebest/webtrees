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
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\MapDataService;
use Fisharebest\Webtrees\TestCase;

use function dirname;

/**
 * Test the location import.
 *
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\MapDataImportAction
 */
class MapDataImportActionTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @return void
     */
    public function testImportAction(): void
    {
        $map_data_service = new MapDataService();
        $csv              = $this->createUploadedFile(dirname(__DIR__, 3) . '/data/places.csv', 'text/csv');
        $handler          = new MapDataImportAction($map_data_service);
        $request          = self::createRequest(RequestMethodInterface::METHOD_POST, [], [], ['serverfile' => $csv]);
        $response         = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
