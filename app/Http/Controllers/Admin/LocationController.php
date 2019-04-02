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

use Exception;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Services\GedcomService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\QueryException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use stdClass;
use function addcslashes;
use function app;
use function fclose;
use function fputcsv;
use function rewind;
use function route;
use const UPLOAD_ERR_OK;

/**
 * Controller for maintaining geographic data.
 */
class LocationController extends AbstractAdminController
{
    /** @var GedcomService */
    private $gedcom_service;

    /**
     * Dependency injection.
     *
     * @param GedcomService $gedcom_service
     */
    public function __construct(GedcomService $gedcom_service)
    {
        $this->gedcom_service = $gedcom_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function mapData(ServerRequestInterface $request): ResponseInterface
    {
        $parent_id   = (int) ($request->getQueryParams()['parent_id'] ?? 0);
        $hierarchy   = $this->gethierarchy($parent_id);
        $title       = I18N::translate('Geographic data');
        $breadcrumbs = [
            route('admin-control-panel') => I18N::translate('Control panel'),
            route('map-data')            => $title,
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
        ]);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    private function gethierarchy(int $id): array
    {
        $arr  = [];
        $fqpn = [];

        while ($id !== 0) {
            $row = DB::table('placelocation')
                ->where('pl_id', '=', $id)
                ->first();

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
            $hierarchy = $this->gethierarchy($id);
            $fqpn      = ', ' . $hierarchy[0]->fqpn;
        }

        $rows = DB::table('placelocation')
            ->where('pl_parent_id', '=', $id)
            ->orderBy('pl_place')
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
            $prefix . 'p0.pl_place IS NOT NULL AND ' . $prefix . 'p0.pl_lati IS NULL OR ' .
            $prefix . 'p1.pl_place IS NOT NULL AND ' . $prefix . 'p1.pl_lati IS NULL OR ' .
            $prefix . 'p2.pl_place IS NOT NULL AND ' . $prefix . 'p2.pl_lati IS NULL OR ' .
            $prefix . 'p3.pl_place IS NOT NULL AND ' . $prefix . 'p3.pl_lati IS NULL OR ' .
            $prefix . 'p4.pl_place IS NOT NULL AND ' . $prefix . 'p4.pl_lati IS NULL OR ' .
            $prefix . 'p5.pl_place IS NOT NULL AND ' . $prefix . 'p5.pl_lati IS NULL OR ' .
            $prefix . 'p6.pl_place IS NOT NULL AND ' . $prefix . 'p6.pl_lati IS NULL OR ' .
            $prefix . 'p7.pl_place IS NOT NULL AND ' . $prefix . 'p7.pl_lati IS NULL OR ' .
            $prefix . 'p8.pl_place IS NOT NULL AND ' . $prefix . 'p8.pl_lati IS NULL OR ' .
            $prefix . 'p9.pl_place IS NOT NULL AND ' . $prefix . 'p9.pl_lati IS NULL';

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
            ->select([DB::raw('COUNT(*) AS child_count'), DB::raw('SUM(' . $expression . ') AS no_coord')])
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
            ->select(['pl0.*']);

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
        $place_id  = (int) $request->getQueryParams()['place_id'];
        $hierarchy = $this->gethierarchy($place_id);
        $fqpn      = empty($hierarchy) ? '' : $hierarchy[0]->fqpn;
        $location  = new Location($fqpn);

        if ($location->id() !== 0) {
            $lat = $location->latitude();
            $lng = $location->longitude();
            $id  = $place_id;
        } else {
            $lat = '';
            $lng = '';
            $id  = $parent_id;
        }

        $title = e($location->locationName());

        $breadcrumbs = [
            route('admin-control-panel') => I18N::translate('Control panel'),
            route('map-data')            => I18N::translate('Geographic data'),
        ];

        foreach ($hierarchy as $row) {
            $breadcrumbs[route('map-data', ['parent_id' => $row->pl_id])] = $row->pl_place;
        }

        if ($place_id === 0) {
            $breadcrumbs[] = I18N::translate('Add');
        } else {
            $breadcrumbs[] = I18N::translate('Edit');
            $title         .= ' — ' . I18N::translate('Edit');
        }

        return $this->viewResponse('admin/location-edit', [
            'breadcrumbs' => $breadcrumbs,
            'title'       => $title,
            'location'    => $location,
            'place_id'    => $place_id,
            'parent_id'   => $parent_id,
            'hierarchy'   => $hierarchy,
            'lat'         => $lat,
            'lng'         => $lng,
            'ref'         => $id,
            'data'        => $this->mapLocationData($id),
        ]);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    private function mapLocationData(int $id): array
    {
        $row = DB::table('placelocation')
            ->where('pl_id', '=', $id)
            ->first();

        if (empty($row)) {
            $json = [
                'zoom'        => 2,
                'coordinates' => [
                    0.0,
                    0.0,
                ],
            ];
        } else {
            $json = [
                'zoom'        => (int) $row->pl_zoom ?: 2,
                'coordinates' => [
                    (float) strtr($row->pl_lati ?? '0', ['N' => '', 'S' => '-', ',' => '.']),
                    (float) strtr($row->pl_long ?? '0', ['E' => '', 'W' => '-', ',' => '.']),
                ],
            ];
        }

        return $json;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function mapDataSave(ServerRequestInterface $request): ResponseInterface
    {
        $parent_id = (int) $request->getParsedBody()['parent_id'];
        $place_id  = (int) $request->getParsedBody()['place_id'];
        $lat       = round($request->getParsedBody()['new_place_lati'], 5); // 5 decimal places (locate to within about 1 metre)
        $lat       = ($lat < 0 ? 'S' : 'N') . abs($lat);
        $lng       = round($request->getParsedBody()['new_place_long'], 5);
        $lng       = ($lng < 0 ? 'W' : 'E') . abs($lng);
        $hierarchy = $this->gethierarchy($parent_id);
        $level     = count($hierarchy);
        $icon      = $request->getParsedBody()['icon'];
        $zoom      = (int) $request->getParsedBody()['new_zoom_factor'];

        if ($place_id === 0) {
            $place_id = 1 + (int) DB::table('placelocation')->max('pl_id');

            DB::table('placelocation')->insert([
                'pl_id'        => $place_id,
                'pl_parent_id' => $parent_id,
                'pl_level'     => $level,
                'pl_place'     => $request->getParsedBody()['new_place_name'],
                'pl_lati'      => $request->getParsedBody()['lati_control'] . $lat,
                'pl_long'      => $request->getParsedBody()['long_control'] . $lng,
                'pl_zoom'      => $zoom,
                'pl_icon'      => $icon,
            ]);
        } else {
            DB::table('placelocation')
                ->where('pl_id', '=', $place_id)
                ->update([
                    'pl_place' => $request->getParsedBody()['new_place_name'],
                    'pl_lati'  => $request->getParsedBody()['lati_control'] . $lat,
                    'pl_long'  => $request->getParsedBody()['long_control'] . $lng,
                    'pl_zoom'  => (int) $request->getParsedBody()['new_zoom_factor'],
                    'pl_icon'  => $icon,
                ]);
        }
        FlashMessages::addMessage(
            I18N::translate(
                'The details for “%s” have been updated.',
                $request->getParsedBody()['new_place_name']
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
        $place_id  = (int) $request->getParsedBody()['place_id'];
        $parent_id = (int) $request->getParsedBody()['parent_id'];

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
        $maxlevel  = (int) DB::table('placelocation')->max('pl_level');
        $startfqpn = [];
        $hierarchy = $this->gethierarchy($parent_id);

        // Create the file name
        $place_name = empty($hierarchy) ? 'Global' : $hierarchy[0]->fqpn; // $hierarchy[0] always holds the full placename
        $place_name = str_replace(Gedcom::PLACE_SEPARATOR, '-', $place_name);
        $filename   = 'Places-' . preg_replace('/[^a-zA-Z0-9\-\.]/', '', $place_name);

        // Fill in the place names for the starting conditions
        foreach ($hierarchy as $level => $record) {
            $startfqpn[$level] = $record->pl_place;
        }
        $startfqpn = array_pad($startfqpn, $maxlevel + 1, '');

        // Generate an array containing the data to output
        $places = [];
        $this->buildLevel($parent_id, $startfqpn, $places);

        $places = array_filter($places, static function (array $place): bool {
            return $place['pl_long'] !== 0.0 && $place['pl_lati'] !== 0.0;
        });

        if ($format === 'csv') {
            // Create the header line for the output file (always English)
            $header = [
                I18N::translate('Level'),
            ];

            for ($i = 0; $i <= $maxlevel; $i++) {
                $header[] = 'Place' . ($i + 1);
            }

            $header[] = 'Longitude';
            $header[] = 'Latitude';
            $header[] = 'Zoom';
            $header[] = 'Icon';

            return $this->exportCSV($filename . '.csv', $header, $places);
        }

        return $this->exportGeoJSON($filename . '.geojson', $places, $maxlevel);
    }

    /**
     * @param int   $parent_id
     * @param array $placename
     * @param array $places
     *
     * @return void
     * @throws Exception
     */
    private function buildLevel(int $parent_id, array $placename, array &$places): void
    {
        $level = array_search('', $placename);

        $rows = DB::table('placelocation')
            ->where('pl_parent_id', '=', $parent_id)
            ->orderBy('pl_place')
            ->get();

        foreach ($rows as $row) {
            $index             = (int) $row->pl_id;
            $placename[$level] = $row->pl_place;
            $places[]          = array_merge(['pl_level' => $row->pl_level], $placename, ['pl_long' => $row->pl_long, 'pl_lati' => $row->pl_lati, 'pl_zoom' => $row->pl_zoom, 'pl_icon' => $row->pl_icon]);
            $this->buildLevel($index, $placename, $places);
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
        $resource = fopen('php://temp', 'rb+');

        fputcsv($resource, $columns, ';');

        foreach ($places as $place) {
            fputcsv($resource, $place, ';');
        }

        rewind($resource);

        // Use a stream, so that we do not have to load the entire file into memory.
        $stream   = app(StreamFactoryInterface::class)->createStreamFromResource($resource);
        $filename = addcslashes($filename, '"');

        return response()
            ->withBody($stream)
            ->withHeader('Content-type', 'text/csv; charset=UTF-8')
            ->withHeader('Content-disposition', 'attachment; filename="' . $filename . '"');
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

        return response($geojson)
            ->withHeader('Content-type', 'application/vnd.geo+json')
            ->withHeader('Content-disposition', 'attachment; filename="' . addcslashes($filename, '"') . '"');
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function importLocations(ServerRequestInterface $request): ResponseInterface
    {
        $parent_id = (int) $request->getParsedBody()['parent_id'];

        $files = array_merge(
            glob(WT_DATA_DIR . 'places/*.csv', GLOB_NOSORT),
            glob(WT_DATA_DIR . 'places/*.geojson', GLOB_NOSORT)
        );

        $files = array_map(static function (string $place): string {
            return substr($place, strlen(WT_DATA_DIR . 'places/'));
        }, $files);

        asort($files);

        return $this->viewResponse('admin/map-import-form', [
            'data_folder' => WT_DATA_DIR,
            'title'       => I18N::translate('Import geographic data'),
            'parent_id'   => $parent_id,
            'files'       => $files,
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
        $serverfile     = $request->getParsedBody()['serverfile'] ?? '';
        $options        = $request->getParsedBody()['import-options'] ?? '';
        $clear_database = (bool) ($request->getParsedBody()['cleardatabase'] ?? false);
        $local_file     = $request->getUploadedFiles()['localfile'] ?? null;

        $filename    = '';
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

        if ($serverfile !== '' && is_dir(WT_DATA_DIR . 'places')) {
            // first choice is file on server
            $fp = fopen(WT_DATA_DIR . 'places/' . $serverfile, 'rb+');
        } elseif ($local_file instanceof UploadedFileInterface && $local_file->getError() === UPLOAD_ERR_OK) {
            // 2nd choice is local file
            $fp = $local_file->getStream()->detach();
        } else {
            return redirect($url);
        }

        if ($fp !== false) {
            $string = stream_get_contents($fp);

            // Check the filetype
            if (stripos($string, 'FeatureCollection') !== false) {
                $input_array = json_decode($string);

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

            //process places
            $added   = 0;
            $updated = 0;

            foreach ($places as $place) {
                $location = new Location($place['fqpn']);
                $exists   = $location->exists();

                if ($options === 'update' && !$exists) {
                    continue;
                }

                if (!$exists) {
                    $added++;
                }

                if (!$exists || $options === 'update') {
                    DB::table('placelocation')
                        ->where('pl_id', '=', $location->id())
                        ->update([
                            'pl_lati' => $place['pl_lati'],
                            'pl_long' => $place['pl_long'],
                            'pl_zoom' => $place['pl_zoom'] ? $place['pl_zoom'] : null,
                            'pl_icon' => $place['pl_icon'] ? $place['pl_icon'] : null,
                        ]);

                    $updated++;
                }
            }
            FlashMessages::addMessage(
                I18N::translate('locations updated: %s, locations added: %s', I18N::number($updated), I18N::number($added)),
                $added + $updated === 0 ? 'info' : 'success'
            );
        } else {
            throw new Exception('Unable to open file: ' . $filename);
        }

        return redirect($url);
    }

    /**
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function importLocationsFromTree(Tree $tree): ResponseInterface
    {
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
