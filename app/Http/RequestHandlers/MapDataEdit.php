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

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\PlaceLocation;
use Fisharebest\Webtrees\Services\MapDataService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function redirect;
use function route;

/**
 * Edit location data.
 */
class MapDataEdit implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var MapDataService */
    private $map_data_service;

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
        $this->layout = 'layouts/administration';

        $place_id = (int) $request->getAttribute('place_id');
        $location = $this->map_data_service->findById($place_id);

        if ($location->id() === null) {
            return redirect(route(MapDataList::class));
        }

        $title = e($location->locationName()) . ' — ' . I18N::translate('Edit');

        // Build the breadcrumbs in reverse order
        $breadcrumbs = [I18N::translate('Edit')];

        $tmp = $location;
        while ($tmp->id() !== null) {
            $breadcrumbs[route(MapDataList::class, ['parent_id' => $tmp->id()])] = e($tmp->locationName());

            $tmp = $tmp->parent();
        }

        $breadcrumbs[route(MapDataList::class)]  = I18N::translate('Geographic data');
        $breadcrumbs[route(ControlPanel::class)] = I18N::translate('Control panel');

        $latitude      = $location->latitude();
        $longitude     = $location->longitude();
        $map_bounds    = $location->boundingRectangle();

        // If the current co-ordinates are unknown, leave the input fields empty,
        // and show a marker in the middle of the map.
        if ($latitude === null || $longitude === null) {
            $latitude  = '';
            $longitude = '';

            $marker_position = [
                ($map_bounds[0][0] + $map_bounds[1][0]) / 2.0,
                ($map_bounds[0][1] + $map_bounds[1][1]) / 2.0,
            ];
        } else {
            $marker_position = [$latitude, $longitude];
        }

        return $this->viewResponse('admin/location-edit', [
            'breadcrumbs'     => array_reverse($breadcrumbs, true),
            'title'           => $title,
            'location'        => $location,
            'latitude'        => $latitude,
            'longitude'       => $longitude,
            'map_bounds'      => $map_bounds,
            'marker_position' => $marker_position,
            'parent'          => $location->parent(),
            'provider'        => [
                'url'     => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                'options' => [
                    'attribution' => '<a href="https://www.openstreetmap.org/copyright">&copy; OpenStreetMap</a> contributors',
                    'max_zoom'    => 19
                ]
            ],
        ]);
    }
}
