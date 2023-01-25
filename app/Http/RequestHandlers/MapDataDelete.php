<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\Services\MapDataService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

/**
 * Delete a location from the control panel.
 */
class MapDataDelete implements RequestHandlerInterface
{
    private MapDataService $map_data_service;

    /**
     * Dependency injection.
     *
     * @param MapDataService $map_data_service
     */
    public function __construct(MapDataService $map_data_service)
    {
        $this->map_data_service = $map_data_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $location_id = (int) $request->getAttribute('location_id');

        $place = $this->map_data_service->findById($location_id);

        $this->map_data_service->deleteRecursively($location_id);

        $url = route(MapDataList::class, ['parent_id' => $place->parent()->id()]);

        return redirect($url);
    }
}
