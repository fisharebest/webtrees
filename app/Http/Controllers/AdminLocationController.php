<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Exception;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Tree;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller for maintaining geographic data.
 */
class AdminLocationController extends AbstractBaseController
{
    /** @var string */
    protected $layout = 'layouts/administration';

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function mapData(Request $request): Response
    {
        $parent_id   = (int) $request->get('parent_id', 0);
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
     * @param Request $request
     *
     * @return Response
     */
    public function mapDataEdit(Request $request): Response
    {
        $parent_id = (int) $request->get('parent_id');
        $place_id  = (int) $request->get('place_id');
        $hierarchy = $this->gethierarchy($place_id);
        $fqpn      = empty($hierarchy) ? '' : $hierarchy[0]->fqpn;
        $location  = new Location($fqpn);

        if ($location->isValid()) {
            $lat = $location->getLat();
            $lng = $location->getLon();
            $id  = $place_id;
        } else {
            $lat = '';
            $lng = '';
            $id  = $parent_id;
        }

        $title = e($location->getPlace());

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
            $title .= ' — ' . I18N::translate('Edit');
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
            'data'        => $this->mapLocationData($id)
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function mapDataSave(Request $request): RedirectResponse
    {
        $parent_id = (int) $request->get('parent_id');
        $place_id  = (int) $request->get('place_id');
        $lat       = round($request->get('new_place_lati'), 5); // 5 decimal places (locate to within about 1 metre)
        $lat       = ($lat < 0 ? 'S' : 'N') . abs($lat);
        $lng       = round($request->get('new_place_long'), 5);
        $lng       = ($lng < 0 ? 'W' : 'E') . abs($lng);
        $hierarchy = $this->gethierarchy($parent_id);
        $level     = count($hierarchy);
        $icon      = $request->get('icon', null);
        $icon      = $icon === '' ? null : $icon;
        $zoom      = $request->get('new_zoom_factor');
        $zoom      = $zoom === '' ? null : $zoom;


        if ($place_id === 0) {
            Database::prepare(
                "INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon)
						  VALUES (:id, :parent, :level, :place, :lng, :lat, :zoom, :icon)"
            )->execute(
                [
                    'id'     => (int) Database::prepare("SELECT MAX(pl_id)+1 FROM `##placelocation`")->fetchOne(),
                    'parent' => $parent_id,
                    'level'  => $level,
                    'place'  => $request->get('new_place_name'),
                    'lat'    => $request->get('lati_control') . $lat,
                    'lng'    => $request->get('long_control') . $lng,
                    'zoom'   => $zoom,
                    'icon'   => $icon,
                ]
            );
        } else {
            Database::prepare(
                "UPDATE `##placelocation` SET pl_place = :place, pl_lati = :lat, pl_long = :lng, pl_zoom = :zoom, pl_icon = :icon WHERE pl_id = :id"
            )->execute([
                'id'    => $place_id,
                'place' => $request->get('new_place_name'),
                'lat'   => $request->get('lati_control') . $lat,
                'lng'   => $request->get('long_control') . $lng,
                'zoom'  => (int) $request->get('new_zoom_factor'),
                'icon'  => $icon,
            ]);
        }
        FlashMessages::addMessage(
            I18N::translate(
                'The details for “%s” have been updated.',
                $request->get('new_place_name')
            ),
            'success'
        );

        $url = route('map-data', ['parent_id' => $parent_id]);

        return new RedirectResponse($url);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function mapDataDelete(Request $request): RedirectResponse
    {
        $place_id  = (int) $request->get('place_id');
        $parent_id = (int) $request->get('parent_id');

        try {
            Database::prepare(
                "DELETE FROM `##placelocation` WHERE pl_id = :id"
            )->execute(
                [
                    'id' => $place_id,
                ]
            );
        } catch (Exception $ex) {
            DebugBar::addThrowable($ex);

            FlashMessages::addMessage(
                I18N::translate('Location not removed: this location contains sub-locations'),
                'danger'
            );
        }
        // If after deleting there are no more places at this level then go up a level
        $children = (int) Database::prepare(
            "SELECT COUNT(pl_id) FROM `##placelocation` WHERE pl_parent_id = :parent_id"
        )
            ->execute(['parent_id' => $parent_id])
            ->fetchOne();

        if ($children === 0) {
            $row = Database::prepare(
                "SELECT pl_parent_id FROM `##placelocation` WHERE pl_id = :parent_id"
            )->execute([
                'parent_id' => $parent_id,
            ])->fetchOneRow();

            $parent_id = $row->pl_parent_id;
        }

        $url = route('map-data', ['parent_id' => $parent_id]);

        return new RedirectResponse($url);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function exportLocations(Request $request): Response
    {
        $parent_id = (int) $request->get('parent_id');
        $format    = $request->get('format');
        $maxlevel  = (int) Database::prepare("SELECT max(pl_level) FROM `##placelocation`")->execute()->fetchOne();
        $startfqpn = [];
        $hierarchy = $this->gethierarchy($parent_id);

        // Create the file name
        $place_name = empty($hierarchy) ? 'Global' : $hierarchy[0]->fqpn; // $hierarchy[0] always holds the full placename
        $place_name = str_replace(Place::GEDCOM_SEPARATOR, '-', $place_name);
        $filename   = 'Places-' . preg_replace('/[^a-zA-Z0-9\-\.]/', '', $place_name);

        // Fill in the place names for the starting conditions
        foreach ($hierarchy as $level => $record) {
            $startfqpn[$level] = $record->pl_place;
        }
        $startfqpn = array_pad($startfqpn, $maxlevel + 1, '');

        // Generate an array containing the data to output
        $places = [];
        $this->buildLevel($parent_id, $startfqpn, $places);

        // Clean up co-ordinates
        $places = array_map(function (array $place): array {
            $place['pl_long'] = (float) strtr($place['pl_long'] ?? '0', ['E' => '', 'W' => '-', ',' => '.']);
            $place['pl_lati'] = (float) strtr($place['pl_lati'] ?? '0', ['N' => '', 'S' => '-', ',' => '.']);

            return $place;
        }, $places);

        $places = array_filter($places, function (array $place): bool {
            return $place['pl_long'] !== 0 && $place['pl_lati'] !== 0;
        });

        if ($format === 'csv') {
            // Create the header line for the output file (always English)
            $header = [
                I18N::translate('Level')
            ];

            for ($i = 0; $i <= $maxlevel; $i++) {
                $header[] = 'Place' . $i;
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
     * @param Request $request
     *
     * @return Response
     */
    public function importLocations(Request $request): Response
    {
        $parent_id = (int) $request->get('parent_id');

        $files = array_merge(
            glob(WT_DATA_DIR . 'places/*.csv'),
            glob(WT_DATA_DIR . 'places/*.geojson')
        );

        $files = array_map(function (string $place): string {
            return substr($place, strlen(WT_DATA_DIR . 'places/'));
        }, $files);

        asort($files);

        return $this->viewResponse('admin/map-import-form', [
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
     * @param Request $request
     *
     * @return RedirectResponse
     * @throws Exception
     */
    public function importLocationsAction(Request $request): RedirectResponse
    {
        $serverfile  = $request->get('serverfile');
        $options     = $request->get('import-options');
        $parent_id   = $request->get('parent_id');

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

        if ($serverfile !== '') {  // first choice is file on server
            $filename = WT_DATA_DIR . 'places/' . $serverfile;
        } elseif ($_FILES['localfile']['error'] === UPLOAD_ERR_OK) { // 2nd choice is local file
            $filename = $_FILES['localfile']['tmp_name'];
        }

        if (is_file($filename)) {
            $string = file_get_contents($filename);

            // Check the filetype
            if (stripos($string, 'FeatureCollection') !== false) {
                $input_array = json_decode($string);
                foreach ($input_array->features as $feature) {
                    $places[] = array_combine($field_names, [
                        $feature->properties->level ?? substr_count($feature->properties->name, ','),
                        ($feature->geometry->coordinates[0] < 0 ? 'W' : 'E') . abs($feature->geometry->coordinates[0]),
                        ($feature->geometry->coordinates[1] < 0 ? 'S' : 'N') . abs($feature->geometry->coordinates[1]),
                        $feature->properties->zoom ?? null,
                        $feature->properties->icon ?? null,
                        $feature->properties->name,
                    ]);
                }
            } else {
                $fp = fopen($filename, 'r');
                while (($row = fgetcsv($fp, 0, ';')) !== false) {
                    // Skip the header
                    if (!is_numeric($row[0])) {
                        continue;
                    }

                    $fields = count($row);

                    // convert separate place fields into a comma separated placename
                    $fqdn = implode(Place::GEDCOM_SEPARATOR, array_filter(array_reverse(array_slice($row, 1, $fields - 5))));

                    $places[] = [
                        'pl_level' => $row[0],
                        'pl_long'  => $row[$fields - 4],
                        'pl_lati'  => $row[$fields - 3],
                        'pl_zoom'  => $row[$fields - 2],
                        'pl_icon'  => $row[$fields - 1],
                        'fqpn'     => $fqdn,
                    ];
                }
            }

            if ((bool) $request->get('cleardatabase')) {
                Database::exec("DELETE FROM `##placelocation`");
            }

            //process places
            $added   = 0;
            $updated = 0;

            //sort places by level
            usort(
                $places,
                function (array $a, array $b): int {
                    if ((int) $a['pl_level'] === (int) $b['pl_level']) {
                        return I18N::strcasecmp($a['fqpn'], $b['fqpn']);
                    }

                    return (int) $a['pl_level'] - (int) $b['pl_level'];
                }
            );

            foreach ($places as $place) {
                $location = new Location($place['fqpn']);
                $valid    = $location->isValid();

                // can't match data type here because default table values are null
                // but csv file return empty string
                if ($valid && $options !== 'add' && (
                    $place['pl_level'] != $location->getLevel() ||
                    $place['pl_long'] != $location->getLon('DMS+') ||
                    $place['pl_lati'] != $location->getLat('DMS+') ||
                    $place['pl_zoom'] != $location->getZoom() ||
                    $place['pl_icon'] != $location->getIcon()
                )) {
                    // overwrite
                    $location->update((object) $place);
                    $updated++;
                } elseif (!$valid && $options !== 'update') {
                    //add
                    $place_parts = explode(Place::GEDCOM_SEPARATOR, $place['fqpn']);
                    // work throught the place parts starting at level 0,
                    // looking for a record in the database, if not found then add it
                    $parent_id = 0;
                    for ($i = count($place_parts) - 1; $i >= 0; $i--) {
                        $new_parts    = array_slice($place_parts, $i);
                        $new_fqpn     = implode(Place::GEDCOM_SEPARATOR, $new_parts);
                        $new_location = new Location(
                            $new_fqpn,
                            [
                                'fqpn'         => $new_fqpn,
                                'pl_id'        => 0,
                                'pl_parent_id' => $parent_id,
                                'pl_level'     => count($new_parts) - 1,
                                'pl_place'     => $new_parts[0],
                                'pl_long'      => $i === 0 ? $place['pl_long'] : null,
                                'pl_lati'      => $i === 0 ? $place['pl_lati'] : null,
                                'pl_zoom'      => $i === 0 ? $place['pl_zoom'] : null,
                                'pl_icon'      => $i === 0 ? $place['pl_icon'] : null,
                            ]
                        );

                        if ($new_location->isValid()) {
                            $parent_id = $new_location->getId();
                        } else {
                            $parent_id = $new_location->add();
                            $added++;
                        }
                    }
                }
            }
            FlashMessages::addMessage(
                I18N::translate('locations updated: %s, locations added: %s', I18N::number($updated), I18N::number($added)),
                $added + $updated === 0 ? 'info' : 'success'
            );
        } else {
            throw new Exception('Unable to open file: ' . $filename);
        }

        $url = route('map-data', ['parent_id' => $parent_id]);

        return new RedirectResponse($url);
    }

    /**
     * @param Tree $tree
     *
     * @return RedirectResponse
     */
    public function importLocationsFromTree(Tree $tree): RedirectResponse
    {
        // Get all the places from the places table ...
        $places = Database::prepare(
            "SELECT
            CONCAT_WS(',', t1.p_place, t2.p_place, t3.p_place, t4.p_place, t5.p_place, t6.p_place, t7.p_place, t8.p_place, t9.p_place)
            FROM `##places` t1
            LEFT JOIN `##places` t2 ON t1.p_parent_id = t2.p_id
            LEFT JOIN `##places` t3 ON t2.p_parent_id = t3.p_id
            LEFT JOIN `##places` t4 ON t3.p_parent_id = t4.p_id
            LEFT JOIN `##places` t5 ON t4.p_parent_id = t5.p_id
            LEFT JOIN `##places` t6 ON t5.p_parent_id = t6.p_id
            LEFT JOIN `##places` t7 ON t6.p_parent_id = t7.p_id
            LEFT JOIN `##places` t8 ON t7.p_parent_id = t8.p_id
            LEFT JOIN `##places` t9 ON t8.p_parent_id = t9.p_id
            WHERE t1.p_file = :gedcom"
        )->execute([
            'gedcom' => $tree->getTreeId(),
        ])->fetchOneColumn();

        // ... and the placelocation table
        $locations = Database::prepare(
            "SELECT t1.pl_id, CONCAT_WS(',', t1.pl_place, t2.pl_place, t3.pl_place, t4.pl_place, t5.pl_place, t6.pl_place, t7.pl_place, t8.pl_place, t9.pl_place)
            FROM `##placelocation` AS t1
            LEFT JOIN `##placelocation` AS t2 ON t1.pl_parent_id = t2.pl_id
            LEFT JOIN `##placelocation` AS t3 ON t2.pl_parent_id = t3.pl_id
            LEFT JOIN `##placelocation` AS t4 ON t3.pl_parent_id = t4.pl_id
            LEFT JOIN `##placelocation` AS t5 ON t4.pl_parent_id = t5.pl_id
            LEFT JOIN `##placelocation` AS t6 ON t5.pl_parent_id = t6.pl_id
            LEFT JOIN `##placelocation` AS t7 ON t6.pl_parent_id = t7.pl_id
            LEFT JOIN `##placelocation` AS t8 ON t7.pl_parent_id = t8.pl_id
            LEFT JOIN `##placelocation` AS t9 ON t8.pl_parent_id = t9.pl_id"
        )->fetchAssoc();

        // Compare the two ...
        $diff = array_diff($places, $locations);

        // ... and process the differences
        $inserted = 0;
        if (!empty($diff)) {
            $nextRecordId    = Database::prepare("SELECT MAX(pl_id) FROM `##placelocation`")->fetchOne() + 1;
            $insertRecordQry = Database::prepare(
                "INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place)" .
                " VALUES (:id, :parent, :level, :place)"
            );

            foreach ($diff as $place) {
                // For Westminster, London, England, we must also create England and London, England
                $place_parts = explode(',', $place);
                $count = count($place_parts);

                $parent_id = 0;
                for ($i = $count - 1; $i >= 0; $i--) {
                    $parent   = implode(',', array_slice($place_parts, $i));
                    $place_id = array_search($parent, $locations);

                    if ($place_id === false) {
                        $insertRecordQry->execute([
                            'id'     => $nextRecordId,
                            'parent' => $parent_id,
                            'level'  => $count - $i,
                            'place'  => $place_parts[$i],
                        ]);
                        $parent_id = $nextRecordId;
                        $locations[$parent_id] = $parent;
                        $inserted++;
                        $nextRecordId++;
                    } else {
                        $parent_id = $place_id;
                    }
                }
            }
        }

        FlashMessages::addMessage(I18N::plural('%s location has been imported.', '%s locations have been imported.', $inserted, I18N::number($inserted)), 'success');

        $url = route('map-data');

        return new RedirectResponse($url);
    }

    /**
     * @param string $filename
     *
     * @param string[]   $columns
     * @param string[][] $places
     *
     * @return Response
     */
    private function exportCSV(string $filename, array $columns, array $places): Response
    {
        $response = new StreamedResponse(function () use ($columns, $places) {
            $stream = fopen('php://output', 'w');

            fputcsv($stream, $columns);
            foreach ($places as $place) {
                fputcsv($stream, $place);
            }

            fclose($stream);
        });


        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');

        return $response;
    }

    /**
     * @param string $filename
     * @param array  $rows
     * @param int    $maxlevel
     *
     * @return Response
     */
    private function exportGeoJSON(string $filename, array $rows, int $maxlevel): Response
    {
        $geojson = [
            'type'     => 'FeatureCollection',
            'features' => [],
        ];
        foreach ($rows as $place) {
            $fqpn = implode(
                Place::GEDCOM_SEPARATOR,
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
                        (float) $place['pl_long'],
                        (float) $place['pl_lati'],
                    ],
                ],
                'properties' => [
                    'level' => $place[0],
                    'name'  => $fqpn,
                    'zoom'  => $place['pl_zoom'],
                    'icon'  => $place['pl_icon'],
                ],
            ];
        }

        $response = new JsonResponse($geojson);

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'application/vnd.geo+json');

        return $response;
    }

    /**
     * @param int   $parent_id
     * @param array $placename
     * @param array $places
     *
     * @return void
     * @throws Exception
     */
    private function buildLevel(int $parent_id, array $placename, array &$places)
    {
        $level = array_search('', $placename);
        $rows  = Database::prepare(
            "SELECT pl_level, pl_id, pl_place, pl_long, pl_lati, pl_zoom, pl_icon FROM `##placelocation` WHERE pl_parent_id=? ORDER BY pl_place"
        )
            ->execute([$parent_id])
            ->fetchAll();

        foreach ($rows as $row) {
            $index             = (int) $row->pl_id;
            $placename[$level] = $row->pl_place;
            $places[]          = array_merge([$row->pl_level], $placename, [$row->pl_long, $row->pl_lati, $row->pl_zoom, $row->pl_icon]);
            $this->buildLevel($index, $placename, $places);
        }
    }

    /**
     * @param $id
     *
     * @return array
     */
    private function gethierarchy($id): array
    {
        $statement = Database::prepare("SELECT pl_id, pl_parent_id, pl_place FROM `##placelocation` WHERE pl_id=:id");
        $arr       = [];
        $fqpn      = [];
        while ($id !== 0) {
            $row       = $statement->execute(['id' => $id])->fetchOneRow();
            $fqpn[]    = $row->pl_place;
            $row->fqpn = implode(Place::GEDCOM_SEPARATOR, $fqpn);
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
        $child_qry = Database::prepare(
            "SELECT  COUNT(*) AS child_count, SUM(" .
            " p1.pl_place IS NOT NULL AND (p1.pl_lati IS NULL OR p1.pl_long IS NULL) OR " .
            " p2.pl_place IS NOT NULL AND (p2.pl_lati IS NULL OR p2.pl_long IS NULL) OR " .
            " p3.pl_place IS NOT NULL AND (p3.pl_lati IS NULL OR p3.pl_long IS NULL) OR " .
            " p4.pl_place IS NOT NULL AND (p4.pl_lati IS NULL OR p4.pl_long IS NULL) OR " .
            " p5.pl_place IS NOT NULL AND (p5.pl_lati IS NULL OR p5.pl_long IS NULL) OR " .
            " p6.pl_place IS NOT NULL AND (p6.pl_lati IS NULL OR p6.pl_long IS NULL) OR " .
            " p7.pl_place IS NOT NULL AND (p7.pl_lati IS NULL OR p7.pl_long IS NULL) OR " .
            " p8.pl_place IS NOT NULL AND (p8.pl_lati IS NULL OR p8.pl_long IS NULL) OR " .
            " p9.pl_place IS NOT NULL AND (p9.pl_lati IS NULL OR p9.pl_long IS NULL)) AS no_coord" .
            " FROM `##placelocation` AS p1" .
            " LEFT JOIN `##placelocation` AS p2 ON (p2.pl_parent_id = p1.pl_id)" .
            " LEFT JOIN `##placelocation` AS p3 ON (p3.pl_parent_id = p2.pl_id)" .
            " LEFT JOIN `##placelocation` AS p4 ON (p4.pl_parent_id = p3.pl_id)" .
            " LEFT JOIN `##placelocation` AS p5 ON (p5.pl_parent_id = p4.pl_id)" .
            " LEFT JOIN `##placelocation` AS p6 ON (p6.pl_parent_id = p5.pl_id)" .
            " LEFT JOIN `##placelocation` AS p7 ON (p7.pl_parent_id = p6.pl_id)" .
            " LEFT JOIN `##placelocation` AS p8 ON (p8.pl_parent_id = p7.pl_id)" .
            " LEFT JOIN `##placelocation` AS p9 ON (p9.pl_parent_id = p8.pl_id)" .
            " WHERE p1.pl_parent_id = :parent_id"
        );

        // We know the id of the place in the placelocation table,
        // now get the id of the same place in the places table
        if ($id === 0) {
            $place_id = 0;
        } else {
            $hierarchy = $this->gethierarchy($id);
            $fqpn      = preg_quote($hierarchy[0]->fqpn);
            $place_id  = Database::prepare(
                "SELECT p1.p_id" .
                " FROM      `##places` AS p1" .
                " LEFT JOIN `##places` AS p2 ON (p1.p_parent_id = p2.p_id)" .
                " LEFT JOIN `##places` AS p3 ON (p2.p_parent_id = p3.p_id)" .
                " LEFT JOIN `##places` AS p4 ON (p3.p_parent_id = p4.p_id)" .
                " LEFT JOIN `##places` AS p5 ON (p4.p_parent_id = p5.p_id)" .
                " LEFT JOIN `##places` AS p6 ON (p5.p_parent_id = p6.p_id)" .
                " LEFT JOIN `##places` AS p7 ON (p6.p_parent_id = p7.p_id)" .
                " LEFT JOIN `##places` AS p8 ON (p7.p_parent_id = p8.p_id)" .
                " LEFT JOIN `##places` AS p9 ON (p8.p_parent_id = p9.p_id)" .
                " WHERE CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place)=:place_name"
            )->execute(
                [
                    'place_name' => $fqpn,
                ]
            )->fetchOne();
        }

        $rows = Database::prepare(
            "SELECT pl_id, pl_parent_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon," .
            " (t1.p_place IS NULL) AS inactive" .
            " FROM `##placelocation`" .
            " LEFT JOIN (SELECT DISTINCT p_place" .
            " FROM `##places`" .
            " WHERE p_parent_id = :p_id) AS t1 ON pl_place = t1.p_place" .
            " WHERE pl_parent_id=:id" .
            " ORDER BY pl_place COLLATE :collation"
        )->execute([
            'id'        => $id,
            'p_id'      => $place_id,
            'collation' => I18N::collation(),
        ])->fetchAll();

        $list = [];
        foreach ($rows as $row) {
            // Find/count places without co-ordinates
            $children = $child_qry->execute(
                [
                    'parent_id' => $row->pl_id,
                ]
            )->fetchOneRow();

            if ($row->inactive) {
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
     * @param int $id
     *
     * @return array
     */
    private function mapLocationData(int $id): array
    {
        $row = Database::prepare("SELECT * FROM `##placelocation` WHERE pl_id = :id")
            ->execute(['id' => $id])
            ->fetchOneRow();

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
}
