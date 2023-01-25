<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\PlaceLocation;
use Fisharebest\Webtrees\Services\MapDataService;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function addcslashes;
use function array_reverse;
use function array_unshift;
use function count;
use function implode;
use function preg_replace;
use function response;

/**
 * Export geographic data.
 */
class MapDataExportGeoJson implements RequestHandlerInterface
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
        $parent_id = $request->getAttribute('parent_id');

        if ($parent_id === null) {
            $parent = new PlaceLocation('');
        } else {
            $parent = $this->map_data_service->findById((int) $parent_id);
        }

        for ($tmp = $parent, $hierarchy = []; $tmp->id() !== null; $tmp = $tmp->parent()) {
            $hierarchy[] = $tmp->locationName();
        }

        // Create the file name
        $filename = preg_replace('/[^\p{L}]+/u', '-', $hierarchy[0] ?? 'Global') . '.geojson';

        // Recursively search for child places
        $features = [];
        $queue    = [[
            $parent->id(), array_reverse($hierarchy), $parent->latitude(), $parent->longitude()
        ]];

        while ($queue !== []) {
            [$id, $hierarchy, $latitude, $longitude] = array_shift($queue);

            if ($latitude !== null && !$longitude !== null) {
                $features[] = [
                    'type'       => 'Feature',
                    'geometry'   => [
                        'type'        => 'Point',
                        'coordinates' => [
                            (float) $longitude,
                            (float) $latitude,
                        ],
                    ],
                    'properties' => [
                        'name' => implode(Gedcom::PLACE_SEPARATOR, array_reverse($hierarchy)),
                    ],
                ];
            }

            $query = DB::table('place_location');
            // Data for the next level.

            if ($id === null) {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', '=', $id);
            }

            $rows = $query
                ->orderBy('place', 'DESC')
                ->select(['id', 'place', 'latitude', 'longitude'])
                ->get();

            $next_level = count($hierarchy);

            foreach ($rows as $row) {
                $hierarchy[$next_level] = $row->place;
                array_unshift($queue, [$row->id, $hierarchy, $row->latitude, $row->longitude]);
            }
        }

        $geojson = [
            'type'     => 'FeatureCollection',
            'features' => $features,
        ];

        $filename = addcslashes($filename, '"');

        return response($geojson)
            ->withHeader('content-type', 'application/vnd.geo+json')
            ->withHeader('content-disposition', 'attachment; filename="' . $filename . '"');
    }
}
