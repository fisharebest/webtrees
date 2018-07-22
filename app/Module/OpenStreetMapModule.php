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
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Location;
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
    public function postAdminImportPlacesAction(Request $request): RedirectResponse
    {
        $gedcomName = $request->get('ged');
        $tree       = Tree::findByName($gedcomName);

        // Get all the places from the places table ...
        $places = Database::prepare(
        "SELECT
            CONCAT_WS(:separator, t1.p_place, t2.p_place, t3.p_place, t4.p_place, t5.p_place, t6.p_place, t7.p_place, t8.p_place) AS fqpn
            FROM `##places` t1
            LEFT JOIN `##places` t2 ON t1.p_parent_id = t2.p_id
            LEFT JOIN `##places` t3 ON t2.p_parent_id = t3.p_id
            LEFT JOIN `##places` t4 ON t3.p_parent_id = t4.p_id
            LEFT JOIN `##places` t5 ON t4.p_parent_id = t5.p_id
            LEFT JOIN `##places` t6 ON t5.p_parent_id = t6.p_id
            LEFT JOIN `##places` t7 ON t6.p_parent_id = t7.p_id
            LEFT JOIN `##places` t8 ON t7.p_parent_id = t8.p_id
            LEFT JOIN `##places` t9 ON t8.p_parent_id = t9.p_id
            WHERE t1.p_file = :gedcom
            ORDER BY t1.p_parent_id"
        )->execute([
            'separator' => Place::GEDCOM_SEPARATOR,
            'gedcom'    => $tree->getTreeId(),
        ])->fetchOneColumn();

        // ... and the placelocation table
        $locations = Database::prepare(
        "SELECT
            CONCAT_WS(:separator, t1.pl_place, t2.pl_place, t3.pl_place, t4.pl_place, t5.pl_place, t6.pl_place, t7.pl_place, t8.pl_place, t9.pl_place) AS fqpn
            FROM `##placelocation` AS t1
            LEFT JOIN `##placelocation` AS t2 ON t1.pl_parent_id = t2.pl_id
            LEFT JOIN `##placelocation` AS t3 ON t2.pl_parent_id = t3.pl_id
            LEFT JOIN `##placelocation` AS t4 ON t3.pl_parent_id = t4.pl_id
            LEFT JOIN `##placelocation` AS t5 ON t4.pl_parent_id = t5.pl_id
            LEFT JOIN `##placelocation` AS t6 ON t5.pl_parent_id = t6.pl_id
            LEFT JOIN `##placelocation` AS t7 ON t6.pl_parent_id = t7.pl_id
            LEFT JOIN `##placelocation` AS t8 ON t7.pl_parent_id = t8.pl_id
            LEFT JOIN `##placelocation` AS t9 ON t8.pl_parent_id = t9.pl_id
            ORDER BY t1.pl_parent_id"
        )->execute([
            'separator' => Place::GEDCOM_SEPARATOR,
        ])->fetchOneColumn();

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
                        $insertRecordQry->execute([
                            'id'     => $nextRecordId++,
                            'parent' => $parent_id,
                            'level'  => $i,
                            'place'  => $place_part,
                        ]);
                    } else {
                        $parent_id = $id;
                    }
                }
            }
            FlashMessages::addMessage(
                I18N::translate('%s Records added. Now use the edit page to add the coordinates etc.', $inserted),
                'success'
            );
        } else {
            FlashMessages::addMessage(I18N::translate('No Records added.'));
        }

        $url = route('admin-module', ['module' => $this->getName(), 'action' => 'AdminPlaces']);

        return new RedirectResponse($url);
    }
    }
