<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Http\RequestHandlers\MapDataEdit;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\PlaceLocation;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\LeafletJsService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_chunk;
use function array_pop;
use function array_reverse;
use function ceil;
use function count;
use function redirect;
use function route;
use function view;

class PlaceHierarchyListModule extends AbstractModule implements ModuleListInterface, RequestHandlerInterface
{
    use ModuleListTrait;

    protected const string ROUTE_URL = '/tree/{tree}/place-list{/place_id}';

    /** @var int The default access level for this module.  It can be changed in the control panel. */
    protected int $access_level = Auth::PRIV_USER;

    private LeafletJsService $leaflet_js_service;

    private ModuleService $module_service;

    private SearchService $search_service;

    /**
     * @param LeafletJsService $leaflet_js_service
     * @param ModuleService    $module_service
     * @param SearchService    $search_service
     */
    public function __construct(LeafletJsService $leaflet_js_service, ModuleService $module_service, SearchService $search_service)
    {
        $this->leaflet_js_service = $leaflet_js_service;
        $this->module_service     = $module_service;
        $this->search_service     = $search_service;
    }

    /**
     * Initialization.
     *
     * @return void
     */
    public function boot(): void
    {
        Registry::routeFactory()->routeMap()
            ->get(static::class, static::ROUTE_URL, $this);
    }

    public function title(): string
    {
        /* I18N: Name of a module/list */
        return I18N::translate('Place hierarchy');
    }

    public function description(): string
    {
        /* I18N: Description of the “Place hierarchy” module */
        return I18N::translate('The place hierarchy.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function listMenuClass(): string
    {
        return 'menu-list-plac';
    }

    /**
     * @return array<string>
     */
    public function listUrlAttributes(): array
    {
        return [];
    }

    /**
     * @param Tree $tree
     *
     * @return bool
     */
    public function listIsEmpty(Tree $tree): bool
    {
        return !DB::table('places')
            ->where('p_file', '=', $tree->id())
            ->exists();
    }

    /**
     * @param Tree                                      $tree
     * @param array<bool|int|string|array<string>|null> $parameters
     *
     * @return string
     */
    public function listUrl(Tree $tree, array $parameters = []): string
    {
        $parameters['tree'] = $tree->name();

        return route(static::class, $parameters);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree     = Validator::attributes($request)->tree();
        $user     = Validator::attributes($request)->user();
        $place_id = Validator::attributes($request)->integer('place_id', 0);

        Auth::checkComponentAccess($this, ModuleListInterface::class, $tree, $user);

        $action2  = Validator::queryParams($request)->string('action2', 'hierarchy');
        $place    = Place::find($place_id, $tree);

        // Request for a non-existent place?
        if ($place_id !== $place->id()) {
            return redirect($place->url());
        }

        $map_providers = $this->module_service->findByInterface(ModuleMapProviderInterface::class);

        $content = '';
        $showmap = $map_providers->isNotEmpty();
        $data    = null;

        if ($showmap) {
            $content .= view('modules/place-hierarchy/map', [
                'data'           => $this->mapData($place),
                'leaflet_config' => $this->leaflet_js_service->config(),
            ]);
        }

        switch ($action2) {
            case 'list':
            default:
                $alt_link = I18N::translate('Show place hierarchy');
                $alt_url  = $this->listUrl($tree, ['action2' => 'hierarchy', 'place_id' => $place_id]);
                $content .= view('modules/place-hierarchy/list', ['columns' => $this->getList($tree)]);
                break;
            case 'hierarchy':
            case 'hierarchy-e':
                $alt_link = I18N::translate('Show all places in a list');
                $alt_url  = $this->listUrl($tree, ['action2' => 'list', 'place_id' => 0]);
                $data     = $this->getHierarchy($place);
                $content .= ($data === null || $showmap) ? '' : view('place-hierarchy', $data);
                if ($data === null || $action2 === 'hierarchy-e') {
                    $content .= view('modules/place-hierarchy/events', [
                        'indilist' => $this->search_service->searchIndividualsInPlace($place),
                        'famlist'  => $this->search_service->searchFamiliesInPlace($place),
                        'tree'     => $place->tree(),
                    ]);
                }
        }

        if ($data !== null && $action2 !== 'hierarchy-e' && $place->gedcomName() !== '') {
            $events_link = $this->listUrl($tree, ['action2' => 'hierarchy-e', 'place_id' => $place_id]);
        } else {
            $events_link = '';
        }

        $breadcrumbs = $this->breadcrumbs($place);

        return $this->viewResponse('modules/place-hierarchy/page', [
            'alt_link'    => $alt_link,
            'alt_url'     => $alt_url,
            'breadcrumbs' => $breadcrumbs['breadcrumbs'],
            'content'     => $content,
            'current'     => $breadcrumbs['current'],
            'events_link' => $events_link,
            'place'       => $place,
            'title'       => I18N::translate('Place hierarchy'),
            'tree'        => $tree,
            'world_url'   => $this->listUrl($tree),
        ]);
    }

    /**
     * @param Place $place
     *
     * @return array<mixed>
     */
    protected function mapData(Place $place): array
    {
        $children  = $place->getChildPlaces();
        $features  = [];
        $sidebar   = '';
        $show_link = true;

        // No children?  Show ourself on the map instead.
        if ($children === []) {
            $children[] = $place;
            $show_link  = false;
        }

        foreach ($children as $id => $child) {
            $location = new PlaceLocation($child->gedcomName());

            if (Auth::isAdmin()) {
                $this_url = route(self::class, ['tree' => $child->tree()->name(), 'place_id' => $place->id()]);
                $edit_url = route(MapDataEdit::class, ['location_id' => $location->id(), 'url' => $this_url]);
            } else {
                $edit_url = '';
            }

            if ($location->latitude() === null || $location->longitude() === null) {
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
                        'tooltip' => $child->gedcomName(),
                        'popup'   => view('modules/place-hierarchy/popup', [
                            'edit_url'  => $edit_url,
                            'place'     => $child,
                            'latitude'  => $location->latitude(),
                            'longitude' => $location->longitude(),
                            'showlink'  => $show_link,
                        ]),
                    ],
                ];
            }

            $stats = [
                Family::RECORD_TYPE     => $this->familyPlaceLinks($child)->count(),
                Individual::RECORD_TYPE => $this->individualPlaceLinks($child)->count(),
                Location::RECORD_TYPE   => $this->locationPlaceLinks($child)->count(),
            ];

            $sidebar .= view('modules/place-hierarchy/sidebar', [
                'edit_url'      => $edit_url,
                'id'            => $id,
                'place'         => $child,
                'showlink'      => $show_link,
                'sidebar_class' => $sidebar_class,
                'stats'         => $stats,
            ]);
        }

        return [
            'bounds'  => (new PlaceLocation($place->gedcomName()))->boundingRectangle(),
            'sidebar' => $sidebar,
            'markers' => [
                'type'     => 'FeatureCollection',
                'features' => $features,
            ],
        ];
    }

    /**
     * @param Tree $tree
     *
     * @return array<array<Place>>
     */
    private function getList(Tree $tree): array
    {
        $places = $this->search_service->searchPlaces($tree, '')
            ->sort(static fn (Place $x, Place $y): int => I18N::comparator()($x->gedcomName(), $y->gedcomName()))
            ->all();

        $count = count($places);

        if ($places === []) {
            return [];
        }

        $columns = $count > 20 ? 3 : 2;

        return array_chunk($places, (int) ceil($count / $columns));
    }

    /**
     * @param Place $place
     *
     * @return array{columns:array<array<Place>>,place:Place,tree:Tree,col_class:string}|null
     */
    private function getHierarchy(Place $place): array|null
    {
        $child_places = $place->getChildPlaces();
        $numfound     = count($child_places);

        if ($numfound > 0) {
            $divisor = $numfound > 20 ? 3 : 2;

            return [
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
     * @return array{breadcrumbs:array<Place>,current:Place|null}
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
     * @param Place $place
     *
     * @return Builder
     */
    private function placeLinks(Place $place): Builder
    {
        return DB::table('places')
            ->join('placelinks', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'p_file')
                    ->on('pl_p_id', '=', 'p_id');
            })
            ->where('p_file', '=', $place->tree()->id())
            ->where('p_id', '=', $place->id());
    }

    /**
     * @param Place $place
     *
     * @return Builder
     */
    private function familyPlaceLinks(Place $place): Builder
    {
        return $this->placeLinks($place)
            ->join('families', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'f_file')
                    ->on('pl_gid', '=', 'f_id');
            });
    }

    /**
     * @param Place $place
     *
     * @return Builder
     */
    private function individualPlaceLinks(Place $place): Builder
    {
        return $this->placeLinks($place)
            ->join('individuals', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'i_file')
                    ->on('pl_gid', '=', 'i_id');
            });
    }

    /**
     * @param Place $place
     *
     * @return Builder
     */
    private function locationPlaceLinks(Place $place): Builder
    {
        return $this->placeLinks($place)
            ->join('other', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'o_file')
                    ->on('pl_gid', '=', 'o_id');
            })
            ->where('o_type', '=', Location::RECORD_TYPE);
    }
}
