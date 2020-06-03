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

namespace Fisharebest\Webtrees\Http\Controllers;

use Exception;
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\PlaceLocation;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Statistics;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_chunk;
use function array_pop;
use function array_reverse;
use function assert;
use function ceil;
use function count;
use function is_file;
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

        // Request for a non-existent place?
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
                    'url'    => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    'options' => [
                        'attribution' => '<a href="https://www.openstreetmap.org/copyright">&copy; OpenStreetMap</a> contributors',
                        'max_zoom'    => 19
                    ]
                ]
            ]);
        }

        switch ($action2) {
            case 'list':
                $nextaction = ['hierarchy' => I18N::translate('Show place hierarchy')];
                $content .= view('modules/place-hierarchy/list', $this->getList($tree));
                break;
            case 'hierarchy':
            case 'hierarchy-e':
                $nextaction = ['list' => I18N::translate('Show all places in a list')];
                $data       = $this->getHierarchy($place);
                $content .= (null === $data || $showmap) ? '' : view('place-hierarchy', $data);
                if (null === $data || $action2 === 'hierarchy-e') {
                    $content .= view('modules/place-hierarchy/events', [
                        'indilist' => $this->search_service->searchIndividualsInPlace($place),
                        'famlist'  => $this->search_service->searchFamiliesInPlace($place),
                        'tree'     => $place->tree(),
                    ]);
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
     * @param Tree $tree
     *
     * @return Place[][]
     */
    private function getList(Tree $tree): array
    {
        $places = $this->search_service->searchPlaces($tree, '')
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
     * @param Place $place
     *
     * @return array{'tree':Tree,'col_class':string,'columns':array<array<Place>>,'place':Place}|null
     * @throws Exception
     */
    private function getHierarchy(Place $place): ?array
    {
        $child_places = $place->getChildPlaces();
        $numfound     = count($child_places);

        if ($numfound > 0) {
            $divisor = $numfound > 20 ? 3 : 2;

            return
                [
                    'tree'      => $place->tree(),
                    'col_class' => 'w-' . ($divisor === 2 ? '25' : '50'),
                    'columns'   => array_chunk($child_places, (int) ceil($numfound / $divisor)),
                    'place'     => $place,
                ];
        }

        return null;
    }

    /**
     * @param Place $place
     *
     * @return array{'breadcrumbs':array<Place>,'current':Place|null}
     */
    private function breadcrumbs(Place $place): array
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
            $current = null;
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
        $show_link = true;

        if ($places === []) {
            $places[] = $placeObj;
            $show_link = false;
        }

        foreach ($places as $id => $place) {
            $location = new PlaceLocation($place->gedcomName());

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
                            'showlink'  => $show_link,
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
                'showlink'      => $show_link,
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
