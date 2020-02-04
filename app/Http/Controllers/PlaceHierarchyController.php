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

namespace Fisharebest\Webtrees\Http\Controllers;

use Exception;
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Statistics;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function redirect;
use function view;

/**
 * Class PlaceHierarchyController
 */
class PlaceHierarchyController extends AbstractBaseController
{
    /** @var SearchService */
    private $search_service;

    /** @var Statistics */
    private $statistics;

    /**
     * PlaceHierarchy constructor.
     *
     * @param SearchService $search_service
     * @param Statistics    $statistics
     */
    public function __construct(SearchService $search_service, Statistics $statistics)
    {
        $this->search_service = $search_service;
        $this->statistics     = $statistics;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $module   = $request->getAttribute('module');
        $action   = $request->getAttribute('action');
        $action2  = $request->getQueryParams()['action2'] ?? 'hierarchy';
        $place_id = (int) ($request->getQueryParams()['place_id'] ?? 0);
        $place    = Place::find($place_id, $tree);

        // Request for a non-existant place?
        if ($place_id !== $place->id()) {
            return redirect($place->url());
        }

        $content    = '';
        $showmap    = Site::getPreference('map-provider') !== '';
        $data       = null;

        if ($showmap) {
            $content .= view('modules/place-hierarchy/map', [
                'data'     => $this->mapData($place),
                'provider' => [
                    'name'    => 'OpenStreetMap.Mapnik',
                    'options' => []
                ]
            ]);
        }

        switch ($action2) {
            case 'list':
                $nextaction = ['hierarchy' => I18N::translate('Show place hierarchy')];
                $content .= view('modules/place-hierarchy/list', $this->getList($tree, $this->search_service));
                break;
            case 'hierarchy':
            case 'hierarchy-e':
                $nextaction = ['list' => I18N::translate('Show all places in a list')];
                $data       = $this->getHierarchy($tree, $place);
                $content .= (null === $data || $showmap) ? '' : view('place-hierarchy', $data);
                if (null === $data || $action2 === 'hierarchy-e') {
                    $content .= view('modules/place-hierarchy/events', $this->getEvents($tree, $place));
                }
                break;
            default:
                throw new HttpNotFoundException('Invalid action');
        }

        $breadcrumbs = $this->breadcrumbs($place);

        return $this->viewResponse(
            'modules/place-hierarchy/page',
            [
                'title'          => I18N::translate('Places'),
                'tree'           => $tree,
                'current'        => $breadcrumbs['current'],
                'breadcrumbs'    => $breadcrumbs['breadcrumbs'],
                'place'          => $place,
                'content'        => $content,
                'showeventslink' => null !== $data && $place->gedcomName() !== '' && $action2 !== 'hierarchy-e',
                'nextaction'     => $nextaction,
                'module'         => $module,
                'action'         => $action,
            ]
        );
    }

    /**
     * @param Tree          $tree
     * @param SearchService $search_service
     *
     * @return Place[][]
     */
    private function getList(Tree $tree, SearchService $search_service): array
    {
        $places = $search_service->searchPlaces($tree, '')
            ->sort(static function (Place $x, Place $y): int {
                return $x->gedcomName() <=> $y->gedcomName();
            })
            ->all();

        $numfound = count($places);

        if ($numfound === 0) {
            $columns = [];
        } else {
            $divisor = $numfound > 20 ? 3 : 2;
            $columns = array_chunk($places, (int) ceil($numfound / $divisor));
        }

        return [
            'columns' => $columns,
        ];
    }


    /**
     * @param Tree     $tree
     * @param Place    $place
     *
     * @return array|null
     * @throws Exception
     */
    private function getHierarchy(Tree $tree, Place $place): ?array
    {
        $child_places = $place->getChildPlaces();
        $numfound     = count($child_places);

        if ($numfound > 0) {
            $divisor = $numfound > 20 ? 3 : 2;

            return
                [
                    'tree'      => $tree,
                    'col_class' => 'w-' . ($divisor === 2 ? '25' : '50'),
                    'columns'   => array_chunk($child_places, (int) ceil($numfound / $divisor)),
                    'place'     => $place,
                ];
        }

        return null;
    }

    /**
     * @param Tree  $tree
     * @param Place $place
     *
     * @return array
     * @throws Exception
     */
    private function getEvents($tree, $place): array
    {
        $indilist = DB::table('individuals')
            ->join('placelinks', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'i_file')
                    ->on('pl_gid', '=', 'i_id');
            })
            ->where('i_file', '=', $tree->id())
            ->where('pl_p_id', '=', $place->id())
            ->select(['individuals.*'])
            ->distinct()
            ->get()
            ->map(Individual::rowMapper($tree))
            ->filter(Individual::accessFilter())
            ->all();

        $famlist = DB::table('families')
            ->join('placelinks', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'f_file')
                    ->on('pl_gid', '=', 'f_id');
            })
            ->where('f_file', '=', $tree->id())
            ->where('pl_p_id', '=', $place->id())
            ->select(['families.*'])
            ->distinct()
            ->get()
            ->map(Family::rowMapper($tree))
            ->filter(Family::accessFilter())
            ->all();

        return [
            'indilist' => $indilist,
            'famlist'  => $famlist,
            'tree'     => $tree,
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
        if ($place->gedcomName() !== '') {
            $breadcrumbs[] = $place;
            $parent_place  = $place->parent();
            while ($parent_place->gedcomName() !== '') {
                $breadcrumbs[] = $parent_place;
                $parent_place  = $parent_place->parent();
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
     * @param Place $placeObj
     *
     * @return array
     */
    protected function mapData(Place $placeObj): array
    {
        $places    = $placeObj->getChildPlaces();
        $features  = [];
        $sidebar   = '';
        $flag_path = Webtrees::MODULES_DIR . 'openstreetmap/';
        $showlink  = true;
        if ($places === []) {
            $places[] = $placeObj;
            $showlink = false;
        }
        foreach ($places as $id => $place) {
            $location = new Location($place->gedcomName());
            //Flag
            if ($location->icon() !== '' && is_file($flag_path . $location->icon())) {
                $flag = $flag_path . $location->icon();
            } else {
                $flag = '';
            }
            if ($location->latitude() === 0.0 && $location->longitude() === 0.0) {
                $sidebar_class = 'unmapped';
            } else {
                $sidebar_class = 'mapped';
                $features[]    = [
                    'type'       => 'Feature',
                    'id'         => $id,
                    'geometry'   => [
                        'type'        => 'Point',
                        'coordinates' => [$location->longitude(), $location->latitude()],
                    ],
                    'properties' => [
                        'tooltip' => $place->gedcomName(),
                        'popup'   => view('modules/place-hierarchy/popup', [
                            'showlink'  => $showlink,
                            'flag'      => $flag,
                            'place'     => $place,
                            'latitude'  => $location->latitude(),
                            'longitude' => $location->longitude(),
                        ]),
                        'zoom'    => $location->zoom() ?: 2,
                    ],
                ];
            }

            //Stats
            $placeStats = [];
            foreach (['INDI', 'FAM'] as $type) {
                $tmp               = $this->statistics->statsPlaces($type, '', $place->id());
                $placeStats[$type] = $tmp === [] ? 0 : $tmp[0]->tot;
            }
            $sidebar .= view('modules/place-hierarchy/sidebar', [
                'showlink'      => $showlink,
                'flag'          => $flag,
                'id'            => $id,
                'place'         => $place,
                'sidebar_class' => $sidebar_class,
                'stats'         => $placeStats,
            ]);
        }

        return [
            'sidebar' => $sidebar,
            'markers' => [
                'type'     => 'FeatureCollection',
                'features' => $features,
            ]
        ];
    }
}
