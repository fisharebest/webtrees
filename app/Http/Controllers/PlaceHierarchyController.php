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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PlaceHierarchyController
 *
 * @package Fisharebest\Webtrees\Http\Controllers
 */
class PlaceHierarchyController extends AbstractBaseController
{
    private const MAP_MODULE = 'openstreetmap';

    /**
     * @var Statistics
     */
    private $statistics;

    /**
     * TopGivenNamesModule constructor.
     *
     * @param Statistics $statistics
     */
    public function __construct(Statistics $statistics)
    {
        $this->statistics = $statistics;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param SearchService          $search_service
     *
     * @return ResponseInterface
     */
    public function show(ServerRequestInterface $request, Tree $tree, SearchService $search_service): ResponseInterface
    {
        $action2    = $request->getQueryParams()['action2'] ?? 'hierarchy';
        $parent     = $request->getQueryParams()['parent'] ?? [];
        $fqpn       = implode(Gedcom::PLACE_SEPARATOR, array_reverse($parent));
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

        switch ($action2) {
            case 'list':
                $nextaction = ['hierarchy' => I18N::translate('Show place hierarchy')];
                $content .= view('place-list', $this->getList($tree, $search_service));
                break;
            case 'hierarchy':
            case 'hierarchy-e':
                $nextaction = ['list' => I18N::translate('Show all places in a list')];
                $data       = $this->getHierarchy($tree, $place, $parent);
                $content .= (null === $data || $showmap) ? '' : view('place-hierarchy', $data);
                if (null === $data || $action2 === 'hierarchy-e') {
                    $content .= view('place-events', $this->getEvents($tree, $place));
                }
                break;
            default:
                throw new NotFoundHttpException('Invalid action');
        }

        $breadcrumbs = $this->breadcrumbs($place);

        //route is assumed to be 'module'
        $module = $request->get('module');
        $action = $request->get('action');
        
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
     * @param Tree   $tree
     * @param Place  $place
     * @param string $parent []
     *
     * @return array|null
     * @throws Exception
     */
    private function getHierarchy($tree, $place, $parent): ?array
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
            ->map(Individual::rowMapper())
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
            ->map(Family::rowMapper())
            ->filter(Family::accessFilter())
            ->all();

        return [
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
        $flag_path = Webtrees::MODULES_PATH . 'openstreetmap/';
        $showlink  = true;
        if (empty($places)) {
            $places[] = $placeObj;
            $showlink = false;
        }
        foreach ($places as $id => $place) {
            $location = new Location($place->gedcomName());
            //Stats
            $placeStats = [];
            foreach (['INDI', 'FAM'] as $type) {
                $tmp               = $this->statistics->statsPlaces($type, '', $place->id());
                $placeStats[$type] = empty($tmp) ? 0 : $tmp[0]->tot;
            }
            //Flag
            if ($location->icon() !== '' && is_file($flag_path . $location->icon())) {
                $flag = $flag_path . $location->icon();
            } else {
                $flag = '';
            }
            $features[] = [
                'type'       => 'Feature',
                'id'         => $id,
                'valid'      => $location->longitude() !== 0.0 || $location->latitude() !== 0.0,
                'geometry'   => [
                    'type'        => 'Point',
                    'coordinates' => [$location->longitude(), $location->latitude()],
                ],
                'properties' => [
                    'icon'    => [
                        'name'  => 'globe',
                        'color' => '#1e90ff',
                    ],
                    'tooltip' => $place->gedcomName(),
                    'summary' => view('place-sidebar', [
                        'showlink' => $showlink,
                        'flag'     => $flag,
                        'place'    => $place,
                        'stats'    => $placeStats,
                    ]),
                    'zoom'    => $location->zoom() ?: 2,
                ],
            ];
        }

        return [
            'type'     => 'FeatureCollection',
            'features' => $features,
        ];
    }
}
