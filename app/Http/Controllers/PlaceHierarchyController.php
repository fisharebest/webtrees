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

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Stats;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PlaceHierarchyController
 *
 * @package Fisharebest\Webtrees\Http\Controllers
 */
class PlaceHierarchyController extends AbstractBaseController
{
    const MAP_MODULE = 'openstreetmap';

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function show(Request $request, Tree $tree): Response
    {
        $action     = $request->query->get('action', 'hierarchy');
        $parent     = $request->query->get('parent', []);
        $fqpn       = implode(Place::GEDCOM_SEPARATOR, array_reverse($parent));
        $place      = new Place($fqpn, $tree);
        $content    = '';
        $showmap    = Site::getPreference('map-provider') !== '';
        $data       = null;
        $note       = false;

        if ($showmap) {
            $note = true;
            $content .= view('place-map', [
                'module' => self::MAP_MODULE,
                'ref'    => $fqpn,
                'type'   => 'placelist',
                'data'   => $this->mapData($tree, $fqpn),
            ]);
        }

        switch ($action) {
            case 'list':
                $nextaction = ['hierarchy' => I18N::translate('Show place hierarchy')];
                $content .= view('place-list', $this->getList($tree));
                break;
            case 'hierarchy':
            case 'hierarchy-e':
                $nextaction = ['list' => I18N::translate('Show all places in a list')];
                $data       = $this->getHierarchy($tree, $place, $parent);
                $content .= (null === $data || $showmap) ? '' : view('place-hierarchy', $data);
                if (null === $data || $action === 'hierarchy-e') {
                    $content .= view('place-events', $this->getEvents($tree, $place));
                }
                break;
            default:
                throw new NotFoundHttpException('Invalid action');
        }

        $breadcrumbs = $this->breadcrumbs($place);

        return $this->viewResponse(
            'places-page',
            [
                'title'          => I18N::translate('Places'),
                'note'           => $note,
                'tree'           => $tree,
                'current'        => $breadcrumbs['current'],
                'breadcrumbs'    => $breadcrumbs['breadcrumbs'],
                'parent'         => $parent,
                'place'          => $fqpn,
                'content'        => $content,
                'showeventslink' => null !== $data && !$place->isEmpty() && $action !== 'hierarchy-e',
                'nextaction'     => $nextaction,
            ]
        );
    }

    /**
     * @param $tree
     *
     * @return array
     * @throws \Exception
     */
    private function getList(Tree $tree): array
    {
        $list_places = Place::allPlaces($tree);
        $numfound    = count($list_places);

        if ($numfound === 0) {
            $columns = [];
        } else {
            $divisor = $numfound > 20 ? 3 : 2;
            $columns = array_chunk($list_places, (int) ceil($numfound / $divisor));
        }

        return [
            'columns' => $columns,
        ];
    }

    /**
     * @param Tree   $tree
     * @param Place  $place
     * @param string $parent []
     *
     * @return array|null
     * @throws \Exception
     */
    private function getHierarchy($tree, $place, $parent)
    {
        $child_places = $place->getChildPlaces();
        $numfound     = count($child_places);

        if ($numfound > 0) {
            $divisor = $numfound > 20 ? 3 : 2;

            return
                [
                    'tree'      => $tree,
                    'col_class' => "w-" . ($divisor === 2 ? "25" : "50"),
                    'columns'   => array_chunk($child_places, (int) ceil($numfound / $divisor)),
                    'place'     => $place,
                    'parent'    => $parent,
                ];
        }

        return null;
    }

    /**
     * @param Tree  $tree
     * @param Place $place
     *
     * @return array
     * @throws \Exception
     */
    private function getEvents($tree, $place): array
    {
        $indilist = [];
        $famlist  = [];

        $xrefs = Database::prepare(
            "SELECT DISTINCT pl_gid FROM `##placelinks` WHERE pl_p_id=:id AND pl_file=:gedcom"
        )->execute([
            'id'     => $place->getPlaceId(),
            'gedcom' => $tree->getTreeId(),
        ])->fetchOneColumn();

        foreach ($xrefs as $xref) {
            $record = GedcomRecord::getInstance($xref, $tree);
            if ($record && $record->canShow()) {
                if ($record instanceof Individual) {
                    $indilist[] = $record;
                }
                if ($record instanceof Family) {
                    $famlist[] = $record;
                }
            }
        }

        return
            [
                'indilist' => $indilist,
                'famlist'  => $famlist,
            ];
    }

    /**
     * @param Place $place
     *
     * @return array
     */
    private function breadcrumbs($place): array
    {
        $breadcrumbs = [];
        if (!$place->isEmpty()) {
            $breadcrumbs[] = $place;
            $parent_place  = $place->getParentPlace();
            while (!$parent_place->isEmpty()) {
                $breadcrumbs[] = $parent_place;
                $parent_place  = $parent_place->getParentPlace();
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            $current     = array_pop($breadcrumbs);
        } else {
            $current = '';
        }

        return [
            'breadcrumbs' => $breadcrumbs,
            'current'     => $current,
        ];
    }

    /**
     * @param Tree   $tree
     * @param string $reference
     *
     * @return array
     */
    protected function mapData(Tree $tree, $reference): array
    {
        $placeObj  = new Place($reference, $tree);
        $places    = $placeObj->getChildPlaces();
        $features  = [];
        $flag_path = WT_MODULES_DIR . 'openstreetmap/';
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
            foreach (['INDI', 'FAM'] as $type) {
                $tmp               = $stats->statsPlaces($type, false, $place->getPlaceId());
                $placeStats[$type] = empty($tmp) ? 0 : $tmp[0]->tot;
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
                    'zoom'    => (int) ($location->getZoom() ?? 2),
                ],
            ];
        }

        return [
            'type'     => 'FeatureCollection',
            'features' => $features,
        ];
    }
}
