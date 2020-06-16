<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

use Exception;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Http\RequestHandlers\ControlPanel;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\PlaceLocation;
use Fisharebest\Webtrees\Services\GedcomService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\QueryException;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use stdClass;

use function abs;
use function addcslashes;
use function array_combine;
use function array_filter;
use function array_merge;
use function array_pad;
use function array_pop;
use function array_reverse;
use function array_shift;
use function array_slice;
use function assert;
use function count;
use function e;
use function explode;
use function fclose;
use function fgetcsv;
use function fopen;
use function fputcsv;
use function implode;
use function is_numeric;
use function is_string;
use function json_decode;
use function preg_replace;
use function redirect;
use function response;
use function rewind;
use function round;
use function route;
use function str_replace;
use function stream_get_contents;
use function stripos;
use function substr_count;

use const UPLOAD_ERR_OK;

/**
 * Controller for maintaining geographic data.
 */
class LocationController extends AbstractAdminController
{
    // Location of files to import
    private const PLACES_FOLDER = 'places/';

    /** @var GedcomService */
    private $gedcom_service;

    /** @var TreeService */
    private $tree_service;

    /**
     * Dependency injection.
     *
     * @param GedcomService $gedcom_service
     * @param TreeService   $tree_service
     */
    public function __construct(GedcomService $gedcom_service, TreeService $tree_service)
    {
        $this->gedcom_service = $gedcom_service;
        $this->tree_service   = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function mapData(ServerRequestInterface $request): ResponseInterface
    {
        $parent_id   = (int) ($request->getQueryParams()['parent_id'] ?? 0);
        $hierarchy   = $this->getHierarchy($parent_id);
        $title       = I18N::translate('Geographic data');
        $breadcrumbs = [
            route(ControlPanel::class) => I18N::translate('Control panel'),
            route('map-data')          => $title,
        ];

        foreach ($hierarchy as $row) {
            $breadcrumbs[route('map-data', ['parent_id' => $row->pl_id])] = $row->pl_place;
        }
        $breadcrumbs[] = array_pop($breadcrumbs);

        return $this->viewResponse('admin/locations', [
            'title'       => $title,
            'breadcrumbs' => $breadcrumbs,
            'parent_id'   => $parent_id,
            'placelist'   => $this->getPlaceListLocation($parent_id),
            'tree_titles' => $this->tree_service->titles(),
        ]);
    }

    /**
     * @param int $id
     *
     * @return array<stdClass>
     */
    private function getHierarchy(int $id): array
    {
        $arr  = [];
        $fqpn = [];

        while ($id !== 0) {
            $row = DB::table('placelocation')
                ->where('pl_id', '=', $id)
                ->first();

            // For static analysis tools.
            assert($row instanceof stdClass);

            $fqpn[]    = $row->pl_place;
            $row->fqpn = implode(Gedcom::PLACE_SEPARATOR, $fqpn);
            $id        = (int) $row->pl_parent_id;
            $arr[]     = $row;
        }

        return array_reverse($arr);
    }

    /**
     * Find all of the places in the hierarchy
     *
     * @param int $id
     *
     * @return stdClass[]
     */
    private function getPlaceListLocation(int $id): array
    {
        // We know the id of the place in the placelocation table,
        // now get the id of the same place in the places table
        if ($id === 0) {
            $fqpn = '';
        } else {
            $hierarchy = $this->getHierarchy($id);
            $fqpn      = ', ' . $hierarchy[0]->fqpn;
        }

        $rows = DB::table('placelocation')
            ->where('pl_parent_id', '=', $id)
            ->orderBy(new Expression('pl_place /*! COLLATE ' . I18N::collation() . ' */'))
            ->get();

        $list = [];
        foreach ($rows as $row) {
            // Find/count places without co-ordinates
            $children = $this->childLocationStatus((int) $row->pl_id);
            $active   = $this->isLocationActive($row->pl_place . $fqpn);

            if (!$active) {
                $badge = 'danger';
            } elseif ((int) $children->no_coord > 0) {
                $badge = 'warning';
            } elseif ((int) $children->child_count > 0) {
                $badge = 'info';
            } else {
                $badge = 'secondary';
            }

            $row->child_count = (int) $children->child_count;
            $row->badge       = $badge;

            $list[] = $row;
        }

        return $list;
    }

    /**
     * How many children does place have?  How many have co-ordinates?
     *
     * @param int $parent_id
     *
     * @return stdClass
     */
    private function childLocationStatus(int $parent_id): stdClass
    {
        $prefix = DB::connection()->getTablePrefix();

        $expression =
            $prefix . 'p0.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p0.pl_lati, '') = '' OR " .
            $prefix . 'p1.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p1.pl_lati, '') = '' OR " .
            $prefix . 'p2.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p2.pl_lati, '') = '' OR " .
            $prefix . 'p3.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p3.pl_lati, '') = '' OR " .
            $prefix . 'p4.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p4.pl_lati, '') = '' OR " .
            $prefix . 'p5.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p5.pl_lati, '') = '' OR " .
            $prefix . 'p6.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p6.pl_lati, '') = '' OR " .
            $prefix . 'p7.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p7.pl_lati, '') = '' OR " .
            $prefix . 'p8.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p8.pl_lati, '') = '' OR " .
            $prefix . 'p9.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p9.pl_lati, '') = ''";

        return DB::table('placelocation AS p0')
            ->leftJoin('placelocation AS p1', 'p1.pl_parent_id', '=', 'p0.pl_id')
            ->leftJoin('placelocation AS p2', 'p2.pl_parent_id', '=', 'p1.pl_id')
            ->leftJoin('placelocation AS p3', 'p3.pl_parent_id', '=', 'p2.pl_id')
            ->leftJoin('placelocation AS p4', 'p4.pl_parent_id', '=', 'p3.pl_id')
            ->leftJoin('placelocation AS p5', 'p5.pl_parent_id', '=', 'p4.pl_id')
            ->leftJoin('placelocation AS p6', 'p6.pl_parent_id', '=', 'p5.pl_id')
            ->leftJoin('placelocation AS p7', 'p7.pl_parent_id', '=', 'p6.pl_id')
            ->leftJoin('placelocation AS p8', 'p8.pl_parent_id', '=', 'p7.pl_id')
            ->leftJoin('placelocation AS p9', 'p9.pl_parent_id', '=', 'p8.pl_id')
            ->where('p0.pl_parent_id', '=', $parent_id)
            ->select([new Expression('COUNT(*) AS child_count'), new Expression('SUM(' . $expression . ') AS no_coord')])
            ->first();
    }

    /**
     * Is a place name used in any tree?
     *
     * @param string $place_name
     *
     * @return bool
     */
    private function isLocationActive(string $place_name): bool
    {
        $places = explode(Gedcom::PLACE_SEPARATOR, $place_name);

        $query = DB::table('places AS p0')
            ->where('p0.p_place', '=', $places[0])
            ->select(['p0.*']);

        array_shift($places);

        foreach ($places as $n => $place) {
            $query->join('places AS p' . ($n + 1), static function (JoinClause $join) use ($n, $place): void {
                $join
                    ->on('p' . ($n + 1) . '.p_id', '=', 'p' . $n . '.p_parent_id')
                    ->where('p' . ($n + 1) . '.p_place', '=', $place);
            });
        }

        return $query->exists();
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function mapDataEdit(ServerRequestInterface $request): ResponseInterface
    {
        $parent_id = (int) $request->getQueryParams()['parent_id'];
        $hierarchy = $this->getHierarchy($parent_id);
        $fqpn      = $hierarchy === [] ? '' : $hierarchy[0]->fqpn;
        $parent    = new PlaceLocation($fqpn);

        $place_id  = (int) $request->getQueryParams()['place_id'];
        $hierarchy = $this->getHierarchy($place_id);
        $fqpn      = $hierarchy === [] ? '' : $hierarchy[0]->fqpn;
        $location  = new PlaceLocation($fqpn);

        if ($location->id() !== 0) {
            $title = e($location->locationName());
        } else {
            // Add a place
            if ($parent_id === 0) {
                // We're at the global level so create a minimal
                // place for the page title and breadcrumbs
                $title     = I18N::translate('World');
                $hierarchy = [];
            } else {
                $hierarchy = $this->getHierarchy($parent_id);
                $tmp       = new PlaceLocation($hierarchy[0]->fqpn);
                $title     = e($tmp->locationName());
            }
        }

        $breadcrumbs = [
            route(ControlPanel::class) => I18N::translate('Control panel'),
            route('map-data')          => I18N::translate('Geographic data'),
        ];

        foreach ($hierarchy as $row) {
            $breadcrumbs[route('map-data', ['parent_id' => $row->pl_id])] = e($row->pl_place);
        }

        if ($place_id === 0) {
            $breadcrumbs[]   = I18N::translate('Add');
            $title           .= ' — ' . I18N::translate('Add');
            $latitude        = '';
            $longitude       = '';
            $map_bounds      = $parent->boundingRectangle();
            $marker_position = [$parent->latitude(), $parent->longitude()];
        } else {
            $breadcrumbs[]   = I18N::translate('Edit');
            $title           .= ' — ' . I18N::translate('Edit');
            $latitude        = $location->latitude();
            $longitude       = $location->latitude();
            $map_bounds      = $location->boundingRectangle();
            $marker_position = [$location->latitude(), $location->longitude()];
        }

        return $this->viewResponse('admin/location-edit', [
            'breadcrumbs'     => $breadcrumbs,
            'title'           => $title,
            'location'        => $location,
            'latitude'        => $latitude,
            'longitude'       => $longitude,
            'map_bounds'      => $map_bounds,
            'marker_position' => $marker_position,
            'parent'          => $parent,
            'provider'        => [
                'url'     => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                'options' => [
                    'attribution' => '<a href="https://www.openstreetmap.org/copyright">&copy; OpenStreetMap</a> contributors',
                    'max_zoom'    => 19
                ]
            ],
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function mapDataSave(ServerRequestInterface $request): ResponseInterface
    {
        $params = (array) $request->getParsedBody();

        $parent_id = (int) $request->getQueryParams()['parent_id'];
        $place_id  = (int) $request->getQueryParams()['place_id'];
        $lat       = round($params['new_place_lati'], 5); // 5 decimal places (locate to within about 1 metre)
        $lat       = ($lat < 0 ? 'S' : 'N') . abs($lat);
        $lng       = round($params['new_place_long'], 5);
        $lng       = ($lng < 0 ? 'W' : 'E') . abs($lng);
        $hierarchy = $this->getHierarchy($parent_id);
        $level     = count($hierarchy);
        $icon      = $params['icon'];
        $zoom      = (int) $params['new_zoom_factor'];

        if ($place_id === 0) {
            $place_id = 1 + (int) DB::table('placelocation')->max('pl_id');

            DB::table('placelocation')->insert([
                'pl_id'        => $place_id,
                'pl_parent_id' => $parent_id,
                'pl_level'     => $level,
                'pl_place'     => $params['new_place_name'],
                'pl_lati'      => $lat,
                'pl_long'      => $lng,
                'pl_zoom'      => $zoom,
                'pl_icon'      => $icon,
            ]);
        } else {
            DB::table('placelocation')
                ->where('pl_id', '=', $place_id)
                ->update([
                    'pl_place' => $params['new_place_name'],
                    'pl_lati'  => $lat,
                    'pl_long'  => $lng,
                    'pl_zoom'  => $zoom,
                    'pl_icon'  => $icon,
                ]);
        }

        FlashMessages::addMessage(
            I18N::translate(
                'The details for “%s” have been updated.',
                e($params['new_place_name'])
            ),
            'success'
        );

        $url = route('map-data', ['parent_id' => $parent_id]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function mapDataDelete(ServerRequestInterface $request): ResponseInterface
    {
        $place_id  = (int) $request->getQueryParams()['place_id'];
        $parent_id = (int) $request->getQueryParams()['parent_id'];

        try {
            DB::table('placelocation')
                ->where('pl_id', '=', $place_id)
                ->delete();
        } catch (Exception $ex) {
            FlashMessages::addMessage(
                I18N::translate('Location not removed: this location contains sub-locations'),
                'danger'
            );
        }
        // If after deleting there are no more places at this level then go up a level
        $children = DB::table('placelocation')
            ->where('pl_parent_id', '=', $parent_id)
            ->count();

        if ($children === 0) {
            $parent_id = (int) DB::table('placelocation')
                ->where('pl_id', '=', $parent_id)
                ->value('pl_parent_id');
        }

        $url = route('map-data', ['parent_id' => $parent_id]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function exportLocations(ServerRequestInterface $request): ResponseInterface
    {
        $parent_id = (int) $request->getQueryParams()['parent_id'];
        $format    = $request->getQueryParams()['format'];
        $hierarchy = $this->getHierarchy($parent_id);

        // Create the file name
        // $hierarchy[0] always holds the full placename
        $place_name = $hierarchy === [] ? 'Global' : $hierarchy[0]->fqpn;
        $place_name = str_replace(Gedcom::PLACE_SEPARATOR, '-', $place_name);
        $filename   = 'Places-' . preg_replace('/[^a-zA-Z0-9.-]/', '', $place_name);

        // Fill in the place names for the starting conditions
        $startfqpn = [];
        foreach ($hierarchy as $record) {
            $startfqpn[] = $record->pl_place;
        }

        // Generate an array containing the data to output.
        $places = [];
        $this->buildExport($parent_id, $startfqpn, $places);

        // Pad all locations to the length of the longest.
        $max_level = 0;
        foreach ($places as $place) {
            $max_level = max($max_level, count($place->fqpn));
        }

        $places = array_map(static function (stdClass $place) use ($max_level): array {
            return array_merge(
                [count($place->fqpn) - 1],
                array_pad($place->fqpn, $max_level, ''),
                [$place->pl_long],
                [$place->pl_lati],
                [$place->pl_zoom],
                [$place->pl_icon]
            );
        }, $places);

        if ($format === 'csv') {
            // Create the header line for the output file (always English)
            $header = [
                I18N::translate('Level'),
            ];

            for ($i = 0; $i < $max_level; $i++) {
                $header[] = 'Place' . $i;
            }

            $header[] = 'Longitude';
            $header[] = 'Latitude';
            $header[] = 'Zoom';
            $header[] = 'Icon';

            return $this->exportCSV($filename . '.csv', $header, $places);
        }

        return $this->exportGeoJSON($filename . '.geojson', $places, $max_level);
    }

    /**
     * @param int             $parent_id
     * @param array<string>   $fqpn
     * @param array<stdClass> $places
     *
     * @return void
     * @throws Exception
     */
    private function buildExport(int $parent_id, array $fqpn, array &$places): void
    {
        // Current number of levels.
        $level = count($fqpn);

        // Data for the next level.
        $rows = DB::table('placelocation')
            ->where('pl_parent_id', '=', $parent_id)
            ->orderBy(new Expression('pl_place /*! COLLATE ' . I18N::collation() . ' */'))
            ->get();

        foreach ($rows as $row) {
            $fqpn[$level] = $row->pl_place;

            $row->fqpn    = $fqpn;
            $row->pl_long = $row->pl_long ?? 'E0';
            $row->pl_lati = $row->pl_lati ?? 'N0';
            $row->pl_zoom = (int) $row->pl_zoom;
            $row->pl_icon = (string) $row->pl_icon;

            if ($row->pl_long !== 'E0' || $row->pl_lati !== 'N0') {
                $places[] = $row;
            }

            $this->buildExport((int) $row->pl_id, $fqpn, $places);
        }
    }

    /**
     * @param string     $filename
     * @param string[]   $columns
     * @param string[][] $places
     *
     * @return ResponseInterface
     */
    private function exportCSV(string $filename, array $columns, array $places): ResponseInterface
    {
        $resource = fopen('php://temp', 'wb+');

        if ($resource === false) {
            throw new RuntimeException('Failed to create temporary stream');
        }

        fputcsv($resource, $columns, ';');

        foreach ($places as $place) {
            fputcsv($resource, $place, ';');
        }

        rewind($resource);

        $filename = addcslashes($filename, '"');

        return response(stream_get_contents($resource))
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * @param string $filename
     * @param array  $rows
     * @param int    $maxlevel
     *
     * @return ResponseInterface
     */
    private function exportGeoJSON(string $filename, array $rows, int $maxlevel): ResponseInterface
    {
        $geojson = [
            'type'     => 'FeatureCollection',
            'features' => [],
        ];
        foreach ($rows as $place) {
            $fqpn = implode(
                Gedcom::PLACE_SEPARATOR,
                array_reverse(
                    array_filter(
                        array_slice($place, 1, $maxlevel + 1)
                    )
                )
            );

            $geojson['features'][] = [
                'type'       => 'Feature',
                'geometry'   => [
                    'type'        => 'Point',
                    'coordinates' => [
                        $this->gedcom_service->readLongitude($place['pl_long'] ?? ''),
                        $this->gedcom_service->readLatitude($place['pl_lati'] ?? ''),
                    ],
                ],
                'properties' => [
                    'name' => $fqpn,
                ],
            ];
        }

        $filename = addcslashes($filename, '"');

        return response($geojson)
            ->withHeader('Content-Type', 'application/vnd.geo+json')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function importLocations(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $data_filesystem_name = $request->getAttribute('filesystem.data.name');
        assert(is_string($data_filesystem_name));

        $parent_id = (int) $request->getQueryParams()['parent_id'];

        $files = Collection::make($data_filesystem->listContents('places'))
            ->filter(static function (array $metadata): bool {
                $extension = strtolower($metadata['extension'] ?? '');

                return $extension === 'csv' || $extension === 'geojson';
            })
            ->map(static function (array $metadata): string {
                return $metadata['basename'];
            })
            ->sort();

        return $this->viewResponse('admin/map-import-form', [
            'place_folder' => $data_filesystem_name . self::PLACES_FOLDER,
            'title'        => I18N::translate('Import geographic data'),
            'parent_id'    => $parent_id,
            'files'        => $files,
        ]);
    }

    /**
     * This function assumes the input file layout is
     * level followed by a variable number of placename fields
     * followed by Longitude, Latitude, Zoom & Icon
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws Exception
     */
    public function importLocationsAction(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $params = (array) $request->getParsedBody();

        $serverfile     = $params['serverfile'] ?? '';
        $options        = $params['import-options'] ?? '';
        $clear_database = (bool) ($params['cleardatabase'] ?? false);
        $local_file     = $request->getUploadedFiles()['localfile'] ?? null;

        $places      = [];
        $field_names = [
            'pl_level',
            'pl_long',
            'pl_lati',
            'pl_zoom',
            'pl_icon',
            'fqpn',
        ];

        $url = route('map-data', ['parent_id' => 0]);

        $fp = false;

        if ($serverfile !== '' && $data_filesystem->has(self::PLACES_FOLDER . $serverfile)) {
            // first choice is file on server
            $fp = $data_filesystem->readStream(self::PLACES_FOLDER . $serverfile);
        } elseif ($local_file instanceof UploadedFileInterface && $local_file->getError() === UPLOAD_ERR_OK) {
            // 2nd choice is local file
            $fp = $local_file->getStream()->detach();
        }

        if ($fp === false) {
            return redirect($url);
        }

        $string = stream_get_contents($fp);

        // Check the file type
        if (stripos($string, 'FeatureCollection') !== false) {
            $input_array = json_decode($string, false);

            foreach ($input_array->features as $feature) {
                $places[] = array_combine($field_names, [
                    $feature->properties->level ?? substr_count($feature->properties->name, ','),
                    $this->gedcom_service->writeLongitude($feature->geometry->coordinates[0]),
                    $this->gedcom_service->writeLatitude($feature->geometry->coordinates[1]),
                    $feature->properties->zoom ?? null,
                    $feature->properties->icon ?? null,
                    $feature->properties->name,
                ]);
            }
        } else {
            rewind($fp);
            while (($row = fgetcsv($fp, 0, ';')) !== false) {
                // Skip the header
                if (!is_numeric($row[0])) {
                    continue;
                }

                $level = (int) $row[0];
                $count = count($row);

                // convert separate place fields into a comma separated placename
                $fqdn = implode(Gedcom::PLACE_SEPARATOR, array_reverse(array_slice($row, 1, 1 + $level)));

                $places[] = [
                    'pl_level' => $level,
                    'pl_long'  => $row[$count - 4],
                    'pl_lati'  => $row[$count - 3],
                    'pl_zoom'  => $row[$count - 2],
                    'pl_icon'  => $row[$count - 1],
                    'fqpn'     => $fqdn,
                ];
            }
        }

        fclose($fp);

        if ($clear_database) {
            DB::table('placelocation')->delete();
        }

        $added   = 0;
        $updated = 0;

        foreach ($places as $place) {
            $location = new PlaceLocation($place['fqpn']);
            $exists   = $location->exists();

            // Only update existing records
            if ($options === 'update' && !$exists) {
                continue;
            }

            // Only add new records
            if ($options === 'add' && $exists) {
                continue;
            }

            if (!$exists) {
                $added++;
            }

            $updated += DB::table('placelocation')
                ->where('pl_id', '=', $location->id())
                ->update([
                    'pl_lati' => $place['pl_lati'],
                    'pl_long' => $place['pl_long'],
                    'pl_zoom' => $place['pl_zoom'] ?: null,
                    'pl_icon' => $place['pl_icon'] ?: null,
                ]);
        }
        FlashMessages::addMessage(
            I18N::translate('locations updated: %s, locations added: %s', I18N::number($updated), I18N::number($added)),
            'info'
        );

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function importLocationsFromTree(ServerRequestInterface $request): ResponseInterface
    {
        $params = (array) $request->getParsedBody();

        $ged  = $params['ged'] ?? '';
        $tree = $this->tree_service->all()->get($ged);
        assert($tree instanceof Tree);

        // Get all the places from the places table ...
        $places = DB::table('places AS p0')
            ->leftJoin('places AS p1', 'p1.p_id', '=', 'p0.p_parent_id')
            ->leftJoin('places AS p2', 'p2.p_id', '=', 'p1.p_parent_id')
            ->leftJoin('places AS p3', 'p3.p_id', '=', 'p2.p_parent_id')
            ->leftJoin('places AS p4', 'p4.p_id', '=', 'p3.p_parent_id')
            ->leftJoin('places AS p5', 'p5.p_id', '=', 'p4.p_parent_id')
            ->leftJoin('places AS p6', 'p6.p_id', '=', 'p5.p_parent_id')
            ->leftJoin('places AS p7', 'p7.p_id', '=', 'p6.p_parent_id')
            ->leftJoin('places AS p8', 'p8.p_id', '=', 'p7.p_parent_id')
            ->where('p0.p_file', '=', $tree->id())
            ->select([
                'p0.p_place AS place0',
                'p1.p_place AS place1',
                'p2.p_place AS place2',
                'p3.p_place AS place3',
                'p4.p_place AS place4',
                'p5.p_place AS place5',
                'p6.p_place AS place6',
                'p7.p_place AS place7',
                'p8.p_place AS place8',
            ])
            ->get()
            ->map(static function (stdClass $row): string {
                return implode(', ', array_filter((array) $row));
            });

        // ... and the placelocation table
        $locations = DB::table('placelocation AS p0')
            ->leftJoin('placelocation AS p1', 'p1.pl_id', '=', 'p0.pl_parent_id')
            ->leftJoin('placelocation AS p2', 'p2.pl_id', '=', 'p1.pl_parent_id')
            ->leftJoin('placelocation AS p3', 'p3.pl_id', '=', 'p2.pl_parent_id')
            ->leftJoin('placelocation AS p4', 'p4.pl_id', '=', 'p3.pl_parent_id')
            ->leftJoin('placelocation AS p5', 'p5.pl_id', '=', 'p4.pl_parent_id')
            ->leftJoin('placelocation AS p6', 'p6.pl_id', '=', 'p5.pl_parent_id')
            ->leftJoin('placelocation AS p7', 'p7.pl_id', '=', 'p6.pl_parent_id')
            ->leftJoin('placelocation AS p8', 'p8.pl_id', '=', 'p7.pl_parent_id')
            ->select([
                'p0.pl_id',
                'p0.pl_place AS place0',
                'p1.pl_place AS place1',
                'p2.pl_place AS place2',
                'p3.pl_place AS place3',
                'p4.pl_place AS place4',
                'p5.pl_place AS place5',
                'p6.pl_place AS place6',
                'p7.pl_place AS place7',
                'p8.pl_place AS place8',
            ])
            ->get()
            ->map(static function (stdClass $row): stdClass {
                $row->place = implode(', ', array_filter(array_slice((array) $row, 1)));

                return $row;
            })
            ->pluck('place', 'pl_id');

        // Compare the two ...
        $diff = $places->diff($locations);

        // ... and process the differences
        $inserted = 0;
        if ($diff->isNotEmpty()) {
            $nextRecordId = 1 + (int) DB::table('placelocation')->max('pl_id');

            foreach ($diff as $place) {
                // For Westminster, London, England, we must also create England and London, England
                $place_parts = explode(', ', $place);
                $count       = count($place_parts);

                try {
                    $parent_id = 0;
                    for ($i = $count - 1; $i >= 0; $i--) {
                        $parent   = implode(', ', array_slice($place_parts, $i));
                        $place_id = $locations->search($parent);

                        if ($place_id === false) {
                            DB::table('placelocation')->insert([
                                'pl_id'        => $nextRecordId,
                                'pl_parent_id' => $parent_id,
                                'pl_level'     => $count - $i,
                                'pl_place'     => $place_parts[$i],
                            ]);

                            $parent_id             = $nextRecordId;
                            $locations[$parent_id] = $parent;
                            $inserted++;
                            $nextRecordId++;
                        } else {
                            $parent_id = $place_id;
                        }
                    }
                } catch (QueryException $ex) {
                    // Duplicates are expected due to collation differences.  e.g. Quebec / Québec
                }
            }
        }

        FlashMessages::addMessage(I18N::plural('%s location has been imported.', '%s locations have been imported.', $inserted, I18N::number($inserted)), 'success');

        $url = route('map-data');

        return redirect($url);
    }
}
