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

namespace Fisharebest\Webtrees\Module;

use Exception;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Stats;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OpenStreetMapModule
 */
class OpenStreetMapModule extends AbstractModule
{
    const OSM_MIN_ZOOM = 2;

    /** {@inheritdoc} */
    public function getTitle()
    {
        return 'OpenStreetMap';
    }

    /** {@inheritdoc} */
    public function getDescription()
    {
        return 'Legacy code.  This is being moved to the core';
    }

    /** {@inheritdoc} */
    public function defaultAccessLevel()
    {
        return Auth::PRIV_PRIVATE;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getMapDataAction(Request $request): JsonResponse
    {
        $reference = $request->get('reference');
        $tree      = $request->attributes->get('tree');
        $placeObj  = new Place($reference, $tree);
        $places    = $placeObj->getChildPlaces();
        $features  = [];
        $flag_path = WT_MODULES_DIR . $this->getName() . '/';
        $stats     = new Stats($tree);
        $showlink  = true;
        if (empty($places)) {
            $places[] = $placeObj;
            $showlink = false;
        }
        foreach ($places as $id => $place) {
            $location = new Location($place->getGedcomName());
            //Stats
            $placeStats = [];
            foreach ([
                         'INDI',
                         'FAM',
                     ] as $type) {
                $tmp               = $stats->statsPlaces($type, false, $place->getPlaceId());
                $placeStats[$type] = empty($tmp) ? 0 : $tmp[0]['tot'];
            }
            //Flag
            if ($location->getIcon() !== null && is_file($flag_path . $location->getIcon())) {
                $flag = $flag_path . $location->getIcon();
            } else {
                $flag = '';
            }
            $features[] = [
                'type'       => 'Feature',
                'id'         => $id,
                'valid'      => $location->isValid() && $location->knownLatLon(),
                'geometry'   => [
                    'type'        => 'Point',
                    'coordinates' => $location->getGeoJsonCoords(),
                ],
                'properties' => [
                    'icon'    => [
                        'name'  => 'globe',
                        'color' => '#1e90ff',
                    ],
                    'tooltip' => strip_tags($place->getFullName()),
                    'summary' => view('place-sidebar', [
                        'showlink' => $showlink,
                        'flag'     => $flag,
                        'place'    => $place,
                        'stats'    => $placeStats,
                    ]),
                    'zoom'    => (int)($location->getZoom() ?? 2),
                ],
            ];
        }
        $code = empty($features) ? 204 : 200;
        return new JsonResponse([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ], $code);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getAdminMapDataAction(Request $request): JsonResponse
    {
        $id  = $request->get('id', 0);
        $row = Database::prepare("SELECT * FROM `##placelocation` WHERE pl_id = :id")
            ->execute(['id' => $id])
            ->fetchOneRow();

        if (empty($row)) {
            $json = [
                'zoom'        => self::OSM_MIN_ZOOM,
                'coordinates' => [
                    0,
                    0,
                ],
            ];
        } else {
            $json = [
                'zoom'        => $row->pl_zoom ? $row->pl_zoom : self::OSM_MIN_ZOOM,
                'coordinates' => [
                    $row->pl_lati ? strtr($row->pl_lati, [
                        'N' => '',
                        'S' => '-',
                        ',' => '.',
                    ]) : 0,
                    $row->pl_long ? strtr($row->pl_long, [
                        'E' => '',
                        'W' => '-',
                        ',' => '.',
                    ]) : 0,
                ],
            ];
        }

        return new JsonResponse($json);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     * @throws Exception
     */
    public function getAdminExportAction(Request $request): RedirectResponse
    {
        $parent_id = (int)$request->get('parent_id');
        $format    = $request->get('format', 'csv');
        $maxlevel  = (int)Database::prepare("SELECT max(pl_level) FROM `##placelocation`")->execute()->fetchOne();
        $startfqpn = [];
        $hierarchy = $this->gethierarchy($parent_id);
        $geojson   = [];

        // Create the file name
        $place_name = empty($hierarchy) ? 'Global' : $hierarchy[0]->fqpn; // $hierarchy[0] always holds the full placename
        $place_name = str_replace(Place::GEDCOM_SEPARATOR, '-', $place_name);
        $filename   = 'Places-' . preg_replace('/[^a-zA-Z0-9\-\.]/', '', $place_name) . '.' . $format;

        // Fill in the place names for the starting conditions
        foreach ($hierarchy as $level => $record) {
            $startfqpn[$level] = $record->pl_place;
        }
        $startfqpn = array_pad($startfqpn, $maxlevel + 1, '');

        // Generate an array containing the data to output
        $this->buildLevel($parent_id, $startfqpn, $places);

        if ($format === 'csv') {
            // Create the header line for the output file (always English)
            $placenames[] = I18N::translate('Level');
            for ($i = 0; $i <= $maxlevel; $i++) {
                $placenames[] = 'Place' . $i;
            }
            $header = array_merge($placenames, [
                'Longitude',
                'Latitude',
                'Zoom',
                'Icon',
            ]);
            array_unshift($places, $header);
        } else {
            $geojson = [
                'type'     => 'FeatureCollection',
                'features' => [],
            ];
        }
        // Output the data
        try {
            $fp = fopen('php://output', 'wb');
            header_remove();
            header("Content-Type: application/download charset=utf-8");
            header("Content-Disposition: attachment; filename=$filename");

            foreach ($places as $place) {
                if ($format === 'csv') {
                    fputcsv($fp, $place);
                } else {
                    if (!$place['pl_long'] || !$place['pl_lati']) {
                        continue;
                    }
                    $fqpn = implode(
                        Place::GEDCOM_SEPARATOR,
                        array_reverse(
                            array_filter(
                                array_slice($place, 1, $maxlevel + 1)
                            )
                        )
                    );
                    $long = (float)strtr($place['pl_long'], [
                        'E' => '',
                        'W' => '-',
                        ',' => '.',
                    ]);
                    $lati = (float)strtr($place['pl_lati'], [
                        'N' => '',
                        'S' => '-',
                        ',' => '.',
                    ]);

                    $geojson['features'][] = [
                        'type'       => 'Feature',
                        'geometry'   => [
                            'type'        => 'Point',
                            'coordinates' => [
                                $long,
                                $lati,
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
            }
            if ($format === 'geojson') {
                $jsonstr = json_encode($geojson, JSON_PRETTY_PRINT);
                header("Content-Length: " . strlen($jsonstr));
                fwrite($fp, $jsonstr);
            }
            fclose($fp);
        } catch (Exception $e) {
            Log::addErrorLog($e->getMessage());
            FlashMessages::addMessage($e->getMessage(), 'error');
        }

        return new RedirectResponse(route('admin-module', [
            'module' => $this->getName(),
            'action' => 'AdminPlaces',
        ]));
    }

    /**
     * @param Request $request
     *
     * @return object
     */
    public function getAdminImportFormAction(Request $request)
    {
        $parent_id   = (int)$request->get('parent_id');
        $inactive    = (int)$request->get('inactive');
        $breadcrumbs = [
            route('admin-control-panel') => I18N::translate('Control panel'),
            route('admin-modules')       => I18N::translate('Module administration'),
            route(
                'admin-module',
                [
                    'module'    => $this->getName(),
                    'action'    => 'AdminPlaces',
                    'parent_id' => 0,
                    'inactive'  => $inactive,
                ]
            )                            => $this->getTitle() . ' (' . I18N::translate('Geographic data') . ')',
            I18N::translate('Import file'),
        ];
        $files       = $this->findFiles(WT_MODULES_DIR . $this->getName() . '/extra', [
            'csv',
            'geojson',
            'json',
        ]);
        uasort(
            $files,
            function ($a, $b) {
                $la = strlen($a);
                $lb = strlen($b);

                return $la === $lb ? I18N::strcasecmp($a, $b) : $la - $lb;
            }
        );

        return (object)[
            'name' => 'admin/map-import-form',
            'data' => [
                'title'       => I18N::translate('Import geographic data'),
                'module'      => $this->getName(),
                'breadcrumbs' => $breadcrumbs,
                'parent_id'   => $parent_id,
                'inactive'    => $inactive,
                'files'       => $files,
            ],
        ];
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
    public function postAdminImportAction(Request $request): RedirectResponse
    {
        $serverfile  = $request->get('serverfile');
        $options     = $request->get('import-options');
        $inactive    = $request->get('inactive');
        $filename    = '';
        $places      = [];
        $input_array = [];
        $fields      = 0;
        $delimiter   = '';
        $field_names = [
            'pl_level',
            'pl_long',
            'pl_lati',
            'pl_zoom',
            'pl_icon',
            'fqpn',
        ];

        if ($serverfile !== '') {  // first choice is file on server
            $filename = WT_MODULES_DIR . $this->getName() . '/extra/' . $serverfile;
        } elseif ($_FILES['localfile']['error'] === UPLOAD_ERR_OK) { // 2nd choice is local file
            $filename = $_FILES['localfile']['tmp_name'];
        }

        if (is_file($filename)) {
            $string   = file_get_contents($filename);
            $filetype = '?';

            // Check the filetype
            if (stripos($string, 'FeatureCollection') !== false) {
                $filetype = 'geojson';
            } else {
                $input_array = preg_split("/\r?\n/", $string, -1, PREG_SPLIT_NO_EMPTY);
                $record      = $input_array[0];

                if (strpos($record, ';') !== false) {
                    $delimiter = ';';
                } elseif (strpos($record, ',') !== false) {
                    $delimiter = ',';
                }
                if ($delimiter !== '') {
                    if (!is_numeric($record[0])) { // lose the header
                        array_shift($input_array);
                    }

                    // are the records in a format we can read
                    $row    = explode($delimiter, $input_array[0]);
                    $fields = count($row);
                    if ($fields >= 6 &&
                        (bool)preg_match("/[SN][0-9]*\.?[0-9]*/", $row[$fields - 3]) &&
                        (bool)preg_match("/[EW][0-9]*\.?[0-9]*/", $row[$fields - 4])) {
                        $filetype = 'csv';
                    }
                }
            }

            switch ($filetype) {
                case 'geojson':
                    $input_array = json_decode($string);
                    foreach ($input_array->features as $feature) {
                        $places[] = array_combine(
                            $field_names,
                            [
                                isset($feature->properties->level) ? $feature->properties->level : substr_count(
                                    $feature->properties->name,
                                    ','
                                ),
                                ($feature->geometry->coordinates[0] < 0 ? 'W' : 'E') . abs(
                                    $feature->geometry->coordinates[0]
                                ),
                                ($feature->geometry->coordinates[1] < 0 ? 'S' : 'N') . abs(
                                    $feature->geometry->coordinates[1]
                                ),
                                isset($feature->properties->zoom) ? $feature->properties->zoom : null,
                                isset($feature->properties->icon) ? $feature->properties->icon : null,
                                $feature->properties->name,
                            ]
                        );
                    }
                    break;
                case 'csv':
                    foreach ($input_array as $line) {
                        $row = explode($delimiter, $line);
                        array_walk(
                            $row,
                            function (&$item) {
                                $item = ($item === '') ? null : trim($item, '"\'');
                            }
                        );
                        // convert separate place fields into a comma separated placename
                        $row[]    = implode(
                            Place::GEDCOM_SEPARATOR,
                            array_filter(
                                array_reverse(
                                    array_splice($row, 1, $fields - 5)
                                )
                            )
                        );
                        $places[] = array_combine($field_names, $row);
                    }
                    break;
                default:
                    //invalid file type
            }

            if ($filetype !== '?') {
                if ((bool)$request->get('cleardatabase')) {
                    Database::exec("TRUNCATE TABLE `##placelocation`");
                }
                //process places
                $added   = 0;
                $updated = 0;

                //sort places by level
                usort(
                    $places,
                    function (array $a, array $b) {
                        if ((int)$a['pl_level'] === (int)$b['pl_level']) {
                            return I18N::strcasecmp($a['fqpn'], $b['fqpn']);
                        } else {
                            return (int)$a['pl_level'] - (int)$b['pl_level'];
                        }
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
                        $location->update((object)$place);
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
                            $new_location = new Location($new_fqpn,
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
                    I18N::translate(
                        'locations updated: %s, locations added: %s',
                        I18N::number($updated),
                        I18N::number($added)
                    ),
                    $added + $updated === 0 ? 'info' : 'success'
                );
            } else {
                FlashMessages::addMessage(I18N::translate('Unable to detect the file format: %s', $filename), 'danger');
            }
        } else {
            FlashMessages::addMessage(I18N::translate('Unable to open file: %s', $filename), 'danger');
        }

        return new RedirectResponse(
            route(
                'admin-module',
                [
                    'module'   => $this->getName(),
                    'action'   => 'AdminPlaces',
                    'inactive' => $inactive,
                ]
            )
        );
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     * @throws Exception
     */
    public function postAdminImportPlacesAction(Request $request): RedirectResponse
    {
        $gedcomName = $request->get('ged');
        $inactive   = (int)$request->get('inactive');
        $tree       = Tree::findByName($gedcomName);

        // Get all the places from the places table ...
        $places = Database::prepare(
            "
				SELECT
				CONCAT_WS(:separator, t1.p_place, t2.p_place, t3.p_place, t4.p_place, t5.p_place, t6.p_place, t7.p_place, t8.p_place) AS fqpn
				FROM `##places` t1
				LEFT JOIN `##places` t2 ON t1.p_parent_id = t2.p_id
				LEFT JOIN `##places` t3 ON t2.p_parent_id = t3.p_id
				LEFT JOIN `##places` t4 ON t3.p_parent_id = t4.p_id
				LEFT JOIN `##places` t5 ON t4.p_parent_id = t5.p_id
				LEFT JOIN `##places` t6 ON t5.p_parent_id = t6.p_id
				LEFT JOIN `##places` t7 ON t6.p_parent_id = t7.p_id
				LEFT JOIN `##places` t8 ON t7.p_parent_id = t8.p_id
				WHERE t1.p_file = :gedcom
				ORDER BY t1.p_parent_id
			   "
        )->execute(
            [
                'separator' => Place::GEDCOM_SEPARATOR,
                'gedcom'    => $tree->getTreeId(),
            ]
        )
            ->fetchOneColumn();

        // ... and the placelocation table
        $locations = Database::prepare(
            "
				SELECT
				CONCAT_WS(:separator, t1.pl_place, t2.pl_place, t3.pl_place, t4.pl_place, t5.pl_place, t6.pl_place, t7.pl_place, t8.pl_place) AS fqpn
				FROM `##placelocation` AS t1
				LEFT JOIN `##placelocation` AS t2 ON t1.pl_parent_id = t2.pl_id
				LEFT JOIN `##placelocation` AS t3 ON t2.pl_parent_id = t3.pl_id
				LEFT JOIN `##placelocation` AS t4 ON t3.pl_parent_id = t4.pl_id
				LEFT JOIN `##placelocation` AS t5 ON t4.pl_parent_id = t5.pl_id
				LEFT JOIN `##placelocation` AS t6 ON t5.pl_parent_id = t6.pl_id
				LEFT JOIN `##placelocation` AS t7 ON t6.pl_parent_id = t7.pl_id
				LEFT JOIN `##placelocation` AS t8 ON t8.pl_parent_id = t8.pl_id
				ORDER BY t1.pl_parent_id
			   "
        )->execute(['separator' => Place::GEDCOM_SEPARATOR])
            ->fetchOneColumn();

        // Compare the two ...
        $diff = array_diff($places, $locations);
        // ... and process the differences
        if (!empty($diff)) {
            unset($places, $locations);
            $inserted        = 0;
            $nextRecordId    = Database::prepare("SELECT MAX(pl_id)+1 FROM `##placelocation`")->fetchOne();
            $insertRecordQry = Database::prepare(
                "INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place)" .
                " VALUES (:id, :parent, :level, :place)"
            );
            $checkRecordQry  = Database::prepare(
                "SELECT pl1.pl_id" .
                " FROM	  `##placelocation` AS pl1" .
                " LEFT JOIN `##placelocation` AS pl2 ON (pl1.pl_parent_id = pl2.pl_id)" .
                " LEFT JOIN `##placelocation` AS pl3 ON (pl2.pl_parent_id = pl3.pl_id)" .
                " LEFT JOIN `##placelocation` AS pl4 ON (pl3.pl_parent_id = pl4.pl_id)" .
                " LEFT JOIN `##placelocation` AS pl5 ON (pl4.pl_parent_id = pl5.pl_id)" .
                " LEFT JOIN `##placelocation` AS pl6 ON (pl5.pl_parent_id = pl6.pl_id)" .
                " LEFT JOIN `##placelocation` AS pl7 ON (pl6.pl_parent_id = pl7.pl_id)" .
                " LEFT JOIN `##placelocation` AS pl8 ON (pl7.pl_parent_id = pl8.pl_id)" .
                " LEFT JOIN `##placelocation` AS pl9 ON (pl8.pl_parent_id = pl9.pl_id)" .
                " WHERE CONCAT_WS(:separator1, pl1.pl_place, pl2.pl_place, pl3.pl_place, pl4.pl_place, pl5.pl_place, pl6.pl_place, pl7.pl_place, pl8.pl_place, pl9.pl_place) LIKE CONCAT('%', :f1, '%')" .
                " AND CONCAT_WS(:separator2, pl1.pl_place, pl2.pl_place, pl3.pl_place, pl4.pl_place, pl5.pl_place, pl6.pl_place, pl7.pl_place, pl8.pl_place, pl9.pl_place) NOT LIKE CONCAT('%,%', :f1, '%')"
            );

            foreach ($diff as $place) {
                $parent_id   = 0;
                $place_parts = array_reverse(explode(Place::GEDCOM_SEPARATOR, $place));
                $search      = '';
                for ($i = 0, $count = count($place_parts); $i < $count; $i++) {
                    $place_part = $place_parts[$i];
                    $search     = $place_part . Place::GEDCOM_SEPARATOR . $search;
                    $search     = trim($search, Place::GEDCOM_SEPARATOR);
                    $id         = $checkRecordQry->execute(
                        [
                            'separator1' => Place::GEDCOM_SEPARATOR,
                            'separator2' => Place::GEDCOM_SEPARATOR,
                            'f1'         => $search,
                            'f2'         => $search,
                        ]
                    )->fetchOne();

                    if ($id === null) {
                        $inserted++;
                        $insertRecordQry->execute(
                            [
                                'id'     => $nextRecordId++,
                                'parent' => $parent_id,
                                'level'  => $i,
                                'place'  => $place_part,
                            ]
                        );
                    } else {
                        $parent_id = $id;
                    }
                }
            }
            FlashMessages::addMessage(
                I18N::translate(
                    '%s Records added. Now use the edit page to add the coordinates etc.',
                    $inserted
                ),
                'success'
            );
        } else {
            FlashMessages::addMessage(I18N::translate('No Records added.'));
        }

        return new RedirectResponse(
            route(
                'admin-module',
                [
                    'module'   => $this->getName(),
                    'action'   => 'AdminPlaces',
                    'inactive' => $inactive,
                ]
            )
        );
    }

    /**
     * @param $parent_id
     * @param $placename
     * @param $places
     *
     * @throws Exception
     */
    private function buildLevel($parent_id, $placename, &$places)
    {
        $level = array_search('', $placename);
        $rows  = (array)Database::prepare(
            "SELECT pl_level, pl_id, pl_place, pl_long, pl_lati, pl_zoom, pl_icon FROM `##placelocation` WHERE pl_parent_id=? ORDER BY pl_place"
        )
            ->execute([$parent_id])
            ->fetchAll(\PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            foreach ($rows as $row) {
                $index             = $row['pl_id'];
                $placename[$level] = $row['pl_place'];
                $places[]          = array_merge([$row['pl_level']], $placename, array_splice($row, 3));
                $this->buildLevel($index, $placename, $places);
            }
        }
    }

    /**
     * recursively find all of the files of specified types on the server
     *
     * @param string   $path
     * @param string[] $filetypes
     *
     * @return array
     */
    private function findFiles($path, $filetypes)
    {
        $placefiles = [];

        try {
            $di = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
            $it = new \RecursiveIteratorIterator($di);

            foreach ($it as $file) {
                if (in_array($file->getExtension(), $filetypes)) {
                    $placefiles[] = $file->getFilename();
                }
            }
        } catch (Exception $ex) {
            DebugBar::addThrowable($ex);
            Log::addErrorLog(basename($ex->getFile()) . ' - line: ' . $ex->getLine() . ' - ' . $ex->getMessage());
        }

        return $placefiles;
    }
}
